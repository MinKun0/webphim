<?php
session_start();
include "connect.php";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Form</title>
  <link rel="stylesheet" href="css/register.css">



</head>
<body>

  <a href="index.php" class="close-btn">&times;</a>

  <div class="register-box">
    <form action="process_register.php" method="POST">
    <h2>Đăng Kí</h2>
    <input type="text" name="username" placeholder="Tên tài khoản" require>

    <input type="password" name="password" placeholder="Mật khẩu" required>

    <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>

      <button type="submit" class="btn">Xác nhận đăng ký</button>

      <div class="login-link">
        Đã có tài khoản?
        <a href="login.php">Đăng nhập</a>
      </div>
    </form>
  </div>

</body>
</html>