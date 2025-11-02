<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Accept either JSON body or form-encoded POST from the website
$raw = file_get_contents('php://input');
$data = [];
if ($raw) {
    $data = json_decode($raw, true) ?: [];
}

// Fallback to $_POST for form submissions
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

$email = isset($data['email']) ? trim($data['email']) : null;
$name = isset($data['name']) ? trim($data['name']) : null;

if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing email']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO subscriptions (email, name) VALUES (?, ?)');
    $stmt->execute([$email, $name]);
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Subscribed', 'email' => $email]);
} catch (PDOException $e) {
    // handle duplicate email gracefully
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
        echo json_encode(['success' => true, 'message' => 'Already subscribed', 'email' => $email]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'DB error', 'detail' => $e->getMessage()]);
    }
}

?>
