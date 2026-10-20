<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'admin') {
    http_response_code(403);
    exit('Bạn không có quyền truy cập!');
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Danh sách bảng và tên cột được phép thao tác
$tables = [
    'sanpham' => [
        'table' => 'sanpham',
        'id' => 'id_sp',
        'name' => 'ten_sp',
        'image' => 'anh_sp',
        'summary' => 'tomtat_sp',
        'detail' => 'chitiet_sp',
        'video' => 'video_sp',
        'label' => 'Phim bộ'
    ],
    'sanpham2' => [
        'table' => 'sanpham2',
        'id' => 'id_sp2',
        'name' => 'ten_sp2',
        'image' => 'anh_sp2',
        'summary' => 'tomtat_sp2',
        'detail' => 'chitiet_sp2',
        'video' => 'video_sp2',
        'label' => 'Anime'
    ],
    'sanpham3' => [
        'table' => 'sanpham3',
        'id' => 'id_sp3',
        'name' => 'ten_sp3',
        'image' => 'anh_sp3',
        'summary' => 'tomtat_sp3',
        'detail' => 'chitiet_sp3',
        'video' => 'video_sp3',
        'label' => 'Phim lẻ'
    ]
];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf_token'];
$thongBao = '';
$loi = '';

// ==================== XỬ LÝ THÊM / SỬA / XÓA ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($csrf, $token)) {
        $loi = 'Yêu cầu không hợp lệ. Vui lòng tải lại trang.';
    } else {
        $action = $_POST['action'] ?? '';
        $nguon = $_POST['nguon'] ?? '';

        if (!isset($tables[$nguon])) {
            $loi = 'Nguồn phim không hợp lệ.';
        } else {
            $cfg = $tables[$nguon];
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            // XÓA PHIM
            if ($action === 'delete') {
                if (!$id || $id <= 0) {
                    $loi = 'ID phim không hợp lệ.';
                } else {
                    $sql = "DELETE FROM `{$cfg['table']}`
                            WHERE `{$cfg['id']}` = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $id);

                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $stmt->close();
                            header('Location: quanlyphim.php?msg=xoa');
                            exit;
                        } else {
                            $loi = 'Không tìm thấy phim cần xóa.';
                        }
                    } else {
                        $loi = 'Không thể xóa phim.';
                    }

                    $stmt->close();
                }
            }

            // THÊM / CẬP NHẬT PHIM
            elseif ($action === 'save') {
                $ten = trim($_POST['ten'] ?? '');
                $anh = trim($_POST['anh'] ?? '');
                $tomtat = trim($_POST['tomtat'] ?? '');
                $chitiet = trim($_POST['chitiet'] ?? '');
                $video = trim($_POST['video'] ?? '');
                $mode = $_POST['mode'] ?? 'add';

                if ($ten === '' || $anh === '') {
                    $loi = 'Tên phim và đường dẫn ảnh không được để trống.';
                } elseif (!in_array($mode, ['add', 'edit'], true)) {
                    $loi = 'Thao tác không hợp lệ.';
                } elseif (
                    $mode === 'edit' && (!$id || $id <= 0)
                ) {
                    $loi = 'ID phim không hợp lệ.';
                } else {
                    if ($mode === 'add') {
                        $sql = "INSERT INTO `{$cfg['table']}`
                                (`{$cfg['name']}`,
                                 `{$cfg['image']}`,
                                 `{$cfg['summary']}`,
                                 `{$cfg['detail']}`,
                                 `{$cfg['video']}`)
                                VALUES (?, ?, ?, ?, ?)";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param(
                            'sssss',
                            $ten,
                            $anh,
                            $tomtat,
                            $chitiet,
                            $video
                        );
                    } else {
                        $sql = "UPDATE `{$cfg['table']}`
                                SET `{$cfg['name']}` = ?,
                                    `{$cfg['image']}` = ?,
                                    `{$cfg['summary']}` = ?,
                                    `{$cfg['detail']}` = ?,
                                    `{$cfg['video']}` = ?
                                WHERE `{$cfg['id']}` = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param(
                            'sssssi',
                            $ten,
                            $anh,
                            $tomtat,
                            $chitiet,
                            $video,
                            $id
                        );
                    }

                    if ($stmt->execute()) {
                        $stmt->close();

                        header(
                            'Location: quanlyphim.php?msg=' .
                            ($mode === 'add' ? 'them' : 'sua')
                        );
                        exit;
                    } else {
                        $loi = 'Không thể lưu phim. Hãy kiểm tra cấu trúc bảng.';
                        $stmt->close();
                    }
                }
            } else {
                $loi = 'Thao tác không hợp lệ.';
            }
        }
    }
}

// Thông báo sau khi chuyển hướng
$messages = [
    'them' => 'Đã thêm phim thành công.',
    'sua' => 'Đã cập nhật phim thành công.',
    'xoa' => 'Đã xóa phim thành công.'
];

$msg = $_GET['msg'] ?? '';

if (isset($messages[$msg])) {
    $thongBao = $messages[$msg];
}

// ==================== LẤY PHIM CẦN SỬA ====================
$mode = $_GET['action'] ?? '';
$editMovie = null;
$editNguon = $_GET['nguon'] ?? '';

if ($mode === 'edit') {
    $editId = filter_var(
        $_GET['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$editId || $editId <= 0 || !isset($tables[$editNguon])) {
        $loi = 'Thông tin phim cần sửa không hợp lệ.';
        $mode = '';
    } else {
        $cfg = $tables[$editNguon];

        $sql = "SELECT
                    `{$cfg['id']}` AS id,
                    `{$cfg['name']}` AS ten,
                    `{$cfg['image']}` AS anh,
                    `{$cfg['summary']}` AS tomtat,
                    `{$cfg['detail']}` AS chitiet,
                    `{$cfg['video']}` AS video
                FROM `{$cfg['table']}`
                WHERE `{$cfg['id']}` = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $editMovie = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$editMovie) {
            $loi = 'Không tìm thấy phim cần sửa.';
            $mode = '';
        }
    }
}

// ==================== LỌC DANH SÁCH PHIM ====================
$nguonLoc = $_GET['nguon'] ?? 'tatca';

if (!in_array(
    $nguonLoc,
    ['tatca', 'sanpham', 'sanpham2', 'sanpham3'],
    true
)) {
    $nguonLoc = 'tatca';
}

$queries = [];

foreach ($tables as $key => $cfg) {
    if ($nguonLoc !== 'tatca' && $nguonLoc !== $key) {
        continue;
    }

    $queries[] = "
        SELECT
            `{$cfg['id']}` AS id,
            `{$cfg['name']}` AS ten,
            `{$cfg['image']}` AS anh,
            `{$cfg['summary']}` AS tomtat,
            '{$key}' AS nguon
        FROM `{$cfg['table']}`
    ";
}

$sql = implode(' UNION ALL ', $queries);
$sql = "SELECT * FROM ({$sql}) AS ds_phim ORDER BY ten ASC";

$result = $conn->query($sql);
$phimList = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $phimList[] = $row;
    }
}

// ==================== THỐNG KÊ ====================
$stats = [];

foreach ($tables as $key => $cfg) {
    $res = $conn->query(
        "SELECT COUNT(*) AS tong FROM `{$cfg['table']}`"
    );

    $stats[$key] = $res
        ? (int)$res->fetch_assoc()['tong']
        : 0;
}

$tongPhim = array_sum($stats);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý phim - Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/quanlyphim.css">
</head>
<body>
    
<div class="admin-layout">

    <aside class="sidebar">
        <p class="admin-label">QUẢN TRỊ HỆ THỐNG</p>

        <a href="admin.php">▦ Tổng quan</a>
        <a href="quanlyphim.php" class="active">🎬 Quản lý phim</a>
        <a href="quanlytaikhoan.php">👥 Quản lý tài khoản</a>
        <a href="quanlythongbao.php">🔔 Quản lý thông báo</a>

        <a href="index.php" class="back-home">← Về trang chủ</a>
        <a href="logout.php" class="logout">Đăng xuất</a>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h2>Quản lý phim</h2>
                <p>Thêm, sửa, xóa và quản lý phim.</p>
            </div>

            <div class="admin-account">
                <span>Xin chào, <?= e($_SESSION['username']) ?></span>
                <span class="admin-badge">ADMIN</span>
            </div>
        </header>

        <section class="admin-content">

            <?php if ($thongBao !== ''): ?>
                <div class="notice"><?= e($thongBao) ?></div>
            <?php endif; ?>

            <?php if ($loi !== ''): ?>
                <div class="error"><?= e($loi) ?></div>
            <?php endif; ?>


            <?php if (in_array($mode, ['add', 'edit'], true)): ?>
                <?php
                $isEdit = $mode === 'edit';
                $formNguon = $isEdit ? $editNguon : ($_POST['nguon'] ?? 'sanpham');
                if (!isset($tables[$formNguon])) {
                    $formNguon = 'sanpham';
                }
                $formData = $editMovie ?? [];
                ?>

                <div class="movie-form-card">
                    <h2><?= $isEdit ? 'Sửa thông tin phim' : 'Thêm phim mới' ?></h2>

                    <form method="POST" action="quanlyphim.php" class="movie-form">
                        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="mode" value="<?= $isEdit ? 'edit' : 'add' ?>">

                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= (int)$formData['id'] ?>">
                        <?php endif; ?>

                        <div class="field full">
                            <label>Thể loại / bảng lưu phim</label>
                            <select name="nguon" required <?= $isEdit ? 'disabled' : '' ?>>
                                <?php foreach ($tables as $key => $cfg): ?>
                                    <option value="<?= e($key) ?>"
                                        <?= $formNguon === $key ? 'selected' : '' ?>>
                                        <?= e($cfg['label']) ?> (<?= e($key) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if ($isEdit): ?>
                                <input type="hidden" name="nguon" value="<?= e($formNguon) ?>">
                                <small>Đang sửa phim trong bảng <?= e($formNguon) ?>.</small>
                            <?php endif; ?>
                        </div>

                        <div class="field full">
                            <label>Tên phim *</label>
                            <input
                                type="text"
                                name="ten"
                                maxlength="200"
                                required
                                value="<?= e($formData['ten'] ?? '') ?>">
                        </div>

                        <div class="field full">
                            <label>Đường dẫn ảnh phim *</label>
                            <input type="text" name="anh" required placeholder="Ví dụ: img/sanpham/anh1.jpg"
                                value="<?= e($formData['anh'] ?? '') ?>">
                        </div>

                        <div class="field full">
                            <label>Tóm tắt phim</label>
                            <textarea name="tomtat"><?= e($formData['tomtat'] ?? '') ?></textarea>
                        </div>

                        <div class="field full">
                            <label>Chi tiết phim</label>
                            <textarea name="chitiet"><?= e($formData['chitiet'] ?? '') ?></textarea>
                        </div>

                        <div class="field full">
                            <label>Đường dẫn video / nguồn phát</label>
                            <input type="text" name="video" placeholder="Nhập đường dẫn video nếu có"
                                value="<?= e($formData['video'] ?? '') ?>">
                        </div>

                        <div class="field full">
                            <div class="form-buttons">
                                <button class="btn btn-add" type="submit">
                                    <?= $isEdit ? 'Lưu thay đổi' : 'Thêm phim' ?>
                                </button>

                                <a class="btn btn-cancel" href="quanlyphim.php">
                                    Hủy
                                </a>
                            </div>

                        </div>

                    </form>

                </div>

            <?php endif; ?>

            <div class="movie-toolbar">
                <div>
                    <h2>Danh sách phim</h2>
                    <p class="movie-count">
                        Tổng số phim đang hiển thị:
                        <strong><?= count($phimList) ?></strong>
                    </p>
                </div>

                <div class="movie-filter">
                    <form method="GET" action="quanlyphim.php" class="movie-filter">
                        <select name="nguon">
                            <option value="tatca" <?= $nguonLoc === 'tatca' ? 'selected' : '' ?>> Tất cả phim </option>
                            <option value="sanpham" <?= $nguonLoc === 'sanpham' ? 'selected' : '' ?>> Phim bộ </option>
                            <option value="sanpham2" <?= $nguonLoc === 'sanpham2' ? 'selected' : '' ?>> Anime </option>
                            <option value="sanpham3" <?= $nguonLoc === 'sanpham3' ? 'selected' : '' ?>> Phim lẻ </option>
                        </select>

                        <button type="submit">Lọc phim</button>
                    </form>

                    <a href="quanlyphim.php?action=add" class="btn btn-add"> + Thêm phim </a>

                </div>
            </div>

            <div class="movie-table-wrap">
                <table class="movie-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh phim</th>
                            <th>Tên phim</th>
                            <th>Mô tả</th>
                            <th>Bảng dữ liệu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (empty($phimList)): ?>
                        <tr>
                            <td colspan="6" class="movie-empty">
                                Chưa có phim trong mục này.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($phimList as $phim): ?>
                            <tr>
                                <td><?= (int)$phim['id'] ?></td>
                                <td>
                                    <img
                                        class="movie-poster"
                                        src="<?= e($phim['anh']) ?>"
                                        alt="<?= e($phim['ten']) ?>"
                                        loading="lazy">
                                </td>
                                <td><?= e($phim['ten']) ?></td>
                                <td><?= e($phim['tomtat']) ?></td>
                                <td>
                                    <span class="source-badge">
                                        <?= e($tables[$phim['nguon']]['label']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="movie-actions">
                                        <a
                                            class="btn btn-edit"
                                            href="quanlyphim.php?action=edit&amp;id=<?= (int)$phim['id'] ?>&amp;nguon=<?= e($phim['nguon']) ?>">
                                            Sửa
                                        </a>

                                        <form method="POST" action="quanlyphim.php"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa phim này?');">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int)$phim['id'] ?>">
                                            <input type="hidden" name="nguon" value="<?= e($phim['nguon']) ?>">

                                            <button type="submit" class="btn btn-delete"> Xóa </button>
                                            
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>
                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>
