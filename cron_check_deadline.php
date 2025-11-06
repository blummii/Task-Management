<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


include 'connect.php'; 

// --- CẤU HÌNH GMAIL (Lấy từ forgot_password.php) ---
$smtp_user = 'lephuongthao14072005@gmail.com';  // Email người gửi
$smtp_pass = 'kxdhecaiaixlndjd';                // App Password
$from_email = 'lephuongthao14072005@gmail.com';
$from_name = 'Hệ thống Quản lý Công việc';
// --------------------------------------------------------

// Xác định các ngày cần kiểm tra
$current_date = date('Y-m-d');
$tomorrow_date = date('Y-m-d', strtotime('+1 day'));

$sql = "
    SELECT 
        c.tieude, 
        c.hanchot, 
        u.email 
    FROM congviec c
    JOIN user u ON c.iduser = u.iduser
    WHERE c.trangthai = 0 
    AND (c.hanchot = ? OR c.hanchot = ?)
";

// Sử dụng Prepared Statement để chống SQL Injection
if ($stmt = $ocon->prepare($sql)) {
    $stmt->bind_param("ss", $current_date, $tomorrow_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_user; 
            $mail->Password   = $smtp_pass; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($from_email, $from_name);
            $mail->isHTML(true); // Gửi email dưới dạng HTML

            while ($task = $result->fetch_assoc()) {
                // Tạo nội dung email
                $email_body = "
                    <h2>CẢNH BÁO DEADLINE SẮP TỚI!</h2>
                    <p>Công việc **'{$task['tieude']}'** của bạn sắp đến hạn.</p>
                    <ul>
                        <li>**Deadline:** {$task['hanchot']}</li>
                        <li>**Trạng thái:** Đang làm</li>
                    </ul>
                    <p>Hãy hoàn thành công việc đúng hạn!</p>
                ";
                
                // Gán người nhận và nội dung cho từng email
                $mail->clearAllRecipients(); 
                $mail->addAddress($task['email']); 
                $mail->Subject = "⚠️ Cảnh báo Deadline: {$task['tieude']} (Hạn chót: {$task['hanchot']})";
                $mail->Body    = $email_body;

                if (!$mail->send()) {
                    // Ghi log lỗi nếu không gửi được
                    error_log("CRON JOB: Lỗi gửi mail đến {$task['email']}: " . $mail->ErrorInfo);
                }
            }
        } catch (Exception $e) {
            error_log("CRON JOB: Lỗi cấu hình PHPMailer: " . $e->getMessage());
        }
    }
    $stmt->close();
} else {
    error_log("CRON JOB: Lỗi chuẩn bị truy vấn CSDL: " . $ocon->error);
}

$ocon->close();
?>