<?php
// Bắt buộc phải bắt đầu session để lấy iduser
session_start();
include 'connect.php'; // Kết nối CSDL

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['iduser'];
$task_id = $_GET['id'] ?? null; // Lấy ID công việc từ URL

if (empty($task_id) || !is_numeric($task_id)) {
    // Nếu không có ID hoặc ID không hợp lệ, chuyển hướng
    header("Location: index.php?error=ID công việc không hợp lệ.");
    exit();
}

$sql = "DELETE FROM congviec WHERE id = ? AND iduser = ?";

try {
    $stmt = $ocon->prepare($sql);
    
    // 'ii' nghĩa là 2 tham số đều là integer (task_id, current_user_id)
    $stmt->bind_param("ii", $task_id, $current_user_id);
    
    if ($stmt->execute()) {
        // Kiểm tra xem có hàng nào bị ảnh hưởng không (có task nào bị xóa không)
        if ($stmt->affected_rows > 0) {
            $message = "Xóa công việc thành công.";
            header("Location: index.php?success=" . urlencode($message));
        } else {
            // Task không tồn tại HOẶC task đó không thuộc về người dùng hiện tại
            $message = "Công việc không tồn tại.";
            header("Location: index.php?error=" . urlencode($message));
        }
    } else {
        $message = "Lỗi : " . $stmt->error;
        header("Location: index.php?error=" . urlencode($message));
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Ghi log lỗi chi tiết hơn nếu cần
    $message = "Lỗi : " . $e->getMessage();
    header("Location: index.php?error=" . urlencode($message));
}

$ocon->close();
exit();
?>