<?php
session_start();

// Danh sách tài khoản admin
$admins = [
    'admin' => 'admin@1@#$',
    'admin2' => 'Admin2@#$'
];

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($admins[$username]) && $admins[$username] === $password) {
        $_SESSION['admin'] = $username;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu.";
    }
}

// Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Kiểm tra quyền truy cập
if (!isset($_SESSION['admin'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header text-center bg-primary text-white">
                            <h4>Admin Login</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" name="username" id="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Đăng nhập</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Tạo, chỉnh sửa, xóa key
$keyFile = 'key.txt';
$keys = file_exists($keyFile) ? json_decode(file_get_contents($keyFile), true) : [];

// Thêm key
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_key'])) {
    $key = $_POST['key'];
    $expiry = $_POST['expiry'];

    $keys[$key] = $expiry;
    file_put_contents($keyFile, json_encode($keys, JSON_PRETTY_PRINT));
}

// Xóa key
if (isset($_GET['delete'])) {
    $keyToDelete = $_GET['delete'];
    unset($keys[$keyToDelete]);
    file_put_contents($keyFile, json_encode($keys, JSON_PRETTY_PRINT));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Key</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Quản lý Key</h1>
                <p class="text-center">Xin chào, <b><?php echo $_SESSION['admin']; ?></b>! <a href="?logout" class="btn btn-danger btn-sm">Đăng xuất</a></p>
                <hr>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Danh sách Key</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Hạn sử dụng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keys as $key => $expiry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($key); ?></td>
                                    <td><?php echo htmlspecialchars($expiry); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo urlencode($key); ?>" class="btn btn-danger btn-sm">Xóa</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4>Thêm Key</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="key" class="form-label">Key</label>
                                <input type="text" name="key" id="key" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="expiry" class="form-label">Hạn sử dụng (YYYY-MM-DD)</label>
                                <input type="date" name="expiry" id="expiry" class="form-control" required>
                            </div>
                            <button type="submit" name="add_key" class="btn btn-success">Thêm Key</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
