<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing email']);
    exit;
}

$email = trim($data['email']);
$name = isset($data['name']) ? trim($data['name']) : null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO subscriptions (email, name) VALUES (?, ?)');
    $stmt->execute([$email, $name]);
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Subscribed']);
} catch (PDOException $e) {
    // handle duplicate email gracefully
    if ($e->errorInfo[1] == 1062) {
        echo json_encode(['success' => true, 'message' => 'Already subscribed']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB error', 'detail' => $e->getMessage()]);
    }
}

?>
