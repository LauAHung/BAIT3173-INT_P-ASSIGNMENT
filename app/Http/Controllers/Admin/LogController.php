<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        return view('AdminPage.Log');
    }

    public function list(Request $request)
    {
        $q = AdminActivityLog::query()->orderByDesc('created_at');
        if ($email = $request->get('email')) {
            $q->where('admin_email', 'like', "%{$email}%");
        }
        if ($action = $request->get('action')) {
            $q->where('action', $action);
        }
        $rows = $q->limit(200)->get(['admin_email','action','details','created_at'])
            ->map(function (AdminActivityLog $log) {
                $details = $log->details;
                if (is_string($details)) {
                    $decoded = json_decode($details, true);
                    $details = json_last_error() === JSON_ERROR_NONE ? $decoded : $details;
                }
                return [
                    'admin_email' => $log->admin_email,
                    'action' => $log->action,
                    'details' => $details,
                    'created_at' => optional($log->created_at)->toISOString(),
                ];
            });
        return response()->json(['success' => true, 'data' => $rows]);
    }
}


