<?php
// Test chức năng nhiều thiết bị trong formsc_professional.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Test Chức Năng Nhiều Thiết Bị</h2>";

// Test database connection
echo "<h3>1. Kiểm tra kết nối database</h3>";
include_once("select_data.php");

if ($link) {
    echo "<p style='color: green;'>✓ Kết nối database thành công</p>";
    
    // Test table structure
    $result = mysqli_query($link, "DESCRIBE hososcbd_iso");
    echo "<h4>Cấu trúc bảng hososcbd_iso:</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test sample data
    echo "<h4>Dữ liệu mẫu (5 records mới nhất):</h4>";
    $result = mysqli_query($link, "SELECT stt, phieu, mavt, tenvt, honghoc, ngaytao FROM hososcbd_iso ORDER BY stt DESC LIMIT 5");
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>STT</th><th>Phiếu</th><th>Mã VT</th><th>Tên VT</th><th>Hỏng hóc</th><th>Ngày tạo</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['stt'] . "</td>";
            echo "<td>" . $row['phieu'] . "</td>";
            echo "<td>" . $row['mavt'] . "</td>";
            echo "<td>" . $row['tenvt'] . "</td>";
            echo "<td>" . substr($row['honghoc'], 0, 50) . "...</td>";
            echo "<td>" . $row['ngaytao'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Chưa có dữ liệu</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Kết nối database thất bại</p>";
}

echo "<h3>2. Test UI Components</h3>";
echo "<div style='border: 1px solid #3498db; border-radius: 8px; padding: 20px; margin: 10px 0;'>";
echo "<h4 style='color: #2c3e50; margin-bottom: 15px;'>";
echo "<i class='fas fa-cog'></i> Demo: Thiết bị trong Form";
echo "</h4>";

echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>";
echo "<div>";
echo "<label style='font-weight: bold; display: block; margin-bottom: 5px;'>Tên thiết bị:</label>";
echo "<input type='text' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;' placeholder='VD: Máy bơm ly tâm'>";
echo "</div>";
echo "<div>";
echo "<label style='font-weight: bold; display: block; margin-bottom: 5px;'>Mã thiết bị:</label>";
echo "<input type='text' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;' placeholder='VD: MB001'>";
echo "</div>";
echo "</div>";

echo "<div style='margin-top: 15px;'>";
echo "<label style='font-weight: bold; display: block; margin-bottom: 5px;'>Hiện tượng hỏng hóc:</label>";
echo "<textarea style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;' rows='3' placeholder='VD: Máy bơm không hoạt động, có tiếng kêu bất thường...'></textarea>";
echo "</div>";

echo "</div>";

echo "<h3>3. Tính năng mới</h3>";
echo "<ul style='list-style-type: none; padding: 0;'>";
echo "<li style='padding: 10px; margin: 5px 0; background: #e8f5e8; border-left: 4px solid #27ae60;'>✅ <strong>Nhiều thiết bị trong 1 phiếu:</strong> Có thể thêm/xóa thiết bị động</li>";
echo "<li style='padding: 10px; margin: 5px 0; background: #e8f5e8; border-left: 4px solid #27ae60;'>✅ <strong>Thông tin chi tiết:</strong> Mã thiết bị, số máy, model cho từng thiết bị</li>";
echo "<li style='padding: 10px; margin: 5px 0; background: #e8f5e8; border-left: 4px solid #27ae60;'>✅ <strong>Validation:</strong> Kiểm tra dữ liệu đầu vào</li>";
echo "<li style='padding: 10px; margin: 5px 0; background: #e8f5e8; border-left: 4px solid #27ae60;'>✅ <strong>Animation:</strong> Hiệu ứng smooth khi thêm/xóa thiết bị</li>";
echo "<li style='padding: 10px; margin: 5px 0; background: #e8f5e8; border-left: 4px solid #27ae60;'>✅ <strong>Responsive:</strong> Giao diện tự động điều chỉnh trên mobile</li>";
echo "</ul>";

echo "<h3>4. Hướng dẫn sử dụng</h3>";
echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px;'>";
echo "<ol>";
echo "<li><strong>Nhập thông tin chung:</strong> Số phiếu, ngày, đơn vị yêu cầu, vị trí</li>";
echo "<li><strong>Thêm thiết bị:</strong> Click nút \"Thêm thiết bị\" để thêm thiết bị mới</li>";
echo "<li><strong>Nhập thông tin thiết bị:</strong> Tên, mã, số máy, model, hiện tượng hỏng hóc</li>";
echo "<li><strong>Xóa thiết bị:</strong> Click nút \"Xóa\" nếu muốn bỏ thiết bị (phải có ít nhất 1 thiết bị)</li>";
echo "<li><strong>Lưu phiếu:</strong> Hệ thống sẽ tự động lưu tất cả thiết bị với cùng 1 số phiếu</li>";
echo "</ol>";
echo "</div>";

echo "<h3>5. Links để test</h3>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='formsc_professional.php' target='_blank' style='background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);'>🚀 Test FormSC Professional</a>";
echo "<br>";
echo "<a href='formsc.php' target='_blank' style='background: #95a5a6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 15px; margin: 10px; display: inline-block;'>📄 FormSC Cũ (So sánh)</a>";
echo "</div>";

echo "<h3>6. Kiểm tra JavaScript</h3>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px;'>";
echo "<p><strong>Lưu ý:</strong> Để test đầy đủ chức năng thêm/xóa thiết bị, cần mở trực tiếp file formsc_professional.php</p>";
echo "<p>JavaScript functions được tích hợp:</p>";
echo "<ul>";
echo "<li><code>addEquipment()</code> - Thêm thiết bị mới</li>";
echo "<li><code>removeEquipment()</code> - Xóa thiết bị</li>";
echo "<li><code>updateEquipmentNumbers()</code> - Cập nhật số thứ tự</li>";
echo "<li><code>updateRemoveButtons()</code> - Quản lý nút xóa</li>";
echo "</ul>";
echo "</div>";

?>