<?php
$keyFile = 'key.txt';

if (!isset($_GET['key'])) {
    echo json_encode(['Status' => 'error', 'message' => 'Key không được cung cấp.']);
    exit;
}

$key = $_GET['key'];
$keys = file_exists($keyFile) ? json_decode(file_get_contents($keyFile), true) : [];

if (isset($keys[$key])) {
    $expiryDate = $keys[$key];
    $currentDate = date('Y-m-d');

    if ($currentDate <= $expiryDate) {
        echo json_encode(['Status' => 'success']);
    } else {
        echo json_encode(['Status' => 'error', 'message' => 'Key đã hết hạn.']);
    }
} else {
    echo json_encode(['Status' => 'error', 'message' => 'Key không hợp lệ.']);
}
