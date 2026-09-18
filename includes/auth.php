<?php
// Direct access guard
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Redirects to the login page if there is no active session.
 * Call at the top of any page/endpoint that requires login.
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
