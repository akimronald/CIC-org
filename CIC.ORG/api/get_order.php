<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? trim($_GET['id']) : null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        echo json_encode(['success' => true, 'order' => $row]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Order not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB error', 'detail' => $e->getMessage()]);
}

?>
