-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2024 at 08:01 PM
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
-- Database: `classroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `an_id` int(10) NOT NULL,
  `an_class_id` int(10) NOT NULL,
  `an_user_id` int(10) NOT NULL,
  `an_text` text NOT NULL,
  `an_created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`an_id`, `an_class_id`, `an_user_id`, `an_text`, `an_created_at`) VALUES
(6, 12, 5, '<ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>hello</li></ol>', '2024-06-18 12:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `asgn_id` int(10) NOT NULL,
  `asgn_class_id` int(10) NOT NULL,
  `asgn_title` varchar(255) NOT NULL,
  `asgn_description` text NOT NULL,
  `asgn_due_date` date NOT NULL,
  `asgn_created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(10) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `class_section` varchar(255) NOT NULL,
  `class_subject` varchar(255) NOT NULL,
  `class_code` varchar(10) NOT NULL,
  `class_teacher_id` int(10) NOT NULL,
  `class_created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `class_section`, `class_subject`, `class_code`, `class_teacher_id`, `class_created_at`) VALUES
(11, 'Data Structures', 'BCS 3A', 'Computer Science', 'ox8bwe', 5, '2024-06-17 01:34:38'),
(12, 'Data Structures', 'BCS 3A', 'Computer Science', 'rnl7ue', 5, '2024-06-17 01:36:21');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `enrollment_class_id` int(10) NOT NULL,
  `enrolled_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `enrollment_class_id`, `enrolled_at`) VALUES
(2, 3, 12, '2024-06-17 12:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `material_id` int(10) NOT NULL,
  `material_class_id` int(10) NOT NULL,
  `material_an_id` int(10) NOT NULL,
  `material_title` varchar(255) NOT NULL,
  `material_description` text NOT NULL,
  `material_file` varchar(255) NOT NULL,
  `material_uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`material_id`, `material_class_id`, `material_an_id`, `material_title`, `material_description`, `material_file`, `material_uploaded_at`) VALUES
(1, 12, 6, '', '', '1718695429_google.png', '2024-06-18 12:53:49'),
(2, 12, 6, '', '', '1718695429_defaultAvatar.jpg', '2024-06-18 12:53:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` text NOT NULL,
  `user_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_updated_at` datetime DEFAULT NULL,
  `user_image_url` varchar(255) DEFAULT NULL,
  `user_token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_created_at`, `user_updated_at`, `user_image_url`, `user_token`) VALUES
(3, '1052 Niladri Basak', 'nilaronaldo007@gmail.com', '', '2024-06-16 02:59:34', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKbi-FQonx-bxnZ-LFDo6Ms-1Raafc8REl8Ev8K-UxLcHCCrnFP=s96-c', '117905127042921495538'),
(5, 'test1', 'test1@test.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-16 21:03:49', NULL, NULL, ''),
(6, 'test2', 'test2@gmail.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-16 21:20:14', NULL, NULL, ''),
(7, 'test3', 'test3@gmail.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-16 21:22:43', NULL, NULL, ''),
(8, 'test4', 'test4@gmail.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-16 21:24:34', NULL, NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`an_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`asgn_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `an_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `asgn_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
