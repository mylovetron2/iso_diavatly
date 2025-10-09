<?php
// Giao diện hiện đại với Bootstrap 5
ob_start();
include ("select_data.php");
$curday = date("d/m/Y");
$maxphieu=0;
$r3 = mysqli_query($link, "SELECT DISTINCT phieu  FROM hososcbd_iso");
if(mysqli_num_rows($r3)>0){
    while($row = mysqli_fetch_array($r3))
    {
        $phieu =$row['phieu'];
        $phieu=(int)$phieu;
        if ($phieu>=$maxphieu) $maxphieu=$phieu;
    }
}
$maxphieu++;
if(($maxphieu>0)&&($maxphieu<=9)) $fieu="000$maxphieu";
else if(($maxphieu>=10)&&($maxphieu<=99))  $fieu="00$maxphieu";
else if(($maxphieu>=100)&&($maxphieu<=999))  $fieu="0$maxphieu";
else $fieu="$maxphieu";

$sohoso=isset($_POST['sohoso']) ? $_POST['sohoso'] : $fieu;
$ngay=isset($_POST['ngay']) ? $_POST['ngay'] : $curday;
$khachhang=isset($_POST['khachhang']) ? $_POST['khachhang'] : '';
$nhanvien=isset($_POST['nhanvien']) ? $_POST['nhanvien'] : '';
$donvi=isset($_POST['donvi']) ? $_POST['donvi'] : '';
$dienthoai=isset($_POST['dienthoai']) ? $_POST['dienthoai'] : '';
$ycthemkh=isset($_POST['ycthemkh']) ? $_POST['ycthemkh'] : '';
$xemxetxuong=isset($_POST['xemxetxuong']) ? $_POST['xemxetxuong'] : '';
$lo=isset($_POST['lo']) ? $_POST['lo'] : '';
$gieng=isset($_POST['gieng']) ? $_POST['gieng'] : '';
$mo=isset($_POST['mo']) ? $_POST['mo'] : '';
$solan=isset($_POST['solan']) ? $_POST['solan'] : '';
if ($solan=="") $solan=0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nhập phiếu yêu cầu dịch vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        function getSomay(thietbiSelect, somaySelect, donvi) {
            var val = $(thietbiSelect).val();
            var mavt = val.split('.')[0];
            if (!mavt) {
                $(somaySelect).html('<option value=""></option>');
                return;
            }
            $.post('get_somay_by_thietbi.php', { mavt: mavt, donvi: donvi }, function(data) {
                if (data.success) {
                    var html = '<option value=""></option>';
                    for (var i = 0; i < data.options.length; i++) {
                        html += '<option value="' + data.options[i].value + '">' + data.options[i].label + '</option>';
                    }
                    $(somaySelect).html(html);
                } else {
                    $(somaySelect).html('<option value=""></option>');
                }
            }, 'json');
        }
        var donvi = $('select[name="donvi"]').val();
        for (let i = 1; i <= 5; i++) {
            $(document).on('change', 'select[name="thietbi'+i+'"]', function() {
                getSomay(this, 'select[name="somay'+i+'"]', donvi);
            });
        }
    });
    </script>
</head>
<body>
<div class="container">
    <div class="form-section">
        <h2 class="mb-4 text-center text-primary">PHIẾU YÊU CẦU DỊCH VỤ</h2>
        <form method="post" action="nhaplieu_phieudichvu.php" enctype="multipart/form-data" autocomplete="off">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Số hồ sơ</label>
                    <input type="text" name="sohoso" class="form-control" value="<?php echo htmlspecialchars($sohoso); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngày</label>
                    <input type="text" name="ngay" class="form-control" value="<?php echo htmlspecialchars($ngay); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Đơn vị</label>
                    <select name="donvi" class="form-select" onchange="this.form.submit()" required>
                        <option value="<?php echo htmlspecialchars($donvi); ?>"><?php echo htmlspecialchars($donvi); ?></option>
                        <?php $r3 = mysqli_query($link, "SELECT DISTINCT madv,tendv FROM donvi_iso");
                        while($row = mysqli_fetch_array($r3)) {
                            $madv =$row['madv'];
                            $tendv = $row['tendv'];
                            echo "<option value='$madv'>$madv - $tendv</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label">Khách hàng</label>
                    <input type="text" name="khachhang" class="form-control" value="<?php echo htmlspecialchars($khachhang); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" name="dienthoai" class="form-control" value="<?php echo htmlspecialchars($dienthoai); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nhân viên xưởng</label>
                    <input type="text" name="nhanvien" class="form-control" value="<?php echo htmlspecialchars($nhanvien); ?>" required>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th class="text-center">STT</th>
                            <th class="text-center">Tên thiết bị</th>
                            <th class="text-center">Số máy</th>
                            <th class="text-center">Tình trạng kỹ thuật</th>
                            <th class="text-center">Nội dung yêu cầu</th>
                            <th class="text-center">Vị trí</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=1;$i<=5;$i++): ?>
                        <tr>
                            <td class="text-center fw-bold"><?php echo $i; ?></td>
                            <td>
                                <select name="thietbi<?php echo $i; ?>" class="form-select">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($donvi)) {
                                        $tenthietbisql_tb = mysqli_query($link, "SELECT DISTINCT mavt,tenvt,model FROM thietbi_iso WHERE madv='$donvi' ORDER BY mavt");
                                        if ($tenthietbisql_tb) {
                                            while($row = mysqli_fetch_array($tenthietbisql_tb)) {
                                                $mavt = $row['mavt'];
                                                $tenvt = $row['tenvt'];
                                                $modelt = $row['model'];
                                                $modelmay = ($modelt=="") ? $mavt : "$mavt-$modelt";
                                                echo "<option value='$mavt.$modelt'>$modelmay - $tenvt</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select name="somay<?php echo $i; ?>" class="form-select">
                                    <option value=""></option>
                                </select>
                            </td>
                            <td><input type="text" name="tinhtrang<?php echo $i; ?>" class="form-control"></td>
                            <td><input type="text" name="yeucau<?php echo $i; ?>" class="form-control"></td>
                            <td>
                                <select name="vitri<?php echo $i; ?>" class="form-select">
                                    <option value=""></option>
                                    <?php $tenthietbisql1 = mysqli_query($link, "SELECT DISTINCT mavitri,tenvitri FROM vitri_iso");
                                    while($row = mysqli_fetch_array($tenthietbisql1)) {
                                        $mavitri =$row['mavitri'];
                                        $tenvitri =$row['tenvitri'];
                                        echo "<option value='$mavitri'>$mavitri - $tenvitri</option>";
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Các yêu cầu khác (nếu có)</label>
                    <input type="text" name="ycthemkh" class="form-control" value="<?php echo htmlspecialchars($ycthemkh); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Xem xét của xưởng</label>
                    <input type="text" name="xemxetxuong" class="form-control" value="<?php echo htmlspecialchars($xemxetxuong); ?>">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Phục vụ sản xuất cho Lô</label>
                    <select name="lo" class="form-select">
                        <option value="<?php echo htmlspecialchars($lo); ?>"><?php echo htmlspecialchars($lo); ?></option>
                        <?php $sqllo = mysqli_query($link, "SELECT DISTINCT malo,tenlo FROM lo_iso");
                        while($row = mysqli_fetch_array($sqllo)) {
                            $malo =$row['malo'];
                            $tenlo =$row['tenlo'];
                            echo "<option value='$malo'>$malo - $tenlo</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phục vụ sản xuất cho Mỏ</label>
                    <select name="mo" class="form-select">
                        <option value="<?php echo htmlspecialchars($mo); ?>"><?php echo htmlspecialchars($mo); ?></option>
                        <?php $sqlmo = mysqli_query($link, "SELECT DISTINCT mamo,tenmo FROM mo_iso");
                        while($row = mysqli_fetch_array($sqlmo)) {
                            $mamo =$row['mamo'];
                            $tenmo =$row['tenmo'];
                            echo "<option value='$mamo'>$mamo - $tenmo</option>";
                        } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Phục vụ sản xuất cho Giếng</label>
                    <input type="text" name="gieng" class="form-control" value="<?php echo htmlspecialchars($gieng); ?>">
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" name="nhap" value="nhapphieudichvu" class="btn btn-primary btn-lg shadow">Lưu phiếu</button>
                <input type="hidden" name="hoso" value="phieuscbd">
            </div>
        </form>
        <div class="row mt-3">
            <div class="col text-start">
                <a href="Danhmucsc.php?&start=0&ad=&sort=&s=&f=&mte_a=new" target="_blank" class="btn btn-link">Thêm thiết bị mới</a>
            </div>
            <div class="col text-end">
                <a href="vitri.php?&start=0&ad=&sort=&s=&f=&mte_a=new" target="_blank" class="btn btn-link">Thêm vị trí mới</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
