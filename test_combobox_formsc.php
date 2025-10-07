<?php
// Test chức năng combobox trong formsc_professional.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔗 Test Combobox Kết Nối CSDL - FormSC Professional</h2>";

include_once("select_data.php");

if ($link) {
    echo "<div style='background: #d5f4e6; border: 1px solid #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #27ae60; margin: 0;'>✅ Kết nối CSDL thành công!</h3>";
    echo "</div>";
    
    // Kiểm tra dữ liệu thiết bị
    $result = mysqli_query($link, "SELECT COUNT(*) as total FROM thietbi_iso");
    $total_equipment = mysqli_fetch_assoc($result)['total'];
    
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;'>";
    
    // Card thống kê
    echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;'>";
    echo "<div style='font-size: 2.5em; font-weight: bold; color: #3498db;'>$total_equipment</div>";
    echo "<div style='color: #666; margin-top: 10px;'>Tổng thiết bị</div>";
    echo "</div>";
    
    // Kiểm tra nhóm thiết bị
    $result = mysqli_query($link, "SELECT COUNT(DISTINCT nhomthietbi) as total FROM thietbi_iso WHERE nhomthietbi IS NOT NULL AND nhomthietbi != ''");
    $total_groups = mysqli_fetch_assoc($result)['total'];
    
    echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;'>";
    echo "<div style='font-size: 2.5em; font-weight: bold; color: #e74c3c;'>$total_groups</div>";
    echo "<div style='color: #666; margin-top: 10px;'>Nhóm thiết bị</div>";
    echo "</div>";
    
    // Kiểm tra thiết bị có số máy
    $result = mysqli_query($link, "SELECT COUNT(*) as total FROM thietbi_iso WHERE somay IS NOT NULL AND somay != ''");
    $total_with_somay = mysqli_fetch_assoc($result)['total'];
    
    echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;'>";
    echo "<div style='font-size: 2.5em; font-weight: bold; color: #27ae60;'>$total_with_somay</div>";
    echo "<div style='color: #666; margin-top: 10px;'>Có số máy</div>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<h3>🧪 Demo Combobox Liên Động</h3>";
    echo "<div style='border: 1px solid #3498db; border-radius: 8px; padding: 20px; background: #f8f9fa; margin: 20px 0;'>";
    
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;'>";
    
    // Combobox thiết bị
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px; color: #2c3e50;'>";
    echo "<i class='fas fa-cog'></i> Chọn thiết bị:";
    echo "</label>";
    echo "<select id='test_thietbi' onchange='loadTestSoMay(this.value)' style='width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 1em;'>";
    echo "<option value=''>-- Chọn thiết bị --</option>";
    
    $result = mysqli_query($link, "SELECT DISTINCT mavt, tenvt, nhomthietbi FROM thietbi_iso ORDER BY tenvt LIMIT 20");
    while ($row = mysqli_fetch_assoc($result)) {
        $display = $row['mavt'] . ' - ' . $row['tenvt'];
        if (!empty($row['nhomthietbi'])) {
            $display .= ' (' . $row['nhomthietbi'] . ')';
        }
        echo "<option value='" . $row['mavt'] . "'>" . htmlspecialchars($display) . "</option>";
    }
    echo "</select>";
    echo "</div>";
    
    // Tên thiết bị (auto-fill)
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px; color: #2c3e50;'>";
    echo "<i class='fas fa-tag'></i> Tên thiết bị:";
    echo "</label>";
    echo "<input type='text' id='test_tenvt' style='width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; background: #f8f9fa;' placeholder='Tên thiết bị (tự động)' readonly>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px;'>";
    
    // Combobox số máy
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px; color: #2c3e50;'>";
    echo "<i class='fas fa-hashtag'></i> Chọn số máy:";
    echo "</label>";
    echo "<select id='test_somay' onchange='loadTestModel(this)' style='width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 1em;' disabled>";
    echo "<option value=''>-- Chọn thiết bị trước --</option>";
    echo "</select>";
    echo "</div>";
    
    // Model (auto-fill)
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px; color: #2c3e50;'>";
    echo "<i class='fas fa-industry'></i> Model:";
    echo "</label>";
    echo "<input type='text' id='test_model' style='width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; background: #f8f9fa;' placeholder='Model (tự động)' readonly>";
    echo "</div>";
    
    echo "</div>";
    
    echo "</div>";
    
    // Tạo dữ liệu JavaScript
    $result = mysqli_query($link, "SELECT mavt, somay, model, tenvt FROM thietbi_iso ORDER BY mavt, somay");
    $equipment_data = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $equipment_data[$row['mavt']][] = [
            'somay' => $row['somay'],
            'model' => $row['model'],
            'tenvt' => $row['tenvt']
        ];
    }
    
    echo "<script>
    const testEquipmentData = " . json_encode($equipment_data) . ";
    
    function loadTestSoMay(mavt) {
        const soMaySelect = document.getElementById('test_somay');
        const tenVTInput = document.getElementById('test_tenvt');
        const modelInput = document.getElementById('test_model');
        
        // Reset
        tenVTInput.value = '';
        modelInput.value = '';
        soMaySelect.innerHTML = '<option value=\"\">-- Đang tải... --</option>';
        
        if (!mavt) {
            soMaySelect.innerHTML = '<option value=\"\">-- Chọn thiết bị trước --</option>';
            soMaySelect.disabled = true;
            return;
        }
        
        // Load data
        if (testEquipmentData[mavt]) {
            const equipmentList = testEquipmentData[mavt];
            
            // Set tên thiết bị
            if (equipmentList.length > 0) {
                tenVTInput.value = equipmentList[0].tenvt;
            }
            
            // Load số máy options
            soMaySelect.innerHTML = '<option value=\"\">-- Chọn số máy --</option>';
            equipmentList.forEach(item => {
                const option = document.createElement('option');
                option.value = item.somay;
                option.text = item.somay + (item.model ? ' (' + item.model + ')' : '');
                option.dataset.model = item.model || '';
                soMaySelect.appendChild(option);
            });
            
            soMaySelect.disabled = false;
            
        } else {
            soMaySelect.innerHTML = '<option value=\"\">-- Không có dữ liệu --</option>';
            soMaySelect.disabled = true;
        }
    }
    
    function loadTestModel(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const modelInput = document.getElementById('test_model');
        modelInput.value = selectedOption.dataset.model || '';
    }
    </script>";
    
    echo "<h3>📋 Danh Sách Thiết Bị Mẫu</h3>";
    echo "<div style='max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px;'>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<thead style='background: #3498db; color: white; position: sticky; top: 0;'>";
    echo "<tr><th style='padding: 12px; text-align: left;'>Mã VT</th><th style='padding: 12px; text-align: left;'>Tên VT</th><th style='padding: 12px; text-align: left;'>Số máy</th><th style='padding: 12px; text-align: left;'>Model</th><th style='padding: 12px; text-align: left;'>Nhóm TB</th></tr>";
    echo "</thead>";
    echo "<tbody>";
    
    $result = mysqli_query($link, "SELECT mavt, tenvt, somay, model, nhomthietbi FROM thietbi_iso ORDER BY mavt, somay LIMIT 50");
    $current_mavt = "";
    $row_style = "";
    
    while ($row = mysqli_fetch_assoc($result)) {
        if ($current_mavt != $row['mavt']) {
            $current_mavt = $row['mavt'];
            $row_style = ($row_style == "background: #f9f9f9;") ? "background: white;" : "background: #f9f9f9;";
        }
        
        echo "<tr style='$row_style'>";
        echo "<td style='padding: 8px; border-bottom: 1px solid #eee;'><strong>" . htmlspecialchars($row['mavt']) . "</strong></td>";
        echo "<td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['tenvt']) . "</td>";
        echo "<td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['somay']) . "</td>";
        echo "<td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($row['model']) . "</td>";
        echo "<td style='padding: 8px; border-bottom: 1px solid #eee;'><span style='background: #e8f5e8; padding: 2px 6px; border-radius: 3px; font-size: 0.9em;'>" . htmlspecialchars($row['nhomthietbi']) . "</span></td>";
        echo "</tr>";
    }
    
    echo "</tbody></table></div>";
    
    echo "<h3>🔗 Test Links</h3>";
    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='formsc_professional.php' target='_blank' style='background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);'>";
    echo "🚀 Test FormSC Professional với Combobox";
    echo "</a>";
    echo "<br>";
    echo "<a href='test_combobox_connection.php' target='_blank' style='background: #95a5a6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 15px; margin: 10px; display: inline-block;'>";
    echo "📊 Chi tiết kết nối CSDL";
    echo "</a>";
    echo "</div>";
    
    echo "<h3>✨ Tính năng mới</h3>";
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 8px;'>";
    echo "<ul style='margin: 0; padding-left: 20px;'>";
    echo "<li><strong>Combobox thông minh:</strong> Chọn thiết bị từ danh mục có sẵn trong CSDL</li>";
    echo "<li><strong>Auto-fill thông tin:</strong> Tự động điền tên thiết bị khi chọn</li>";
    echo "<li><strong>Liên động số máy:</strong> Danh sách số máy cập nhật theo thiết bị được chọn</li>";
    echo "<li><strong>Hiển thị model:</strong> Tự động hiển thị model khi chọn số máy</li>";
    echo "<li><strong>Validation:</strong> Đảm bảo dữ liệu chính xác từ CSDL</li>";
    echo "<li><strong>Performance:</strong> Load dữ liệu nhanh với JavaScript</li>";
    echo "</ul>";
    echo "</div>";
    
} else {
    echo "<div style='background: #fdeaea; border: 1px solid #e74c3c; padding: 15px; border-radius: 8px;'>";
    echo "<h3 style='color: #e74c3c; margin: 0;'>❌ Lỗi kết nối CSDL</h3>";
    echo "<p>Không thể kết nối đến cơ sở dữ liệu: " . mysqli_connect_error() . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    color: #333;
}

.fas {
    width: 16px;
    margin-right: 8px;
}

h2, h3 {
    color: #2c3e50;
}

select:focus, input:focus {
    outline: none;
    border-color: #3498db !important;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

table tr:hover {
    background: #e8f4fd !important;
}
</style>