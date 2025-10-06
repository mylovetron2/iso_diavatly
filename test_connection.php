<?php
// File test kết nối cơ sở dữ liệu
echo "<h2>Test kết nối MySQL Database</h2>";

// Khai báo thông tin kết nối
$hostname = 'localhost';
$usernamehost = 'mapselli676e_diavatly_quanly';
$passwordhost = 'cntt2019';
$databasename = 'mapselli676e_diavatly_quanly';

echo "<p><strong>Thông tin kết nối:</strong></p>";
echo "<ul>";
echo "<li>Hostname: " . $hostname . "</li>";
echo "<li>Username: " . $usernamehost . "</li>";
echo "<li>Password: " . (empty($passwordhost) ? '(trống)' : '***') . "</li>";
echo "<li>Database: " . $databasename . "</li>";
echo "</ul>";

echo "<hr>";

// Test kết nối MySQL
echo "<h3>1. Test kết nối MySQL Server:</h3>";
$connection = @mysqli_connect($hostname, $usernamehost, $passwordhost);

if ($connection) {
    echo "<p style='color: green;'>✓ Kết nối MySQL Server thành công!</p>";
    
    // Test database
    echo "<h3>2. Test kết nối Database:</h3>";
    $db_connection = @mysqli_connect($hostname, $usernamehost, $passwordhost, $databasename);
    
    if ($db_connection) {
        echo "<p style='color: green;'>✓ Kết nối Database '$databasename' thành công!</p>";
        
        // Test query
        echo "<h3>3. Test truy vấn Database:</h3>";
        $query = "SHOW TABLES";
        $result = @mysqli_query($db_connection, $query);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Truy vấn Database thành công!</p>";
            echo "<p><strong>Các bảng trong database:</strong></p>";
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠ Database trống hoặc không có quyền truy cập bảng</p>";
            echo "<p>Error: " . mysqli_error($db_connection) . "</p>";
        }
        
        mysqli_close($db_connection);
    } else {
        echo "<p style='color: red;'>✗ Lỗi kết nối Database '$databasename'</p>";
        echo "<p>Error: " . mysqli_connect_error() . "</p>";
        
        // Liệt kê các database có sẵn
        echo "<h3>Các database có sẵn:</h3>";
        $query = "SHOW DATABASES";
        $result = @mysqli_query($connection, $query);
        if ($result) {
            echo "<ul>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
    }
    
    mysqli_close($connection);
} else {
    echo "<p style='color: red;'>✗ Lỗi kết nối MySQL Server</p>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
    
    // Gợi ý khắc phục
    echo "<h3>Gợi ý khắc phục:</h3>";
    echo "<ul>";
    echo "<li>Kiểm tra XAMPP đã khởi động MySQL chưa</li>";
    echo "<li>Kiểm tra username/password MySQL</li>";
    echo "<li>Kiểm tra port MySQL (mặc định 3306)</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><em>File test: " . __FILE__ . "</em></p>";
echo "<p><em>Thời gian: " . date('Y-m-d H:i:s') . "</em></p>";
?>