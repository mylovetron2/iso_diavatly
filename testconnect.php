<?php
// $servername = "localhost";
// $username = "mapselli676e_diavatly_quanly";
// $password = "cntt2019";
// $dbname = "mapselli676e_diavatly_quanly"; // Thay bằng tên database của bạn

$servername = "diavatly.com";
$username = "diavatly";
$password = "cntt2019";
$dbname = "diavatly_quanly"; // Thay bằng tên database của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
echo "Kết nối thành công!";
$conn->close();
?>