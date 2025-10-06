<?php
// FormSC.php - Version tương thích PHP 7+
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '256M');

// Include files
include ("select_data.php");
include ("myfunctions.php");

// Variables
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : '';

// Database connection
$con = mysqli_connect("localhost", $usernamehost, $passwordhost, $databasename);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// CSS
echo '<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.header { background: #007cba; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
.form-group { margin: 15px 0; }
.form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
.btn:hover { background: #005a87; }
.alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #f8f9fa; font-weight: bold; }
</style>';

echo '<div class="container">';
echo '<div class="header"><h1>HỆ THỐNG QUẢN LÝ SỬA CHỮA BẢO DƯỠNG</h1></div>';

// Main logic
if (($submit == "nhapdulieu") && ($hoso == "phieuscbd")) {
    // Xử lý nhập phiếu yêu cầu dịch vụ
    echo '<h2>NHẬP PHIẾU YÊU CẦU DỊCH VỤ</h2>';
    
    $curday = date("d/m/Y");
    
    // Lấy số phiếu tự động
    $maxphieu = 0;
    $result = mysqli_query($con, "SELECT MAX(CAST(phieu AS UNSIGNED)) as max_phieu FROM hososcbd_iso");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $maxphieu = $row['max_phieu'] ? $row['max_phieu'] : 0;
    }
    $maxphieu++;
    
    if ($maxphieu > 0 && $maxphieu <= 9) $fieu = "000$maxphieu";
    else if ($maxphieu >= 10 && $maxphieu <= 99) $fieu = "00$maxphieu";
    else if ($maxphieu >= 100 && $maxphieu <= 999) $fieu = "0$maxphieu";
    else $fieu = "$maxphieu";
    
    echo '<form method="post" action="formsc.php">
        <div class="form-group">
            <label>Số phiếu:</label>
            <input type="text" name="phieu" value="' . $fieu . '" readonly>
        </div>
        <div class="form-group">
            <label>Ngày tạo:</label>
            <input type="text" name="ngay" value="' . $curday . '" readonly>
        </div>
        <div class="form-group">
            <label>Nội dung yêu cầu:</label>
            <textarea name="noidung" rows="5" placeholder="Nhập nội dung yêu cầu dịch vụ..."></textarea>
        </div>
        <input type="hidden" name="submit" value="save_phieu">
        <input type="hidden" name="username" value="' . $username . '">
        <button type="submit" class="btn">Lưu phiếu</button>
    </form>';
    
} else if ($submit == "save_phieu") {
    // Lưu phiếu vào database
    $phieu = $_POST['phieu'];
    $ngay = $_POST['ngay'];
    $noidung = $_POST['noidung'];
    
    $sql = "INSERT INTO hososcbd_iso (phieu, ngaytao, noidung, nguoitao) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $phieu, $ngay, $noidung, $username);
    
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="alert alert-success">Lưu phiếu thành công!</div>';
    } else {
        echo '<div class="alert alert-error">Lỗi: ' . mysqli_error($con) . '</div>';
    }
    mysqli_stmt_close($stmt);
    
} else {
    // Menu chính
    echo '<h2>CHỨC NĂNG CHÍNH</h2>';
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">';
    
    $functions = [
        ['name' => 'Nhập phiếu yêu cầu dịch vụ', 'hoso' => 'phieuscbd'],
        ['name' => 'Nhập phiếu thực hiện công việc', 'hoso' => 'phieuthuchien'],
        ['name' => 'Xuất biên bản bàn giao', 'hoso' => 'bienban'],
        ['name' => 'Thống kê báo cáo', 'hoso' => 'thongke']
    ];
    
    foreach ($functions as $func) {
        echo '<div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center;">
            <h3>' . $func['name'] . '</h3>
            <form method="post" style="margin: 0;">
                <input type="hidden" name="submit" value="nhapdulieu">
                <input type="hidden" name="hoso" value="' . $func['hoso'] . '">
                <input type="hidden" name="username" value="' . $username . '">
                <button type="submit" class="btn">Chọn</button>
            </form>
        </div>';
    }
    
    echo '</div>';
}

echo '</div>';

mysqli_close($con);
?>