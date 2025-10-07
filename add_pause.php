<?php
// add_pause.php: Xử lý thêm lần tạm dừng cho hồ sơ
require_once("select_data.php");

// Lấy dữ liệu POST
$hoso = isset($_POST['hoso']) ? trim($_POST['hoso']) : '';
$pause_start = isset($_POST['pause_start']) ? $_POST['pause_start'] : '';
$pause_end = isset($_POST['pause_end']) ? $_POST['pause_end'] : '';
$pause_reason = isset($_POST['pause_reason']) ? trim($_POST['pause_reason']) : '';

// Kiểm tra dữ liệu hợp lệ
$errors = [];
if ($hoso == '' || $pause_start == '' || $pause_end == '') {
    $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
}
if (strtotime($pause_start) > strtotime($pause_end)) {
    $errors[] = 'Ngày bắt đầu không được lớn hơn ngày kết thúc.';
}

if (count($errors) === 0) {
    // Thêm vào bảng hososcbd_iso_pauses
    $sql = "INSERT INTO hososcbd_iso_pauses (hoso, pause_start, pause_end, pause_reason) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $hoso, $pause_start, $pause_end, $pause_reason);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($success) {
            echo '<div style="color:green;font-weight:bold;margin:20px;">Thêm lần tạm dừng thành công!</div>';
        } else {
            echo '<div style="color:red;font-weight:bold;margin:20px;">Lỗi khi lưu dữ liệu. Vui lòng thử lại.</div>';
        }
    } else {
        echo '<div style="color:red;font-weight:bold;margin:20px;">Không thể chuẩn bị truy vấn.</div>';
    }
} else {
    foreach ($errors as $err) {
        echo '<div style="color:red;font-weight:bold;margin:10px;">' . htmlspecialchars($err) . '</div>';
    }
}

// Gợi ý quay lại
echo '<a href="javascript:history.back()" style="color:#007bff;">Quay lại</a>';
