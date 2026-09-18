<?php
require 'includes/initialize.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    set_alert('error', 'Username and password are required.');
    header('Location: login.php');
    exit;
}

$stmt = $connection->prepare("SELECT id, firstname, lastname, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$authenticated = false;

if ($user) {
    if (password_verify($password, $user['password'])) {
        $authenticated = true;
    } elseif ($password === $user['password']) {
        // Legacy plaintext row from the original lab exercise: accept once
        // and upgrade it to a proper hash so it's secure from now on.
        $authenticated = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $user['id']);
        $update->execute();
    }
}

if ($authenticated) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['fullname'] = $user['firstname'] . ' ' . $user['lastname'];
    header('Location: dashboard.php');
} else {
    set_alert('error', 'Invalid username or password.');
    header('Location: login.php');
}
exit;
?>
