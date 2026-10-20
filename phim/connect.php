<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "data";

// Tạo connection
$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>