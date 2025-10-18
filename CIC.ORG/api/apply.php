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
    $stmt = $pdo->prepare('INSERT INTO applications (name, email, phone, position, resume_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $data['name'] ?? null,
        $data['email'] ?? null,
        $data['phone'] ?? null,
        $data['position'] ?? null,
        $data['resume'] ?? null,
    ]);
    http_response_code(201);
    echo json_encode(['success' => true, 'application' => array_merge(['id' => (int)$pdo->lastInsertId()], $data)]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save application', 'detail' => $e->getMessage()]);
}

?>
