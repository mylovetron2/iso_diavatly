<?php
// FormSC.php với error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Include files
    if (!file_exists("select_data.php")) {
        throw new Exception("File select_data.php không tồn tại");
    }
    include ("select_data.php");
    
    if (!file_exists("myfunctions.php")) {
        throw new Exception("File myfunctions.php không tồn tại");  
    }
    include ("myfunctions.php");

    // Variables
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $submit = isset($_POST['submit']) ? $_POST['submit'] : '';
    $hoso = isset($_POST['hoso']) ? $_POST['hoso'] : '';

    // Database connection  
    $con = mysqli_connect("localhost", $usernamehost, $passwordhost, $databasename);
    if (!$con) {
        throw new Exception("Lỗi kết nối database: " . mysqli_connect_error());
    }

    // CSS Style (minimal)
    echo '<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; background: #fee; padding: 10px; border: 1px solid red; }
        .success { color: green; background: #efe; padding: 10px; border: 1px solid green; }
    </style>';

    // Main logic based on submit type
    if (($submit == "nhapdulieu") && ($hoso == "phieuscbd")) {
        echo '<div class="success">Xử lý nhập phiếu yêu cầu dịch vụ...</div>';
        // Thêm logic xử lý ở đây
        
    } else {
        // Default form hoặc menu
        echo '<h2>Hệ thống quản lý SC/BD</h2>';
        echo '<p>Chọn chức năng từ menu...</p>';
    }

    mysqli_close($con);
    
} catch (Exception $e) {
    echo '<div class="error">LỖI: ' . $e->getMessage() . '</div>';
    echo '<p>File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . '</p>';
}
?>