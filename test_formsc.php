<?php
// Test formsc.php đơn giản
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test formsc.php cơ bản</h2>";

// Test các include cần thiết
echo "<h3>1. Test include files</h3>";

if (file_exists("select_data.php")) {
    include_once("select_data.php");
    echo "<p style='color: green;'>✓ select_data.php OK</p>";
} else {
    echo "<p style='color: red;'>✗ select_data.php missing</p>";
}

if (file_exists("myfunctions.php")) {
    include_once("myfunctions.php");
    echo "<p style='color: green;'>✓ myfunctions.php OK</p>";
} else {
    echo "<p style='color: red;'>✗ myfunctions.php missing</p>";
}

// Test các biến POST/GET
echo "<h3>2. Test variables</h3>";
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$submit = isset($_POST['submit']) ? $_POST['submit'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : '';

echo "<p>username: " . htmlspecialchars($username) . "</p>";
echo "<p>submit: " . htmlspecialchars($submit) . "</p>";
echo "<p>hoso: " . htmlspecialchars($hoso) . "</p>";

// Test kết nối database
echo "<h3>3. Test database connection</h3>";
if (isset($usernamehost) && isset($passwordhost) && isset($databasename)) {
    $con = @mysqli_connect("localhost", $usernamehost, $passwordhost, $databasename);
    
    if ($con) {
        echo "<p style='color: green;'>✓ Database connection OK</p>";
        mysqli_close($con);
    } else {
        echo "<p style='color: red;'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database variables not set</p>";
}

echo "<hr>";
echo "<p>Test completed. If no errors above, formsc.php should work.</p>";
?>