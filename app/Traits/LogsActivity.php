<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected function logActivity($action, $description, $model = null, $properties = null)
    {
        ActivityLog::create([
            'pegawai_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? class_basename($model) : null,
            'model_id' => $model->id ?? null,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
        ]);
    }
}
