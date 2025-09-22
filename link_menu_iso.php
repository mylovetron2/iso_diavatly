<?php
// Kết nối CSDL (dựa vào file formsc.php)
$servername = "localhost";
$username = "diavatly";
$password = "cntt2019";
$dbname = "diavatly_db"; // Thay bằng tên database của bạn

$user = isset($_GET['user']) ? $_GET['user'] : null;
//include ("select_data.php") ;
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}



// Xử lý thêm mới
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $link = $_POST['link'];
    $sql = "INSERT INTO link_iso (title, link) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $link);
    $stmt->execute();
    $stmt->close();
    $redirect = "link_menu_iso.php";
    if ($user) $redirect .= "?user=" . urlencode($user);
    header("Location: $redirect");
    exit();
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM link_iso WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $redirect = "link_menu_iso.php";
    if ($user) $redirect .= "?user=" . urlencode($user);
    header("Location: $redirect");
    exit();
}

// Xử lý cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $link = $_POST['link'];
    $sql = "UPDATE link_iso SET title=?, link=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $link, $id);
    $stmt->execute();
    $stmt->close();
    $redirect = "link_menu_iso.php";
    if ($user) $redirect .= "?user=" . urlencode($user);
    header("Location: $redirect");
    exit();
}

// Lấy dữ liệu để sửa
$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $sql = "SELECT * FROM link_iso WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit = $result->fetch_assoc();
    $stmt->close();
}

// Lấy danh sách link_iso
$sql = "SELECT * FROM link_iso ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý các đường Link</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 40px 24px 40px;
        }
        h2 {
            text-align: center;
            color: #2d3a4b;
            margin-bottom: 32px;
        }
        form {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        form input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #bfc9d1;
            border-radius: 5px;
            font-size: 15px;
            width: 180px;
            background: #f8fafb;
            transition: border 0.2s;
        }
        form input[type="text"]:focus {
            border: 1.5px solid #1976d2;
            outline: none;
        }
        form button, form a {
            padding: 8px 18px;
            border: none;
            border-radius: 5px;
            background: #1976d2;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            margin-left: 4px;
            transition: background 0.2s;
        }
        form button:hover, form a:hover {
            background: #125ea7;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        th, td {
            border: none;
            padding: 12px 10px;
            text-align: left;
        }
        td.link-col {
            word-break: break-all;
            max-width: 320px;
        }
        th {
            background: #1976d2;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f4f6f8;
        }
        tr:hover {
            background: #e3eefa;
        }
        .actions a {
            color: #1976d2;
            background: none;
            padding: 0 6px;
            border: none;
            font-size: 15px;
            border-radius: 3px;
            transition: color 0.2s;
        }
        .actions a:hover {
            color: #d32f2f;
            background: #fbe9e7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quản lý các đường Link</h2>
        <?php if ($user === 'dieuvx' || $user === 'admin'): ?>
        <form method="post">
            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?php echo $edit ? $edit['id'] : ''; ?>">
            <?php endif; ?>
            <input type="text" name="title" placeholder="Tiêu đề" required value="<?php echo $edit ? htmlspecialchars($edit['title']) : ''; ?>">
            <input type="text" name="link" placeholder="Link" required value="<?php echo $edit ? htmlspecialchars($edit['link']) : ''; ?>">
            <?php if ($edit): ?>
                <button type="submit" name="update">Cập nhật</button>
                <a href="link_menu_iso.php<?php echo $user ? ('?user=' . urlencode($user)) : ''; ?>">Hủy</a>
            <?php else: ?>
                <button type="submit" name="add">Thêm mới</button>
            <?php endif; ?>
        </form>
        <?php endif; ?>
        <table>
            <tr>
                <th>Tiêu đề</th>
                <th>Link</th>
                <th>Hành động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td class="link-col"><a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" style="color:#1976d2;text-decoration:underline;"><?php echo htmlspecialchars($row['link']); ?></a></td>
                <td class="actions">
                <?php if ($user === 'dieuvx' || $user === 'admin'): ?>
                    <a href="?edit=<?php echo $row['id']; ?>&user=<?php echo urlencode($user); ?>">Sửa</a>|
                    <a href="?delete=<?php echo $row['id']; ?>&user=<?php echo urlencode($user); ?>" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</a>
                <?php endif; ?>
                </td>
                
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>