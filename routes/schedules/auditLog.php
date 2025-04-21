<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\Config;
use App\Models\AuditLog;

Schedule::call(function () {
    $config = Config::where('name', 'audit log scheduler')->first() ?? null;
    
    if ($config && !empty($config->options)) {
        $options = json_decode($config->options, true);

        if (!empty($options['key']) && !empty($options['value'])) {
            $value = $options['value'];

            $auditLogs = AuditLog::where('created_at', '<=', now()->subDays($value))->delete(); 
            Log::info('Audit logs older than ' . $value . ' days have been deleted.');
        }
    }
})->daily();
