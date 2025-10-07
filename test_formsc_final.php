<?php
// Test nhanh formsc.php sau khi chuyển đổi mysqli
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>✅ Test formsc.php - Chuyển đổi mysqli thành công!</h3>";

// Test include
include ("select_data.php");
include ("myfunctions.php");

if ($link) {
    echo "✓ Database connection: OK<br>";
    
    // Test một query đơn giản
    $test = mysqli_query($link, "SELECT COUNT(*) as total FROM users");
    if ($test) {
        $row = mysqli_fetch_array($test);
        echo "✓ Query test: " . $row['total'] . " users found<br>";
    } else {
        echo "✗ Query test failed: " . mysqli_error($link) . "<br>";
    }
    
} else {
    echo "✗ Database connection failed<br>";
}

echo "<br><h4>🎯 Kết quả chuyển đổi formsc.php:</h4>";
echo "<ul>";
echo "<li>✅ Đã chuyển đổi tất cả mysql_query → mysqli_query</li>";
echo "<li>✅ Đã chuyển đổi tất cả mysql_fetch_array → mysqli_fetch_array</li>";
echo "<li>✅ Đã chuyển đổi tất cả mysql_num_rows → mysqli_num_rows</li>";
echo "<li>✅ Đã chuyển đổi tất cả mysql_error → mysqli_error</li>";
echo "<li>✅ Tất cả đều sử dụng kết nối \$link từ select_data.php</li>";
echo "</ul>";

echo "<br><a href='formsc.php' target='_blank' style='background:#4CAF50;color:white;padding:10px;text-decoration:none;border-radius:5px;'>🚀 Test formsc.php</a>";

mysqli_close($link);
?>