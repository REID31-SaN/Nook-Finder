-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 04:38 PM
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
(2, 'Almariego', 'james', 'uploads/1773415783_cec8f5c5-38e6-43ed-a999-e65ce6eb3cf6.jpg', '2026-02-18 14:09:51', 'Admin'),
(3, 'Incognito', 'rich', 'uploads/1773058865_jarbihs.png', '2026-02-18 14:10:55', 'Admin'),
(4, 'Montoya', 'dohn', NULL, '2026-02-18 14:11:25', 'Admin'),
(5, 'Santos', 'jeorge', NULL, '2026-02-18 14:11:51', 'Admin'),
(7, 'Tourist', 'password', NULL, '2026-03-17 07:57:39', 'User');

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

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `account_id`, `created_at`, `place_id`) VALUES
(31, 3, '2026-03-17 14:46:12', 5);

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
  `wifi` enum('Yes','No') NOT NULL DEFAULT 'No',
  `outlet` enum('Yes','No') NOT NULL DEFAULT 'No',
  `aircon` enum('Yes','No') NOT NULL DEFAULT 'No',
  `parking` enum('Yes','No') NOT NULL DEFAULT 'No',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `proposed_by` int(11) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name`, `location`, `distance_km`, `description`, `created_by`, `created_at`, `image`, `latitude`, `longitude`, `wifi`, `outlet`, `aircon`, `parking`, `status`, `proposed_by`, `reviewed_by`, `reviewed_at`, `rejection_reason`) VALUES
(1, 'Kuwento Cafe', 'Angeles City, Pampanga', 1.3, 'A cozy cafe near HAU perfect for studying and relaxing.', NULL, '2026-03-09 12:47:54', 'images/kwento.jpg', 15.1344100, 120.5971200, 'Yes', 'Yes', 'Yes', 'No', 'approved', NULL, NULL, NULL, NULL),
(2, 'Cush Lounge', 'Angeles City, Pampanga', 1.4, 'A comfortable lounge space for students to unwind and work.', NULL, '2026-03-09 12:47:54', 'images/Cush.jpg', 15.1521700, 120.5925400, 'Yes', 'Yes', 'Yes', 'Yes', 'approved', NULL, NULL, NULL, NULL),
(3, 'Vessel Coworking Space', 'Angeles City, Pampanga', 0.6, 'A coworking space ideal for collaborative work and meetings.', NULL, '2026-03-09 12:47:54', 'images/Vessel.jpg', 15.1368900, 120.5918900, 'Yes', 'Yes', 'Yes', 'Yes', 'approved', NULL, NULL, NULL, NULL),
(4, 'Co.Create', 'Angeles City, Pampanga', 0.3, 'A creative shared workspace close to HAU.', NULL, '2026-03-09 12:47:54', 'images/CoCreate.PNG', 15.1332700, 120.5918200, 'Yes', 'Yes', 'Yes', 'No', 'approved', NULL, NULL, NULL, NULL),
(5, 'oFTr', 'Angeles City, Pampanga', 0.3, 'A student-friendly nook near the university.', NULL, '2026-03-09 12:47:54', 'images/OFTR.jpg', 15.1343900, 120.5914300, 'Yes', 'Yes', 'Yes', 'No', 'approved', NULL, NULL, NULL, NULL),
(6, 'Angeles City Library', 'Angeles City, Pampanga', 0.8, 'A public library offering a quiet space for focused study.', NULL, '2026-03-09 12:47:54', 'images/ACLib.jpg', 15.1352800, 120.5908100, 'No', 'No', 'Yes', 'Yes', 'approved', NULL, NULL, NULL, NULL),
(7, 'BRUDR', 'Angeles City, Pampanga', 0.5, 'A cafe and hangout spot near HAU.', NULL, '2026-03-09 12:47:54', 'images/BRUDR.jpg', 15.1363800, 120.5907000, 'Yes', 'Yes', 'Yes', 'Yes', 'approved', NULL, NULL, NULL, NULL),
(8, 'Arte Cafe', 'Angeles City, Pampanga', 1.0, 'An artsy cafe with a relaxed atmosphere for students.', NULL, '2026-03-09 12:47:54', 'images/ARTE.jpg', 15.1384300, 120.5935200, 'Yes', 'Yes', 'Yes', 'Yes', 'approved', NULL, NULL, NULL, NULL),
(20, 'HAU Library', 'Inside HAU', 0.0, 'Library for Students', NULL, '2026-03-17 15:32:29', NULL, 15.1334652, 120.5909854, 'Yes', 'Yes', 'Yes', '', 'pending', 3, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `allow_replies` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
--

CREATE TABLE `review_likes` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_replies`
--

CREATE TABLE `review_replies` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_place_idx` (`place_id`),
  ADD KEY `fk_reviews_account_idx` (`account_id`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`review_id`,`account_id`),
  ADD KEY `fk_like_account` (`account_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reply_review` (`review_id`),
  ADD KEY `fk_reply_account` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_likes`
--
ALTER TABLE `review_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_account_constraint` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_place_constraint` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD CONSTRAINT `fk_like_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_like_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD CONSTRAINT `fk_reply_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reply_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
