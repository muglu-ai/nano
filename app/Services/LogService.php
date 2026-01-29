<?php

namespace App\Services;

use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogService
{
    public static function log($action, $module = null, $oldData = null, $newData = null)
    {
        AdminActionLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => $action,
            'module' => $module,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => Request::ip(),
        ]);
    }
}
