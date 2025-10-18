<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = read_json_body();
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([
        $data['name'] ?? null,
        $data['email'] ?? null,
        $data['phone'] ?? null,
        $data['message'] ?? null,
    ]);
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Received']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save contact', 'detail' => $e->getMessage()]);
}

?>
