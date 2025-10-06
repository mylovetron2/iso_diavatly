<?php
// Bật hiển thị lỗi PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug formsc.php</h2>";

try {
    echo "<p>1. Kiểm tra file select_data.php...</p>";
    
    // Test include file
    if (file_exists("select_data.php")) {
        echo "<p style='color: green;'>✓ File select_data.php tồn tại</p>";
        
        // Include và test
        include_once("select_data.php");
        echo "<p style='color: green;'>✓ Include select_data.php thành công</p>";
        
        echo "<p>Thông tin kết nối:</p>";
        echo "<ul>";
        echo "<li>hostname: " . (isset($hostname) ? $hostname : 'KHÔNG TỒN TẠI') . "</li>";
        echo "<li>usernamehost: " . (isset($usernamehost) ? $usernamehost : 'KHÔNG TỒN TẠI') . "</li>";
        echo "<li>passwordhost: " . (isset($passwordhost) ? '***' : 'KHÔNG TỒN TẠI') . "</li>";
        echo "<li>databasename: " . (isset($databasename) ? $databasename : 'KHÔNG TỒN TẠI') . "</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>✗ File select_data.php không tồn tại</p>";
    }
    
    echo "<p>2. Test kết nối database...</p>";
    
    // Test connection như trong formsc.php
    $con = @mysqli_connect("localhost", $usernamehost, $passwordhost, $databasename);
    
    if ($con) {
        echo "<p style='color: green;'>✓ Kết nối database thành công</p>";
        mysqli_close($con);
    } else {
        echo "<p style='color: red;'>✗ Lỗi kết nối database</p>";
        echo "<p>Error: " . mysqli_connect_error() . "</p>";
    }
    
    echo "<p>3. Test include myfunctions.php...</p>";
    
    if (file_exists("myfunctions.php")) {
        echo "<p style='color: green;'>✓ File myfunctions.php tồn tại</p>";
        include_once("myfunctions.php");
        echo "<p style='color: green;'>✓ Include myfunctions.php thành công</p>";
    } else {
        echo "<p style='color: red;'>✗ File myfunctions.php không tồn tại</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>LỖI: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Debug hoàn tất</em></p>";
?>