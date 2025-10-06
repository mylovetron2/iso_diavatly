<?php
// FormSC.php Wrapper - Bắt lỗi và xử lý an toàn
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Buffer output để tránh lỗi headers
ob_start();

try {
    // Backup original file và include
    if (file_exists("formsc_backup.php")) {
        include("formsc_backup.php");
    } else {
        throw new Exception("File formsc_backup.php không tồn tại");
    }
    
} catch (Exception $e) {
    // Clear buffer và hiển thị lỗi
    ob_clean();
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Lỗi FormSC</title>
        <meta charset="utf-8">
        <style>
            body { font-family: Arial; margin: 20px; }
            .error { background: #fee; border: 1px solid #f00; padding: 15px; color: #800; }
            .fallback { background: #eef; border: 1px solid #00f; padding: 15px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h2>Lỗi FormSC.php</h2>
            <p><strong>Error:</strong> ' . $e->getMessage() . '</p>
            <p><strong>File:</strong> ' . $e->getFile() . '</p>
            <p><strong>Line:</strong> ' . $e->getLine() . '</p>
        </div>
        
        <div class="fallback">
            <h3>Giải pháp tạm thời:</h3>
            <ul>
                <li><a href="formsc_debug.php">Sử dụng FormSC Debug</a></li>
                <li><a href="formsc_simple.php">Sử dụng FormSC Simple</a></li>
                <li><a href="index.php">Quay về trang chính</a></li>
            </ul>
        </div>
    </body>
    </html>';
}

// Flush output
ob_end_flush();
?>