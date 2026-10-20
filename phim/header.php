<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Header</title>
    <link rel="stylesheet" href="css/header.css">
</head>
<body>

<header>

    <div class="logo"></div>

    <nav>
        <a href="index.php">Trang chủ</a>
        <a href="phimle.php">Phim lẻ</a>
        <a href="phimbo.php">Phim bộ</a>
        <a href="anime.php">Anime</a>

        <a href="timkiem.php" class="timkiem-btn" title="Tìm kiếm">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"/><line x1="16.65" y1="16.65" x2="21" y2="21"/>
            </svg>
        </a>

        <div class="notification-box">
            <button class="notification-btn" title="Thông báo" onclick="toggleNotification()">

                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>

                <span class="notification-count"></span>
            </button>

            <!-- Danh sách thông báo -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <strong>Thông báo</strong>
                </div>

                <div class="notification-item">
                    <span class="notification-icon">🎬</span>
                    <div>
                        <strong>Phim mới</strong>
                        <p>Khám phá những bộ phim mới.</p>
                        <small>Vừa xong</small>
                    </div>
                </div>

                <div class="notification-item">
                    <span class="notification-icon">🔔</span>
                    <div>
                        <strong>Chào mừng bạn!</strong>
                        <p>Chúc bạn có những phút giây giải trí vui vẻ.</p>
                        <small>Hôm nay</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-modal-overlay" id="loginModal">
            <div class="login-modal">
                <h2>Đăng nhập tài khoản</h2>
                <p>Bạn cần thực hiện đăng nhập để tiếp tục sử dụng dịch vụ</p>

                <div class="login-modal-buttons">
                    <button class="modal-close-btn"
                            onclick="closeLoginModal()">
                        Đóng
                    </button>

                    <button class="modal-login-btn"
                            onclick="window.location.href='login.php'">
                        Đăng nhập
                    </button>
                </div>

            </div>
        </div>

        <!-- Đăng nhập tài khoản admin -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
            <a href="admin.php">Quản trị</a>
        <?php } ?>

        <!-- Đăng nhập / thông tin người dùng -->
        <?php if (isset($_SESSION['username'])) { ?>

            <div class="user-box">
                <span class="user-name">
                    Xin chào, <?php echo $_SESSION['username']; ?>
                </span>

                <button class="logout-btn"
                        onclick="window.location.href='logout.php'">
                    Đăng xuất
                </button>
            </div>

        <?php } else { ?>

            <button class="login-btn"
                    onclick="window.location.href='login.php'">
                Đăng nhập
            </button>

        <?php } ?>

    </nav>

</header>

<script>
function toggleNotification() {
    const daDangNhap = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

    if (!daDangNhap) {
        document.getElementById('loginModal').classList.add('show');
        return;
    }

    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) dropdown.classList.toggle('show');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('show');
}

document.addEventListener('click', function(event) {
    const box = document.querySelector('.notification-box');
    const dropdown = document.getElementById('notificationDropdown');

    if (box && dropdown && !box.contains(event.target)) {
        dropdown.classList.remove('show');
    }

    const modal = document.getElementById('loginModal');
    if (modal && event.target === modal) closeLoginModal();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeLoginModal();
});
</script>

<script src="js/script.js"></script>

</body>
</html>
```