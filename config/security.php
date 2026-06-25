<?php

return [
    'password_policy' => [
        'min_length' => 10,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_number' => true,
        'require_special' => true,
        'history_count' => 5,
        'max_age_days' => 90,
    ],

    'login' => [
        'max_attempts' => 5,
        'lockout_minutes' => 15,
        'throttle' => '60,1',
    ],

    'session' => [
        'lifetime' => 120,
        'expire_on_close' => true,
        'encrypt' => true,
        'idle_timeout_minutes' => 30,
    ],

    'two_factor' => [
        'enforced' => env('TWO_FACTOR_ENFORCED', false),
        'grace_period_days' => 7,
    ],

    'registration' => [
        'enabled' => env('ALLOW_REGISTRATION', false),
    ],

    'login_notification' => [
        'enabled' => env('LOGIN_NOTIFICATION_ENABLED', true),
        'notify_on_new_device' => true,
    ],

    'backup' => [
        'enabled' => true,
        'retention_daily' => 7,
        'retention_weekly' => 4,
        'retention_monthly' => 3,
        'encryption_enabled' => true,
        'path' => storage_path('backups'),
    ],

    'audit' => [
        'enabled' => true,
        'excluded_models' => [
            'App\Models\AuditLog',
            'App\Models\LoginActivity',
            'App\Models\Session',
        ],
        'log_rotation_days' => 90,
    ],

    'api' => [
        'rate_limit' => 60,
        'rate_limit_auth' => 120,
    ],

    'encryption' => [
        'enabled' => true,
        'sensitive_fields' => [
            'phone',
            'email',
            'address',
        ],
    ],

    'cors' => [
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:8000')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'max_age' => 86400,
    ],

    'maintenance' => [
        'ip_whitelist' => explode(',', env('MAINTENANCE_IP_WHITELIST', '127.0.0.1')),
    ],
];
