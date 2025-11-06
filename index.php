<?php

include 'connect.php';
$message = "";



$sql = "SELECT id, tieude, mota, trangthai, hanchot FROM congviec WHERE iduser = ? ORDER BY hanchot ASC";

$stmt = $ocon->prepare($sql);
if ($stmt === false) {
    die("Lỗi chuẩn bị truy vấn: " . $ocon->error);
}

$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = []; // Mảng chứa dữ liệu công việc
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
$stmt->close();

// Kiểm tra nếu không có tasks, gán $tasks = 0 để phù hợp với logic HTML
if (empty($tasks)) {
    $tasks = 0;
}

// Hàm chuyển đổi trạng thái (0/1) sang văn bản mô tả
function getStatusText($status_value) {
    return ($status_value == 1) ? "Hoàn thành" : "Đang làm";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <input type="checkbox" id="checkbox">
    
    <?php 
    include "inc/header.php";
    ?>

    <div class="body">
        <?php 
        include "inc/nav.php"; 
        ?>

        <section class="section-1">
            <h4 class="title">Công việc cần làm</h4>
            
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
                <?php echo stripcslashes($_GET['success']); ?>
            </div>
            <?php } ?>
            
            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>STT</th>
                    <th>Tiêu đề</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Deadline</th>
                    <th>Thao tác</th>
                </tr>
                <?php $i=0; foreach ($tasks as $task) { 
                    $task_title = $task['tieude'];
                    $task_description = $task['mota'];
                    $task_status = getStatusText($task['trangthai']);
                    $task_due_date = $task['hanchot'];
                    $task_id = $task['id'];
                ?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=$task_title?></td>
                    <td><?=$task_description?></td>
                    <td><?=$task_status?></td>
                    <td><?=$task_due_date?></td>

                    <td>
                        <a href="" class="edit-btn">Sửa</a>
                        <a href="" class="delete-btn">Xóa</a>
                        <a href="" class="detail-btn">Chi tiết</a>
                    </td>
                </tr>
               <?php } ?>
            </table>
            <?php } else { ?>
                <h3>Bạn chưa có công việc nào được giao.</h3>
            <?php }?>
            
            <div class="add-btn-container">
                <a href="" class="add-task-btn">
                    <i class="fa fa-plus" aria-hidden="true"></i> Thêm công việc mới
                </a>
            </div>
            </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(2)");
    if (active) {
        active.classList.add("active");
    }
</script>

</body>
</html>