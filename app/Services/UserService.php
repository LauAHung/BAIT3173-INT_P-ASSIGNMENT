<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */

namespace App\Services;

use App\Models\User;
use App\Factories\UserFactoryManager;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    private UserFactoryManager $userFactoryManager;

    public function __construct(UserFactoryManager $userFactoryManager)
    {
        $this->userFactoryManager = $userFactoryManager;
    }

    /**
     * Get all users with pagination and search
     */
    public function getUsers($page = 1, $perPage = 10, $search = null)
    {
        try {
            $query = User::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                'data' => $users
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get users: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            return [
                'success' => true,
                'data' => $user
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update user status
     */
    public function updateUserStatus($userId, $status)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            $validStatuses = ['active', 'inactive', 'suspended', 'pending_verification'];
            
            if (!in_array($status, $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status'
                ];
            }

            $user->account_status = $status;
            $user->save();

            return [
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => $user
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }

            $user->delete();

            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('account_status', 'active')->count(),
                'inactive_users' => User::where('account_status', 'inactive')->count(),
                'suspended_users' => User::where('account_status', 'suspended')->count(),
                'pending_users' => User::where('account_status', 'pending_verification')->count(),
                'social_users' => User::whereNotNull('social_provider')->count(),
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
                'users_by_provider' => User::whereNotNull('social_provider')
                    ->selectRaw('social_provider, count(*) as count')
                    ->groupBy('social_provider')
                    ->get(),
            ];

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user stats: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateUserStatus($userIds, $status)
    {
        try {
            $validStatuses = ['active', 'inactive', 'suspended'];
            
            if (!in_array($status, $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status'
                ];
            }

            $updatedCount = User::whereIn('user_id', $userIds)->update(['account_status' => $status]);

            return [
                'success' => true,
                'message' => "Updated {$updatedCount} users successfully",
                'updated_count' => $updatedCount
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to bulk update users: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export users data
     */
    public function exportUsers($format = 'csv')
    {
        try {
            $users = User::all();
            
            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.' . $format;

            return [
                'success' => true,
                'data' => $users,
                'filename' => $filename,
                'format' => $format
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to export users: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available user factory types
     */
    public function getAvailableUserTypes()
    {
        try {
            $types = $this->userFactoryManager->getAvailableTypes();
            
            return [
                'success' => true,
                'data' => $types
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user types: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate user data using factory pattern
     */
    public function validateUserData($userType, $userData)
    {
        try {
            if (!$this->userFactoryManager->isSupported($userType)) {
                return [
                    'success' => false,
                    'message' => "Unsupported user type: {$userType}"
                ];
            }

            $factory = $this->userFactoryManager->getFactory($userType);
            $isValid = $factory->validateUserData($userData);

            return [
                'success' => $isValid,
                'valid' => $isValid,
                'errors' => $factory->getErrors()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to validate user data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user statistics by factory type
     */
    public function getUserStatsByType()
    {
        try {
            $types = $this->userFactoryManager->getAvailableTypes();
            $stats = [];

            foreach ($types as $type) {
                $factory = $this->userFactoryManager->getFactory($type);
                $stats[$type] = [
                    'factory_class' => get_class($factory),
                    'supported' => true
                ];
            }

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get user stats by type: ' . $e->getMessage()
            ];
        }
    }
} 