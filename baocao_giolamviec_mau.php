<?php
// Báo cáo liệt kê hồ sơ và giờ làm việc từng người trong khoảng thời gian (theo ngày hoặc theo tháng, cộng dồn lũy kế)
// Sử dụng bảng hososcbd_iso và ngthuchien_iso
// Tham số: from_month, to_month, year, from_date, to_date (GET)

include ("select_data.php");

$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$thang_batdau = isset($_GET['from_month']) ? intval($_GET['from_month']) : date('m');
$thang_ketthuc = isset($_GET['to_month']) ? intval($_GET['to_month']) : date('m');
$nam = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Giao diện nhập khoảng thời gian và tìm kiếm
?>
<h2><?php echo $tieude; ?></h2>

<form method="get" style="margin-bottom:20px;">
  <fieldset style="display:inline-block;padding:10px 20px;">
    <legend><b>Chọn khoảng thời gian</b></legend>
    <label>Từ ngày: <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : ''; ?>"></label>
    <label>Đến ngày: <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : ''; ?>"></label>
    <br><span style="font-size:12px;color:#888;">(Hoặc chọn theo tháng)</span><br>
    <label>Từ tháng: <input type="number" min="1" max="12" name="from_month" value="<?php echo isset($_GET['from_month']) ? intval($_GET['from_month']) : date('m'); ?>" style="width:50px;"></label>
    <label>Đến tháng: <input type="number" min="1" max="12" name="to_month" value="<?php echo isset($_GET['to_month']) ? intval($_GET['to_month']) : date('m'); ?>" style="width:50px;"></label>
    <label>Năm: <input type="number" min="2000" max="2100" name="year" value="<?php echo isset($_GET['year']) ? intval($_GET['year']) : date('Y'); ?>" style="width:70px;"></label>
    <br><br>
    <label>Tìm kiếm: <input type="text" name="search" placeholder="Số hồ sơ, tên nhân viên, tên thiết bị" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="width:220px;"></label>
    <br><br>
    <label>Lọc hồ sơ:
      <select name="staff_filter">
        <option value="all" <?php if(!isset($_GET['staff_filter']) || $_GET['staff_filter']==='all') echo 'selected'; ?>>Tất cả</option>
        <option value="hide_no_staff" <?php if(isset($_GET['staff_filter']) && $_GET['staff_filter']==='hide_no_staff') echo 'selected'; ?>>Không hiển thị "Không có nhân viên thực hiện"</option>
        <option value="only_no_staff" <?php if(isset($_GET['staff_filter']) && $_GET['staff_filter']==='only_no_staff') echo 'selected'; ?>>Chỉ hiển thị "Không có nhân viên thực hiện"</option>
      </select>
    </label>
    <button type="submit">Xem báo cáo</button>
  </fieldset>
</form>
<?php
// Xử lý search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Khi truy vấn hồ sơ, bổ sung điều kiện search nếu có
if ($from_date && $to_date) {
    $date_from = date('Y-m-d', strtotime($from_date));
    $date_to = date('Y-m-d', strtotime($to_date));
    $where = "(ngayth BETWEEN '$date_from' AND '$date_to') OR (ngaykt BETWEEN '$date_from' AND '$date_to')";
} else {
    $date_from = sprintf('%04d-%02d-01', $nam, $thang_batdau);
    $date_to = sprintf('%04d-%02d-31', $nam, $thang_ketthuc);
    $where = "(ngayth BETWEEN '$date_from' AND '$date_to') OR (ngaykt BETWEEN '$date_from' AND '$date_to')";
}
if ($search !== '') {
    $search_sql = mysql_real_escape_string($search);
    $where .= " AND (hoso LIKE '%$search_sql%'";
    $where .= " OR EXISTS (SELECT 1 FROM ngthuchien_iso WHERE mahoso=hososcbd_iso.hoso AND hoten LIKE '%$search_sql%')";
    $where .= " OR EXISTS (SELECT 1 FROM thietbi_iso WHERE mavt=hososcbd_iso.mavt AND tenvt LIKE '%$search_sql%'))";
}
// Lấy trạng thái lọc
$staff_filter = isset($_GET['staff_filter']) ? $_GET['staff_filter'] : 'all';
if ($staff_filter === 'only_no_staff') {
    $where .= " AND NOT EXISTS (SELECT 1 FROM ngthuchien_iso WHERE mahoso=hososcbd_iso.hoso)";
} elseif ($staff_filter === 'hide_no_staff') {
    $where .= " AND EXISTS (SELECT 1 FROM ngthuchien_iso WHERE mahoso=hososcbd_iso.hoso)";
}
$sql_hoso = mysql_query("SELECT * FROM hososcbd_iso WHERE $where ORDER BY hoso");
?>

<table border="1" cellpadding="5" cellspacing="0">
<tr><th>STT</th><th>Số hồ sơ</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>Người thực hiện</th><th>Tổng số giờ làm việc</th></tr>
<?php
$stt = 1;
while($row_hoso = mysql_fetch_array($sql_hoso)) {
    $hoso = $row_hoso['hoso'];
    $ngayth = $row_hoso['ngayth'];
    $ngaykt = $row_hoso['ngaykt'];
    $sql_nv = mysql_query("SELECT * FROM ngthuchien_iso WHERE mahoso='$hoso'");
    $tong_gio = 0;
    $ds_nv = [];
    while($row_nv = mysql_fetch_array($sql_nv)) {
        $hoten = $row_nv['hoten'];
        if ($from_date && $to_date) {
            $gio_nv = 0;
            for ($m = 1; $m <= 12; $m++) {
                $field = 'giolv'.$m;
                $thang = $m;
                $nam_gio = $nam;
                $ngay_dau_thang = strtotime("$nam_gio-$thang-01");
                $ngay_cuoi_thang = strtotime("$nam_gio-$thang-31");
                $from = strtotime($from_date);
                $to = strtotime($to_date);
                if ($ngay_cuoi_thang >= $from && $ngay_dau_thang <= $to) {
                    $gio_nv += isset($row_nv[$field]) ? intval($row_nv[$field]) : 0;
                }
            }
        } else {
            $field_end = 'giolv'.$thang_ketthuc;
            $luylke_end = isset($row_nv[$field_end]) ? intval($row_nv[$field_end]) : 0;
            if($thang_batdau > 1) {
                $field_start = 'giolv'.($thang_batdau-1);
                $luylke_start = isset($row_nv[$field_start]) ? intval($row_nv[$field_start]) : 0;
            } else {
                $luylke_start = 0;
            }
            $gio_nv = $luylke_end - $luylke_start;
        }
        if($gio_nv > 0) {
            $tong_gio += $gio_nv;
            $ds_nv[] = $hoten;
        }
    }
    if ($staff_filter === 'only_no_staff') {
        if ($tong_gio == 0) {
            echo '<tr>';
            echo '<td>'.$stt.'</td>';
            echo '<td>'.$hoso.'</td>';
            echo '<td>'.$ngayth.'</td>';
            echo '<td>'.$ngaykt.'</td>';
            echo '<td colspan="2">Không có nhân viên thực hiện</td>';
            echo '</tr>';
            $stt++;
        }
    } elseif ($staff_filter === 'hide_no_staff') {
        if ($tong_gio > 0) {
            echo '<tr>';
            echo '<td>'.$stt.'</td>';
            echo '<td>'.$hoso.'</td>';
            echo '<td>'.$ngayth.'</td>';
            echo '<td>'.$ngaykt.'</td>';
            echo '<td>'.htmlspecialchars(implode(", ", $ds_nv)).'</td>';
            echo '<td>'.$tong_gio.'</td>';
            echo '</tr>';
            $stt++;
        }
    } else { // all
        if ($tong_gio > 0) {
            echo '<tr>';
            echo '<td>'.$stt.'</td>';
            echo '<td>'.$hoso.'</td>';
            echo '<td>'.$ngayth.'</td>';
            echo '<td>'.$ngaykt.'</td>';
            echo '<td>'.htmlspecialchars(implode(", ", $ds_nv)).'</td>';
            echo '<td>'.$tong_gio.'</td>';
            echo '</tr>';
            $stt++;
        } else {
            echo '<tr>';
            echo '<td>'.$stt.'</td>';
            echo '<td>'.$hoso.'</td>';
            echo '<td>'.$ngayth.'</td>';
            echo '<td>'.$ngaykt.'</td>';
            echo '<td colspan="2">Không có nhân viên thực hiện</td>';
            echo '</tr>';
            $stt++;
        }
    }
}
?>
</table>
