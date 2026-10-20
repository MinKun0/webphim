<?php
session_start();
require_once "connect.php";

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM taikhoan WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    if (password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['role']));

        // Điều hướng theo quyền
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }

        exit;

    } else {
        echo "Sai mật khẩu!";
    }

} else {
    echo "Tài khoản không tồn tại!";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
