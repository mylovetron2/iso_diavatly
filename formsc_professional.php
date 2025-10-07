<?php
/**
 * ========================================================================
 * HỆ THỐNG QUẢN LÝ SỬA CHỮA BẢO DƯỠNG - PHIÊN BẢN HIỆN ĐẠI
 * ========================================================================
 * File: formsc_professional.php
 * Description: Hệ thống quản lý sửa chữa bảo dưỡng với giao diện chuyên nghiệp
 * Version: 2.0
 * Author: Modernized System
 * Date: 2025
 */

// Cấu hình hệ thống
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

// Bắt đầu session và output buffering
session_start();
ob_start();

// Include các file cần thiết
require_once("select_data.php");
require_once("myfunctions.php");

// Class chính cho hệ thống
class MaintenanceSystem {
    private $link;
    private $username;
    private $userRole;
    private $config;
    
    public function __construct() {
        $this->initDatabase();
        $this->initUser();
        $this->initConfig();
    }
    
    /**
     * Khởi tạo kết nối database
     */
    private function initDatabase() {
        global $link;
        if (!$link) {
            throw new Exception("Không thể kết nối database");
        }
        $this->link = $link;
    }
    
    /**
     * Khởi tạo thông tin user
     */
    private function initUser() {
        $this->username = isset($_POST['username']) ? $this->sanitizeInput($_POST['username']) : 
                         (isset($_GET['username']) ? $this->sanitizeInput($_GET['username']) : '');
        
        if ($this->username) {
            $this->getUserRole();
        }
    }
    
    /**
     * Lấy quyền của user
     */
    private function getUserRole() {
        $stmt = mysqli_prepare($this->link, "SELECT phanquyen, nhom FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $this->userRole = $row['phanquyen'];
        }
        mysqli_stmt_close($stmt);
    }
    
    /**
     * Khởi tạo cấu hình
     */
    private function initConfig() {
        $this->config = [
            'app_name' => 'HỆ THỐNG QUẢN LÝ SỬA CHỮA BẢO DƯỠNG',
            'version' => '2.0',
            'items_per_page' => 15,
            'date_format' => 'd/m/Y',
            'datetime_format' => 'd/m/Y H:i:s'
        ];
    }
    
    /**
     * Làm sạch input
     */
    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render header HTML
     */
    public function renderHeader() {
        echo '<!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $this->config['app_name'] . '</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>' . $this->getCSS() . '</style>
        </head>
        <body>';
    }
    
    /**
     * CSS Styles hiện đại
     */
    private function getCSS() {
        return '
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --border-radius: 8px;
            --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            color: var(--primary-color);
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 1.1em;
        }
        
        .user-info {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .function-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .function-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--secondary-color);
        }
        
        .function-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .function-card i {
            font-size: 2.5em;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .function-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--success-color);
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .btn-warning {
            background: var(--warning-color);
        }
        
        .btn-warning:hover {
            background: #d68910;
        }
        
        .btn-danger {
            background: var(--danger-color);
        }
        
        .btn-danger:hover {
            background: #cd4035;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: var(--border-radius);
            font-weight: bold;
        }
        
        .alert-success {
            background: #d5f4e6;
            color: var(--success-color);
            border: 1px solid #b8e6c8;
        }
        
        .alert-error {
            background: #fdeaea;
            color: var(--danger-color);
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fef9e7;
            color: var(--warning-color);
            border: 1px solid #f7dc6f;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .table th {
            background: var(--primary-color);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .breadcrumb {
            background: white;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
        }
        
        .breadcrumb a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
        }
        
        .loading i {
            font-size: 3em;
            color: var(--secondary-color);
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        
        .equipment-item {
            animation: slideIn 0.3s ease-in-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .equipment-item:hover {
            transform: translateY(-2px);
            transition: transform 0.3s ease;
        }
        
        .equipment-counter {
            background: var(--secondary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .function-grid {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                flex-direction: column;
                gap: 10px;
            }
        }';
    }
    
    /**
     * Render navigation breadcrumb
     */
    public function renderBreadcrumb($items) {
        echo '<div class="breadcrumb">';
        foreach ($items as $index => $item) {
            if ($index > 0) echo ' <i class="fas fa-chevron-right"></i> ';
            
            if (isset($item['url'])) {
                echo '<a href="' . $item['url'] . '">' . $item['text'] . '</a>';
            } else {
                echo '<strong>' . $item['text'] . '</strong>';
            }
        }
        echo '</div>';
    }
    
    /**
     * Render thông tin user
     */
    public function renderUserInfo() {
        if ($this->username) {
            echo '<div class="user-info">
                <div>
                    <i class="fas fa-user"></i> 
                    Xin chào, <strong>' . $this->username . '</strong>
                    ' . ($this->userRole ? '(Quyền: ' . $this->userRole . ')' : '') . '
                </div>
                <div>
                    <i class="fas fa-clock"></i> ' . date('d/m/Y H:i:s') . '
                </div>
            </div>';
        }
    }
    
    /**
     * Trang chủ - Menu chính
     */
    public function renderMainMenu() {
        $this->renderHeader();
        
        echo '<div class="container">';
        
        // Header
        echo '<div class="header">
            <h1><i class="fas fa-tools"></i> ' . $this->config['app_name'] . '</h1>
            <div class="subtitle">Phiên bản ' . $this->config['version'] . ' - Giao diện chuyên nghiệp</div>
        </div>';
        
        $this->renderUserInfo();
        
        // Breadcrumb
        $this->renderBreadcrumb([
            ['text' => 'Trang chủ']
        ]);
        
        // Thống kê nhanh
        $this->renderQuickStats();
        
        // Menu chức năng chính
        echo '<div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> CHỨC NĂNG CHÍNH
            </div>
            <div class="card-body">
                <div class="function-grid">';
        
        $functions = [
            [
                'name' => 'Nhập phiếu yêu cầu dịch vụ',
                'description' => 'Tạo phiếu yêu cầu sửa chữa, bảo dưỡng thiết bị',
                'icon' => 'fas fa-file-plus',
                'action' => 'create_request',
                'color' => 'success'
            ],
            [
                'name' => 'Nhập phiếu thực hiện công việc',
                'description' => 'Cập nhật tiến độ và kết quả thực hiện',
                'icon' => 'fas fa-tasks',
                'action' => 'work_progress',
                'color' => 'warning'
            ],
            [
                'name' => 'Xuất biên bản bàn giao',
                'description' => 'Tạo biên bản bàn giao thiết bị sau sửa chữa',
                'icon' => 'fas fa-handshake',
                'action' => 'handover_report',
                'color' => 'primary'
            ],
            [
                'name' => 'Quản lý thiết bị',
                'description' => 'Quản lý danh mục thiết bị và thông tin',
                'icon' => 'fas fa-cogs',
                'action' => 'manage_equipment',
                'color' => 'secondary'
            ],
            [
                'name' => 'Thống kê báo cáo',
                'description' => 'Xem báo cáo thống kê và phân tích',
                'icon' => 'fas fa-chart-bar',
                'action' => 'reports',
                'color' => 'info'
            ],
            [
                'name' => 'Cài đặt hệ thống',
                'description' => 'Cấu hình và quản lý hệ thống',
                'icon' => 'fas fa-cog',
                'action' => 'settings',
                'color' => 'dark'
            ]
        ];
        
        foreach ($functions as $func) {
            echo '<div class="function-card">
                <i class="' . $func['icon'] . '"></i>
                <h3>' . $func['name'] . '</h3>
                <p style="color: #666; margin-bottom: 20px;">' . $func['description'] . '</p>
                <form method="post" style="margin: 0;">
                    <input type="hidden" name="action" value="' . $func['action'] . '">
                    <input type="hidden" name="username" value="' . $this->username . '">
                    <button type="submit" class="btn">
                        <i class="fas fa-arrow-right"></i> Chọn
                    </button>
                </form>
            </div>';
        }
        
        echo '</div>
            </div>
        </div>';
        
        echo '</div>';
        echo '</body></html>';
    }
    
    /**
     * Render thống kê nhanh
     */
    private function renderQuickStats() {
        $stats = $this->getQuickStats();
        
        echo '<div class="card">
            <div class="card-header">
                <i class="fas fa-tachometer-alt"></i> THỐNG KÊ NHANH
            </div>
            <div class="card-body">
                <div class="stats-grid">';
        
        foreach ($stats as $stat) {
            echo '<div class="stat-card">
                <div class="stat-number">' . $stat['number'] . '</div>
                <div class="stat-label">' . $stat['label'] . '</div>
            </div>';
        }
        
        echo '</div>
            </div>
        </div>';
    }
    
    /**
     * Lấy dữ liệu thống kê nhanh
     */
    private function getQuickStats() {
        $stats = [];
        
        // Tổng số phiếu
        $result = mysqli_query($this->link, "SELECT COUNT(*) as total FROM hososcbd_iso");
        $row = mysqli_fetch_assoc($result);
        $total_tickets = $row ? $row['total'] : 0;
        $stats[] = ['number' => $total_tickets, 'label' => 'Tổng số phiếu'];
        
        // Phiếu đang xử lý
        $result = mysqli_query($this->link, "SELECT COUNT(*) as total FROM hososcbd_iso WHERE ngaykt = '0000-00-00'");
        $row = mysqli_fetch_assoc($result);
        $pending_tickets = $row ? $row['total'] : 0;
        $stats[] = ['number' => $pending_tickets, 'label' => 'Đang xử lý'];
        
        // Phiếu hoàn thành
        $result = mysqli_query($this->link, "SELECT COUNT(*) as total FROM hososcbd_iso WHERE ngaykt != '0000-00-00'");
        $row = mysqli_fetch_assoc($result);
        $completed_tickets = $row ? $row['total'] : 0;
        $stats[] = ['number' => $completed_tickets, 'label' => 'Đã hoàn thành'];
        
        // Tổng thiết bị
        $result = mysqli_query($this->link, "SELECT COUNT(*) as total FROM thietbi_iso");
        $row = mysqli_fetch_assoc($result);
        $total_equipment = $row ? $row['total'] : 0;
        $stats[] = ['number' => $total_equipment, 'label' => 'Tổng thiết bị'];
        
        return $stats;
    }
    
    /**
     * Xử lý form tạo phiếu yêu cầu
     */
    public function renderCreateRequestForm() {
        $this->renderHeader();
        
        echo '<div class="container">';
        
        // Header
        echo '<div class="header">
            <h1><i class="fas fa-file-plus"></i> TẠO PHIẾU YÊU CẦU DỊCH VỤ</h1>
        </div>';
        
        $this->renderUserInfo();
        
        // Breadcrumb
        $this->renderBreadcrumb([
            ['text' => 'Trang chủ', 'url' => '?'],
            ['text' => 'Tạo phiếu yêu cầu']
        ]);
        
        // Form tạo phiếu
        echo '<div class="card">
            <div class="card-header">
                <i class="fas fa-edit"></i> THÔNG TIN PHIẾU YÊU CẦU
            </div>
            <div class="card-body">
                <form method="post" id="requestForm">
                    <input type="hidden" name="action" value="save_request">
                    <input type="hidden" name="username" value="' . $this->username . '">
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hashtag"></i> Số phiếu
                        </label>
                        <input type="text" name="so_phieu" class="form-control" value="' . $this->generateTicketNumber() . '" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i> Ngày yêu cầu
                        </label>
                        <input type="date" name="ngay_yc" class="form-control" value="' . date('Y-m-d') . '" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building"></i> Đơn vị yêu cầu
                        </label>
                        <select name="don_vi" class="form-control" required>
                            <option value="">-- Chọn đơn vị --</option>';
                            
        $this->renderDonViOptions();
        
        echo '      </select>
                    </div>
                    
                    <!-- DANH SÁCH THIẾT BỊ -->
                    <div class="card" style="margin: 20px 0; border: 2px solid #3498db;">
                        <div class="card-header" style="background: #3498db; color: white;">
                            <i class="fas fa-list"></i> DANH SÁCH THIẾT BỊ CẦN SỬA CHỮA
                            <button type="button" class="btn btn-success" onclick="addEquipment()" style="float: right; padding: 5px 15px; font-size: 0.9em;">
                                <i class="fas fa-plus"></i> Thêm thiết bị
                            </button>
                        </div>
                        <div class="card-body" id="equipmentList">
                            <div class="equipment-item" data-index="1">
                                <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9;">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                        <h4 style="color: #2c3e50; margin: 0;">
                                            <i class="fas fa-cog"></i> Thiết bị #1
                                        </h4>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-cog"></i> Chọn thiết bị *
                                            </label>
                                            <select name="ma_thiet_bi[]" class="form-control equipment-select" onchange="loadEquipmentInfo(this, 1)" required>
                                                <option value="">-- Chọn thiết bị --</option>';
        
        $this->renderThietBiOptions();
        
        echo '                              </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-tag"></i> Tên thiết bị
                                            </label>
                                            <input type="text" name="thiet_bi[]" class="form-control equipment-name" placeholder="Tên thiết bị (tự động)" readonly style="background: #f8f9fa;">
                                        </div>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-hashtag"></i> Số máy
                                            </label>
                                            <select name="so_may[]" class="form-control somay-select" disabled>
                                                <option value="">-- Chọn thiết bị trước --</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-industry"></i> Model
                                            </label>
                                            <input type="text" name="model[]" class="form-control model-input" placeholder="Model (tự động)" readonly style="background: #f8f9fa;">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Hiện tượng hỏng hóc *</label>
                                        <textarea name="hien_tuong[]" class="form-control" rows="3" placeholder="Mô tả chi tiết hiện tượng hỏng hóc của thiết bị này..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Vị trí
                        </label>
                        <select name="vi_tri" class="form-control">
                            <option value="">-- Chọn vị trí --</option>';
                            
        $this->renderViTriOptions();
        
        echo '      </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Người yêu cầu
                        </label>
                        <input type="text" name="nguoi_yc" class="form-control" value="' . $this->username . '" required>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu phiếu
                        </button>
                        <a href="?" class="btn" style="margin-left: 10px;">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>';
        
        // Thêm dữ liệu thiết bị cho JavaScript
        echo '<script>
        // Dữ liệu thiết bị từ database
        const equipmentDatabase = ' . $this->getEquipmentDataForJS() . ';
        let equipmentCount = 1;
        
        function addEquipment() {
            equipmentCount++;
            const equipmentList = document.getElementById("equipmentList");
            
            const newEquipment = document.createElement("div");
            newEquipment.className = "equipment-item";
            newEquipment.setAttribute("data-index", equipmentCount);
            
            newEquipment.innerHTML = `
                <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="color: #2c3e50; margin: 0;">
                            <i class="fas fa-cog"></i> Thiết bị #${equipmentCount}
                        </h4>
                        <button type="button" class="btn btn-danger" onclick="removeEquipment(this)" style="padding: 5px 10px; font-size: 0.8em;">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-cog"></i> Chọn thiết bị *
                            </label>
                            <select name="ma_thiet_bi[]" class="form-control equipment-select" onchange="loadEquipmentInfo(this, ${equipmentCount})" required>
                                <option value="">-- Chọn thiết bị --</option>
                                ${getEquipmentOptions()}
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag"></i> Tên thiết bị
                            </label>
                            <input type="text" name="thiet_bi[]" class="form-control equipment-name" placeholder="Tên thiết bị (tự động)" readonly style="background: #f8f9fa;">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-hashtag"></i> Số máy
                            </label>
                            <select name="so_may[]" class="form-control somay-select" disabled>
                                <option value="">-- Chọn thiết bị trước --</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-industry"></i> Model
                            </label>
                            <input type="text" name="model[]" class="form-control model-input" placeholder="Model (tự động)" readonly style="background: #f8f9fa;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hiện tượng hỏng hóc *</label>
                        <textarea name="hien_tuong[]" class="form-control" rows="3" placeholder="Mô tả chi tiết hiện tượng hỏng hóc của thiết bị này..." required></textarea>
                    </div>
                </div>
            `;
            
            equipmentList.appendChild(newEquipment);
            newEquipment.scrollIntoView({ behavior: "smooth" });
            updateRemoveButtons();
        }
        
        function removeEquipment(button) {
            const equipmentItem = button.closest(".equipment-item");
            const totalEquipment = document.querySelectorAll(".equipment-item").length;
            
            if (totalEquipment > 1) {
                equipmentItem.remove();
                updateEquipmentNumbers();
                updateRemoveButtons();
            } else {
                alert("Phải có ít nhất một thiết bị trong phiếu!");
            }
        }
        
        function updateEquipmentNumbers() {
            const equipmentItems = document.querySelectorAll(".equipment-item");
            equipmentItems.forEach((item, index) => {
                const header = item.querySelector("h4");
                header.innerHTML = `<i class="fas fa-cog"></i> Thiết bị #${index + 1}`;
                item.setAttribute("data-index", index + 1);
            });
            equipmentCount = equipmentItems.length;
        }
        
        function updateRemoveButtons() {
            const equipmentItems = document.querySelectorAll(".equipment-item");
            
            equipmentItems.forEach(item => {
                const headerDiv = item.querySelector("div:first-child");
                const existingBtn = headerDiv.querySelector(".btn-danger");
                
                if (equipmentItems.length > 1) {
                    if (!existingBtn) {
                        headerDiv.style.justifyContent = "space-between";
                        const removeBtn = document.createElement("button");
                        removeBtn.type = "button";
                        removeBtn.className = "btn btn-danger";
                        removeBtn.style.cssText = "padding: 5px 10px; font-size: 0.8em;";
                        removeBtn.innerHTML = `<i class="fas fa-trash"></i> Xóa`;
                        removeBtn.onclick = function() { removeEquipment(this); };
                        headerDiv.appendChild(removeBtn);
                    }
                } else {
                    if (existingBtn) {
                        existingBtn.remove();
                        headerDiv.style.justifyContent = "flex-start";
                    }
                }
            });
        }
        
        // Function để tạo options cho combobox thiết bị
        function getEquipmentOptions() {
            let options = "";';
            
        // Tạo options từ PHP
        $result = mysqli_query($this->link, "SELECT DISTINCT mavt, tenvt, nhomthietbi FROM thietbi_iso ORDER BY tenvt");
        while ($row = mysqli_fetch_assoc($result)) {
            $display = $row['mavt'] . ' - ' . $row['tenvt'];
            if (!empty($row['nhomthietbi'])) {
                $display .= ' (' . $row['nhomthietbi'] . ')';
            }
            echo "options += '<option value=\"" . $row['mavt'] . "\">" . addslashes($display) . "</option>';";
        }
        
        echo 'return options;
        }
        
        // Function xử lý khi chọn thiết bị
        function loadEquipmentInfo(selectElement, equipmentIndex) {
            const mavt = selectElement.value;
            const equipmentItem = selectElement.closest(".equipment-item");
            
            // Tìm các elements liên quan
            const nameInput = equipmentItem.querySelector(".equipment-name");
            const soMaySelect = equipmentItem.querySelector(".somay-select");
            const modelInput = equipmentItem.querySelector(".model-input");
            
            if (!mavt) {
                // Reset tất cả khi không chọn thiết bị
                nameInput.value = "";
                soMaySelect.innerHTML = "<option value=\"\">-- Chọn thiết bị trước --</option>";
                soMaySelect.disabled = true;
                modelInput.value = "";
                return;
            }
            
            // Tìm thông tin thiết bị trong database
            if (equipmentDatabase[mavt]) {
                const equipmentData = equipmentDatabase[mavt];
                
                // Set tên thiết bị (lấy từ item đầu tiên)
                if (equipmentData.length > 0) {
                    nameInput.value = equipmentData[0].tenvt;
                }
                
                // Load số máy options
                soMaySelect.innerHTML = "<option value=\"\">-- Chọn số máy --</option>";
                equipmentData.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.somay;
                    option.text = item.somay + (item.model ? " (" + item.model + ")" : "");
                    option.dataset.model = item.model || "";
                    soMaySelect.appendChild(option);
                });
                
                soMaySelect.disabled = false;
                
                // Xử lý khi chọn số máy
                soMaySelect.onchange = function() {
                    const selectedOption = this.options[this.selectedIndex];
                    modelInput.value = selectedOption.dataset.model || "";
                };
                
            } else {
                nameInput.value = "";
                soMaySelect.innerHTML = "<option value=\"\">-- Không có dữ liệu --</option>";
                soMaySelect.disabled = true;
                modelInput.value = "";
            }
        }
        
        // Initialize on page load
        document.addEventListener("DOMContentLoaded", function() {
            updateRemoveButtons();
        });
        </script>';
        
        echo '</div>';
        echo '</body></html>';
    }
    
    /**
     * Tạo số phiếu tự động
     */
    private function generateTicketNumber() {
        $result = mysqli_query($this->link, "SELECT MAX(CAST(phieu AS UNSIGNED)) as max_phieu FROM hososcbd_iso");
        $max_phieu = 0;
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $max_phieu = $row['max_phieu'] ? $row['max_phieu'] : 0;
        }
        
        $max_phieu++;
        
        if ($max_phieu <= 9) return "000$max_phieu";
        else if ($max_phieu <= 99) return "00$max_phieu";
        else if ($max_phieu <= 999) return "0$max_phieu";
        else return "$max_phieu";
    }
    
    /**
     * Render options cho đơn vị
     */
    private function renderDonViOptions() {
        $result = mysqli_query($this->link, "SELECT DISTINCT madv, tendv FROM donvi_iso ORDER BY tendv");
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['madv'] . '">' . $row['madv'] . ' - ' . $row['tendv'] . '</option>';
        }
    }
    
    /**
     * Render options cho vị trí
     */
    private function renderViTriOptions() {
        $result = mysqli_query($this->link, "SELECT DISTINCT mavitri, tenvitri FROM vitri_iso ORDER BY tenvitri");
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['mavitri'] . '">' . $row['mavitri'] . ' - ' . $row['tenvitri'] . '</option>';
        }
    }
    
    /**
     * Render options cho thiết bị
     */
    private function renderThietBiOptions() {
        $result = mysqli_query($this->link, "SELECT DISTINCT mavt, tenvt, nhomthietbi FROM thietbi_iso ORDER BY tenvt");
        while ($row = mysqli_fetch_assoc($result)) {
            $display = $row['mavt'] . ' - ' . $row['tenvt'];
            if (!empty($row['nhomthietbi'])) {
                $display .= ' (' . $row['nhomthietbi'] . ')';
            }
            echo '<option value="' . $row['mavt'] . '">' . $display . '</option>';
        }
    }
    
    /**
     * Lấy dữ liệu thiết bị cho JavaScript (AJAX)
     */
    private function getEquipmentDataForJS() {
        $result = mysqli_query($this->link, "SELECT mavt, somay, model, tenvt FROM thietbi_iso ORDER BY mavt, somay");
        $equipment_data = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $equipment_data[$row['mavt']][] = [
                'somay' => $row['somay'],
                'model' => $row['model'],
                'tenvt' => $row['tenvt']
            ];
        }
        
        return json_encode($equipment_data);
    }
    
    /**
     * Lưu phiếu yêu cầu với nhiều thiết bị
     */
    public function saveRequest() {
        // Lấy thông tin chung của phiếu
        $so_phieu = $_POST['so_phieu'];
        $ngay_yc = $_POST['ngay_yc'];
        $don_vi = $_POST['don_vi'];
        $vi_tri = isset($_POST['vi_tri']) ? $_POST['vi_tri'] : '';
        $nguoi_yc = $_POST['nguoi_yc'];
        $ngay_tao = date('Y-m-d H:i:s');
        
        // Lấy danh sách thiết bị
        $thiet_bi_list = $_POST['thiet_bi'];
        $ma_thiet_bi_list = $_POST['ma_thiet_bi'];
        $so_may_list = $_POST['so_may'];
        $model_list = $_POST['model'];
        $hien_tuong_list = $_POST['hien_tuong'];
        
        $success_count = 0;
        $error_messages = [];
        
        // Lưu từng thiết bị
        for ($i = 0; $i < count($thiet_bi_list); $i++) {
            if (!empty($thiet_bi_list[$i]) && !empty($hien_tuong_list[$i])) {
                
                $sql = "INSERT INTO hososcbd_iso (
                    phieu, ngayyc, madv, mavt, somay, model, tenvt, 
                    vitrimaybd, honghoc, nguoiyeucau, ngaytao, nguoitao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->link, $sql);
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssssssssssss", 
                        $so_phieu,
                        $ngay_yc,
                        $don_vi,
                        $ma_thiet_bi_list[$i],
                        $so_may_list[$i],
                        $model_list[$i],
                        $thiet_bi_list[$i],
                        $vi_tri,
                        $hien_tuong_list[$i],
                        $nguoi_yc,
                        $ngay_tao,
                        $this->username
                    );
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success_count++;
                    } else {
                        $error_messages[] = "Lỗi lưu thiết bị " . ($i+1) . ": " . mysqli_error($this->link);
                    }
                    
                    mysqli_stmt_close($stmt);
                } else {
                    $error_messages[] = "Lỗi chuẩn bị câu lệnh cho thiết bị " . ($i+1);
                }
            }
        }
        
        // Hiển thị kết quả
        if ($success_count > 0) {
            $this->showMessage("✅ Lưu thành công phiếu $so_phieu với $success_count thiết bị!", 'success');
            
            if (count($error_messages) > 0) {
                $this->showMessage("⚠️ Một số lỗi: " . implode(", ", $error_messages), 'warning');
            }
        } else {
            $this->showMessage("❌ Không thể lưu phiếu: " . implode(", ", $error_messages), 'error');
        }
        
        $this->renderMainMenu();
    }
    
    /**
     * Hiển thị thông báo
     */
    private function showMessage($message, $type = 'info') {
        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
    }
    
    /**
     * Router chính
     */
    public function route() {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        switch ($action) {
            case 'create_request':
                $this->renderCreateRequestForm();
                break;
                
            case 'save_request':
                $this->saveRequest();
                break;
                
            case 'work_progress':
                $this->renderWorkProgressForm();
                break;
                
            case 'handover_report':
                $this->renderHandoverReport();
                break;
                
            case 'manage_equipment':
                $this->renderEquipmentManagement();
                break;
                
            case 'reports':
                $this->renderReports();
                break;
                
            case 'settings':
                $this->renderSettings();
                break;
                
            default:
                $this->renderMainMenu();
                break;
        }
    }
    
    /**
     * Form cập nhật tiến độ công việc
     */
    public function renderWorkProgressForm() {
        // TODO: Implement work progress form
        $this->showMessage('Chức năng đang phát triển', 'warning');
        $this->renderMainMenu();
    }
    
    /**
     * Form biên bản bàn giao
     */
    public function renderHandoverReport() {
        // TODO: Implement handover report
        $this->showMessage('Chức năng đang phát triển', 'warning');
        $this->renderMainMenu();
    }
    
    /**
     * Quản lý thiết bị
     */
    public function renderEquipmentManagement() {
        // TODO: Implement equipment management
        $this->showMessage('Chức năng đang phát triển', 'warning');
        $this->renderMainMenu();
    }
    
    /**
     * Báo cáo thống kê
     */
    public function renderReports() {
        // TODO: Implement reports
        $this->showMessage('Chức năng đang phát triển', 'warning');
        $this->renderMainMenu();
    }
    
    /**
     * Cài đặt hệ thống
     */
    public function renderSettings() {
        // TODO: Implement settings
        $this->showMessage('Chức năng đang phát triển', 'warning');
        $this->renderMainMenu();
    }
    
    /**
     * Destructor - đóng kết nối database
     */
    public function __destruct() {
        if ($this->link) {
            mysqli_close($this->link);
        }
    }
}

// ========================================================================
// MAIN EXECUTION
// ========================================================================

try {
    $system = new MaintenanceSystem();
    $system->route();
    
} catch (Exception $e) {
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Lỗi hệ thống</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 50px; background: #f8f9fa; }
            .error-container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .error-icon { color: #e74c3c; font-size: 3em; text-align: center; margin-bottom: 20px; }
            .error-title { color: #e74c3c; text-align: center; margin-bottom: 20px; }
            .error-message { background: #fdeaea; padding: 15px; border-radius: 5px; color: #721c24; }
            .btn { background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h2 class="error-title">Lỗi hệ thống</h2>
            <div class="error-message">
                <strong>Chi tiết lỗi:</strong><br>
                ' . $e->getMessage() . '
            </div>
            <a href="index.php" class="btn">🏠 Về trang chủ</a>
        </div>
    </body>
    </html>';
}
?>