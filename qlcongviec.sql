-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2025 at 11:29 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qlcongviec`
--

-- --------------------------------------------------------

--
-- Table structure for table `congviec`
--

CREATE TABLE `congviec` (
  `id` int(11) NOT NULL,
  `tieude` varchar(255) NOT NULL,
  `mota` text NOT NULL,
  `hanchot` date NOT NULL,
  `mduutien` enum('Thấp','Trung bình','Cao','') NOT NULL,
  `trangthai` tinyint(1) NOT NULL DEFAULT 0,
  `thutu` int(11) NOT NULL,
  `ngaytao` date NOT NULL DEFAULT current_timestamp(),
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `iduser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `congviec`
--

INSERT INTO `congviec` (`id`, `tieude`, `mota`, `hanchot`, `mduutien`, `trangthai`, `thutu`, `ngaytao`, `ngaycapnhat`, `iduser`) VALUES
(1, 'Thanh toán tiền trọ', 'Kiểm tra hóa đơn điện, nước và chuyển tiền cho chủ trọ trước ngày 5 hàng tháng', '2025-11-05', 'Trung bình', 1, 2, '2025-10-31', '2025-11-03 10:56:08', 1),
(2, 'Đi siêu thị', 'Mua đò ăn sáng, rau củ, sữa và trứng dự trữ cho cả tuần', '2025-11-03', 'Thấp', 0, 5, '2025-11-01', '2025-11-01 07:30:08', 1),
(3, 'Giặt quần áo', 'Giặt đồ trắng riêng, giặt chăn và gối ôm', '2025-11-02', 'Trung bình', 1, 4, '2025-10-29', '2025-11-02 08:00:02', 1),
(4, 'Dọn phòng trọ', 'Lau sàn, sắp xếp bàn học và đổ rác', '2025-11-02', 'Cao', 1, 3, '2025-10-30', '2025-11-02 15:00:02', 1),
(5, 'Lên kế hoạch chi tiêu tháng mới', 'Thống kê các khoản cần chi tiêu: ăn uống, học tập, tiết kiệm, giải trí', '2025-11-04', 'Thấp', 0, 6, '2025-11-02', '2025-11-02 16:03:31', 1),
(6, 'Học tiếng anh', 'Học 1 tiếng mỗi ngày: luyện nghe tiếng anh trong 20 phút và học từ vựng, học cấu trúc trong 40 phút', '2025-11-03', 'Cao', 1, 1, '2025-11-02', '2025-11-03 21:03:31', 1),
(7, 'Đi cắt tóc', 'Cắt tóc gọn gàng, cắt tóc mái mới', '2025-11-02', 'Cao', 1, 1, '2025-11-01', '2025-11-02 14:38:47', 3),
(8, 'Đi siêu thị', 'Đi siêu thị mua đồ dùng cá nhân: bàn chải đánh răng, sữa rửa mặt, dầu gội', '2025-11-07', 'Trung bình', 0, 2, '2025-11-04', '2025-11-04 17:08:47', 3),
(9, 'Chạy deadline bài tập nhóm SQL', 'Tạo cơ sở dữ liệu và kiểm tra bằng các câu lệnh: Select, insert, update, delete cơ bản.', '2025-11-02', 'Trung bình', 1, 3, '2025-10-30', '2025-10-31 20:15:39', 3),
(10, 'Giặt chăn mùa đông', 'Mang chăn ra tiệm giặt đồ đầu ngõ', '2025-10-28', 'Cao', 1, 1, '2025-10-15', '2025-10-21 15:18:38', 2),
(11, 'Dọn nhà', 'Hút bụi, gấp quần áo và rửa cốc chén', '2025-11-06', 'Trung bình', 0, 2, '2025-11-03', '2025-11-05 11:18:38', 2),
(12, 'Đi xem phim với bạn', 'Đi xem phim mới nhất với bạn để thư giãn', '2025-11-09', 'Thấp', 0, 3, '2025-11-07', '2025-11-07 22:21:57', 2),
(13, 'Về quê', 'Bắt xe khách về quê', '2025-11-05', 'Thấp', 1, 4, '2025-11-02', '2025-11-05 17:21:57', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `hoten` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `matkhau` varchar(255) NOT NULL,
  `ngaytao` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`iduser`, `hoten`, `email`, `matkhau`, `ngaytao`) VALUES
(1, 'Nguyễn Văn An', 'nguyenan@gmail.com', '123456', '2023-09-19'),
(2, 'Trần Văn Nam', 'namtran@gmail.com', '123456', '2024-11-13'),
(3, 'Lê Thị Hoa', 'hoale@gmail.com', '123456', '2025-06-25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `congviec`
--
ALTER TABLE `congviec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foreign key` (`iduser`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `congviec`
--
ALTER TABLE `congviec`
  ADD CONSTRAINT `congviec_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
