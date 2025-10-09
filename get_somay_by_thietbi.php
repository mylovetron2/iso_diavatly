<?php
// Endpoint trả về danh sách số máy theo thiết bị (AJAX)
include("select_data.php");
header('Content-Type: application/json; charset=utf-8');
if (isset($_POST['mavt']) && isset($_POST['donvi'])) {
    $mavt = mysqli_real_escape_string($link, $_POST['mavt']);
    $donvi = mysqli_real_escape_string($link, $_POST['donvi']);
    $sql = "SELECT DISTINCT somay FROM thietbi_iso WHERE mavt='$mavt' AND madv='$donvi' ORDER BY somay";
    $result = mysqli_query($link, $sql);
    $options = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $somay = $row['somay'];
        $options[] = ["value" => $somay, "label" => $somay];
    }
    echo json_encode(["success" => true, "options" => $options]);
    exit;
}
echo json_encode(["success" => false, "options" => []]);
