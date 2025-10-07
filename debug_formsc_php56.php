<?php
// Debug formsc.php - kiểm tra tại sao không hiển thị gì
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug FormSC.php - PHP 5.6</h2>";

// Kiểm tra các biến GET/POST
echo "<h3>1. Biến GET/POST:</h3>";
echo "<p><strong>\$_GET:</strong> " . print_r($_GET, true) . "</p>";
echo "<p><strong>\$_POST:</strong> " . print_r($_POST, true) . "</p>";

// Kiểm tra các biến quan trọng
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : '';

echo "<h3>2. Biến được xử lý:</h3>";
echo "<ul>";
echo "<li>username: '" . htmlspecialchars($username) . "'</li>";
echo "<li>password: '" . htmlspecialchars($password) . "'</li>";
echo "<li>submit: '" . htmlspecialchars($submit) . "'</li>";
echo "<li>hoso: '" . htmlspecialchars($hoso) . "'</li>";
echo "</ul>";

// Kiểm tra logic điều kiện
echo "<h3>3. Logic điều kiện:</h3>";
echo "<ul>";
echo "<li>submit == 'nhapdulieu': " . ($submit == "nhapdulieu" ? "TRUE" : "FALSE") . "</li>";
echo "<li>hoso == 'phieuscbd': " . ($hoso == "phieuscbd" ? "TRUE" : "FALSE") . "</li>";
echo "<li>Điều kiện chính: " . (($submit == "nhapdulieu") && ($hoso == "phieuscbd") ? "TRUE" : "FALSE") . "</li>";
echo "</ul>";

// Kiểm tra include files
echo "<h3>4. Include files:</h3>";
if (file_exists("select_data.php")) {
    include_once("select_data.php");
    echo "<p style='color: green;'>✓ select_data.php included</p>";
} else {
    echo "<p style='color: red;'>✗ select_data.php missing</p>";
}

if (file_exists("myfunctions.php")) {
    include_once("myfunctions.php");
    echo "<p style='color: green;'>✓ myfunctions.php included</p>";
} else {
    echo "<p style='color: red;'>✗ myfunctions.php missing</p>";
}

// Test kết nối database
echo "<h3>5. Database connection:</h3>";
if (isset($usernamehost) && isset($passwordhost) && isset($databasename)) {
    $con = mysql_connect("localhost", $usernamehost, $passwordhost);
    if ($con) {
        $db = mysql_select_db($databasename, $con);
        if ($db) {
            echo "<p style='color: green;'>✓ Database connected successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Database selection failed: " . mysql_error() . "</p>";
        }
        mysql_close($con);
    } else {
        echo "<p style='color: red;'>✗ MySQL connection failed: " . mysql_error() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database variables not set</p>";
}

echo "<hr>";
echo "<h3>Gợi ý:</h3>";
echo "<p>Nếu tất cả OK nhưng formsc.php vẫn trống, có thể:</p>";
echo "<ul>";
echo "<li>File formsc.php chỉ xuất output khi có điều kiện cụ thể</li>";
echo "<li>Cần truyền đúng tham số GET/POST</li>";
echo "<li>Logic trong file quá phức tạp và bị stuck</li>";
echo "</ul>";

echo "<p><strong>Thử truy cập:</strong></p>";
echo "<ul>";
echo "<li><a href='formsc.php?submit=nhapdulieu&hoso=phieuscbd'>formsc.php với tham số</a></li>";
echo "<li><a href='index.php'>Quay về index.php</a></li>";
echo "</ul>";
?>