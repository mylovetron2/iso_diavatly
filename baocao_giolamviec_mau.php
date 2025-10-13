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

<!-- Lọc bảng bằng JS, không reload SQL -->
<div style="margin-bottom:10px;">
  <input type="text" id="tableSearchInput" placeholder="Tìm kiếm nhanh trong bảng..." style="width:200px;">
  <button type="button" onclick="filterTableRows()">Tìm kiếm</button>
  <button type="button" onclick="resetTableRows()">Hiện tất cả</button>
</div>
<script>
function filterTableRows() {
  var input = document.getElementById('tableSearchInput');
  var filter = input.value.toLowerCase();
  var table = document.querySelector('table');
  var trs = table.getElementsByTagName('tr');
  for (var i = 1; i < trs.length; i++) { // Bỏ qua header
    var rowText = trs[i].innerText.toLowerCase();
    if (filter === '' || rowText.indexOf(filter) !== -1) {
      trs[i].style.display = '';
    } else {
      trs[i].style.display = 'none';
    }
  }
}
function resetTableRows() {
  document.getElementById('tableSearchInput').value = '';
  filterTableRows();
}
</script>

<?php
$tong_sohoso = 0;
$tong_gio_all = 0;
$rows_html = '';
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
    $show_row = false;
    if ($staff_filter === 'only_no_staff') {
        if ($tong_gio == 0) $show_row = true;
    } elseif ($staff_filter === 'hide_no_staff') {
        if ($tong_gio > 0) $show_row = true;
    } else {
        $show_row = true;
    }
    if ($show_row) {
        $rows_html .= '<tr>';
        $rows_html .= '<td>'.$stt.'</td>';
        $rows_html .= '<td>'.$hoso.'</td>';
        $rows_html .= '<td>'.$ngayth.'</td>';
        $rows_html .= '<td>'.$ngaykt.'</td>';
        if ($tong_gio > 0) {
            $rows_html .= '<td>'.htmlspecialchars(implode(", ", $ds_nv)).'</td>';
            $rows_html .= '<td>'.$tong_gio.'</td>';
            $tong_gio_all += $tong_gio;
        } else {
            $rows_html .= '<td colspan="2">Không có nhân viên thực hiện</td>';
        }
        $rows_html .= '</tr>';
        $stt++;
        $tong_sohoso++;
    }
}
?>
<div style="margin-bottom:10px;font-weight:bold;">Tổng số hồ sơ: <?php echo $tong_sohoso; ?> | Tổng số giờ làm việc: <?php echo $tong_gio_all; ?></div>
<table border="1" cellpadding="5" cellspacing="0">
<tr><th>STT</th><th>Số hồ sơ</th><th>Ngày bắt đầu</th><th>Ngày kết thúc</th><th>Người thực hiện</th><th>Tổng số giờ làm việc</th></tr>
<?php echo $rows_html; ?>
</table>
