<?php
// router.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('/\.(?:txt|css|js|svg|png|jpg|jpeg|gif|ico|ttf|json)$/', $path)) {
    // If the file exists as a static file, serve it directly without routing.
    if (file_exists(__DIR__ . $path)) {
        return false;
    }
} 

include_once __DIR__ . '/../index.php';