<?php
// Direct access guard
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit('Forbidden');
}

// ---------------------------------------------------------------------
// Database connection
// ---------------------------------------------------------------------
// Reads from environment variables when they're set (Vercel + Aiven),
// and falls back to the original local XAMPP defaults otherwise, so the
// SAME codebase runs unmodified in both places.
//
// On Vercel, set these in Project Settings > Environment Variables using
// the values from your Aiven for MySQL service's Overview page:
//   DB_HOST      e.g. mysql-xxxxx-yourproject.aivencloud.com
//   DB_PORT      e.g. 12345
//   DB_USER      e.g. avnadmin
//   DB_PASSWORD  the Aiven service password
//   DB_NAME      e.g. defaultdb (or a database you created)
//   DB_SSL       set to "true" (Aiven requires SSL)
// ---------------------------------------------------------------------

$host     = getenv('DB_HOST') ?: 'localhost';
$port     = (int)(getenv('DB_PORT') ?: 3306);
$user     = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'lab_app';
$useSSL   = getenv('DB_SSL') === 'true';
$caPath   = __DIR__ . '/../certs/aiven-ca.pem';

$connection = mysqli_init();

if ($useSSL) {
    $connection->ssl_set(null, null, $caPath, null, null);
    $connected = $connection->real_connect($host, $user, $password, $database, $port, null, MYSQLI_CLIENT_SSL);
} else {
    $connected = $connection->real_connect($host, $user, $password, $database, $port);
}

if (!$connected) {
    die("Connection failed: " . mysqli_connect_error());
}

// ---------------------------------------------------------------------
// Sessions
// ---------------------------------------------------------------------
// Store sessions in the database rather than the local filesystem. This
// is required on Vercel (ephemeral containers, no shared/persistent disk
// between requests) and works identically on XAMPP.
require_once __DIR__ . '/db_session_handler.php';
session_set_save_handler(new DbSessionHandler($connection), true);
session_start();

function set_alert($type, $message) {
    $_SESSION['alert_type'] = $type;
    $_SESSION['alert_message'] = $message;
}

function get_alert() {
    if (isset($_SESSION['alert_message'])) {
        $alert = [
            'type' => $_SESSION['alert_type'] ?? 'success',
            'message' => $_SESSION['alert_message'],
        ];
        unset($_SESSION['alert_message'], $_SESSION['alert_type']);
        return $alert;
    }
    return null;
}

function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
