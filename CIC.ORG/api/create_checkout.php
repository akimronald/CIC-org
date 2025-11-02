<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = [];
if ($raw) {
    $data = json_decode($raw, true) ?: [];
}
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

$amount = isset($data['amount']) ? (float)$data['amount'] : 0;
$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$payment_method = isset($data['paymentMethod']) ? trim($data['paymentMethod']) : (isset($data['payment-method']) ? trim($data['payment-method']) : '');

if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit;
}

// Build a PayPal donation redirect URL. The server needs a valid PAYPAL_BUSINESS email set in config.php.
$business = isset($paypal_business) ? $paypal_business : 'donations@example.com';

// Use PayPal "donations" endpoint
$params = http_build_query([
    'cmd' => '_donations',
    'business' => $business,
    'amount' => number_format($amount, 2, '.', ''),
    'currency_code' => 'USD',
    'item_name' => 'Donation to CIC',
    'return' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/donate.html?status=success',
    'cancel_return' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/donate.html?status=cancel'
]);

$redirect = 'https://www.paypal.com/cgi-bin/webscr?' . $params;

echo json_encode(['success' => true, 'redirect_url' => $redirect]);

?>
