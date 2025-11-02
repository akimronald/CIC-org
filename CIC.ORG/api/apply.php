<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Accept JSON body or form-encoded posts
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
    $stmt = $pdo->prepare('INSERT INTO applications (name, email, phone, position, resume_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $data['name'] ?? null,
        $data['email'] ?? null,
        $data['phone'] ?? null,
        $data['position'] ?? null,
        $data['resume'] ?? null,
    ]);
    $id = (int)$pdo->lastInsertId();
    http_response_code(201);
    echo json_encode(['success' => true, 'application' => array_merge(['id' => $id], [
        'name' => $data['name'] ?? null,
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'position' => $data['position'] ?? null,
    ])]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save application', 'detail' => $e->getMessage()]);
}

?>
