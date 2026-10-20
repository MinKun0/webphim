-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 08:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `price`, `old_price`, `image`, `description`, `rating`, `created_at`) VALUES
(1, 'Samsung Galaxy Z Flip7 512GB', 'Samsung', 28990000.00, 32990000.00, NULL, 'Màn hình gập Dynamic AMOLED 2X, 120Hz, camera 50MP, sạc nhanh 25W, pin 4000mAh', 5, '2026-05-06 16:52:46'),
(2, 'iPhone 17 Pro Max 256GB', 'Apple', 37590000.00, 39990000.00, NULL, 'Chip A19 Pro, camera 48MP, màn hình Super Retina XDR 120Hz, pin 4800mAh, sạc 35W', 5, '2026-05-06 16:52:46'),
(3, 'Samsung Galaxy S26 Ultra 12GB 256GB', 'Samsung', 32490000.00, 35990000.00, NULL, 'Chip Snapdragon 8 Gen 4, camera 200MP, S Pen tích hợp, pin 5500mAh, sạc 45W', 5, '2026-05-06 16:52:46'),
(4, 'OPPO Find N5 16GB 512GB', 'OPPO', 42990000.00, 46990000.00, NULL, 'Màn hình gập, chip Dimensity 9400, camera Hasselblad, sạc nhanh 100W, pin 5600mAh', 4, '2026-05-06 16:52:46'),
(5, 'Xiaomi 17 Ultra 16GB 512GB', 'Xiaomi', 27990000.00, 30990000.00, NULL, 'Chip Snapdragon 8 Gen 4, camera Leica 200MP, màn hình AMOLED 144Hz, pin 6000mAh, sạc 120W', 5, '2026-05-06 16:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `id_sp` int(10) NOT NULL,
  `ten_sp` varchar(50) NOT NULL,
  `anh_sp` varchar(100) NOT NULL,
  `tomtat_sp` varchar(200) NOT NULL,
  `chitiet_sp` text NOT NULL,
  `video_sp` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`id_sp`, `ten_sp`, `anh_sp`, `tomtat_sp`, `chitiet_sp`, `video_sp`) VALUES
(1, 'Giai Ngẫu Thiên Thành', 'img/sanpham/anh1.webp', '', '', 'https://youtu.be/N_Ku2fbVrzg?si=GFVhD1PvHCNrXlsa'),
(2, 'Bù Nhìn Bóng Đêm', 'img/sanpham/anh2.webp', '', '', ''),
(3, 'Nguyệt Lân Ỷ Kỷ', 'img/sanpham/anh3.webp', '', '', ''),
(4, 'Dục Vọng Thầm Kín', 'img/sanpham/anh4.webp', '', '', ''),
(5, 'Ôi! Tôi Là Thiếp!', 'img/sanpham/anh5.webp', '', '', ''),
(6, 'Giải  Mã Trọng Án', 'img/sanpham/anh6.webp', '', '', ''),
(7, 'Luật Sư Bóng Ma', 'img/sanpham/anh7.webp', '', '', ''),
(8, 'Băng Hồ Trọng Sinh', 'img/sanpham/anh8.webp', '', '', ''),
(9, 'Bạch Nhật Đế Đăng', 'img/sanpham/anh9.webp', '', '', ''),
(10, 'Cuốc Xe Âm Phủ', 'img/sanpham/anh10.webp', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham2`
--

CREATE TABLE `sanpham2` (
  `id_sp2` int(10) NOT NULL,
  `ten_sp2` varchar(50) NOT NULL,
  `anh_sp2` varchar(100) NOT NULL,
  `tomtat_sp2` varchar(200) NOT NULL,
  `chitiet_sp2` text NOT NULL,
  `video_sp2` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham2`
--

INSERT INTO `sanpham2` (`id_sp2`, `ten_sp2`, `anh_sp2`, `tomtat_sp2`, `chitiet_sp2`, `video_sp2`) VALUES
(1, '7 Viên Ngọc Rồng Z Kai', 'img/sanpham2/anh1.webp', '', '0', ''),
(2, 'Chú Thuật Hồi Chiến (Mùa 1)', 'img/sanpham2/anh2.webp', '', '0', ''),
(3, 'Đại Chiến Titan (Phần 1)', 'img/sanpham2/anh3.webp', '', '0', ''),
(4, 'Thanh Gươm Diệt Quỷ (Phần 1)', 'img/sanpham2/anh4.webp', '', '0', ''),
(5, 'Thanh Gươm Diệt Quỷ (Phần 3): Kỹ Viện Trấn', 'img/sanpham2/anh5.webp', '', '0', ''),
(6, 'Thanh Gươm Diệt Quỷ (Phần 4): Làng Thợ Rèn', 'img/sanpham2/anh6.webp', '', '0', ''),
(7, 'Black Clover (Phần 1)', 'img/sanpham2/anh7.webp', '', '', ''),
(8, 'Wistoria: Trượng và Kiếm ', 'img/sanpham2/anh8.webp', '', '0', ''),
(9, 'Đảo Hải Tặc (Phần 6): Đại Chiến Enies Lobby', 'img/sanpham2/anh9.webp', '', '0', ''),
(10, 'Đám Cưới Đom Đóm', 'img/sanpham2/anh10.webp\r\n', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham3`
--

CREATE TABLE `sanpham3` (
  `id_sp3` int(10) NOT NULL,
  `ten_sp3` varchar(50) NOT NULL,
  `anh_sp3` varchar(100) NOT NULL,
  `tomtat_sp3` varchar(200) NOT NULL,
  `chitiet_sp3` text NOT NULL,
  `video_sp3` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham3`
--

INSERT INTO `sanpham3` (`id_sp3`, `ten_sp3`, `anh_sp3`, `tomtat_sp3`, `chitiet_sp3`, `video_sp3`) VALUES
(1, 'Song Hỷ Lâm Nguy', 'img/sanpham3/anh1.webp\r\n', '', '', ''),
(2, 'Nhà Bà Nữ', 'img/sanpham3/anh2.webp\r\n', '', '', ''),
(3, 'Đẳng Cấp Thú Cưng', 'img/sanpham3/anh3.webp\r\n', '', '', ''),
(5, 'Pacific Rim: Trỗi Dậy', 'img/sanpham3/anh5.webp', '', '', ''),
(9, 'Người Nhện: Xa Nhà', 'img/sanpham3/anh9.webp', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

CREATE TABLE `taikhoan` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(225) NOT NULL,
  `role` enum('user','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`id`, `username`, `password`, `role`) VALUES
(1, 'Admin', '$2y$10$6XaWH.fnvfJTbcxsUXg8.uPTTZXP8t8sxpFX54Y0vJgo2c9naZ6.C', 'admin'),
(2, 'abc', '$2y$10$VrZKxLeK5Ku//iVLJtFt/u9UCLomL5sYfOLpCiuT4jxZA4VO4Aory', 'user'),
(3, 'abcd', '$2y$10$qVcu6FgosZg7zl0D0dlRMeXuqjB.WSGt0.6mms0/YduboeYUYAaeS', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`id_sp`);

--
-- Indexes for table `sanpham2`
--
ALTER TABLE `sanpham2`
  ADD PRIMARY KEY (`id_sp2`);

--
-- Indexes for table `sanpham3`
--
ALTER TABLE `sanpham3`
  ADD PRIMARY KEY (`id_sp3`);

--
-- Indexes for table `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `id_sp` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sanpham2`
--
ALTER TABLE `sanpham2`
  MODIFY `id_sp2` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sanpham3`
--
ALTER TABLE `sanpham3`
  MODIFY `id_sp3` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
