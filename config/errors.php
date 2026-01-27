<?php

return [
    // Enable debug mode.
    'debug' => $_ENV['APP_DEBUG'] ?? false,

    // Log errors to file.
    'log' => true,

    // Log file path.
    'log_path' => BASE_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log',

    // Maximum log file size.
    'log_max_size' => 10 * 1024 * 1024,

    // Number of log files to keep when rotating.
    'log_max_files' => 5,

    // Error reporting level.
    'error_reporting' => E_ALL,

    // Display errors in browser.
    'display_errors' => $_ENV['APP_DEBUG'] ?? true,

    // Email notifications for critical errors.
    'email_notifications' => false,

    // Email address to send error notifications to.
    'notification_email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com',

    // HTTP status codes and their corresponding error pages.
    'error_pages' => [
        403 => '403.php',
        404 => '404.php',
        500 => '500.php',
        503 => '503.php',
    ],
];