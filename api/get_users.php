<?php
require '../includes/initialize.php';
require '../includes/auth.php';
require_login();

header('Content-Type: application/json');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;
$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $like = '%' . $search . '%';

    $countStmt = $connection->prepare("SELECT COUNT(*) as total FROM users WHERE firstname LIKE ? OR lastname LIKE ? OR username LIKE ?");
    $countStmt->bind_param("sss", $like, $like, $like);
    $countStmt->execute();
    $total = (int)$countStmt->get_result()->fetch_assoc()['total'];

    $stmt = $connection->prepare("SELECT id, firstname, lastname, username, created_at FROM users WHERE firstname LIKE ? OR lastname LIKE ? OR username LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sssii", $like, $like, $like, $limit, $offset);
} else {
    $total = (int)$connection->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

    $stmt = $connection->prepare("SELECT id, firstname, lastname, username, created_at FROM users ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    'data' => $users,
    'total' => $total,
    'page' => $page,
    'totalPages' => max(1, (int)ceil($total / $limit)),
]);
?>
