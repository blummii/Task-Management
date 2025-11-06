<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="side-bar">
    <div class="user-p">
        <img src="img/user.png" alt="User Avatar">
        <h4>@<?= htmlspecialchars($_SESSION['username'] ?? 'Khách') ?></h4>
    </div>

    <ul id="navList">
        <li>
            <a href="#">
                <i class="fa fa-tasks" aria-hidden="true"></i>
                <span>Công việc</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</nav>
