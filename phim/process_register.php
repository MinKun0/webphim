<?php
session_start();
include "connect.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$confirm = trim($_POST['confirm_password']);

if ($password != $confirm) {
    echo "Mật khẩu không khớp!";
    exit();
}

$check = mysqli_query($conn, "SELECT * FROM taikhoan WHERE username='$username'");

if (mysqli_num_rows($check) > 0) {
    echo "Tên tài khoản đã tồn tại!";
    exit();
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO taikhoan(username, password)
        VALUES('$username', '$hashed')";

if (mysqli_query($conn, $sql)) {

    $_SESSION['username'] = $username;

    header("Location: index.php");

} else {
    echo "Đăng ký thất bại!";
}
?>