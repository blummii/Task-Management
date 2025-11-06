<?php
include 'connect.php';
$message = "";

session_start();

$errors = [];

// Khi người dùng nhấn nút đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['matkhau']; // tên input là 'matkhau' cho thống nhất

    if (empty($email) || empty($password)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Tìm user theo email
        $stmt = $ocon->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu (so sánh với password_hash)
        if ($user && $password === $user['matkhau']) {

            $_SESSION['iduser'] = $user['iduser'];
            $_SESSION['username'] = $user['hoten'];

            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Email hoặc mật khẩu không đúng.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css"> <!-- Thêm dòng này -->
</head>
<body>


    <div class="register-box">
        <h2>Đăng nhập</h2>

        <?php if (!empty($errors)): ?>
            <div class="danger"><?= implode("<br>", $errors) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email..." required>
            </div>

            <div class="input-box">
                <label for="matkhau">Mật khẩu</label>
                <input type="password" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit">Đăng nhập</button>
        </form>

        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        <p><a href="forgot_password.php">Quên mật khẩu?</a></p>

    </div>

</body>
</html>
