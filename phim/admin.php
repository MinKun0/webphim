<?php
// Khởi động session nếu chưa hoạt động
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// 2. Kiểm tra quyền admin
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin') {
    http_response_code(403);
    exit('Bạn không có quyền truy cập!');
}

// Hàm đếm số bản ghi trong bảng
function demSoPhim($conn, $tenBang) {
    // Chỉ sử dụng tên bảng đã xác định trong chương trình
    $cacBangHopLe = ['sanpham', 'sanpham2', 'sanpham3'];

    if (!in_array($tenBang, $cacBangHopLe, true)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) AS tong FROM `$tenBang`";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['tong'];
    }

    return 0;
}

// 3. Đếm phim trong 3 bảng
$tongPhim1 = demSoPhim($conn, 'sanpham');
$tongPhim2 = demSoPhim($conn, 'sanpham2');
$tongPhim3 = demSoPhim($conn, 'sanpham3');

// 4. Tổng số phim của cả 3 bảng
$tongPhim = $tongPhim1 + $tongPhim2 + $tongPhim3;

// 5. Đếm tổng tài khoản
$sqlTaiKhoan = "SELECT COUNT(*) AS tong FROM taikhoan";
$resultTaiKhoan = mysqli_query($conn, $sqlTaiKhoan);

$tongTaiKhoan = 0;

if ($resultTaiKhoan) {
    $rowTaiKhoan = mysqli_fetch_assoc($resultTaiKhoan);
    $tongTaiKhoan = (int)$rowTaiKhoan['tong'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị viên</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <p class="admin-label">QUẢN TRỊ HỆ THỐNG</p>

        <a href="admin.php" class="active">▦ Tổng quan</a>
        <a href="quanlyphim.php">🎬 Quản lý phim</a>
        <a href="quanlytaikhoan.php">👥 Quản lý tài khoản</a>
        <a href="quanlythongbao.php">🔔 Quản lý thông báo</a>

        <a href="index.php" class="back-home">← Về trang chủ</a>
        <a href="logout.php" class="logout">Đăng xuất</a>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2>Tổng quan</h2>
                <p>Chào mừng bạn đến với trang quản trị .</p>
            </div>

            <div class="admin-account">
                <span>Xin chào, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="admin-badge">ADMIN</span>
            </div>
        </header>

        <section class="stats">
            <div class="stat-card">
                <div class="stat-icon">🎬</div>
                <div>
                    <p>Tổng số phim</p>
                    <h2><?= $tongPhim ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🎞️</div>
                <div>
                    <p>Phim bộ</p>
                    <h2><?= $tongPhim1 ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🍥</div>
                <div>
                    <p>Anime</p>
                    <h2><?= $tongPhim2 ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📺</div>
                <div>
                    <p>Phim lẻ</p>
                    <h2><?= $tongPhim3 ?></h2>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div>
                    <p>Tổng tài khoản</p>
                    <h2><?= $tongTaiKhoan ?></h2>
                </div>
            </div>
        </section>

        <section class="admin-content">
            <h2>Chức năng quản trị</h2>

            <div class="function-grid">
                <a href="quanlyphim.php" class="function-card">
                    <span>🎬</span>
                    <h3>Quản lý phim</h3>
                    <p>Thêm, sửa, xóa và cập nhật thông tin phim.</p>
                    <b>Mở chức năng →</b>
                </a>

                <a href="quanlytaikhoan.php" class="function-card">
                    <span>👥</span>
                    <h3>Quản lý tài khoản</h3>
                    <p>Xem và quản lý tài khoản người dùng.</p>
                    <b>Mở chức năng →</b>
                </a>

                <a href="quanlythongbao.php" class="function-card">
                    <span>🔔</span>
                    <h3>Quản lý thông báo</h3>
                    <p>Tạo và quản lý thông báo trên website.</p>
                    <b>Mở chức năng →</b>
                </a>
            </div>
        </section>

    </main>
</div>

</body>
</html>
