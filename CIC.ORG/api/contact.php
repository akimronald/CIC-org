<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Accept JSON or form POSTS
$raw = file_get_contents('php://input');
$data = $raw ? json_decode($raw, true) : [];
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

if (empty($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing form data']);
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
