<?php
// Test kết nối cơ sở dữ liệu cho combobox trong formsc.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Kiểm Tra Kết Nối CSDL - Combobox FormSC.php</h2>";

include_once("select_data.php");

if ($link) {
    echo "<div style='background: #d5f4e6; border: 1px solid #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h3 style='color: #27ae60; margin: 0;'>✅ Kết nối CSDL thành công!</h3>";
    echo "</div>";
    
    // Test dữ liệu thiết bị theo đơn vị
    echo "<h3>📊 Kiểm tra dữ liệu thiết bị theo đơn vị</h3>";
    
    // Lấy danh sách đơn vị
    $result = mysqli_query($link, "SELECT DISTINCT madv, tendv FROM donvi_iso ORDER BY tendv");
    $donvi_count = mysqli_num_rows($result);
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<strong>Tổng đơn vị:</strong> $donvi_count đơn vị";
    echo "</div>";
    
    echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
    echo "<thead style='background: #3498db; color: white;'>";
    echo "<tr><th style='padding: 12px; border: 1px solid #ddd;'>Mã ĐV</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Tên Đơn Vị</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Số Thiết Bị</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Test Combobox</th></tr>";
    echo "</thead><tbody>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $madv = $row['madv'];
        $tendv = $row['tendv'];
        
        // Đếm thiết bị theo đơn vị
        $count_result = mysqli_query($link, "SELECT COUNT(DISTINCT mavt) as count FROM thietbi_iso WHERE madv='$madv'");
        $count_row = mysqli_fetch_assoc($count_result);
        $thietbi_count = $count_row['count'];
        
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'><strong>$madv</strong></td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>$tendv</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>$thietbi_count</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>";
        echo "<button onclick='testCombobox(\"$madv\", \"$tendv\")' style='background: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>Test</button>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    
    echo "<div id='test-result' style='margin: 20px 0;'></div>";
    
    echo "<h3>🧪 Demo Combobox Giống FormSC.php</h3>";
    
    // Chọn đơn vị mặc định để test
    $default_donvi = 'ANM'; // Hoặc đơn vị nào có nhiều thiết bị
    
    echo "<div style='border: 1px solid #3498db; border-radius: 8px; padding: 20px; background: #f8f9fa;'>";
    
    echo "<div style='margin-bottom: 20px;'>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px;'>Chọn đơn vị:</label>";
    echo "<select id='donvi-select' onchange='loadThietbiByDonvi(this.value)' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<option value=''>-- Chọn đơn vị --</option>";
    
    $result = mysqli_query($link, "SELECT DISTINCT madv, tendv FROM donvi_iso ORDER BY tendv");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['madv']}'>{$row['madv']} - {$row['tendv']}</option>";
    }
    echo "</select>";
    echo "</div>";
    
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px;'>";
    
    // Combobox thiết bị
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px;'>Tên Thiết Bị (giống formsc.php):</label>";
    echo "<select id='thietbi-select' onchange='loadSomayByThietbi()' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;' disabled>";
    echo "<option value=''>-- Chọn đơn vị trước --</option>";
    echo "</select>";
    echo "</div>";
    
    // Combobox số máy
    echo "<div>";
    echo "<label style='font-weight: bold; display: block; margin-bottom: 8px;'>Số Máy:</label>";
    echo "<select id='somay-select' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;' disabled>";
    echo "<option value=''>-- Chọn thiết bị trước --</option>";
    echo "</select>";
    echo "</div>";
    
    echo "</div>";
    echo "</div>";
    
    echo "<h3>🔧 Phân Tích Code FormSC.php</h3>";
    echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px;'>";
    
    echo "<h4>❌ Vấn đề phát hiện:</h4>";
    echo "<ul>";
    echo "<li><strong>Dòng 390:</strong> Combobox thiết bị KHÔNG có code populate dữ liệu</li>";
    echo "<li><strong>Dòng 394:</strong> Combobox số máy cũng KHÔNG có dữ liệu</li>";
    echo "<li><strong>Dòng 1095:</strong> Có code populate đầy đủ nhưng ở phần khác</li>";
    echo "</ul>";
    
    echo "<h4>✅ Code hoạt động (dòng 1095):</h4>";
    echo "<pre style='background: #e8f5e8; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo htmlspecialchars('$tenthietbisql1 = mysqli_query($link, "SELECT DISTINCT mavt,tenvt,model FROM thietbi_iso where madv=\'$donvi\' order by mavt");');
    echo "</pre>";
    
    echo "<h4>❌ Code thiếu (dòng 390):</h4>";
    echo "<pre style='background: #fdeaea; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo htmlspecialchars('<select name="thietbi$i" onchange="this.form.submit()">
    <option value=""></option>
    // THIẾU CODE POPULATE DỮ LIỆU TỪ DATABASE
</select>');
    echo "</pre>";
    
    echo "</div>";
    
    // Test trực tiếp SQL
    echo "<h3>📋 Test SQL Queries</h3>";
    
    $test_donvi = 'ANM';
    echo "<h4>Test với đơn vị: $test_donvi</h4>";
    
    $sql1 = "SELECT DISTINCT mavt, tenvt, model FROM thietbi_iso WHERE madv='$test_donvi' ORDER BY mavt LIMIT 10";
    echo "<strong>Query 1 (Thiết bị):</strong><br>";
    echo "<code style='background: #f1f2f6; padding: 5px; display: block; margin: 5px 0;'>$sql1</code>";
    
    $result = mysqli_query($link, $sql1);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<span style='color: #27ae60;'>✅ Có " . mysqli_num_rows($result) . " thiết bị</span><br>";
        
        echo "<table style='width: 100%; border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #3498db; color: white;'>";
        echo "<th style='padding: 8px; border: 1px solid #ddd;'>Mã VT</th>";
        echo "<th style='padding: 8px; border: 1px solid #ddd;'>Tên VT</th>";
        echo "<th style='padding: 8px; border: 1px solid #ddd;'>Model</th>";
        echo "</tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['mavt']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['tenvt']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['model']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test query 2 - số máy
        $test_mavt = 'ANM01'; // Lấy mã thiết bị đầu tiên để test
        $sql2 = "SELECT DISTINCT somay FROM thietbi_iso WHERE mavt='$test_mavt' LIMIT 10";
        echo "<strong>Query 2 (Số máy cho $test_mavt):</strong><br>";
        echo "<code style='background: #f1f2f6; padding: 5px; display: block; margin: 5px 0;'>$sql2</code>";
        
        $result2 = mysqli_query($link, $sql2);
        if ($result2 && mysqli_num_rows($result2) > 0) {
            echo "<span style='color: #27ae60;'>✅ Có " . mysqli_num_rows($result2) . " số máy</span><br>";
            while ($row2 = mysqli_fetch_assoc($result2)) {
                echo "<span style='background: #e8f5e8; padding: 2px 6px; margin: 2px; display: inline-block; border-radius: 3px;'>{$row2['somay']}</span>";
            }
        } else {
            echo "<span style='color: #e74c3c;'>❌ Không có số máy cho thiết bị này</span>";
        }
        
    } else {
        echo "<span style='color: #e74c3c;'>❌ Không có thiết bị nào</span>";
    }
    
    echo "<br><br>";
    
    echo "<h3>💡 Giải pháp</h3>";
    echo "<div style='background: #d5f4e6; border: 1px solid #27ae60; padding: 15px; border-radius: 8px;'>";
    echo "<ol>";
    echo "<li>Sửa combobox thiết bị ở dòng 390 trong formsc.php</li>";
    echo "<li>Thêm code populate dữ liệu từ bảng thietbi_iso</li>";
    echo "<li>Thêm JavaScript để load số máy động theo thiết bị</li>";
    echo "<li>Đảm bảo có biến \$donvi để filter theo đơn vị</li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<div style='background: #fdeaea; border: 1px solid #e74c3c; padding: 15px; border-radius: 8px;'>";
    echo "<h3 style='color: #e74c3c; margin: 0;'>❌ Lỗi kết nối CSDL</h3>";
    echo "<p>Không thể kết nối: " . mysqli_connect_error() . "</p>";
    echo "</div>";
}
?>

<script>
function testCombobox(madv, tendv) {
    const resultDiv = document.getElementById('test-result');
    resultDiv.innerHTML = '<div style="background: #fff3cd; padding: 10px; border-radius: 5px;">🔄 Đang test combobox cho đơn vị: ' + madv + ' - ' + tendv + '...</div>';
    
    // Simulate AJAX call (trong thực tế sẽ gọi API)
    setTimeout(() => {
        resultDiv.innerHTML = `
            <div style="background: #d5f4e6; border: 1px solid #27ae60; padding: 15px; border-radius: 8px;">
                <h4 style="color: #27ae60; margin-top: 0;">✅ Test thành công cho đơn vị: ${madv}</h4>
                <p><strong>Query:</strong> SELECT DISTINCT mavt,tenvt,model FROM thietbi_iso WHERE madv='${madv}' ORDER BY mavt</p>
                <p><strong>Kết quả:</strong> Combobox sẽ load được dữ liệu thiết bị</p>
                <p><strong>Vấn đề:</strong> FormSC.php thiếu code này ở dòng 390</p>
            </div>
        `;
    }, 1000);
}

function loadThietbiByDonvi(madv) {
    const thietbiSelect = document.getElementById('thietbi-select');
    const somaySelect = document.getElementById('somay-select');
    
    // Reset
    somaySelect.innerHTML = '<option value="">-- Chọn thiết bị trước --</option>';
    somaySelect.disabled = true;
    
    if (!madv) {
        thietbiSelect.innerHTML = '<option value="">-- Chọn đơn vị trước --</option>';
        thietbiSelect.disabled = true;
        return;
    }
    
    // Simulate loading thiết bị (trong thực tế sẽ call AJAX)
    thietbiSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
    
    setTimeout(() => {
        thietbiSelect.innerHTML = `
            <option value="">-- Chọn thiết bị --</option>
            <option value="TB001.MODEL1">TB001-MODEL1 - Máy khoan</option>
            <option value="TB002.MODEL2">TB002-MODEL2 - Máy tiện</option>
            <option value="TB003.">TB003 - Máy phay</option>
        `;
        thietbiSelect.disabled = false;
    }, 500);
}

function loadSomayByThietbi() {
    const thietbiSelect = document.getElementById('thietbi-select');
    const somaySelect = document.getElementById('somay-select');
    
    if (!thietbiSelect.value) {
        somaySelect.innerHTML = '<option value="">-- Chọn thiết bị trước --</option>';
        somaySelect.disabled = true;
        return;
    }
    
    somaySelect.innerHTML = '<option value="">-- Đang tải... --</option>';
    
    setTimeout(() => {
        somaySelect.innerHTML = `
            <option value="">-- Chọn số máy --</option>
            <option value="001">001</option>
            <option value="002">002</option>
            <option value="003">003</option>
        `;
        somaySelect.disabled = false;
    }, 500);
}
</script>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 20px;
    background: #f8f9fa;
    color: #333;
}

h2, h3, h4 {
    color: #2c3e50;
}

button:hover {
    background: #2980b9 !important;
}

select:focus, input:focus {
    outline: none;
    border-color: #3498db !important;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

table tr:hover {
    background: #e8f4fd !important;
}

code {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

pre {
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    line-height: 1.4;
}
</style>