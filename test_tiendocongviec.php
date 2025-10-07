<?php
// Test kết nối cho tiendocongviec.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Test kết nối tiendocongviec.php</h3>";

include ("select_data.php");
include ("myfunctions.php");

echo "Database connection: ";
if ($link) {
    echo "✓ Connected<br>";
    echo "Connection type: " . get_resource_type($link) . "<br>";
} else {
    echo "✗ Failed - " . mysqli_connect_error() . "<br>";
    exit;
}

// Test query hososcbd_iso table
echo "<br><h4>Testing hososcbd_iso table:</h4>";
$test_query = mysqli_query($link, "SELECT COUNT(*) as total FROM hososcbd_iso WHERE bg='0'");
if ($test_query) {
    $row = mysqli_fetch_array($test_query);
    echo "✓ hososcbd_iso table accessible<br>";
    echo "Total records with bg='0': " . $row['total'] . "<br>";
} else {
    echo "✗ Error accessing hososcbd_iso: " . mysqli_error($link) . "<br>";
}

// Test query thietbi_iso table  
echo "<br><h4>Testing thietbi_iso table:</h4>";
$test_query2 = mysqli_query($link, "SELECT COUNT(*) as total FROM thietbi_iso");
if ($test_query2) {
    $row = mysqli_fetch_array($test_query2);
    echo "✓ thietbi_iso table accessible<br>";
    echo "Total records: " . $row['total'] . "<br>";
} else {
    echo "✗ Error accessing thietbi_iso: " . mysqli_error($link) . "<br>";
}

// Test query thietbihotro_iso table
echo "<br><h4>Testing thietbihotro_iso table:</h4>";
$test_query3 = mysqli_query($link, "SELECT COUNT(*) as total FROM thietbihotro_iso WHERE cdung='1'");
if ($test_query3) {
    $row = mysqli_fetch_array($test_query3);
    echo "✓ thietbihotro_iso table accessible<br>";
    echo "Total records with cdung='1': " . $row['total'] . "<br>";
} else {
    echo "✗ Error accessing thietbihotro_iso: " . mysqli_error($link) . "<br>";
}

// Test specific query from the original file
echo "<br><h4>Testing specific query from tiendocongviec.php:</h4>";
$nhomsct = ""; // Default value for testing
$test_query4 = mysqli_query($link, "select stt from hososcbd_iso where bg='0' and nhomsc like '%$nhomsct%'");
if ($test_query4) {
    echo "✓ Main query works<br>";
    echo "Rows found: " . mysqli_num_rows($test_query4) . "<br>";
} else {
    echo "✗ Main query failed: " . mysqli_error($link) . "<br>";
}

mysqli_close($link);
?>