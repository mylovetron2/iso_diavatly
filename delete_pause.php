<?php
// delete_pause.php: Xóa lần tạm dừng (AJAX, trả về JSON)
ob_clean();
require_once("select_data.php");
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID không hợp lệ."]);
    exit;
}

// Kiểm tra tồn tại
$sql = "SELECT hoso FROM hososcbd_iso_pauses WHERE id=?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pause = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$pause) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy dữ liệu."]);
    exit;
}

// Thực hiện xóa
$sql = "DELETE FROM hososcbd_iso_pauses WHERE id=?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
if ($ok) {
    echo json_encode(["success" => true, "message" => "Đã xóa thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi xóa."]);
}
