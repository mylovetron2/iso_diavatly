<?php
ob_clean();
require_once("select_data.php");
header('Content-Type: application/json');

$hoso = isset($_POST['hoso']) ? trim($_POST['hoso']) : '';
$pause_start = isset($_POST['pause_start']) ? $_POST['pause_start'] : '';
$pause_end = isset($_POST['pause_end']) ? $_POST['pause_end'] : '';
$pause_reason = isset($_POST['pause_reason']) ? trim($_POST['pause_reason']) : '';

$errors = [];
if ($hoso == '' || $pause_start == '' || $pause_end == '') {
    $errors[] = 'Vui lòng nhập đầy đủ thông tin.';
}
if (strtotime($pause_start) > strtotime($pause_end)) {
    $errors[] = 'Ngày bắt đầu không được lớn hơn ngày kết thúc.';
}

if (count($errors) === 0) {
    $sql = "INSERT INTO hososcbd_iso_pauses (hoso, pause_start, pause_end, pause_reason) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $hoso, $pause_start, $pause_end, $pause_reason);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($success) {
            echo json_encode(["success" => true, "message" => "Thêm lần tạm dừng thành công!"]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "Lỗi khi lưu dữ liệu. Vui lòng thử lại."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Không thể chuẩn bị truy vấn."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => implode("\\n", $errors)]);
    exit;
}