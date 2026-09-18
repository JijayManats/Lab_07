<?php
require '../includes/initialize.php';
require '../includes/auth.php';
require_login();

header('Content-Type: application/json');

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
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $connection->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $lastname, $username, $hashed);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User "' . $username . '" was created successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>
