<?php
require_once __DIR__ . '/config.php';

// POST /api/orders.php -> create order
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

// Basic fields expected: name, phone, fuelType, liters, latitude, longitude, address, payment
try {
    $stmt = $pdo->prepare('INSERT INTO orders (name, phone, fuel_type, liters, latitude, longitude, address, payment_method, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $data['name'] ?? null,
        $data['phone'] ?? null,
        $data['fuelType'] ?? null,
        isset($data['liters']) ? $data['liters'] : null,
        isset($data['latitude']) ? $data['latitude'] : null,
        isset($data['longitude']) ? $data['longitude'] : null,
        $data['address'] ?? null,
        $data['payment'] ?? null,
    ]);
    $id = $pdo->lastInsertId();
    http_response_code(201);
    echo json_encode(['success' => true, 'order' => array_merge(['id' => (int)$id], $data)]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save order', 'detail' => $e->getMessage()]);
}

?>
