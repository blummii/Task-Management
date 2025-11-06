<?php
include 'connect.php';
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['iduser'];

$message = "";



// --- LOGIC TRUY VẤN CÔNG VIỆC ---
$where_clauses = ["iduser = ?"];
$bind_types = "i";
$bind_params = [$current_user_id];

if (!empty($search)) {
    // Thêm điều kiện tìm kiếm theo tieude HOẶC mota
    $where_clauses[] = "(tieude LIKE ? OR mota LIKE ?)";
    $search_param = "%" . $search . "%";
    $bind_types .= "ss";
    $bind_params[] = $search_param;
    $bind_params[] = $search_param;
}

$where_sql = implode(" AND ", $where_clauses);

// Sắp xếp: Ưu tiên công việc chưa hoàn thành (trangthai ASC), sau đó đến thứ tự kéo thả (thutu)
$sql = "SELECT id, tieude, mota, trangthai, hanchot, thutu, mduutien FROM congviec WHERE {$where_sql} ORDER BY trangthai ASC, thutu ASC";

$stmt = $ocon->prepare($sql);
if ($stmt === false) {
    die("Lỗi chuẩn bị truy vấn: " . $ocon->error);
}

// Gắn tham số
if (!empty($bind_params)) {
    // FIX: mysqli_stmt::bind_param() yêu cầu các tham số được truyền dưới dạng reference.
    // Chúng ta cần sử dụng Reflection để gọi hàm, đồng thời tạo ra các reference cho mảng tham số.
    
    // Bước 1: Chuẩn bị mảng tham số. Phần tử đầu tiên là chuỗi types.
    $bind_args = [$bind_types];

    // Bước 2: Thêm các tham số bằng cách tạo reference cho từng giá trị trong $bind_params
    // Vòng lặp foreach(&$value) sẽ đảm bảo $bind_args chứa các reference
    foreach ($bind_params as &$value) {
        $bind_args[] = &$value;
    }
    // Quan trọng: Hủy liên kết reference cuối cùng để tránh ảnh hưởng đến các biến khác
    unset($value); 

    // Bước 3: Dùng Reflection để gọi bind_param với mảng tham số đã được tham chiếu
    $ref = new ReflectionClass('mysqli_stmt');
    $method = $ref->getMethod('bind_param');
    $method->invokeArgs($stmt, $bind_args);
}
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
$stmt->close();

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
    <title>Task Management Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    
    <?php 
    // Giả định bạn có file inc/header.php
    include "inc/header.php";
    ?>

<div class="body">
    <?php 
    include "inc/nav.php"; 
    ?>

    <section class="section-1" style="display: flex; gap: 30px;"> 
        
        <div style="flex: 1; max-width: 400px; display: flex; flex-direction: column; gap: 20px;margin-top: 40px;">
            
            <a href="#" class="add-task-btn" 
               style="width: 100%; text-align: center; padding: 25px 10px; font-size: 20px; text-transform: uppercase;">
                <i class="fa fa-plus" aria-hidden="true"></i> THÊM CÔNG VIỆC
            </a>
            <a href="#" class="search-btn" 
               style="width: 100%; text-align: center; padding: 25px 10px; font-size: 20px; text-transform: uppercase;">
                <i class="fa fa-search" aria-hidden="true"></i> TÌM KIẾM
            </a>


            </div>
        <div style="flex: 2; min-width: 600px;">
            <h4 class="title">Danh sách công việc</h4>
            
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
                <?php echo stripcslashes($_GET['success']); ?>
            </div>
            <?php } ?>
            
            <?php if ($tasks != 0) { ?>
            
            <ul id="taskList" class="task-list"> 
                <?php 
                $i=0; 
                foreach ($tasks as $task) { 
                    $task_id = $task['id'];
                    $task_title = htmlspecialchars($task['tieude']);
                    $task_description = nl2br(htmlspecialchars($task['mota']));
                    $task_status = $task['trangthai'];
                    $status_text = getStatusText($task_status);
                    $task_due_date = $task['hanchot'];
                    // Lấy mduutien, nếu không có trong query cũ, sẽ báo lỗi. 
                    // Tôi đã thêm thutu và mduutien vào query PHP ở trên.
                    $task_priority = $task['mduutien'] ?? 'Không rõ'; 
                    
                    // Thêm class dựa trên trạng thái
                    $status_class = ($task_status == 1) ? 'task-done' : 'task-todo';
                    $draggable = ($task_status == 0) ? 'true' : 'false'; // Chỉ kéo thả khi chưa hoàn thành
                ?>
                
                <li class="task-item <?=$status_class?>" data-id="<?=$task_id?>" draggable="<?=$draggable?>">
                    <div class="task-details">
                        <div class="task-header">
                            <span class="task-title" style="text-decoration: <?=$task_status == 1 ? 'line-through' : 'none'?>;"><?=$task_title?></span>
                            <span class="task-status <?=$status_class?>"><?=$status_text?></span>
                        </div>
                        <p class="task-description"><?=$task_description?></p>
                        <div class="task-meta">
                            <span>Hạn chót: <strong><?=$task_due_date?></strong></span>
                            <span>| Ưu tiên: <strong><?=$task_priority?></strong></span>
                        </div>
                    </div>
                    
                    <div class="task-actions">
                        <a href="#" class="edit-btn">Sửa</a>
                        <a href="xlxoa.php?id=<?=$task_id?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa công việc này không?');">Xóa</a>
                        <a href="#" class="detail-btn">Chi tiết</a>
                        
                        <?php if ($task_status == 0): ?>
    <a href="update_status.php?id=<?= $task_id ?>&action=done" class="complete-btn">Hoàn thành</a>
<?php else: ?>
    <a href="update_status.php?id=<?= $task_id ?>&action=undo" class="undo-btn">Hoàn lại</a>
<?php endif; ?>

                    </div>
                </li>
               <?php } ?>
            </ul>
            
            <?php } else { ?>
                <h3>Bạn chưa có công việc nào được giao.</h3>
            <?php }?>
            
        </div>
    </section>
</div>

<script type="text/javascript">
    // Script đánh dấu menu đang active
    var active = document.querySelector("#navList li:nth-child(2)");
    if (active) {
        active.classList.add("active");
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const taskList = document.getElementById('taskList');
    if (!taskList) return; 

    let draggedItem = null;

    // 1. Gán sự kiện kéo (chỉ cho phép kéo nếu task chưa hoàn thành)
    taskList.addEventListener('dragstart', (e) => {
        if (e.target.classList.contains('task-item') && !e.target.classList.contains('task-done')) {
            draggedItem = e.target;
            setTimeout(() => {
                e.target.classList.add('dragging'); 
            }, 0);
        } else {
            e.preventDefault(); // Ngăn kéo thả mục đã hoàn thành
        }
    });

    taskList.addEventListener('dragend', (e) => {
        e.target.classList.remove('dragging');
        draggedItem = null;
        saveNewOrder(); // Lưu thứ tự sau khi thả
    });

    // 2. Xử lý vị trí thả
    taskList.addEventListener('dragover', (e) => {
        e.preventDefault(); 
        if (!draggedItem || draggedItem.classList.contains('task-done')) return;

        const afterElement = getDragAfterElement(taskList, e.clientY);
        const currentItem = draggedItem;

        if (afterElement == null) {
            // Chèn vào cuối nhóm CHƯA HOÀN THÀNH (trước mục đã hoàn thành đầu tiên)
            const doneItems = taskList.querySelector('.task-item.task-done');
            if (doneItems) {
                taskList.insertBefore(currentItem, doneItems);
            } else {
                taskList.appendChild(currentItem);
            }
        } else {
            taskList.insertBefore(currentItem, afterElement);
        }
    });

    // Hàm tìm ra phần tử nằm ngay sau vị trí con trỏ chuột (CHỈ TRONG NHÓM CHƯA HOÀN THÀNH)
    function getDragAfterElement(container, y) {
        // Chỉ lấy các phần tử CHƯA HOÀN THÀNH để kéo thả trong nhóm đó
        const draggableElements = [...container.querySelectorAll('.task-item:not(.dragging):not(.task-done)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: -Infinity }).element;
    }

    // 3. Hàm lưu thứ tự mới vào database qua AJAX
    function saveNewOrder() {
        // CHỈ GỬI THỨ TỰ CỦA CÁC MỤC CHƯA HOÀN THÀNH (vì thứ tự hoàn thành không quan trọng)
        const items = taskList.querySelectorAll('.task-item:not(.task-done)');
        const newOrder = [];

        items.forEach((item, index) => {
            newOrder.push({
                id: item.dataset.id, 
                thutu: index + 1     
            });
        });

        if (newOrder.length === 0) return;

        // Gửi dữ liệu lên server (cần file update_thutu.php)
        fetch('update_thutu.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order: newOrder })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Đã cập nhật thứ tự thành công:', data.message);
            } else {
                console.error('Lỗi khi cập nhật thứ tự:', data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi kết nối hoặc xử lý:', error);
        });
    }
});
</script>
</body>
</html>
