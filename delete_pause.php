<?php
// delete_pause.php: Xóa lần tạm dừng
require_once("select_data.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo '<div style="color:red;">ID không hợp lệ.</div>';
    exit;
}

// Lấy thông tin để hiển thị xác nhận (nếu cần)
$sql = "SELECT hoso FROM hososcbd_iso_pauses WHERE id=?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pause = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$pause) {
    echo '<div style="color:red;">Không tìm thấy dữ liệu.</div>';
    exit;
}

// Thực hiện xóa
$sql = "DELETE FROM hososcbd_iso_pauses WHERE id=?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
if ($ok) {
    echo '<div style="color:green;">Đã xóa thành công!</div>';
} else {
    echo '<div style="color:red;">Lỗi khi xóa.</div>';
}
echo '<a href="javascript:history.back()">Quay lại</a>';
