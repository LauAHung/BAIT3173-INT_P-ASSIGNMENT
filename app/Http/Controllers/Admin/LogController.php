<?php

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
        $rows = $q->limit(200)->get(['admin_email','action','details','created_at']);
        return response()->json(['success' => true, 'data' => $rows]);
    }
}


