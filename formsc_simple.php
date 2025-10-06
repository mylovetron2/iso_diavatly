<?php
// Test formsc.php minimal
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include files
include ("select_data.php");
include ("myfunctions.php");

// Get variables
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : '';

// Database connection
$con = mysqli_connect("localhost", $usernamehost, $passwordhost, $databasename);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h2>FormSC.php Test - Hoạt động bình thường</h2>";
echo "<p>✓ Kết nối database thành công</p>";
echo "<p>✓ Include files thành công</p>";
echo "<p>Server: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Thời gian: " . date('Y-m-d H:i:s') . "</p>";

mysqli_close($con);
?>