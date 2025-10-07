<?php
// Test file cho formsc_professional.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🚀 Test FormSC Professional Version</h2>";

// Test 1: Kiểm tra file tồn tại
echo "<h3>1. Kiểm tra files</h3>";
if (file_exists("formsc_professional.php")) {
    echo "<p style='color: green;'>✓ formsc_professional.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ formsc_professional.php missing</p>";
}

if (file_exists("select_data.php")) {
    echo "<p style='color: green;'>✓ select_data.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ select_data.php missing</p>";
}

if (file_exists("myfunctions.php")) {
    echo "<p style='color: green;'>✓ myfunctions.php exists</p>";
} else {
    echo "<p style='color: red;'>✗ myfunctions.php missing</p>";
}

// Test 2: Kiểm tra database connection
echo "<h3>2. Test database connection</h3>";
try {
    include_once("select_data.php");
    if ($link) {
        echo "<p style='color: green;'>✓ Database connection OK</p>";
        
        // Test queries
        $result = mysqli_query($link, "SELECT COUNT(*) as count FROM hososcbd_iso");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p style='color: green;'>✓ hososcbd_iso table: " . $row['count'] . " records</p>";
        }
        
        $result = mysqli_query($link, "SELECT COUNT(*) as count FROM thietbi_iso");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p style='color: green;'>✓ thietbi_iso table: " . $row['count'] . " records</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Links to test</h3>";
echo "<p><a href='formsc_professional.php' target='_blank' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Test FormSC Professional</a></p>";
echo "<p><a href='formsc.php' target='_blank' style='background: #95a5a6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📄 FormSC Original</a></p>";

echo "<h3>4. Tính năng mới</h3>";
echo "<ul>";
echo "<li>✅ Giao diện hiện đại với CSS Grid và Flexbox</li>";
echo "<li>✅ Responsive design cho mobile</li>";
echo "<li>✅ Font Awesome icons</li>";
echo "<li>✅ Class-based architecture</li>";
echo "<li>✅ Prepared statements cho bảo mật</li>";
echo "<li>✅ Error handling tốt hơn</li>";
echo "<li>✅ Code structure rõ ràng, dễ maintain</li>";
echo "</ul>";

?>