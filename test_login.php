<?php
// Test login system với mysqli
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Test Login System</h3>";

include ("select_data.php");
include ("myfunctions.php");

echo "Database connection: ";
if ($link) {
    echo "✓ Connected<br>";
    echo "Connection resource: " . get_resource_type($link) . "<br>";
} else {
    echo "✗ Failed<br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    exit;
}

// Test simple query
echo "<br>Testing user query:<br>";
$test_query = mysqli_query($link, "SELECT username, phanquyen FROM users LIMIT 5");
if ($test_query) {
    echo "✓ Query successful<br>";
    echo "Number of rows: " . mysqli_num_rows($test_query) . "<br>";
    
    echo "<table border='1'>";
    echo "<tr><th>Username</th><th>Phanquyen</th></tr>";
    while($row = mysqli_fetch_array($test_query)) {
        echo "<tr><td>" . $row['username'] . "</td><td>" . $row['phanquyen'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "✗ Query failed<br>";
    echo "Error: " . mysqli_error($link) . "<br>";
}

// Test specific user
echo "<br><h4>Test specific user lookup:</h4>";
$username = 'admin'; // Change this to test different users
$r2 = mysqli_query($link, "SELECT madv,phanquyen FROM users WHERE username='$username' ");
if ($r2) {
    echo "Query for user '$username': ✓<br>";
    echo "Rows found: " . mysqli_num_rows($r2) . "<br>";
    
    while($row = mysqli_fetch_array($r2)) {
        echo "madv: " . $row['madv'] . ", phanquyen: " . $row['phanquyen'] . "<br>";
    }
} else {
    echo "Query for user '$username': ✗<br>";
    echo "Error: " . mysqli_error($link) . "<br>";
}

mysqli_close($link);
?>