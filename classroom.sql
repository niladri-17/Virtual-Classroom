-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2024 at 07:01 PM
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
  `an_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `an_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`an_id`, `an_class_id`, `an_user_id`, `an_text`, `an_created_at`, `an_updated_at`) VALUES
(33, 12, 3, '<p>hello</p>', '2024-06-19 10:29:37', NULL),
(34, 12, 3, '<ul><li>wefwefe</li></ul><ol><li>efwfwef</li><li>fefewf</li></ol>', '2024-06-19 10:30:19', NULL),
(36, 12, 5, '<p>hi</p>', '2024-06-19 14:12:21', NULL),
(38, 12, 5, '<p>google pic</p>', '2024-06-19 14:15:36', NULL),
(41, 34, 3, '<p>chhapdo</p>', '2024-06-19 17:41:27', NULL),
(42, 39, 3, '<p>#include &lt;stdio.h&gt;</p><p>int main() {</p><p>   // printf() displays the string inside quotation</p><p>   printf(&quot;Hello, World!&quot;);</p><p>   return 0;</p><p>}</p><p></p>', '2024-06-19 20:19:26', '2024-06-20 18:07:29'),
(63, 39, 3, '<p>dummy announcement 1</p>', '2024-06-20 18:56:36', NULL),
(64, 39, 12, '<p>dummy announcement 2</p>', '2024-06-20 18:58:03', NULL);

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
(39, 'Data Structures', 'BCS 3A', 'Computer Science', 'excotq', 3, '2024-06-19 19:27:46'),
(40, 'DAA', 'BCS 3A', 'Computer Science', 'lq793o', 3, '2024-06-19 19:27:57'),
(41, 'DAA', 'BCS 3B', 'Computer Science', 'j6bbs4', 12, '2024-06-19 19:28:33');

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
(6, 12, 40, '2024-06-19 19:28:18'),
(9, 12, 39, '2024-06-20 12:28:46');

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
(45, 39, 63, '', '', '1718889996_2022-11-15.jpg', '2024-06-20 18:56:36'),
(46, 39, 64, '', '', '1718890083_index.php', '2024-06-20 18:58:03'),
(47, 39, 64, '', '', '1718890083_se1.pdf', '2024-06-20 18:58:03');

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
(11, 'Temp ', 'temp6t9@gmail.com', '', '2024-06-19 17:13:06', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocK3KlNXuPi5A7u1k0G4V1vu4yrfZGs8_Arj4bEI0yKsWFPd8Q=s96-c', '107248010894719249390'),
(12, 'test1', 'test1@test.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-19 17:24:06', NULL, NULL, ''),
(13, 'test2', 'test2@gmail.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-20 11:26:55', NULL, NULL, ''),
(14, 'test3', 'test3@test.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-20 11:33:41', NULL, NULL, ''),
(15, 'test4', 'test4@test.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-06-20 11:36:31', NULL, NULL, '');

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
  MODIFY `an_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `asgn_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
