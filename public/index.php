<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Safety check for vendor autoloader
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(500);
    echo "<div style='font-family:sans-serif;padding:30px;background:#fff1f2;color:#9f1239;border:1px solid #f43f5e;border-radius:8px;max-width:700px;margin:50px auto;'>";
    echo "<h3>Composer Vendor directory missing</h3>";
    echo "<p>Please ensure latest commits are pulled or deployed from Git repository.</p>";
    echo "</div>";
    exit;
}

try {
    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
    echo "<div style='font-family:sans-serif;background:#fff1f2;color:#9f1239;padding:24px;border:2px solid #f43f5e;border-radius:12px;max-width:900px;margin:40px auto;box-shadow:0 10px 25px rgba(0,0,0,0.1);'>";
    echo "<h2 style='margin-top:0;'>⚠️ Rayka Server Diagnostic</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line: " . $e->getLine() . ")</p>";
    echo "<details open><summary style='cursor:pointer;font-weight:bold;margin:12px 0;'>Full Trace</summary>";
    echo "<pre style='background:#1e1e1e;color:#f8fafc;padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;line-height:1.5;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</details>";
    echo "</div>";
    exit;
}
