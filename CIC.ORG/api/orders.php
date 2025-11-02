<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
// POST /api/orders.php -> create order
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Accept JSON or form posts
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

// Basic fields expected: name, phone, fuelType, liters, latitude, longitude, address, payment
try {
    $stmt = $pdo->prepare('INSERT INTO orders (name, phone, fuel_type, liters, latitude, longitude, address, payment_method, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $data['name'] ?? null,
        $data['phone'] ?? null,
        $data['fuelType'] ?? ($data['fuel_type'] ?? null),
        isset($data['liters']) ? $data['liters'] : null,
        isset($data['latitude']) ? $data['latitude'] : null,
        isset($data['longitude']) ? $data['longitude'] : null,
        $data['address'] ?? null,
        $data['payment'] ?? ($data['payment_method'] ?? null),
    ]);
    $id = (int)$pdo->lastInsertId();
    http_response_code(201);
    echo json_encode(['success' => true, 'order' => array_merge(['id' => $id], [
        'name' => $data['name'] ?? null,
        'phone' => $data['phone'] ?? null,
        'fuel_type' => $data['fuelType'] ?? ($data['fuel_type'] ?? null),
        'liters' => isset($data['liters']) ? $data['liters'] : null,
    ])]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save order', 'detail' => $e->getMessage()]);
}

?>
