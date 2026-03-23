SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `nook_finder`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `nook_finder`;

DROP VIEW IF EXISTS `v_places`;
DROP VIEW IF EXISTS `v_accounts`;

DROP TABLE IF EXISTS `user_pins`;
DROP TABLE IF EXISTS `review_replies`;
DROP TABLE IF EXISTS `review_likes`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `places`;
DROP TABLE IF EXISTS `accounts`;
DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `role_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'User'),
(2, 'Admin');

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `Type` varchar(10) NOT NULL DEFAULT 'User',
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `uq_username` (`username`),
  KEY `fk_accounts_role` (`role_id`),
  CONSTRAINT `fk_accounts_role`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER $$

CREATE TRIGGER `bi_accounts_role`
BEFORE INSERT ON `accounts`
FOR EACH ROW
BEGIN
  IF NEW.Type = 'Admin' THEN
    SET NEW.role_id = 2;
  ELSE
    SET NEW.role_id = 1;
  END IF;
END$$

CREATE TRIGGER `bu_accounts_role`
BEFORE UPDATE ON `accounts`
FOR EACH ROW
BEGIN
  IF NEW.Type = 'Admin' THEN
    SET NEW.role_id = 2;
  ELSE
    SET NEW.role_id = 1;
  END IF;
END$$

DELIMITER ;

INSERT INTO `accounts` (`account_id`, `username`, `password`, `profile_pic`, `created_at`, `role_id`, `Type`) VALUES
(18, 'Almariego', '$2y$10$TsZVuuD9fe4SaxqazW9YbuoBT052D0g0aVHM1ZAtqR/HJgxekOLr.', 'uploads/1774175923_1773415783_cec8f5c5-38e6-43ed-a999-e65ce6eb3cf6.jpg', '2026-03-22 10:27:55', 2, 'Admin'),
(19, 'Montoya', '$2y$10$f67r08mnhIv7ohEyEc4Aju6FYCYEpXBQQC/oYLPZyvSxbzqDO.8su', NULL, '2026-03-22 10:28:38', 2, 'Admin'),
(20, 'Incognito', '$2y$10$yYlTznduQFq6TXQJrDe33O2Su3Hm82SFnQlKTVd8WcUvuQCD3RLvC', 'uploads/1774175990_1773058865_jarbihs.png', '2026-03-22 10:28:49', 2, 'Admin'),
(21, 'Santos', '$2y$10$mVxiG988NtM4db7o7KPUmu0fJN1hbuQw2SzNiRuzEcm1sxpp4bf.e', 'uploads/1774176134_channels4_profile.jpg', '2026-03-22 10:28:57', 2, 'Admin'),
(22, 'Tester', '$2y$10$4clpfISNWFoBGq9VdRnDt.youjSLlWl52ZsDQdh03DZ7jeGvqkm4G', NULL, '2026-03-22 10:29:16', 2, 'Admin'),
(23, 'Tourist', '$2y$10$5.x4QBmARtg5vo6DuGrDJOVD.wEWg04VledAxFtdDEjC5tKaLOTNm', NULL, '2026-03-22 10:30:55', 1, 'User');

ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

CREATE TABLE `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_places_created_by` (`created_by`),
  KEY `fk_places_proposed_by` (`proposed_by`),
  KEY `fk_places_reviewed_by` (`reviewed_by`),
  CONSTRAINT `fk_places_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_places_proposed_by`
    FOREIGN KEY (`proposed_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_places_reviewed_by`
    FOREIGN KEY (`reviewed_by`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `places` (`id`, `name`, `location`, `distance_km`, `description`, `image`, `latitude`, `longitude`, `wifi`, `outlet`, `aircon`, `parking`, `hours_weekday`, `hours_weekend`, `status`, `proposed_by`, `reviewed_by`, `reviewed_at`, `rejection_reason`, `created_by`, `created_at`) VALUES
(1, 'Kuwento Cafe', 'Angeles City, Pampanga', 1.30, 'A cozy cafe near HAU perfect for studying and relaxing.', 'images/kwento.jpg', 15.1344100, 120.5971200, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(2, 'Cush Lounge', 'Angeles City, Pampanga', 1.40, 'A comfortable lounge space for students to unwind and work.', 'images/Cush.jpg', 15.1521700, 120.5925400, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(3, 'Vessel Coworking Space', 'Angeles City, Pampanga', 0.60, 'A coworking space ideal for collaborative work and meetings.', 'images/Vessel.jpg', 15.1368900, 120.5918900, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(4, 'Co.Create', 'Angeles City, Pampanga', 0.30, 'A creative shared workspace close to HAU.', 'images/CoCreate.PNG', 15.1332700, 120.5918200, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(5, 'oFTr', 'Angeles City, Pampanga', 0.30, 'A student-friendly nook near the university.', 'images/OFTR.jpg', 15.1343900, 120.5914300, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(6, 'Angeles City Library', 'Angeles City, Pampanga', 0.80, 'A public library offering a quiet space for focused study.', 'images/ACLib.jpg', 15.1352800, 120.5908100, 'No', 'No', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(7, 'BRUDR', 'Angeles City, Pampanga', 0.50, 'A cafe and hangout spot near HAU.', 'images/BRUDR.jpg', 15.1363800, 120.5907000, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(8, 'Arte Cafe', 'Angeles City, Pampanga', 1.00, 'An artsy cafe with a relaxed atmosphere for students.', 'images/ARTE.jpg', 15.1384300, 120.5935200, 'Yes', 'Yes', 'Yes', 'Yes', NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-03-09 12:47:54'),
(20, 'HAU Library', 'Inside HAU', 0.00, 'Library for Students.', NULL, 15.1334652, 120.5909854, 'Yes', 'Yes', 'Yes', 'No', NULL, NULL, 'pending', 23, NULL, NULL, NULL, NULL, '2026-03-17 15:32:29'),
(21, 'Mixue', 'Bart Mall', 0.00, 'Ice cream shop.', NULL, 15.1338691, 120.5917042, 'No', 'Yes', 'Yes', 'No', NULL, NULL, 'pending', 23, NULL, NULL, NULL, NULL, '2026-03-19 07:18:37');

ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fav` (`account_id`, `place_id`),
  KEY `fk_fav_place` (`place_id`),
  CONSTRAINT `fk_fav_account`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fav_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `review_text` text NOT NULL,
  `allow_replies` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reviews_place` (`place_id`),
  KEY `fk_reviews_account` (`account_id`),
  CONSTRAINT `fk_reviews_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_account`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

CREATE TABLE `review_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_like` (`review_id`, `account_id`),
  KEY `fk_like_account` (`account_id`),
  CONSTRAINT `fk_like_review`
    FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_like_account`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `review_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

CREATE TABLE `review_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reply_review` (`review_id`),
  KEY `fk_reply_account` (`account_id`),
  CONSTRAINT `fk_reply_review`
    FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reply_account`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `review_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

CREATE TABLE `user_pins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `note` text DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pins_account` (`account_id`),
  CONSTRAINT `fk_pins_account`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `user_pins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

CREATE OR REPLACE VIEW `v_accounts` AS
SELECT account_id, username, password, profile_pic, created_at, Type
FROM `accounts`;

CREATE OR REPLACE VIEW `v_places` AS
SELECT * FROM `places`;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;