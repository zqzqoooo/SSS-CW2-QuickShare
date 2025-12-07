<?php

return [
    // 站点名称
    'name' => env('APP_NAME', 'QuickShare'),

    // 管理员邮箱 (用于 Seeder 或系统通知)
    'admin_email' => env('ADMIN_EMAIL', 'admin@quickshare.com'),

    // 上传限制 (单位: 字节)
    'upload_limits' => [
        'guest' => 10 * 1024 * 1024,      // 访客: 5MB
        'user'  => 50 * 1024 * 1024,     // 用户: 50MB
    ],

    // 过期时间 (单位: 天)
    'expiration_days' => [
        'guest' => 3,  // 访客: 3天
        'user'  => 7,  // 用户: 7天
    ],
    
    // 取件码长度
    'code_length' => 6,
];