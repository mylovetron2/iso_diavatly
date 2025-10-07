<?php
/**
 * Trang chủ Export Analysis - TienDoConvViec Professional
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Phân Tích Logic - TienDoConvViec Professional</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 1.2em;
            line-height: 1.6;
        }
        
        .description {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            text-align: left;
        }
        
        .description h3 {
            color: #2980b9;
            margin-bottom: 20px;
            font-size: 1.3em;
        }
        
        .description ul {
            list-style: none;
            padding: 0;
        }
        
        .description li {
            margin: 10px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .description li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
        }
        
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .action-btn {
            padding: 20px 30px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(45deg, #27ae60, #219a52);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
        }
        
        .file-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .file-info h4 {
            color: #0c5460;
            margin-bottom: 15px;
        }
        
        .file-info table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        
        .file-info td {
            padding: 8px 12px;
            border-bottom: 1px solid #bee5eb;
        }
        
        .file-info td:first-child {
            font-weight: 600;
            color: #0c5460;
            width: 30%;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.85em;
        }
        
        .preview-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
        }
        
        .preview-note h5 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .preview-note p {
            color: #856404;
            margin: 0;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .card {
                padding: 20px;
                margin: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1><i class="fas fa-file-word"></i> Export Phân Tích Logic</h1>
                <div class="subtitle">
                    Tạo tài liệu Word chuyên nghiệp cho phân tích hệ thống<br>
                    <strong>TienDoConvViec Professional</strong>
                </div>
            </div>
            
            <!-- File Information -->
            <div class="file-info">
                <h4><i class="fas fa-info-circle"></i> Thông Tin Tài Liệu</h4>
                <table>
                    <tr>
                        <td>Tên file:</td>
                        <td><strong>Phan_Tich_Logic_TienDoConvViec_Professional_<?php echo date('Y_m_d'); ?>.doc</strong></td>
                    </tr>
                    <tr>
                        <td>Loại tài liệu:</td>
                        <td>Microsoft Word Document (.doc)</td>
                    </tr>
                    <tr>
                        <td>Ngày tạo:</td>
                        <td><?php echo date('d/m/Y H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>Kích thước ước tính:</td>
                        <td>~500KB - 1MB</td>
                    </tr>
                    <tr>
                        <td>Số trang:</td>
                        <td>15-20 trang A4</td>
                    </tr>
                </table>
            </div>
            
            <!-- Statistics -->
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Phần chính</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">Methods phân tích</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5</div>
                    <div class="stat-label">Trạng thái logic</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Database tables</div>
                </div>
            </div>
            
            <!-- Content Description -->
            <div class="description">
                <h3><i class="fas fa-list-ul"></i> Nội Dung Tài Liệu</h3>
                <ul>
                    <li><strong>Tổng quan hệ thống</strong> - Kiến trúc và đặc điểm nổi bật</li>
                    <li><strong>Phân tích OOP</strong> - Class WorkProgressManager chi tiết</li>
                    <li><strong>Input Parameters</strong> - Xử lý tham số đầu vào</li>
                    <li><strong>Database Logic</strong> - Query optimization và structure</li>
                    <li><strong>Methods Analysis</strong> - Phân tích từng method quan trọng</li>
                    <li><strong>Status Logic</strong> - Logic trạng thái và màu sắc</li>
                    <li><strong>Responsive Design</strong> - CSS Grid và UX enhancements</li>
                    <li><strong>Performance</strong> - Tối ưu hóa hiệu suất</li>
                    <li><strong>Security</strong> - Bảo mật và error handling</li>
                    <li><strong>Flow Diagram</strong> - Sơ đồ luồng hệ thống</li>
                    <li><strong>Code Examples</strong> - Ví dụ code với syntax highlighting</li>
                    <li><strong>Conclusions</strong> - Kết luận và future enhancements</li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="actions">
                <a href="export_analysis_to_word.php" class="action-btn btn-primary">
                    <i class="fas fa-download"></i>
                    Download Word Document
                </a>
                
                <a href="analysis_tiendocongviec_professional.php" class="action-btn btn-info" target="_blank">
                    <i class="fas fa-eye"></i>
                    Preview Online
                </a>
                
                <a href="tiendocongviec_professional.php" class="action-btn btn-success" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Xem Hệ Thống
                </a>
                
                <a href="tiendocongviec.php" class="action-btn btn-warning" target="_blank">
                    <i class="fas fa-code"></i>
                    File Gốc
                </a>
            </div>
            
            <!-- Preview Note -->
            <div class="preview-note">
                <h5><i class="fas fa-lightbulb"></i> Lưu Ý</h5>
                <p>
                    <strong>Word Document</strong> được tạo sẽ có format chuyên nghiệp với:
                    Table of Contents, Headers/Footers, Syntax highlighting, 
                    Tables, Diagrams, và Professional styling phù hợp cho báo cáo kỹ thuật.
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Add download tracking
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href.includes('export_analysis_to_word.php')) {
                    // Show download indicator
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo file...';
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    }, 1000);
                }
            });
        });
        
        // Add hover effects
        document.querySelectorAll('.stat-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>