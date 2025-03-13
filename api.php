<?php
// Danh sách tài khoản admin
$adminKey = 'Admin1@#$'; // Đặt key admin cố định

// File lưu key
$keyFile = 'key.txt';
$keys = file_exists($keyFile) ? json_decode(file_get_contents($keyFile), true) : [];

// Kiểm tra tham số đầu vào
if (!isset($_GET['admin']) || !isset($_GET['action'])) {
    die(json_encode(['status' => 'error', 'message' => 'Thiếu tham số admin hoặc action.']));
}

$admin = $_GET['admin'];
$action = $_GET['action'];

// Xác thực admin
if ($admin !== $adminKey) {
    die(json_encode(['status' => 'error', 'message' => 'Key admin không hợp lệ.']));
}

// Hiển thị danh sách key
if ($action === 'list') {
    die(json_encode(['status' => 'success', 'keys' => $keys]));
}

// Xóa key
if ($action === 'delete') {
    if (!isset($_GET['key'])) {
        die(json_encode(['status' => 'error', 'message' => 'Thiếu tham số key để xóa.']));
    }
    $key = $_GET['key'];

    if (!isset($keys[$key])) {
        die(json_encode(['status' => 'error', 'message' => 'Key không tồn tại.']));
    }

    unset($keys[$key]);
    file_put_contents($keyFile, json_encode($keys, JSON_PRETTY_PRINT));
    die(json_encode(['status' => 'success', 'message' => 'Key đã được xóa.']));
}

// Thêm key mới
if ($action === 'add') {
    if (!isset($_GET['key']) || !isset($_GET['day'])) {
        die(json_encode(['status' => 'error', 'message' => 'Thiếu tham số key hoặc day.']));
    }

    $key = $_GET['key'];
    $day = (int)$_GET['day'];

    if ($day < 1) {
        die(json_encode(['status' => 'error', 'message' => 'Số ngày phải lớn hơn hoặc bằng 1.']));
    }

    if (isset($keys[$key])) {
        die(json_encode(['status' => 'error', 'message' => 'Key đã tồn tại.', 'expiry' => $keys[$key]]));
    }

    $expiryDate = date('Y-m-d', strtotime("+$day days"));
    $keys[$key] = $expiryDate;
    file_put_contents($keyFile, json_encode($keys, JSON_PRETTY_PRINT));

    die(json_encode(['status' => 'success', 'message' => 'Key đã được thêm.', 'expiry' => $expiryDate]));
}

die(json_encode(['status' => 'error', 'message' => 'Action không hợp lệ.']));
?>
