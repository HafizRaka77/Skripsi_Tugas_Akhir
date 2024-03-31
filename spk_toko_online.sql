-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 27, 2024 at 01:20 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_toko_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatives`
--

CREATE TABLE `alternatives` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nilai_preferensi` double DEFAULT NULL
);
--
-- Dumping data for table `alternatives`
--

INSERT INTO `alternatives` (`id`,`kode`, `name`, `nilai_preferensi`) VALUES
(1, 'A1', 'Tokopedia', 0.818),
(2, 'A2', 'Shopee', 0.489),
(3, 'A3', 'Lazada', 0.609),
(4, 'A4', 'Bukalapak', 0.143),
(5, 'A5', 'BliBli', 0.363);

-- --------------------------------------------------------

--
-- Table structure for table `alternative_values`
--

CREATE TABLE `alternative_values` (
  `id` int NOT NULL,
  `alternative_id` int NOT NULL,
  `criteria_id` int NOT NULL,
  `value` int NOT NULL
);

--
-- Dumping data for table `alternative_values`
--

INSERT INTO `alternative_values` (`id`, `alternative_id`, `criteria_id`, `value`) VALUES
(1, 1, 1, 4),
(2, 1, 2, 4),
(3, 1, 3, 5),
(4, 1, 4, 4),
(5, 1, 5, 1),
(6, 1, 6, 4),
(7, 1, 7, 4),
(8, 2, 1, 5),
(9, 2, 2, 5),
(10, 2, 3, 4),
(11, 2, 4, 4),
(12, 2, 5, 3),
(13, 2, 6, 3),
(14, 2, 7, 3),
(15, 3, 1, 4),
(16, 3, 2, 4),
(17, 3, 3, 4),
(18, 3, 4, 3),
(19, 3, 5, 2),
(20, 3, 6, 4),
(21, 3, 7, 4),
(22, 4, 1, 3),
(23, 4, 2, 3),
(24, 4, 3, 3),
(25, 4, 4, 2),
(26, 4, 5, 3),
(27, 4, 6, 3),
(28, 4, 7, 2),
(29, 5, 1, 3),
(30, 5, 2, 4),
(31, 5, 3, 2),
(32, 5, 4, 3),
(33, 5, 5, 2),
(34, 5, 6, 3),
(35, 5, 7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `criterias`
--

CREATE TABLE `criterias` (
  `id` int NOT NULL,
  `kode` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `categories` enum('benefit','cost') NOT NULL
);

--
-- Dumping data for table `criterias`
--

INSERT INTO `criterias` (`id`, `kode`, `name`, `weight`, `categories`) VALUES
(1, 'C1', 'Promo', 90.00, 'benefit'),
(2, 'C2', 'Metode Pembayaran', 80.00, 'benefit'),
(3, 'C3', 'Tampilan Aplikasi (UI/UX)', 85.00, 'benefit'),
(4, 'C4', 'Expedisi Pengiriman', 70.00, 'benefit'),
(5, 'C5', 'Harga', 100.00, 'cost'),
(6, 'C6', 'Rating', 75.00, 'benefit'),
(7, 'C7', 'Kualitas Aplikasi', 95.00, 'benefit');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `role_id` int NOT NULL
);

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`, `role_id`) VALUES
(1, 'admin', 'password', 1),
(2, 'user', 'password', 2);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int NOT NULL,
  `role` varchar(31) NOT NULL
);

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `alternative_values`
--
ALTER TABLE `alternative_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alternative_id` (`alternative_id`),
  ADD KEY `fk_criteria_id` (`criteria_id`);

--
-- Indexes for table `criterias`
--
ALTER TABLE `criterias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `alternative_values`
--
ALTER TABLE `alternative_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `criterias`
--
ALTER TABLE `criterias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alternative_values`
--
ALTER TABLE `alternative_values`
  ADD CONSTRAINT `fk_alternative_id` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_criteria_id` FOREIGN KEY (`criteria_id`) REFERENCES `criterias` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
