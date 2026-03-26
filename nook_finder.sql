-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 07:18 AM
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
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `username`, `password`, `profile_pic`, `created_at`, `role_id`) VALUES
(18, 'Almariego', '$2y$10$TsZVuuD9fe4SaxqazW9YbuoBT052D0g0aVHM1ZAtqR/HJgxekOLr.', 'uploads/1774175923_1773415783_cec8f5c5-38e6-43ed-a999-e65ce6eb3cf6.jpg', '2026-03-22 10:27:55', 2),
(19, 'Montoya', '$2y$10$f67r08mnhIv7ohEyEc4Aju6FYCYEpXBQQC/oYLPZyvSxbzqDO.8su', NULL, '2026-03-22 10:28:38', 2),
(20, 'Incognito', '$2y$10$yYlTznduQFq6TXQJrDe33O2Su3Hm82SFnQlKTVd8WcUvuQCD3RLvC', 'uploads/1774175990_1773058865_jarbihs.png', '2026-03-22 10:28:49', 2),
(23, 'Tourist', '$2y$10$5.x4QBmARtg5vo6DuGrDJOVD.wEWg04VledAxFtdDEjC5tKaLOTNm', NULL, '2026-03-22 10:30:55', 1),
(24, 'Santos', '$2y$10$cDTfgbOeO72Tvkx5pyI6G.hAdvsvxn3BaQ7BZFuke6SUGaJtE6Hle', 'uploads/1774505355_1774176134_channels4_profile.jpg', '2026-03-26 05:58:51', 2);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `distance_km` decimal(4,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `wifi` enum('Yes','No') NOT NULL DEFAULT 'No',
  `outlet` enum('Yes','No') NOT NULL DEFAULT 'No',
  `aircon` enum('Yes','No') NOT NULL DEFAULT 'No',
  `parking` enum('Yes','No') NOT NULL DEFAULT 'No',
  `hours_weekday` varchar(50) DEFAULT NULL,
  `hours_weekend` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `proposed_by` int(11) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name`, `location`, `distance_km`, `description`, `image`, `latitude`, `longitude`, `wifi`, `outlet`, `aircon`, `parking`, `hours_weekday`, `hours_weekend`, `status`, `proposed_by`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_at`) VALUES
(1, 'Kuwento Cafe', 'Angeles City, Pampanga', 1.30, 'A cozy cafe near HAU perfect for studying and relaxing.', 'images/kwento.jpg', 15.1344100, 120.5971200, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(2, 'Vessel Coworking Space', 'Angeles City, Pampanga', 0.60, 'A coworking space ideal for collaborative work and meetings.', 'images/Vessel.jpg', 15.1368900, 120.5918900, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(3, 'Co.Create', 'Angeles City, Pampanga', 0.30, 'A creative shared workspace close to HAU.', 'images/CoCreate.PNG', 15.1332700, 120.5918200, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(4, 'oFTr', 'Angeles City, Pampanga', 0.30, 'A student-friendly nook near the university.', 'images/OFTR.jpg', 15.1343900, 120.5914300, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(5, 'Angeles City Library', 'Angeles City, Pampanga', 0.80, 'A public library offering a quiet space for focused study.', 'images/ACLib.jpg', 15.1352800, 120.5908100, 'No', 'No', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(6, 'BRUDR', 'Angeles City, Pampanga', 0.50, 'A cafe and hangout spot near HAU.', 'images/BRUDR.jpg', 15.1363800, 120.5907000, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(7, 'Arte Cafe', 'Angeles City, Pampanga', 1.00, 'An artsy cafe with a relaxed atmosphere for students.', 'images/ARTE.jpg', 15.1384300, 120.5935200, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(20, 'HAU Library', 'Inside HAU', 0.00, 'Library for Students.', NULL, 15.1334652, 120.5909854, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'pending', 23, NULL, NULL, NULL, '2026-03-17 15:32:29'),
(21, 'Mixue', 'Bart Mall', 0.00, 'Ice cream shop.', NULL, 15.1338691, 120.5917042, 'No', 'Yes', 'Yes', 'No', NULL, NULL, 'pending', 23, NULL, NULL, NULL, '2026-03-19 07:18:37');

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

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` tinyint(3) UNSIGNED NOT NULL,
  `role_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(2, 'Admin'),
(1, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `user_pins`
--

CREATE TABLE `user_pins` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `note` text DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_accounts`
-- (See below for the actual view)
--
CREATE TABLE `v_accounts` (
`account_id` int(11)
,`username` varchar(50)
,`password` varchar(255)
,`profile_pic` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_places`
-- (See below for the actual view)
--
CREATE TABLE `v_places` (
`id` int(11)
,`name` varchar(100)
,`location` varchar(150)
,`distance_km` decimal(4,2)
,`description` text
,`image` varchar(255)
,`latitude` decimal(10,7)
,`longitude` decimal(10,7)
,`wifi` enum('Yes','No')
,`outlet` enum('Yes','No')
,`aircon` enum('Yes','No')
,`parking` enum('Yes','No')
,`hours_weekday` varchar(50)
,`hours_weekend` varchar(50)
,`status` enum('pending','approved','rejected')
,`proposed_by` int(11)
,`reviewed_by` int(11)
,`reviewed_at` datetime
,`rejection_reason` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `v_accounts`
--
DROP TABLE IF EXISTS `v_accounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_accounts`  AS SELECT `accounts`.`account_id` AS `account_id`, `accounts`.`username` AS `username`, `accounts`.`password` AS `password`, `accounts`.`profile_pic` AS `profile_pic`, `accounts`.`created_at` AS `created_at` FROM `accounts` ;

-- --------------------------------------------------------

--
-- Structure for view `v_places`
--
DROP TABLE IF EXISTS `v_places`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_places`  AS SELECT `places`.`id` AS `id`, `places`.`name` AS `name`, `places`.`location` AS `location`, `places`.`distance_km` AS `distance_km`, `places`.`description` AS `description`, `places`.`image` AS `image`, `places`.`latitude` AS `latitude`, `places`.`longitude` AS `longitude`, `places`.`wifi` AS `wifi`, `places`.`outlet` AS `outlet`, `places`.`aircon` AS `aircon`, `places`.`parking` AS `parking`, `places`.`hours_weekday` AS `hours_weekday`, `places`.`hours_weekend` AS `hours_weekend`, `places`.`status` AS `status`, `places`.`proposed_by` AS `proposed_by`, `places`.`reviewed_by` AS `reviewed_by`, `places`.`reviewed_at` AS `reviewed_at`, `places`.`rejection_reason` AS `rejection_reason`, `places`.`created_at` AS `created_at` FROM `places` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD KEY `fk_accounts_role` (`role_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fav` (`account_id`,`place_id`),
  ADD KEY `fk_fav_place` (`place_id`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_places_proposed_by` (`proposed_by`),
  ADD KEY `fk_places_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_place` (`place_id`),
  ADD KEY `fk_reviews_account` (`account_id`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_like` (`review_id`,`account_id`),
  ADD KEY `fk_like_account` (`account_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reply_review` (`review_id`),
  ADD KEY `fk_reply_account` (`account_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `uq_role_name` (`role_name`);

--
-- Indexes for table `user_pins`
--
ALTER TABLE `user_pins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pins_account` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_pins`
--
ALTER TABLE `user_pins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_accounts_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_fav_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fav_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `fk_places_proposed_by` FOREIGN KEY (`proposed_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_places_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_place` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Constraints for table `user_pins`
--
ALTER TABLE `user_pins`
  ADD CONSTRAINT `fk_pins_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
