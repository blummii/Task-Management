<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['iduser'])) { 
    header('Location: login.php'); 
    exit; 
}

if (!isset($_GET['id']) || !isset($_GET['action'])) { 
    header('Location: index.php'); 
    exit; 
}

$id = $_GET['id'];  // ví dụ: cv01
$action = $_GET['action'];
$user_id = $_SESSION['iduser'];
$status = ($action === 'done') ? 1 : 0;

// Kiểm tra kết nối
if (!$ocon) {
    die("Không thể kết nối cơ sở dữ liệu.");
}

// Debug nếu prepare lỗi
$stmt = $ocon->prepare("UPDATE congviec SET trangthai = ?, ngaycapnhat = NOW() WHERE id = ? AND iduser = ?");
if (!$stmt) {
    die("Lỗi prepare: " . $ocon->error);
}

$stmt->bind_param("isi", $status, $id, $user_id);

if ($stmt->execute()) {
    header('Location: index.php');
    exit;
} else {
    echo "Lỗi cập nhật: " . $stmt->error;
}
?>
