<?php
session_start();
require_once 'connect.php';

/* =========================
   KIỂM TRA QUYỀN ADMIN
========================= */
if (
    !isset($_SESSION['username']) ||
    !isset($_SESSION['role']) ||
    strtolower($_SESSION['role']) !== 'admin'
) {
    header('Location: login.php');
    exit;
}

$thongBao = '';
$loaiThongBao = '';

/* =========================
   XỬ LÝ CẬP NHẬT ROLE / XÓA
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        $thongBao = 'ID tài khoản không hợp lệ!';
        $loaiThongBao = 'error';
    } elseif ($hanhDong === 'capnhat_role') {
        $roleMoi = $_POST['role'] ?? '';

        if (!in_array($roleMoi, ['admin', 'user'], true)) {
            $thongBao = 'Quyền tài khoản không hợp lệ!';
            $loaiThongBao = 'error';
        } else {
            // Lấy tài khoản đang được cập nhật
            $stmt = $conn->prepare(
                "SELECT username, role FROM taikhoan WHERE id = ?"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($usernameHienTai, $roleHienTai);
            $tonTai = $stmt->fetch();
            $stmt->close();

            if (!$tonTai) {
                $thongBao = 'Tài khoản không tồn tại!';
                $loaiThongBao = 'error';
            } elseif (
                $usernameHienTai === $_SESSION['username']
                && $roleMoi !== 'admin'
            ) {
                $thongBao = 'Bạn không thể tự hạ quyền admin của mình!';
                $loaiThongBao = 'error';
            } else {
                $stmt = $conn->prepare(
                    "UPDATE taikhoan SET role = ? WHERE id = ?"
                );
                $stmt->bind_param("si", $roleMoi, $id);

                if ($stmt->execute()) {
                    $thongBao = 'Cập nhật quyền tài khoản thành công!';
                    $loaiThongBao = 'success';
                } else {
                    $thongBao = 'Không thể cập nhật quyền tài khoản!';
                    $loaiThongBao = 'error';
                }

                $stmt->close();
            }
        }
    } elseif ($hanhDong === 'xoa') {
        // Lấy username của tài khoản cần xóa
        $stmt = $conn->prepare(
            "SELECT username FROM taikhoan WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($usernameCanXoa);
        $timThay = $stmt->fetch();
        $stmt->close();

        if (!$timThay) {
            $thongBao = 'Tài khoản không tồn tại!';
            $loaiThongBao = 'error';
        } elseif ($usernameCanXoa === $_SESSION['username']) {
            $thongBao = 'Bạn không thể xóa tài khoản đang đăng nhập!';
            $loaiThongBao = 'error';
        } else {
            $stmt = $conn->prepare(
                "DELETE FROM taikhoan WHERE id = ?"
            );
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $thongBao = 'Đã xóa tài khoản thành công!';
                $loaiThongBao = 'success';
            } else {
                $thongBao = 'Không thể xóa tài khoản!';
                $loaiThongBao = 'error';
            }

            $stmt->close();
        }
    }
}

/* =========================
   TÌM KIẾM VÀ LẤY DANH SÁCH
========================= */
$keyword = trim($_GET['keyword'] ?? '');

if ($keyword !== '') {
    $tuKhoa = '%' . $keyword . '%';

    $stmt = $conn->prepare(
        "SELECT id, username, role
         FROM taikhoan
         WHERE username LIKE ?
         ORDER BY id DESC"
    );
    $stmt->bind_param("s", $tuKhoa);
} else {
    $stmt = $conn->prepare(
        "SELECT id, username, role
         FROM taikhoan
         ORDER BY id DESC"
    );
}

$stmt->execute();
$stmt->bind_result($idTaiKhoan, $tenTaiKhoan, $quyenTaiKhoan);

$danhSachTaiKhoan = [];

while ($stmt->fetch()) {
    $danhSachTaiKhoan[] = [
        'id' => (int)$idTaiKhoan,
        'username' => (string)$tenTaiKhoan,
        'role' => (string)$quyenTaiKhoan
    ];
}

$stmt->close();

/* =========================
   THỐNG KÊ TOÀN BỘ TÀI KHOẢN
========================= */
$stmt = $conn->prepare(
    "SELECT
        COUNT(*),
        COALESCE(SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END), 0)
     FROM taikhoan"
);

$stmt->execute();
$stmt->bind_result($tongTatCa, $tongAdmin, $tongUser);
$stmt->fetch();
$stmt->close();

$tongTatCa = (int)$tongTatCa;
$tongAdmin = (int)$tongAdmin;
$tongUser = (int)$tongUser;

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$urlDanhSach = 'quanlytaikhoan.php';
if ($keyword !== '') {
    $urlDanhSach .= '?keyword=' . urlencode($keyword);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản</title>

    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/quanlytaikhoan.css">
</head>

<body>
<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <p class="admin-label">QUẢN TRỊ HỆ THỐNG</p>

        <a href="admin.php">▦ Tổng quan</a>
        <a href="quanlyphim.php">🎬 Quản lý phim</a>
        <a href="quanlytaikhoan.php" class="active">
            👥 Quản lý tài khoản
        </a>
        <a href="quanlythongbao.php">🔔 Quản lý thông báo</a>

        <a href="index.php" class="back-home">← Về trang chủ</a>
        <a href="logout.php" class="logout">Đăng xuất</a>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="admin-main">

        <div class="account-header">
            <div>
                <h2>Quản lý tài khoản</h2>
                <p>Xem danh sách và quản lý quyền người dùng trên website.</p>
            </div>

            <div class="admin-account">
                <span>Xin chào, <?= e($_SESSION['username']) ?></span>
                <span class="admin-badge">ADMIN</span>
            </div>
        </div>

        <!-- THỐNG KÊ -->
        <div class="account-stats">
            <div class="account-stat-card">
                <span class="account-stat-icon">👥</span>
                <div>
                    <p>Tổng tài khoản</p>
                    <h2><?= $tongTatCa ?></h2>
                </div>
            </div>

            <div class="account-stat-card">
                <span class="account-stat-icon">🛡️</span>
                <div>
                    <p>Quản trị viên</p>
                    <h2><?= $tongAdmin ?></h2>
                </div>
            </div>

            <div class="account-stat-card">
                <span class="account-stat-icon">👤</span>
                <div>
                    <p>Người dùng</p>
                    <h2><?= $tongUser ?></h2>
                </div>
            </div>
        </div>

        <!-- THÔNG BÁO -->
        <?php if ($thongBao !== ''): ?>
            <div class="account-message <?= e($loaiThongBao) ?>">
                <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <!-- DANH SÁCH TÀI KHOẢN -->
        <section class="account-panel">

            <div class="account-panel-header">
                <div>
                    <h2>Danh sách tài khoản</h2>
                    <p>
                        <?php if ($keyword !== ''): ?>
                            Kết quả tìm kiếm cho:
                            <strong><?= e($keyword) ?></strong>
                        <?php else: ?>
                            Danh sách tất cả tài khoản trong hệ thống.
                        <?php endif; ?>
                    </p>
                </div>

                <span class="account-count">
                    <?= count($danhSachTaiKhoan) ?> kết quả
                </span>
            </div>

            <!-- TÌM KIẾM -->
            <form action="quanlytaikhoan.php" method="GET"
                  class="account-search">

                <input
                    type="search"
                    name="keyword"
                    placeholder="Nhập username cần tìm..."
                    value="<?= e($keyword) ?>"
                >

                <button type="submit">Tìm kiếm</button>

                <?php if ($keyword !== ''): ?>
                    <a href="quanlytaikhoan.php" class="account-reset">
                        Xóa tìm kiếm
                    </a>
                <?php endif; ?>
            </form>

            <!-- BẢNG TÀI KHOẢN -->
            <div class="account-table-wrap">
                <table class="account-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!empty($danhSachTaiKhoan)): ?>

                        <?php foreach ($danhSachTaiKhoan as $tk): ?>
                            <tr>
                                <!-- ID -->
                                <td>
                                    <span class="account-id"> #<?= (int)$tk['id'] ?> </span>
                                </td>

                                <!-- USERNAME -->
                                <td>
                                    <div class="account-user">
                                        <span class="account-avatar">
                                            <?= e(
                                                strtoupper(
                                                    substr($tk['username'], 0, 1)
                                                )
                                            ) ?>
                                        </span>

                                        <strong>
                                            <?= e($tk['username']) ?>
                                        </strong>
                                    </div>
                                </td>

                                <!-- PASSWORD: KHÔNG HIỂN THỊ MẬT KHẨU THẬT -->
                                <td>
                                    <span class="password-hidden">
                                        ••••••••
                                    </span>
                                </td>

                                <!-- ROLE -->
                                <td>
                                    <span class="role-badge <?= $tk['role'] === 'admin'
                                        ? 'role-admin'
                                        : 'role-user' ?>">
                                        <?= e($tk['role']) ?>
                                    </span>
                                </td>

                                <!-- THAO TÁC -->
                                <td>
                                    <div class="account-actions">

                                        <!-- CẬP NHẬT QUYỀN -->
                                        <form method="POST" action="<?= e($urlDanhSach) ?>" class="role-form">

                                            <input type="hidden" name="action" value="capnhat_role">
                                            <input type="hidden" name="id" value="<?= (int)$tk['id'] ?>">
                                            <select name="role" aria-label="Chọn quyền tài khoản">

                                                <option value="user"
                                                    <?= $tk['role'] === 'user'
                                                        ? 'selected'
                                                        : '' ?>>
                                                    User
                                                </option>

                                                <option value="admin"
                                                    <?= $tk['role'] === 'admin'
                                                        ? 'selected'
                                                        : '' ?>>
                                                    Admin
                                                </option>
                                            </select>

                                            <button type="submit"
                                                    class="btn-role"
                                                    onclick="return confirm('Bạn có chắc muốn cập nhật quyền tài khoản này?')">
                                                Lưu quyền
                                            </button>
                                        </form>

                                        <!-- XÓA TÀI KHOẢN -->
                                        <form method="POST"
                                              action="<?= e($urlDanhSach) ?>"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">

                                            <input type="hidden" name="action" value="xoa">
                                            <input type="hidden" name="id" value="<?= (int)$tk['id'] ?>">

                                            <button type="submit" class="btn-delete"> Xóa </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="account-empty">
                                <span>🔎</span>
                                <p>Không tìm thấy tài khoản phù hợp.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="account-panel-footer">
                Hiển thị <?= count($danhSachTaiKhoan) ?> tài khoản

                <?php if ($keyword !== ''): ?>
                    · <a href="quanlytaikhoan.php">
                        Xem tất cả tài khoản
                    </a>
                <?php endif; ?>
            </div>

        </section>
    </main>
</div>
</body>
</html>
