<?php
// Bao gồm các file cần thiết
require_once("select_data.php");
require_once("myfunctions.php");

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';

// Xử lý đăng nhập
$login_error = '';
if ($submit === 'login') {
    if (empty($username) || empty($password)) {
        $login_error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        $result = mysqli_query($link, "SELECT password, madv, phanquyen FROM users WHERE username='$username'");
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['password'] !== $password) {
                $login_error = 'Mật khẩu không đúng.';
            } else {
                // Đăng nhập thành công, lưu session
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['madv'] = $row['madv'];
                $_SESSION['phanquyen'] = $row['phanquyen'];
                header('Location: index_professional.php');
                exit;
            }
        } else {
            $login_error = 'Tên đăng nhập không tồn tại.';
        }
    }
}
// Xử lý đăng xuất
if ($submit === 'logout') {
    session_start();
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header('Location: index_professional.php');
    exit;
}

// Kiểm tra đăng nhập
session_start();
$is_logged_in = isset($_SESSION['username']);
$current_user = $is_logged_in ? $_SESSION['username'] : '';
$current_role = $is_logged_in ? $_SESSION['phanquyen'] : '';
$current_madv = $is_logged_in ? $_SESSION['madv'] : '';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Online DVL Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-card {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            padding: 32px 28px;
        }
        .brand {
            font-size: 2.2em;
            font-weight: 700;
            color: #764ba2;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .sidebar {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            padding: 30px 0;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
            font-size: 1.1em;
            border-radius: 8px;
            margin: 6px 0;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .sidebar .fa {
            margin-right: 10px;
        }
        .main-content {
            padding: 40px 30px;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        .marquee {
            background: #f8f9fa;
            color: #764ba2;
            font-weight: 600;
            padding: 8px 0;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        @media (max-width: 900px) {
            .sidebar { min-height: auto; }
            .main-content { padding: 20px 5px; }
        }
    </style>
</head>
<body>
<?php if (!$is_logged_in): ?>
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="images/logo2.png" style="height:48px; margin-right:10px;">
            <span class="brand">Quản lý Online</span>
        </div>
        <form method="post" action="index_professional.php">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <?php if ($login_error): ?>
                <div class="alert alert-danger py-2"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <button type="submit" name="submit" value="login" class="btn btn-primary w-100">
                <i class="fa fa-sign-in-alt"></i> Đăng nhập
            </button>
        </form>
    </div>
<?php else: ?>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 sidebar d-flex flex-column align-items-center">
                <img src="images/logo2.png" style="height:48px; margin-bottom:10px;">
                <div class="mb-3 text-center">
                    <span class="fw-bold text-primary">Xin chào, <?php echo htmlspecialchars($current_user); ?></span>
                </div>
                <div class="marquee">
                    <marquee scrollamount="5">XÍ NGHIỆP ĐỊA VẬT LÝ GIẾNG KHOAN - PHẦN MỀM TÍCH HỢP ISO-OHSAS-VẬT TƯ TÀI SẢN</marquee>
                </div>
                <ul class="nav flex-column w-100">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fa fa-home"></i> Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="tiendocongviec_professional.php?user=<?php echo urlencode($current_user); ?>" target="_blank"><i class="fa fa-tasks"></i> Tiến độ công việc</a></li>
                    <li class="nav-item"><a class="nav-link" href="formsc.php" target="_blank"><i class="fa fa-tools"></i> Hồ sơ SC/BD</a></li>
                    <li class="nav-item"><a class="nav-link" href="baocaothongke.php" target="_blank"><i class="fa fa-chart-bar"></i> Thống kê - Báo cáo</a></li>
                    <li class="nav-item"><a class="nav-link" href="Danhmucsc.php" target="_blank"><i class="fa fa-list"></i> Danh mục thiết bị</a></li>
                    <li class="nav-item"><a class="nav-link" href="baoduongxuong.php" target="_blank"><i class="fa fa-calendar-check"></i> Bảo dưỡng định kỳ</a></li>
                    <li class="nav-item"><a class="nav-link" href="formnhatky.php" target="_blank"><i class="fa fa-book"></i> Viết nhật ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="thongkenhatky.php" target="_blank"><i class="fa fa-book-open"></i> Thống kê nhật ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="quanlyuser.php" target="_blank"><i class="fa fa-users-cog"></i> Quản lý users</a></li>
                    <li class="nav-item"><a class="nav-link" href="danhmuclk.php" target="_blank"><i class="fa fa-warehouse"></i> Kho linh kiện vật tư</a></li>
                    <li class="nav-item"><a class="nav-link" href="formlinhkien.php" target="_blank"><i class="fa fa-exchange-alt"></i> Nhập xuất linh kiện</a></li>
                    <li class="nav-item"><a class="nav-link" href="baocaolinhkien.php" target="_blank"><i class="fa fa-file-alt"></i> Thống kê báo cáo - vt</a></li>
                </ul>
                <form method="post" action="index_professional.php" class="mt-4 w-100 text-center">
                    <button type="submit" name="submit" value="logout" class="btn btn-outline-danger w-75">
                        <i class="fa fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            </nav>
            <main class="col-md-9 col-lg-10 main-content">
                <div class="card p-4 mb-4">
                    <h2 class="mb-3"><i class="fa fa-home text-primary"></i> Trang chủ hệ thống ISO Professional</h2>
                    <p class="lead">Chào mừng bạn đến với hệ thống quản lý ISO chuyên nghiệp. Vui lòng chọn chức năng từ menu bên trái để bắt đầu công việc.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> Giao diện hiện đại, dễ sử dụng, hỗ trợ mọi thiết bị</li>
                        <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> Đầy đủ chức năng quản lý, báo cáo, thống kê, bảo dưỡng</li>
                        <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> Tích hợp bảo mật, phân quyền, quản lý người dùng</li>
                        <li class="list-group-item"><i class="fa fa-check-circle text-success"></i> Hỗ trợ xuất file, nhập liệu, quản lý vật tư thiết bị</li>
                    </ul>
                </div>
            </main>
        </div>
    </div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
