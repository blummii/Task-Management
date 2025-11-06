<?php
include 'connect.php';
session_start();

// Kiểm tra phương thức và người dùng
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['iduser'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Truy cập không hợp lệ.']);
    exit;
}

// Lấy dữ liệu JSON
$data = json_decode(file_get_contents('php://input'), true);
$order = $data['order'] ?? [];

if (empty($order)) {
    echo json_encode(['status' => 'error', 'message' => 'Không có dữ liệu thứ tự được gửi.']);
    exit;
}

$ocon->begin_transaction(); // Bắt đầu transaction

try {
    $stmt = $ocon->prepare("UPDATE congviec SET thutu = ? WHERE id = ? AND iduser = ?");
    
    foreach ($order as $item) {
        $task_id = (int)$item['id'];
        $new_thutu = (int)$item['thutu'];
        $user_id = (int)$_SESSION['iduser'];

        // Cập nhật thứ tự cho từng công việc
        $stmt->bind_param("iii", $new_thutu, $task_id, $user_id);
        $stmt->execute();
    }
    
    $ocon->commit(); // Lưu thay đổi
    $stmt->close();
    
    echo json_encode(['status' => 'success', 'message' => 'Cập nhật thứ tự thành công.']);

} catch (Exception $e) {
    $ocon->rollback(); // Hoàn tác nếu có lỗi
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi server khi cập nhật: ' . $e->getMessage()]);
}
?>