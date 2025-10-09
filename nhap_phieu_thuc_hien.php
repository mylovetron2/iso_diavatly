<?php
// File: nhap_phieu_thuc_hien.php
// Chức năng: Nhập phiếu thực hiện công việc (tách từ formsc.php)
ob_start();
include ("select_data.php");
$link = isset($link) ? $link : null;
$search = isset($_POST['maql']) ? $_POST['maql'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : 'phieusuachua';
$edithoso = isset($_POST['hosomay']) ? $_POST['hosomay'] : '';
$sohs = isset($_POST['sohs']) ? $_POST['sohs'] : '';
$nhomsc = isset($_POST['nhomsc']) ? $_POST['nhomsc'] : '';
$ngayth = isset($_POST['ngayth']) ? $_POST['ngayth'] : '';
$ngaykt = isset($_POST['ngaykt']) ? $_POST['ngaykt'] : '';
$ttktbefore = isset($_POST['ttktbefore']) ? $_POST['ttktbefore'] : '';
$honghoc = isset($_POST['honghoc']) ? $_POST['honghoc'] : '';
$khacphuc = isset($_POST['khacphuc']) ? $_POST['khacphuc'] : '';
$ttktafter = isset($_POST['ttktafter']) ? $_POST['ttktafter'] : '';
$ghichufinal = isset($_POST['ghichufinal']) ? $_POST['ghichufinal'] : '';

// Xử lý lưu phiếu thực hiện công việc
if (isset($_POST['submit']) && $_POST['submit'] == 'luuphieuthuchien') {
    // TODO: Xử lý lưu dữ liệu vào CSDL
    // ...
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nhập phiếu thực hiện công việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
    <div class="form-section">
        <h2 class="mb-4 text-center text-primary">PHIẾU THỰC HIỆN CÔNG VIỆC</h2>
        <form method="post" action="nhap_phieu_thuc_hien.php" autocomplete="off">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Mã quản lý</label>
                    <input type="text" name="maql" class="form-control" value="<?php echo htmlspecialchars($search); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Chọn thiết bị</label>
                    <select name="hosomay" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Chọn thiết bị --</option>
                        <?php
                        if (!empty($search)) {
                            $tenthietbisql1 = mysqli_query($link, "SELECT DISTINCT mavt,somay,hoso,model FROM hososcbd_iso WHERE maql='".mysqli_real_escape_string($link, $search)."'");
                            while($row = mysqli_fetch_array($tenthietbisql1)) {
                                $mavt = $row['mavt'];
                                $somay = $row['somay'];
                                $model = $row['model'];
                                $hosom = $row['hoso'];
                                $mamay = ($model=="") ? $mavt : "$mavt-$model";
                                $selected = ($edithoso == $hosom) ? 'selected' : '';
                                echo "<option value='$hosom' $selected>$mamay ($somay)</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nhóm sửa chữa</label>
                    <select name="nhomsc" class="form-select">
                        <option value=""></option>
                        <option value="RDNGA" <?php if($nhomsc=="RDNGA") echo 'selected'; ?>>Nhóm RDNGA</option>
                        <option value="CNC" <?php if($nhomsc=="CNC") echo 'selected'; ?>>Nhóm CNC</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Số hồ sơ</label>
                    <input type="text" name="sohs" class="form-control" value="<?php echo htmlspecialchars($sohs); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="text" name="ngayth" class="form-control" value="<?php echo htmlspecialchars($ngayth); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="text" name="ngaykt" class="form-control" value="<?php echo htmlspecialchars($ngaykt); ?>">
                </div>
            </div>
            <h5>I. Người thực hiện</h5>
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>Giờ làm việc</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1;$i<=5;$i++): ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><input type="text" name="hoten<?php echo $i; ?>" class="form-control"></td>
                        <td><input type="text" name="gio<?php echo $i; ?>" class="form-control"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <h5>II. Các thiết bị hỗ trợ</h5>
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên viết tắt</th>
                        <th>Serial Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1;$i<=5;$i++): ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><input type="text" name="tenvt<?php echo $i; ?>" class="form-control"></td>
                        <td><input type="text" name="sn<?php echo $i; ?>" class="form-control"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <h5>III. Nội dung công việc</h5>
            <div class="mb-3">
                <label class="form-label">Mô tả chi tiết tình trạng thiết bị, các hỏng hóc (nếu có) do nhân viên xưởng kiểm tra, phát hiện</label>
                <textarea name="ttktbefore" class="form-control" rows="2"><?php echo htmlspecialchars($ttktbefore); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Hỏng hóc</label>
                <textarea name="honghoc" class="form-control" rows="2"><?php echo htmlspecialchars($honghoc); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả công việc thực hiện hoặc cách khắc phục hỏng hóc</label>
                <textarea name="khacphuc" class="form-control" rows="2"><?php echo htmlspecialchars($khacphuc); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Kiểm tra tình trạng kỹ thuật của thiết bị sau khi BD/SC</label>
                <textarea name="kiemtra_bdsc" class="form-control" rows="2"><?php echo isset($_POST['kiemtra_bdsc']) ? htmlspecialchars($_POST['kiemtra_bdsc']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Tình trạng kỹ thuật của thiết bị sau khi sửa chữa</label>
                <select name="ttktafter" class="form-select">
                    <option value=""></option>
                    <option value="Đạt" <?php if($ttktafter=="Đạt") echo 'selected'; ?>>Đạt</option>
                    <option value="Hỏng" <?php if($ttktafter=="Hỏng") echo 'selected'; ?>>Hỏng (Không khắc phục được)</option>
                    <option value="Chờ vật tư thay thế" <?php if($ttktafter=="Chờ vật tư thay thế") echo 'selected'; ?>>Chờ vật tư thay thế</option>
                    <option value="Chưa kết luận" <?php if($ttktafter=="Chưa kết luận") echo 'selected'; ?>>Chưa kết luận</option>
                    <option value="Đang sửa chữa" <?php if($ttktafter=="Đang sửa chữa") echo 'selected'; ?>>Đang sửa chữa</option>
                    <option value="TTKTDB" <?php if($ttktafter=="TTKTDB") echo 'selected'; ?>>TTKT Đặc biệt</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ghi chú tình trạng kỹ thuật</label>
                <textarea name="ghichufinal" class="form-control" rows="2"><?php echo htmlspecialchars($ghichufinal); ?></textarea>
            </div>
            <h5>IV. BẢNG SỐ LIỆU CẦN THIẾT (nếu có)</h5>
            <table class="table table-bordered align-middle" style="table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="background:#7d8cff; color:#fff; text-align:center;" colspan="2">5. BẢNG SỐ LIỆU CẦN THIẾT (nếu có)</th>
                    </tr>
                    <tr>
                        <th style="background:#7d8cff; color:#fff; text-align:center;">Tên Tài Liệu</th>
                        <th style="background:#7d8cff; color:#fff; text-align:center;">Link Tài Liệu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1;$i<=5;$i++): ?>
                    <tr>
                        <td><input type="text" name="ten_tl<?php echo $i; ?>" class="form-control"></td>
                        <td><input type="file" name="file_tl<?php echo $i; ?>" class="form-control"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <div class="mt-3 mb-3">
                <label style="font-weight:bold; color:#222;">Kiểm tra tình trạng kỹ thuật của thiết bị sau khi BD/SC</label>
                <textarea name="kiemtra_bdsc" class="form-control" style="min-height:120px; background:#FFDAB9; border:1px solid #e0e3e8; resize:vertical;"><?php echo isset($_POST['kiemtra_bdsc']) ? htmlspecialchars($_POST['kiemtra_bdsc']) : ''; ?></textarea>
            </div>
            <input type="hidden" name="hoso" value="phieusuachua">
            <div class="text-center mt-4">
                <button type="submit" name="submit" value="luuphieuthuchien" class="btn btn-primary">Lưu phiếu thực hiện</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>