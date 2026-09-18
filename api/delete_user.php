<?php
require '../includes/initialize.php';
require '../includes/auth.php';
require_login();

header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user id.']);
    exit;
}

if ($id === (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account while logged in.']);
    exit;
}

$stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
?>
