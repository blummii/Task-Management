<?php
include 'connect.php';
$message = "";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $matkhau = password_hash($_POST['matkhau'], PASSWORD_DEFAULT);

    // Kiểm tra trùng email
    $check = $ocon->prepare("SELECT iduser FROM user WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email đã tồn tại!";
    } else {
        $stmt = $ocon->prepare("INSERT INTO user(hoten, email, matkhau) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $hoten, $email, $matkhau);
        if ($stmt->execute()) {
            header("Location: login.php?success=Đăng ký thành công! Mời đăng nhập.");
            exit();
        } else {
            $message = "Lỗi đăng ký!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
</head>

<body>

    <div class="register-box">
        <h2>Đăng ký tài khoản</h2>

        <?php if ($message): ?>
            <div class="danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="input-box">
                <label for="hoten">Họ tên</label>
                <input type="text" id="hoten" name="hoten" placeholder="Nhập họ tên..." required>
            </div>

            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email..." required>
            </div>

            <div class="input-box">
                <label for="matkhau">Mật khẩu</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit">Đăng ký</button>
        </form>

        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>

</body>
</html>


