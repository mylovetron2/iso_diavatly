<?php
// edit_pause.php: Sửa thông tin tạm dừng
require_once("select_data.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo '<div style="color:red;">ID không hợp lệ.</div>';
    exit;
}

// Lấy dữ liệu hiện tại
$sql = "SELECT * FROM hososcbd_iso_pauses WHERE id=?";
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

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pause_start = isset($_POST['pause_start']) ? $_POST['pause_start'] : '';
    $pause_end = isset($_POST['pause_end']) ? $_POST['pause_end'] : '';
    $pause_reason = isset($_POST['pause_reason']) ? $_POST['pause_reason'] : '';
    if ($pause_start && $pause_end && strtotime($pause_start) <= strtotime($pause_end)) {
        $sql = "UPDATE hososcbd_iso_pauses SET pause_start=?, pause_end=?, pause_reason=? WHERE id=?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $pause_start, $pause_end, $pause_reason, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($ok) {
            echo '<div style="color:green;">Cập nhật thành công!</div>';
        } else {
            echo '<div style="color:red;">Lỗi khi cập nhật.</div>';
        }
    } else {
        echo '<div style="color:red;">Dữ liệu không hợp lệ.</div>';
    }
}
?>
<h3>Sửa lần tạm dừng</h3>
<form method="post">
    <div>
        <label>Ngày bắt đầu:</label>
        <input type="date" name="pause_start" value="<?php echo htmlspecialchars($pause['pause_start']); ?>" required>
    </div>
    <div>
        <label>Ngày kết thúc:</label>
        <input type="date" name="pause_end" value="<?php echo htmlspecialchars($pause['pause_end']); ?>" required>
    </div>
    <div>
        <label>Lý do:</label>
        <input type="text" name="pause_reason" value="<?php echo htmlspecialchars($pause['pause_reason']); ?>">
    </div>
    <button type="submit">Lưu</button>
    <a href="javascript:history.back()">Quay lại</a>
</form>
