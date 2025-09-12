<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the user management page with user data
     */
    public function index(Request $request)
    {
        try {
            // Get search and filter parameters
            $search = $request->get('search');
            $status = $request->get('status');
            $perPage = $request->get('per_page', 10);

            // Build query
            $query = User::query();

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($status && $status !== '') {
                $query->where('account_status', $status);
            }

            // Get paginated results
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Get statistics
            $stats = $this->getUserStats();

            // Allowed statuses for filter and select
            $allowedStatuses = [
                'active' => 'Active',
                'suspended' => 'Suspended',
                'not_verified' => 'Not Verified',
                'admin' => 'Admin',
            ];

            return view('AdminPage.UserManagement', compact('users', 'stats', 'allowedStatuses'));
        } catch (\Exception $e) {
            return view('AdminPage.UserManagement', [
                'users' => collect([]),
                'stats' => $this->getUserStats(),
                'error' => 'Failed to load users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $userId)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,suspended,not_verified,admin'
            ]);

            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $prev = $user->account_status;
            $user->account_status = $request->status;
            $user->save();

            // If promoted to admin, force 2FA setup next login
            if ($prev !== 'admin' && $user->account_status === 'admin') {
                $user->two_factor_enabled = false;
                $user->two_factor_secret = null;
                $user->two_factor_confirmed_at = null;
                $user->save();
            }

            app(\App\Services\AdminActivityLogger::class)->log('change_user_status', [
                'target_user_id' => $user->user_id,
                'new_status' => $user->account_status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy($userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Soft delete by setting status to deleted
            $user->account_status = 'deleted';
            $user->save();

            app(\App\Services\AdminActivityLogger::class)->log('delete_user', [
                'target_user_id' => $user->user_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        try {
            // Session-based per-minute limit (2 per minute per session)
            $nowMinute = date('YmdHi');
            $minuteKey = 'ux.min.' . $nowMinute;
            $currentCount = (int) $request->session()->get($minuteKey, 0);
            if ($currentCount >= 2) {
                return response()->json(['success' => false, 'message' => 'Export rate limit exceeded. Try again later.'], 429);
            }
            $request->session()->put($minuteKey, $currentCount + 1);

            // Cooldown 30s to avoid repeated export spamming
            $coolKey = 'ux.cool.users';
            $lastTs = (int) $request->session()->get($coolKey, 0);
            if (time() - $lastTs < 30) {
                return response()->json(['success' => false, 'message' => 'Please wait before exporting users again.'], 429);
            }
            $request->session()->put($coolKey, time());

            $search = $request->get('search');
            $status = $request->get('status');

            $query = User::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($status && $status !== '') {
                $query->where('account_status', $status);
            }

            $users = $query->get();

            $csvData = [];
            $csvData[] = ['User ID', 'First Name', 'Last Name', 'Email', 'Status', 'Email Verified', 'Created At', 'Last Login'];

            foreach ($users as $user) {
                $csvData[] = [
                    $user->user_id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->account_status,
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'
                ];
            }

            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            return response()->streamDownload(function () use ($csvData) {
                $output = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('account_status', 'active')->count();
            $suspendedUsers = User::where('account_status', 'suspended')->count();
            $notVerifiedUsers = User::where('account_status', 'not_verified')->count();
            $adminUsers = User::where('account_status', 'admin')->count();

            return [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
                'not_verified' => $notVerifiedUsers,
                'admin' => $adminUsers
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'suspended' => 0,
                'not_verified' => 0,
                'admin' => 0
            ];
        }
    }
}
