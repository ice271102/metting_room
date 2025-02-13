-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:4306
-- Generation Time: Feb 13, 2025 at 04:23 AM
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
-- Database: `meetingroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `room_id` int(20) NOT NULL,
  `booking_date` date NOT NULL COMMENT 'วันที่กดจอง',
  `purpose` text NOT NULL,
  `status` enum('อนุมัติ','ยกเลิกแล้ว') DEFAULT NULL,
  `time` enum('ช่วงเช้า','ช่วงบ่าย','เต็มวัน') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `booking_date`, `purpose`, `status`, `time`) VALUES
(154, 8, 1, '2025-01-28', 'การจัดแถลงข่าว หรือกิจกรรมพิเศษ', 'อนุมัติ', 'ช่วงบ่าย'),
(155, 8, 2, '2025-01-28', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(162, 8, 1, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(165, 8, 3, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(166, 8, 4, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(167, 8, 4, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(168, 8, 5, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(169, 8, 6, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(172, 8, 6, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(174, 8, 7, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(175, 8, 8, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(177, 8, 9, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(179, 8, 1, '2025-01-30', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(182, 8, 2, '2025-01-30', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(185, 8, 3, '2025-01-30', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(186, 8, 10, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(190, 8, 2, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(191, 8, 2, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(192, 8, 1, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(193, 8, 3, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(194, 8, 1, '2025-01-30', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(195, 8, 5, '2025-01-29', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(196, 8, 1, '2025-01-31', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'เต็มวัน'),
(197, 8, 4, '2025-01-30', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(200, 8, 1, '2025-02-01', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(201, 8, 1, '2025-02-01', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(202, 8, 2, '2025-02-01', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(203, 8, 2, '2025-02-01', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(204, 8, 4, '2025-02-01', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(206, 8, 1, '2025-02-06', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(207, 8, 1, '2025-02-02', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(208, 8, 1, '2025-02-04', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(209, 8, 8, '2025-02-03', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(210, 8, 5, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(211, 8, 6, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(212, 8, 8, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'เต็มวัน'),
(214, 8, 4, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(215, 8, 7, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(216, 8, 10, '2025-02-07', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงเช้า'),
(217, 8, 10, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(219, 8, 3, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(220, 8, 3, '2025-02-07', 'การจัดแถลงข่าว หรือกิจกรรมพิเศษ', 'อนุมัติ', 'ช่วงเช้า'),
(222, 8, 8, '2025-02-08', 'ประชุมแผนก', 'อนุมัติ', 'ช่วงเช้า'),
(223, 8, 5, '2025-02-08', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(224, 8, 5, '2025-02-08', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงบ่าย'),
(225, 8, 9, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(226, 8, 9, '2025-02-07', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงบ่าย'),
(227, 8, 7, '2025-02-07', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงเช้า'),
(228, 8, 12, '2025-02-05', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'เต็มวัน'),
(229, 8, 2, '2025-01-31', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(230, 8, 2, '2025-01-31', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(231, 8, 2, '2025-02-04', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(232, 8, 2, '2025-02-04', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงเช้า'),
(233, 8, 3, '2025-02-05', 'การประชุมกับลูกค้า', 'อนุมัติ', 'เต็มวัน'),
(234, 8, 4, '2025-02-05', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(235, 46, 1, '2025-02-05', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(236, 46, 2, '2025-02-05', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'เต็มวัน'),
(237, 8, 2, '2025-02-06', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'เต็มวัน'),
(238, 8, 2, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(240, 48, 2, '2025-02-08', 'การประชุมกับลูกค้า', 'อนุมัติ', 'ช่วงบ่าย'),
(241, 48, 3, '2025-02-08', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'เต็มวัน'),
(243, 48, 4, '2025-02-08', 'ประชุมพนักงานใหม่', 'อนุมัติ', 'ช่วงบ่าย'),
(244, 8, 1, '2025-02-07', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงเช้า'),
(245, 8, 1, '2025-02-07', 'การนำเสนอแผนงานให้ผู้บริหาร', 'อนุมัติ', 'ช่วงบ่าย'),
(247, 8, 2, '2025-02-11', 'ประชุมเพื่อให้ความรู้', 'อนุมัติ', 'ช่วงบ่าย'),
(248, 8, 2, '2025-02-19', 'การประชุมกับลูกค้า', 'อนุมัติ', 'เต็มวัน');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_room`
--

CREATE TABLE `meeting_room` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `screen` int(11) NOT NULL,
  `microphone` int(11) NOT NULL,
  `picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meeting_room`
--

INSERT INTO `meeting_room` (`room_id`, `room_name`, `capacity`, `location`, `screen`, `microphone`, `picture`) VALUES
(1, 'ห้องประชุมอาคาร1 ชั้น2', 19, 'อาคาร1 ชั้น2', 7, 3, 'อาคาร1ชั้น2.jpg'),
(2, 'ห้องประชุมอาคาร1 ชั้น4', 79, 'อาคาร1 ชั้น4', 5, 4, 'อาคาร1ชั้น4.jpg'),
(3, 'ห้องประชุมอาคาร2 ชั้น1', 34, 'อาคาร2 ชั้น1', 3, 19, 'อาคาร2ชั้น1.jpg'),
(4, 'ห้องประชุมอาคาร2 ชั้น2 ศูนย์ปฏิบัติงานสำรอง', 28, 'อาคาร2 ชั้น2', 3, 17, 'อาคาร2ชั้น2 ศูนย์ปฏิบัติงานสำรอง.jpg'),
(5, 'ห้องประชุมอาคาร3 ชั้น2 ห้องสมุด', 9, 'อาคาร3 ชั้น2', 1, 0, 'อาคาร3ชั้น2ห้องสมุด.jpg'),
(6, 'ห้องประชุมอาคาร3 ชั้น2 สชก.(ฉ3) 1', 13, 'อาคาร3 ชั้น2', 1, 8, 'อาคาร3ชั้น2 สชก.(ฉ1)ห้องประชุม1.jpg'),
(7, 'ห้องประชุมอาคาร3 ชั้น2 สชก.(ฉ3) 2', 11, 'อาคาร3 ชั้น2', 2, 5, 'อาคาร3ชั้น2ห้อง สชก.(ฉ3).jpg'),
(8, 'ห้องประชุมอาคาร3 ชั้น 2 ห้องประชุมอเนกประสงค์', 150, 'อาคาร3 ชั้น 2', 1, 1, 'อาคาร3ชั้น2 ห้องประชุมอเนกประสงค์.jpg'),
(9, 'ห้องประชุมอาคาร 3 ชั้น5 ฝวบ.ฉ.3', 28, 'อาคาร 3 ชั้น5', 1, 7, 'อาคาร3ชั้น5 ฝวบ.ฉ3.jpg'),
(10, 'ห้องประชุม อาคาร 4 ชั้น3 ห้องฝ่ายสนับสนุนการบริการ กฟฉ.3', 51, 'อาคาร 4 ชั้น3', 7, 21, 'อาคาร4ชั้น3 ห้องฝ่ายสนับสนุนการบริหาร กฟฉ.3.jpg'),
(11, 'ห้องประชุม อาคาร 4 ชั้น2', 10, 'อาคาร 4 ชั้น2', 2, 4, 'อาคาร4 ชั้น2.jpg'),
(12, 'ห้องประชุม อาคาร 4 ชั้น1', 13, 'อาคาร 4 ชั้น1', 1, 1, 'อาคาร4ชั้น1.jpg'),
(17, 'ห้องประชุมอาคาร5', 10, 'ประชุมอาคาร5', 2, 2, '6786159931879_Capture.JPG'),
(18, 'ห้องประชุมอาคาร8', 30, 'อาคาร8', 3, 4, '6788cb8b34974_6.png'),
(21, 'ห้องประชุมอาคาร5ชั้น4', 16, 'อาคาร5 ชั้น4', 2, 4, '679c794deb3be_1.jpg'),
(22, 'ห้องประชุมอาคาร6ชั้น26', 13, 'อาคาร6 ชั้น6', 2, 3, '67a320cd0647b_5.jpg'),
(23, 'ห้องประชุมอาคาร8 ชั้น1', 20, 'อาคาร8 ชั้น1', 3, 4, '67a58316bbf9e_อาคาร4ชั้น3 ห้องฝ่ายสนับสนุนการบริหาร กฟฉ.3.jpg'),
(24, 'ห้องประชุมอาคาร7ชั้น2', 30, 'อาคาร7ชั้น2', 5, 4, '67a5b5aaca2cd_2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL COMMENT 'รหัส',
  `rating` int(11) DEFAULT NULL COMMENT 'การให้คะแนน',
  `comment` text DEFAULT NULL COMMENT 'ความคิดเห็น',
  `user_id` int(11) DEFAULT NULL COMMENT 'รหัสผู้ใช้',
  `room_id` int(11) DEFAULT NULL COMMENT 'รหัสห้อง',
  `review_date` date DEFAULT NULL COMMENT 'วันที่รีวิว'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `rating`, `comment`, `user_id`, `room_id`, `review_date`) VALUES
(1, 5, 'โอเคดี', 8, 1, '2025-01-27'),
(2, 5, 'ดีเลยนะ', 8, 1, '2025-01-27'),
(3, 5, 'ระบบพอใช้ได้โอเค', 8, 2, '2025-01-27'),
(4, 5, 'พนักงานบริการดีมาก มีกาแฟและน้ำบริการระหว่างประชุม', 8, 1, '2025-01-27'),
(5, 5, 'ห้องประชุมสะอาดและอุปกรณ์ครบครัน  สะดวกสบายดีครับ', 8, 2, '2025-01-27'),
(6, 5, 'มีหลายขนาดห้องให้เลือก  เหมาะกับการประชุมทุกขนาด', 8, 2, '2025-01-27'),
(7, 4, 'ห้องกว้างขวาง - รองรับผู้เข้าร่วมได้เยอะ', 8, 1, '2025-01-27'),
(8, 5, 'เทคโนโลยีใหม่ล่าสุด - ระบบเสียงและโปรเจคเตอร์ดีมาก', 8, 8, '2025-01-27'),
(9, 5, 'สะดวกในการจอง ระบบออนไลน์ใช้งานง่าย', 8, 3, '2025-01-27'),
(10, 5, 'อาหารอร่อยและมีหลากหลาย ทำให้การประชุมไม่น่าเบื่อ', 8, 2, '2025-01-27'),
(11, 4, 'ห้องเย็นสบาย - ระบบแอร์เยี่ยม', 8, 4, '2025-01-27'),
(12, 5, 'บริการรวดเร็ว - เช็คอินง่ายและรวดเร็ว', 8, 6, '2025-01-27'),
(13, 4, 'สถานที่สะอาด - ห้องประชุมถูกจัดเตรียมอย่างดี', 8, 2, '2025-01-27'),
(14, 5, 'อุปกรณ์ครบครัน - โปรเจคเตอร์ ไมโครโฟน ใช้งานได้ดี', 8, 1, '2025-01-27'),
(15, 5, 'เหมาะสำหรับการประชุมทั้งเล็กและใหญ่ - ห้องปรับขนาดได้', 8, 10, '2025-01-27'),
(16, 4, 'การเดินทางสะดวก - ที่จอดรถเยอะ', 8, 4, '2025-01-27'),
(17, 4, 'อุปกรณ์ทำงานได้ดี - ไม่มีปัญหากับการใช้งาน', 8, 3, '2025-01-27'),
(18, 5, 'บริการดีเยี่ยม - พนักงานช่วยเหลือดีตลอดเวลา', 8, 3, '2025-01-27'),
(19, 5, 'ระบบการจองง่าย - ไม่ยุ่งยาก และสามารถจองได้ทันที', 8, 1, '2025-01-27'),
(20, 5, 'ดีมาก', 8, 1, '2025-01-28'),
(33, 5, 'ดีมาก', 46, 1, '2025-02-05'),
(34, 5, 'ห้องประชุมสะอาดดี', 8, 7, '2025-02-07'),
(35, 4, 'ห้องประชุมเป็นระเบียบดี แต่อยากให้มีที่นั่งมากกว่านี้ค่ะ', 8, 5, '2025-02-07'),
(36, 4, 'ดีมาก', 48, 2, '2025-02-07'),
(38, 4, 'ห้องประชุมสะอาดมาก', 8, 1, '2025-02-07'),
(39, 4, 'ห้องประชุมสะอาดดีแต่อยากให้เพิ่มความจุคนเพิ่มขึ้น', 8, 1, '2025-02-11'),
(40, 5, 'ห้องประชุมกว้างดี มีอุปกรณ์ครบ', 48, 1, '2025-02-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL COMMENT 'รหัสผู้ใช้',
  `password` varchar(255) NOT NULL COMMENT 'รหัสผ่าน',
  `first_name` varchar(50) DEFAULT NULL COMMENT 'ชื่อจริง',
  `Last_name` varchar(50) NOT NULL COMMENT 'นามสกุล',
  `email` varchar(100) DEFAULT NULL,
  `picture_url` varchar(255) NOT NULL COMMENT 'รูป',
  `department` varchar(50) NOT NULL COMMENT 'แผนก',
  `phone` varchar(15) DEFAULT NULL COMMENT 'โทรศัพท์',
  `role` enum('admin','user') DEFAULT 'user' COMMENT 'บทบาท',
  `username` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `first_name`, `Last_name`, `email`, `picture_url`, `department`, `phone`, `role`, `username`, `position`) VALUES
(7, '$2y$10$eZuJtNUKR9EYFGlMVPMKj.R6vdAPAf1Uz5bJAw0KPP7Cp44eCldvW', 'กนกวรรณ', 'ภักดี', 'pin@gmail.com', '1735792319_โปรไฟล์4.jpg', 'คอมพิวเตอร์', '0986785411', 'admin', 'admin', 'นักพัฒนา'),
(8, '$2y$10$jdp326AGenHWuinDC0XYc.BbizHvAo1rbJdh3B85U8ZzQWIvjjpOe', 'เนตรอัปสร', 'เพ็ชรสูงเนิน', 'ice@gmail.com', '1738899367_โปรไฟล์4.jpg', 'ปฏิบัติงานดิจิทัล', '0902738999', 'user', 'kanokwan', 'คอมพิวเตอร์'),
(25, '$2y$10$p581cxtpRETz3TvtkBf7tu2JxRwoYluOU.Zd.3nw80gBwflf5jmQa', 'วิทยา', 'ทองแท้', 'witaya.tm@pea.co.th', '', 'พัสดุ', '0812345673	', 'user', 'witaya.tm', 'พนักงานพัสดุ'),
(27, '$2y$10$/CvEYzDwhWtahzGHkBq6q.IGyCcvoYyEbHi5IzQaoxKflMBjvot3a', 'ธนา', 'ศรีสุข', 'thana.ss@pea.co.th', '', 'บัญชี', '0812345675', 'admin', 'thana.ss', 'การเงิน'),
(28, '$2y$10$ehzHxzsbZYaWDvxyw3vMfutvusJrcdG2SLMlWUw1llm2OgRvPDVz2', 'ชลิตา', 'รัตนพร', 'chalita.rp@pea.co.th', '', 'บริการลูกค้า', '0812345676', 'user', 'chalita.rp', 'เจ้าหน้าที่บริการ'),
(29, '$2y$10$vJ5R3XemzWSWuukrcUXUueD/SWw7XrW3jfEGKtBwGMejWI3mETNU2', 'กิตติ', 'พัฒนกิจ', 'kitti.pk@pea.co.th', '', 'ระบบไฟฟ้า', '0812345677', 'user', 'kitti.pk', 'วิศวกรไฟฟ้า'),
(30, '$2y$10$xDRWjTFli0gIES6MaWAHRO/EJK1Kt9Ogm/Zl4BVGObnPjTnEiFOG2', 'สุพจน์', 'วัฒนากิจ', 'supoj.wk@pea.co.th', '', 'การจัดการ', '0812345678', 'user', 'supoj.wk', 'ผู้จัดการ	'),
(31, '$2y$10$48KBX4xE4LdW3tDmIzBSPuBpDK08ZEkDzmRm1iQRnmEN9Kuz4plra', 'ภาวิณี', 'อุดมทรัพย์', 'pavinee.us@pea.co.th', '', 'ขนส่ง', '0812345679	', 'user', 'pavinee.us', 'เจ้าหน้าที่ขนส่ง'),
(33, '$2y$10$qmL/ZFgYlgu3VKiC1LLX8ukZIV/3yH2NreQCFAjRlMfemoEIqVH7a', 'ศิริพร', 'แสงทอง', 'siriporn.st@pea.co.th', '', 'การตรวจสอบ', '0812345681', 'user', 'siriporn.st', 'นักตรวจสอบ'),
(34, '$2y$10$.WbXtArzSdfulKrBiV0MwuOpYX1TUGgyb/xEHpLQFHcM0zCBw4ksW', 'สุชาติ', 'แก้วใส', 'suchart.ks@pea.co.th', '', 'บุคคล', '0812345682	', 'user', 'suchart.ks', 'พนักงานบุคคล'),
(35, '$2y$10$nv7jyzW7H9w56StHh.pjkOZt9VSTAZVDrlRrFB9DWxpwdzB65Ac56', 'วิไลวรรณ', 'สุขสวัสดิ์', 'wilaiwan.ss@pea.co.th', '', 'การเงิน', '0812345683', 'user', 'wilaiwan.ss', 'เจ้าหน้าที่การเงิน'),
(36, '$2y$10$.fW6pgNbAt/ciqcO2bPJIuMqX9ojrG/b2UdFWvUGu85dd3c6blaHC', 'อธิชาติ', 'รุ่งโรจน์', 'atichat.rr@pea.co.th', '', 'บริการลูกค้า', '0812345684', 'user', 'atichat.rr', 'หัวหน้าฝ่าย'),
(37, '$2y$10$FxYd6IYR9maIuaqASeCIV.dD8QkR10FBKLh8pDHB2RoHH.IZQSz/S', 'วัฒนา ', 'พงศ์พิทักษ์', 'wattana.pp@pea.co.th', '', 'ข้อมูล', '0812345685', 'user', 'wattana.pp', 'นักวิเคราะห์'),
(38, '$2y$10$KoywahqpSZiY2dPcBo1t1.Vw3.LGdl1NsFX9ZoGjfFEGorauwmThG', 'สุภาวดี', 'รุ่งเรืองสุข', 'supawadee.rs@pea.co.th', '', 'การตลาด', '0812345686', 'user', 'supawadee.rs', 'เจ้าหน้าที่การตลาด'),
(39, '$2y$10$dN7KxkaEXjowhyigNNHSfe369Ut27jCpI7N9p/Bb9z6Wpd9emLoKC', 'เกรียงไกร', 'รัตนวรรณ', 'kriangkrai.rv@pea.co.th', '', 'พัสดุ', '0812345687', 'user', 'kriangkrai.rv', 'พนักงานพัสดุ'),
(40, '$2y$10$6XZcE.nfFbu79LG1g6xLleraLtHTBdNEGJEA3jbaihRwcyyFdDi.G', 'พงศธร', 'จงเจริญ', 'pongsathorn.cj@pea.co.th', '', 'ขนส่ง', '0812345688', 'user', 'pongsathorn.cj', 'เจ้าหน้าที่ขนส่ง'),
(41, '$2y$10$3nxtHRy4oNco1JgLkVIwAOpPXBBN6gtcbxcrMsfkJT31mUyRadyFG', 'อัญชลี', 'ใจเย็น', 'anchalee.jy@pea.co.th', '1738307437_6.png', 'การบัญชี', '0812345689', 'user', 'anchalee.jy', 'พนักงานบัญชี'),
(42, '$2y$10$Q8v6T6fdPglDzfompxm4dug.mDoRwmDXSD952/w2IvDTcYBLwv1mu', 'นภัสร', 'กิจเจริญ', 'napasorn.kj@pea.co.th', '', 'ระบบไฟฟ้า', '0812345690', 'user', 'napasorn.kj', 'วิศวกรไฟฟ้า'),
(46, '$2y$10$Ea3vqErV7fAFw65JVaDL1O2SqDa2.jOa4NfgvD3eJaK8mtXKG6cJO', 'สุมิตร', 'ภูมิศักดิ์', 'sumit.p@pea.co.th', '', 'ฝ่ายบริหาร', '0891234567', 'user', 'sumit_p', 'ผู้จัดการ'),
(48, '$2y$10$.87635x7I6ufEj.W4JlXnubzKVapVH1xj.v1lpljaU63z0LLb1EWe', 'มานพ', 'ทองดี', 'kanok@gmail.com', '', 'การเงิน', '0823456789', 'user', 'manop_t', 'เจ้าหน้าที่บัญชี'),
(49, '$2y$10$/x/cXIAute7gEeb/RK1yquk6IGyyyrr7SnuyVuNyBrF55hztf6xfy', 'กนกวรณ', 'ภักดี', 'kanokwan.pd@rmuti.ac.th', '', 'จัดซื้อ', '0987645231', 'user', 'kanok', 'พนักงาน');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_bookings_users` (`user_id`),
  ADD KEY `fk_bookings_meeting_room` (`room_id`);

--
-- Indexes for table `meeting_room`
--
ALTER TABLE `meeting_room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `meeting_room`
--
ALTER TABLE `meeting_room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัส', AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสผู้ใช้', AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_room_id` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
