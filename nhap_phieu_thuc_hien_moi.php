<?php
// File: nhap_phieu_thuc_hien_moi.php
// Giao diện hoàn toàn mới, hiện đại, tối giản, responsive, dùng Bootstrap 5
ob_start();
include("header_bootstrap.php");
include("select_data.php");
$link = isset($link) ? $link : null;
$search = isset($_POST['maql']) ? $_POST['maql'] : '';
$hoso = isset($_POST['hoso']) ? $_POST['hoso'] : 'phieusuachua';
?>
<div class="container py-5">
    <div class="mx-auto" style="max-width:900px;">
        <div class="card shadow border-0 mb-4" style="border-radius:20px;">
            <div class="card-header bg-primary text-white d-flex align-items-center" style="border-radius:20px 20px 0 0;">
                <span class="me-2" style="font-size:2rem;">🗂️</span>
                <h2 class="mb-0 fw-bold" style="font-size:1.6rem;letter-spacing:1px;">Phiếu Thực Hiện Công Việc (Mới)</h2>
            </div>
            <div class="card-body p-4 bg-light">
                <form method="post" action="nhap_phieu_thuc_hien_moi.php" autocomplete="off" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Mã quản lý</label>
                            <input type="text" name="maql" class="form-control form-control-lg rounded-3 shadow-sm" value="<?php echo htmlspecialchars($search); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">Chọn thiết bị</label>
                            <select name="hosomay" class="form-select form-select-lg rounded-3 shadow-sm" onchange="this.form.submit()">
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
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Số hồ sơ</label>
                            <input type="text" name="sohs" class="form-control rounded-3 shadow-sm" value="<?php echo htmlspecialchars($sohs); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="ngayth" class="form-control rounded-3 shadow-sm" value="<?php echo htmlspecialchars($ngayth); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="ngaykt" class="form-control rounded-3 shadow-sm" value="<?php echo htmlspecialchars($ngaykt); ?>">
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                                <div class="card-header bg-info text-white fw-bold" style="border-radius:14px 14px 0 0;">Người thực hiện</div>
                                <div class="card-body p-2">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-2 text-end pe-0"><span class="badge bg-secondary">#<?php echo $i; ?></span></div>
                                        <div class="col-6"><input type="text" name="hoten<?php echo $i; ?>" class="form-control form-control-sm rounded-2" placeholder="Họ tên"></div>
                                        <div class="col-4"><input type="text" name="gio<?php echo $i; ?>" class="form-control form-control-sm rounded-2" placeholder="Giờ làm"></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                                <div class="card-header bg-success text-white fw-bold" style="border-radius:14px 14px 0 0;">Thiết bị hỗ trợ</div>
                                <div class="card-body p-2">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-2 text-end pe-0"><span class="badge bg-secondary">#<?php echo $i; ?></span></div>
                                        <div class="col-6"><input type="text" name="tenvt<?php echo $i; ?>" class="form-control form-control-sm rounded-2" placeholder="Tên VT"></div>
                                        <div class="col-4"><input type="text" name="sn<?php echo $i; ?>" class="form-control form-control-sm rounded-2" placeholder="Serial"></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                        <div class="card-header bg-warning text-dark fw-bold" style="border-radius:14px 14px 0 0;">Nội dung công việc</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tình trạng thiết bị, hỏng hóc (nếu có)</label>
                                <textarea name="ttktbefore" class="form-control rounded-3" rows="2"><?php echo htmlspecialchars($ttktbefore); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hỏng hóc</label>
                                <textarea name="honghoc" class="form-control rounded-3" rows="2"><?php echo htmlspecialchars($honghoc); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cách khắc phục</label>
                                <textarea name="khacphuc" class="form-control rounded-3" rows="2"><?php echo htmlspecialchars($khacphuc); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                                <div class="card-header bg-primary text-white fw-bold" style="border-radius:14px 14px 0 0;">Bảng số liệu cần thiết</div>
                                <div class="card-body p-2">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-7"><input type="text" name="ten_tl<?php echo $i; ?>" class="form-control form-control-sm rounded-2" placeholder="Tên tài liệu"></div>
                                        <div class="col-5"><input type="file" name="file_tl<?php echo $i; ?>" class="form-control form-control-sm rounded-2"></div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                                <div class="card-header bg-danger text-white fw-bold" style="border-radius:14px 14px 0 0;">Kiểm tra kỹ thuật sau sửa</div>
                                <div class="card-body p-2 bg-light">
                                    <textarea name="kiemtra_bdsc" class="form-control rounded-3" style="min-height:120px;" placeholder="Nhận xét kỹ thuật..."><?php echo isset($_POST['kiemtra_bdsc']) ? htmlspecialchars($_POST['kiemtra_bdsc']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                        <div class="card-header bg-secondary text-white fw-bold" style="border-radius:14px 14px 0 0;">Kết luận</div>
                        <div class="card-body">
                            <textarea name="ghichufinal" class="form-control rounded-3" rows="3" placeholder="Nhập kết luận, nhận xét tổng thể..."><?php echo htmlspecialchars($ghichufinal); ?></textarea>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tình trạng kỹ thuật sau sửa</label>
                            <select name="ttktafter" class="form-select rounded-3">
                                <option value=""></option>
                                <option value="Đạt" <?php if($ttktafter=="Đạt") echo 'selected'; ?>>Đạt</option>
                                <option value="Hỏng" <?php if($ttktafter=="Hỏng") echo 'selected'; ?>>Hỏng (Không khắc phục được)</option>
                                <option value="Chờ vật tư thay thế" <?php if($ttktafter=="Chờ vật tư thay thế") echo 'selected'; ?>>Chờ vật tư thay thế</option>
                                <option value="Chưa kết luận" <?php if($ttktafter=="Chưa kết luận") echo 'selected'; ?>>Chưa kết luận</option>
                                <option value="Đang sửa chữa" <?php if($ttktafter=="Đang sửa chữa") echo 'selected'; ?>>Đang sửa chữa</option>
                                <option value="TTKTDB" <?php if($ttktafter=="TTKTDB") echo 'selected'; ?>>TTKT Đặc biệt</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="hoso" value="phieusuachua">
                    <div class="text-center mt-4">
                        <button type="submit" name="submit" value="luuphieuthuchien" class="btn btn-primary btn-lg px-5 py-2 fw-bold rounded-pill shadow">Lưu phiếu thực hiện</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
