-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 02:35 PM
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
-- Database: `nook_finder`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Type` varchar(10) NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--
INSERT INTO `accounts` (`account_id`, `username`, `password`, `profile_pic`, `created_at`, `Type`) VALUES
(1, 'tester', 'test', NULL, '2026-02-18 09:36:13', 'Admin'),
(2, 'Almariego', 'james', NULL, '2026-02-18 14:09:51', 'Admin'),
(3, 'Incognito', 'rich', 'uploads/1773058865_jarbihs.png', '2026-02-18 14:10:55', 'Admin'),
(4, 'Montoya', 'dohn', NULL, '2026-02-18 14:11:25', 'Admin'),
(5, 'Santos', 'jeorge', NULL, '2026-02-18 14:11:51', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `place_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `distance_km` decimal(3,1) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `wifi` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `outlet` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `aircon` ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
  `parking` ENUM('Yes', 'No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name`, `location`, `distance_km`, `description`, `created_by`, `created_at`, `image`, `latitude`, `longitude`, `wifi`, `outlet`, `aircon`, `parking`) VALUES
(1, 'Kuwento Cafe',           'Angeles City, Pampanga', 1.3, 'A cozy cafe near HAU perfect for studying and relaxing.',       NULL, '2026-03-09 12:47:54', 'images/kwento.jpg',   15.1344100, 120.5971200, 'Yes', 'Yes', 'Yes', 'No' ),
(2, 'Cush Lounge',            'Angeles City, Pampanga', 1.4, 'A comfortable lounge space for students to unwind and work.',   NULL, '2026-03-09 12:47:54', 'images/Cush.jpg',     15.1521700, 120.5925400, 'Yes', 'Yes', 'Yes', 'Yes'),
(3, 'Vessel Coworking Space', 'Angeles City, Pampanga', 0.6, 'A coworking space ideal for collaborative work and meetings.',  NULL, '2026-03-09 12:47:54', 'images/Vessel.jpg',   15.1368900, 120.5918900, 'Yes', 'Yes', 'Yes', 'Yes'),
(4, 'Co.Create',              'Angeles City, Pampanga', 0.3, 'A creative shared workspace close to HAU.',                     NULL, '2026-03-09 12:47:54', 'images/CoCreate.PNG', 15.1332700, 120.5918200, 'Yes', 'Yes', 'Yes', 'No' ),
(5, 'oFTr',                   'Angeles City, Pampanga', 0.3, 'A student-friendly nook near the university.',                  NULL, '2026-03-09 12:47:54', 'images/OFTR.jpg',     15.1343900, 120.5914300, 'Yes', 'Yes', 'Yes', 'No' ),
(6, 'Angeles City Library',   'Angeles City, Pampanga', 0.8, 'A public library offering a quiet space for focused study.',    NULL, '2026-03-09 12:47:54', 'images/ACLib.jpg',    15.1352800, 120.5908100, 'No',  'No',  'Yes', 'Yes'),
(7, 'BRUDR',                  'Angeles City, Pampanga', 0.5, 'A cafe and hangout spot near HAU.',                             NULL, '2026-03-09 12:47:54', 'images/BRUDR.jpg',    15.1363800, 120.5907000, 'Yes', 'Yes', 'Yes', 'Yes'),
(8, 'Arte Cafe',              'Angeles City, Pampanga', 1.0, 'An artsy cafe with a relaxed atmosphere for students.',         NULL, '2026-03-09 12:47:54', 'images/ARTE.jpg',     15.1384300, 120.5935200, 'Yes', 'Yes', 'Yes', 'Yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `fk_favorites_place` (`place_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `fk_places_account` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `places_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
