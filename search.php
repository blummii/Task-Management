<?php
// ====== KẾT NỐI CSDL ======
$servername = "localhost";
$username = "root";
$password = "";
$database = "qlcongviec";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

// ====== NHẬN DỮ LIỆU TỪ FORM ======
$hanchot_filter = $_GET['hanchot'] ?? '';
$trangthai_filter = $_GET['trangthai'] ?? '';
$mduutien_filter = $_GET['mduutien'] ?? '';
$keyword = $_GET['keyword'] ?? '';

// ====== XÂY DỰNG TRUY VẤN LINH HOẠT ======
$sql = "SELECT * FROM congviec WHERE 1=1";
$params = [];
$types = "";

// Ngày hạn chót
if (!empty($hanchot_filter)) {
  $sql .= " AND hanchot = ?";
  $params[] = $hanchot_filter;
  $types .= "s";
}

// Trạng thái (0 hoặc 1)
if ($trangthai_filter !== '') {
  // Ép kiểu về số nguyên để tránh sai kiểu
  $trangthai_value = intval($trangthai_filter);
  $sql .= " AND trangthai = ?";
  $params[] = $trangthai_value;
  $types .= "i";
}

// Mức độ ưu tiên
if (!empty($mduutien_filter)) {
  $sql .= " AND mduutien = ?";
  $params[] = $mduutien_filter;
  $types .= "s";
}

// Từ khóa tiêu đề hoặc mô tả (LIKE)
if (!empty($keyword)) {
  $sql .= " AND (tieude LIKE ? OR mota LIKE ?)";
  $search = "%$keyword%";
  $params[] = $search;
  $params[] = $search;
  $types .= "ss";
}

// ====== CHUẨN BỊ & THỰC THI TRUY VẤN ======
$result = null;
$tasks = [];

if (!empty($_GET)) { // Chỉ thực hiện khi người dùng bấm Tìm kiếm
  $stmt = $conn->prepare($sql);
  if ($params) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $tasks[] = $row;
    }
  }
  $stmt->close();
}

// ====== HÀM XỬ LÝ TRẠNG THÁI ======
function getStatusText($status_value) {
  return ($status_value == 1) ? "Hoàn thành" : "Đang làm";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Tìm kiếm công việc</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<input type="checkbox" id="checkbox">

<?php include "inc/header.php"; ?>

<div class="body">
<?php include "inc/nav.php"; ?>

<section class="section-1">
  <h2 class="title-2">
  <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm công việc ...
</h2>

<div class="search-layout">

  <!-- FORM TÌM KIẾM -->
  <form method="get" action="" class="form-1">
    <div class="input-holder">
      <label>Từ khóa:</label>
      <input type="text" class="input-1" name="keyword" placeholder="Nhập tiêu đề hoặc mô tả..."
             value="<?= htmlspecialchars($keyword) ?>">
    </div>

    <div class="input-holder">
      <label>Trạng thái:</label>
      <select class="input-1" name="trangthai">
        <option value="">-- Tất cả --</option>
        <option value="0" <?= $trangthai_filter==='0'?'selected':'' ?>>Đang làm</option>
        <option value="1" <?= $trangthai_filter==='1'?'selected':'' ?>>Hoàn thành</option>
      </select>
    </div>

    <div class="input-holder">
      <label>Độ ưu tiên:</label>
      <select class="input-1" name="mduutien">
        <option value="">-- Tất cả --</option>
        <option value="Cao" <?= $mduutien_filter==='Cao'?'selected':'' ?>>Cao</option>
        <option value="Trung bình" <?= $mduutien_filter==='Trung bình'?'selected':'' ?>>Trung bình</option>
        <option value="Thấp" <?= $mduutien_filter==='Thấp'?'selected':'' ?>>Thấp</option>
      </select>
    </div>

    <div class="input-holder">
      <label>Ngày hạn chót:</label>
      <input type="date" class="input-1" name="hanchot" value="<?= htmlspecialchars($hanchot_filter) ?>">
    </div>

    <div class="buton">
      <button type="submit" class="btn" style="background-color: dodgerblue; color:aliceblue; border-color: dodgerblue; border-radius:5px; ">Tìm kiếm</button>
      <button style=" color:black; border-color:darkgray; border-radius:7px; padding : 4px;"><a href="search.php" class="btn" style=" color:black; border-color:dimgrey; border-radius:10px; padding : 5px;" >Xóa lọc</a></button>
    </div>
  </form>

  <!-- KẾT QUẢ -->
  <div class="result-area" style="margin-top:25px;">
    <h4>Kết quả tìm kiếm:</h4>
    <?php if ($result === null): ?>
      <p>Vui lòng nhập điều kiện tìm kiếm.</p>
    <?php elseif (empty($tasks)): ?>
      <p>Không có công việc nào phù hợp.</p>
    <?php else: ?>
      <p style="padding: 5px;">Tìm thấy <strong><?= count($tasks) ?></strong> công việc.</p>
      <ul class="task-list">
        <?php foreach ($tasks as $task) :
          $status = $task['trangthai'];
          $status_text = getStatusText($status);
          $status_class = $status == 1 ? 'task-done' : 'task-todo';
        ?>
        <li class="task-item <?= $status_class ?>">
          <div class="task-details">
            <div class="task-header">
              <span class="task-title" style="text-decoration: <?= $status==1 ? 'line-through':'none' ?>;">
                <?= htmlspecialchars($task['tieude']) ?>
              </span>
              <span class="task-status <?= $status_class ?>"><?= $status_text ?></span>
            </div>
            <p class="task-description"><?= nl2br(htmlspecialchars($task['mota'])) ?></p>
            <div class="task-meta">
              <span>Hạn chót: <strong><?= $task['hanchot'] ?></strong></span>
              <span>| Ưu tiên: <strong><?= $task['mduutien'] ?></strong></span>
            </div>
          </div>
          <div class="task-actions">
            <a href="update_status.php?id=<?= $task['id'] ?>&action=done" class="complete-btn">Hoàn thành</a>
            <a href="update_status.php?id=<?= $task['id'] ?>&action=undo" class="undo-btn">Hoàn lại</a>
            <a href="xlxoa.php?id=<?= $task['id'] ?>" class="delete-btn" onclick="return confirm('Xóa công việc này?');">Xóa</a>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
</section>
</div>

<script>
  var active = document.querySelector("#navList li:nth-child(3)");
  if (active) active.classList.add("active");
</script>
</body>
</html>
