<?php
// Test nhanh tiendocongviec.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Test kết nối cho tiendocongviec.php</h3>";

// Include các file cần thiết giống như trong tiendocongviec.php
include ("select_data.php");
include ("myfunctions.php");

// Kiểm tra kết nối
if ($link) {
    echo "✓ Database connected successfully<br>";
} else {
    echo "✗ Database connection failed: " . mysqli_connect_error() . "<br>";
    exit;
}

// Test các table chính được sử dụng trong tiendocongviec.php
echo "<br><h4>Testing main tables:</h4>";

// Test hososcbd_iso
$test1 = mysqli_query($link, "SELECT COUNT(*) as count FROM hososcbd_iso WHERE bg='0'");
if ($test1) {
    $row = mysqli_fetch_array($test1);
    echo "✓ hososcbd_iso: " . $row['count'] . " records<br>";
} else {
    echo "✗ hososcbd_iso error: " . mysqli_error($link) . "<br>";
}

// Test thietbi_iso
$test2 = mysqli_query($link, "SELECT COUNT(*) as count FROM thietbi_iso");
if ($test2) {
    $row = mysqli_fetch_array($test2);
    echo "✓ thietbi_iso: " . $row['count'] . " records<br>";
} else {
    echo "✗ thietbi_iso error: " . mysqli_error($link) . "<br>";
}

// Test thietbihotro_iso
$test3 = mysqli_query($link, "SELECT COUNT(*) as count FROM thietbihotro_iso WHERE cdung='1'");
if ($test3) {
    $row = mysqli_fetch_array($test3);
    echo "✓ thietbihotro_iso: " . $row['count'] . " records<br>";
} else {
    echo "✗ thietbihotro_iso error: " . mysqli_error($link) . "<br>";
}

echo "<br><h4>All database connections working properly!</h4>";
echo "<a href='tiendocongviec.php' target='_blank'>→ Test tiendocongviec.php</a>";

mysqli_close($link);
?>