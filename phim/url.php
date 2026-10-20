<?php
switch ($home) {
    case 'home':
        include 'index.php';
        break;

    case 'phimbo':
        include 'phimbo.php';
        break;

    case 'anime':
        include 'anime.php';
        break;

    default:
        echo "<h2>404 - Trang không tồn tại</h2>";
        break;
}
?>