<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pages = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/login.php' => 'login.php',
    '/register.php' => 'register.php',
    '/dashboard.php' => 'dashboard.php',
    '/login_process.php' => 'login_process.php',
    '/register_process.php' => 'register_process.php',
    '/logout.php' => 'logout.php',
];

if (!isset($pages[$path])) {
    http_response_code(404);
    exit('Not Found');
}

require dirname(__DIR__) . '/' . $pages[$path];