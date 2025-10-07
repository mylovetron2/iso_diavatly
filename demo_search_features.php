<?php
/**
 * Demo Chức Năng Tìm Kiếm - TienDoConvViec Professional
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Tìm Kiếm - TienDoConvViec Professional</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-card h3 {
            color: #2980b9;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            margin: 10px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .feature-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
        }
        
        .demo-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .demo-search {
            display: grid;
            grid-template-columns: auto 1fr auto auto;
            gap: 15px;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .demo-search input {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1em;
            min-width: 300px;
        }
        
        .demo-search select {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            background: white;
            font-size: 1em;
        }
        
        .demo-btn {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .demo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .search-examples {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .example-card {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .example-card:hover {
            background: #d1ecf1;
            transform: translateY(-2px);
        }
        
        .example-title {
            font-weight: bold;
            color: #0c5460;
            margin-bottom: 5px;
        }
        
        .example-query {
            font-family: monospace;
            color: #495057;
            font-size: 0.9em;
        }
        
        .how-to-use {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .how-to-use h4 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .how-to-use ol {
            color: #856404;
            padding-left: 20px;
        }
        
        .how-to-use li {
            margin: 8px 0;
        }
        
        .test-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .test-btn {
            background: linear-gradient(45deg, #27ae60, #219a52);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        
        .test-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
        }
        
        .highlight {
            background: #ffeb3b;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        @media (max-width: 768px) {
            .demo-search {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }
            
            .demo-search input {
                min-width: auto;
                width: 100%;
            }
            
            .test-links {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-search"></i> Demo Chức Năng Tìm Kiếm</h1>
            <div class="subtitle">Hệ thống tìm kiếm thông minh cho TienDoConvViec Professional</div>
        </div>

        <!-- Features Grid -->
        <div class="feature-grid">
            <div class="feature-card">
                <h3><i class="fas fa-bolt"></i> Tìm Kiếm Nhanh</h3>
                <ul class="feature-list">
                    <li>Tìm kiếm realtime với kết quả ngay lập tức</li>
                    <li>Giao diện đẹp với placeholder gợi ý</li>
                    <li>Tự động highlight kết quả tìm thấy</li>
                    <li>Responsive design trên mọi thiết bị</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-filter"></i> Tìm Kiếm Theo Loại</h3>
                <ul class="feature-list">
                    <li><strong>Tất cả:</strong> Tìm trong mọi trường dữ liệu</li>
                    <li><strong>Mã thiết bị:</strong> Tìm theo mã vật tư (mavt)</li>
                    <li><strong>Số máy:</strong> Tìm theo số máy cụ thể</li>
                    <li><strong>Số hồ sơ:</strong> Tìm theo mã hồ sơ</li>
                    <li><strong>Model:</strong> Tìm theo model thiết bị</li>
                    <li><strong>Nhóm SC:</strong> Tìm theo nhóm sửa chữa</li>
                    <li><strong>Vị trí:</strong> Tìm theo vị trí thiết bị</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-database"></i> Tích Hợp Database</h3>
                <ul class="feature-list">
                    <li>SQL injection protection với escape</li>
                    <li>LIKE search với wildcard tự động</li>
                    <li>Pagination giữ nguyên kết quả tìm kiếm</li>
                    <li>Export Excel bao gồm filter tìm kiếm</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h3><i class="fas fa-magic"></i> UX Thông Minh</h3>
                <ul class="feature-list">
                    <li>Clear button để xóa tìm kiếm nhanh</li>
                    <li>Thông tin số kết quả tìm thấy</li>
                    <li>Kết hợp với filter nhóm thiết bị</li>
                    <li>URL parameters để bookmark tìm kiếm</li>
                </ul>
            </div>
        </div>

        <!-- Demo Section -->
        <div class="demo-section">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">
                <i class="fas fa-play-circle"></i> Demo Tìm Kiếm Trực Tiếp
            </h2>
            
            <div class="demo-search">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-search" style="color: #3498db;"></i>
                    <label style="font-weight: 600; color: #2c3e50;">Tìm kiếm:</label>
                </div>
                
                <input type="text" 
                       id="demo-search-input"
                       placeholder="Nhập mã thiết bị, số máy, số hồ sơ..." 
                       value="TB001">
                
                <select id="demo-search-type">
                    <option value="all">Tất cả</option>
                    <option value="mavt" selected>Mã thiết bị</option>
                    <option value="somay">Số máy</option>
                    <option value="hoso">Số hồ sơ</option>
                    <option value="model">Model</option>
                    <option value="nhomsc">Nhóm SC</option>
                    <option value="vitri">Vị trí</option>
                </select>
                
                <button class="demo-btn" onclick="performDemoSearch()">
                    <i class="fas fa-search"></i>
                    Tìm kiếm
                </button>
            </div>
            
            <div id="search-result" style="margin-top: 20px;"></div>
            
            <!-- Search Examples -->
            <h3 style="color: #2c3e50; margin: 25px 0 15px 0;">
                <i class="fas fa-lightbulb"></i> Ví Dụ Tìm Kiếm
            </h3>
            
            <div class="search-examples">
                <div class="example-card" onclick="setDemoSearch('CNC', 'mavt')">
                    <div class="example-title">Tìm thiết bị CNC</div>
                    <div class="example-query">Từ khóa: "CNC" trong Mã thiết bị</div>
                </div>
                
                <div class="example-card" onclick="setDemoSearch('001', 'somay')">
                    <div class="example-title">Tìm số máy 001</div>
                    <div class="example-query">Từ khóa: "001" trong Số máy</div>
                </div>
                
                <div class="example-card" onclick="setDemoSearch('BD', 'hoso')">
                    <div class="example-title">Hồ sơ bảo dưỡng</div>
                    <div class="example-query">Từ khóa: "BD" trong Số hồ sơ</div>
                </div>
                
                <div class="example-card" onclick="setDemoSearch('RDNGA', 'nhomsc')">
                    <div class="example-title">Nhóm RDNGA</div>
                    <div class="example-query">Từ khóa: "RDNGA" trong Nhóm SC</div>
                </div>
                
                <div class="example-card" onclick="setDemoSearch('Khu A', 'vitri')">
                    <div class="example-title">Vị trí Khu A</div>
                    <div class="example-query">Từ khóa: "Khu A" trong Vị trí</div>
                </div>
                
                <div class="example-card" onclick="setDemoSearch('Model-X', 'model')">
                    <div class="example-title">Model thiết bị</div>
                    <div class="example-query">Từ khóa: "Model-X" trong Model</div>
                </div>
            </div>
            
            <!-- How to Use -->
            <div class="how-to-use">
                <h4><i class="fas fa-question-circle"></i> Cách Sử Dụng</h4>
                <ol>
                    <li>Nhập <span class="highlight">từ khóa tìm kiếm</span> vào ô input</li>
                    <li>Chọn <span class="highlight">loại tìm kiếm</span> từ dropdown (mặc định: Tất cả)</li>
                    <li>Click <span class="highlight">nút Tìm kiếm</span> hoặc nhấn Enter</li>
                    <li>Xem <span class="highlight">kết quả</span> được highlight trong bảng</li>
                    <li>Sử dụng <span class="highlight">pagination</span> để xem thêm kết quả</li>
                    <li>Click <span class="highlight">nút Xóa</span> để clear tìm kiếm</li>
                </ol>
            </div>
        </div>

        <!-- Technical Details -->
        <div class="demo-section">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">
                <i class="fas fa-code"></i> Chi Tiết Kỹ Thuật
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <h4 style="color: #e74c3c;">🔒 Security</h4>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>mysqli_real_escape_string() protection</li>
                        <li>Input validation và sanitization</li>
                        <li>Prepared statement ready architecture</li>
                        <li>XSS protection với htmlspecialchars()</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <h4 style="color: #3498db;">⚡ Performance</h4>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>LIKE queries với index optimization</li>
                        <li>Pagination preserves search state</li>
                        <li>Minimal DOM manipulation</li>
                        <li>Responsive design với CSS Grid</li>
                    </ul>
                </div>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                    <h4 style="color: #27ae60;">🎨 UX Features</h4>
                    <ul style="margin: 10px 0 0 20px;">
                        <li>Real-time search result counter</li>
                        <li>Search type indicator</li>
                        <li>Clear search functionality</li>
                        <li>URL parameters for bookmarking</li>
                    </ul>
                </div>
            </div>
            
            <div style="background: #e8f4fd; border: 1px solid #bee5eb; border-radius: 10px; padding: 20px; margin-top: 20px;">
                <h4 style="color: #0c5460; margin-bottom: 15px;">
                    <i class="fas fa-database"></i> SQL Query Example
                </h4>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-family: 'Courier New', monospace; font-size: 0.9em; color: #495057;">
SELECT mavt, somay, model, cv, DATE_FORMAT(`ngayyc`,'%d-%m-%Y') as ngayyc,
       DATE_FORMAT(`ngayth`,'%d-%m-%Y') as ngayth, hoso, nhomsc,
       DATE_FORMAT(`ngaykt`,'%d-%m-%Y') as ngaykt, ttktafter, vitrimaybd, lo, gieng,
       DATEDIFF(ngaykt,ngayth) as ngaysc 
FROM hososcbd_iso 
WHERE bg='0' AND nhomsc LIKE '%CNC%' 
  AND (mavt LIKE '%TB001%' OR somay LIKE '%TB001%' OR 
       hoso LIKE '%TB001%' OR model LIKE '%TB001%' OR 
       nhomsc LIKE '%TB001%' OR vitrimaybd LIKE '%TB001%')
ORDER BY stt DESC 
LIMIT 0, 15
                </pre>
            </div>
        </div>

        <!-- Test Links -->
        <div class="test-links">
            <a href="tiendocongviec_professional.php" class="test-btn">
                <i class="fas fa-rocket"></i>
                Test Hệ Thống Professional
            </a>
            
            <a href="tiendocongviec_professional.php?search_query=CNC&search_type=mavt" class="test-btn">
                <i class="fas fa-search"></i>
                Test Tìm Kiếm "CNC"
            </a>
            
            <a href="tiendocongviec_professional.php?search_query=001&search_type=somay" class="test-btn">
                <i class="fas fa-hashtag"></i>
                Test Tìm Số Máy "001"
            </a>
        </div>
    </div>

    <script>
        function setDemoSearch(query, type) {
            document.getElementById('demo-search-input').value = query;
            document.getElementById('demo-search-type').value = type;
            performDemoSearch();
        }
        
        function performDemoSearch() {
            const query = document.getElementById('demo-search-input').value;
            const type = document.getElementById('demo-search-type').value;
            const resultDiv = document.getElementById('search-result');
            
            if (!query.trim()) {
                resultDiv.innerHTML = '<div style="background: #fdeaea; border: 1px solid #e74c3c; border-radius: 5px; padding: 15px; color: #721c24;"><strong>Lỗi:</strong> Vui lòng nhập từ khóa tìm kiếm!</div>';
                return;
            }
            
            const typeNames = {
                'all': 'tất cả',
                'mavt': 'mã thiết bị',
                'somay': 'số máy',
                'hoso': 'số hồ sơ',
                'model': 'model',
                'nhomsc': 'nhóm SC',
                'vitri': 'vị trí'
            };
            
            resultDiv.innerHTML = `
                <div style="background: #d5f4e6; border: 1px solid #27ae60; border-radius: 10px; padding: 20px;">
                    <h4 style="color: #155724; margin-bottom: 15px;">
                        <i class="fas fa-check-circle"></i> Kết Quả Demo
                    </h4>
                    <p style="margin: 10px 0; color: #155724;">
                        <strong>Từ khóa:</strong> "${query}"<br>
                        <strong>Tìm trong:</strong> ${typeNames[type]}<br>
                        <strong>URL sẽ được tạo:</strong>
                    </p>
                    <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 0.9em; margin: 10px 0; word-break: break-all;">
                        tiendocongviec_professional.php?search_query=${encodeURIComponent(query)}&search_type=${type}
                    </div>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="tiendocongviec_professional.php?search_query=${encodeURIComponent(query)}&search_type=${type}" 
                           style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-external-link-alt"></i>
                            Test Thực Tế
                        </a>
                    </div>
                </div>
            `;
        }
        
        // Auto demo on page load
        document.addEventListener('DOMContentLoaded', function() {
            performDemoSearch();
        });
        
        // Enter key support
        document.getElementById('demo-search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performDemoSearch();
            }
        });
    </script>
</body>
</html>