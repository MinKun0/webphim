<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Form</title>
  <link rel="stylesheet" href="css/login.css">

</head>
<body>

  <a href="index.php" class="close-btn">&times;</a>

  <div class="login-box">
    <form action="process_login.php" method="POST">
      <h2>Đăng Nhập</h2>
      <input type="text" name="username" placeholder="Tên tài khoản" required>
      <input type="password" name="password" placeholder="Mật khẩu" required>
      <button type="submit" class="btn">Xác nhận đăng nhập</button>
      <div class="register-link">
        Chưa có tài khoản?
        <a href="register.php">Đăng ký</a>
      </div>
    </form>
  </div>

</body>
</html>