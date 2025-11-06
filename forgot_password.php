<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = trim($_POST['email']);
    $subject = "Quên mật khẩu";
    $body = "Xin chào,\n\nBạn vừa yêu cầu quên mật khẩu. Đây là email thử nghiệm được gửi bằng PHPMailer.";
    $msgClass = "";

    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP của Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lephuongthao14072005@gmail.com';  // Gmail của bạn
            $mail->Password   = 'kxdhecaiaixlndjd';                // App Password (bỏ khoảng trắng)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Gửi - nhận
            $mail->setFrom('lephuongthao14072005@gmail.com', 'Hệ thống hỗ trợ');
            $mail->addAddress($to); // Email người nhận

            // Nội dung
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // Gửi mail
            $mail->send();
            $message = "✅ Đã gửi email thành công tới $to!";
            $msgClass = "success";
        } catch (Exception $e) {
            $message = "❌ Lỗi khi gửi email: " . $mail->ErrorInfo;
            $msgClass = "danger";
        }
    } else {
        $message = "⚠️ Email không hợp lệ!";
        $msgClass = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <!-- Dùng lại CSS của trang đăng ký -->
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="register-box">
    <h2>Quên mật khẩu</h2>

    <?php if (!empty($message)): ?>
        <div class="<?= $msgClass ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-box">
            <label for="email">Email người nhận</label>
            <input type="email" id="email" name="email" placeholder="Nhập email cần gửi..." required>
        </div>
        <button type="submit">Gửi email</button>
    </form>

    <p>Quay lại <a href="login.php">Đăng nhập</a></p>
</div>

</body>
</html>
