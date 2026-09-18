<?php
require 'includes/initialize.php';

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$error = null;
if (empty($firstname)) {
    $error = "Firstname is required";
} elseif (empty($lastname)) {
    $error = "Lastname is required";
} elseif (empty($username)) {
    $error = "Username is required";
} elseif (empty($password)) {
    $error = "Password is required";
} elseif (empty($confirm_password)) {
    $error = "Confirm Password is required";
} elseif ($password !== $confirm_password) {
    $error = "Password and confirm password do not match";
} elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters";
}

if (!$error) {
    $check = $connection->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "That username is already taken";
    }
}

if ($error) {
    set_alert('error', $error);
    header('Location: register.php');
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $connection->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $lastname, $username, $hashed);

if ($stmt->execute()) {
    set_alert('success', 'Account created successfully! You can now log in.');
    header('Location: login.php');
} else {
    set_alert('error', 'Something went wrong. Please try again.');
    header('Location: register.php');
}
exit;
?>
