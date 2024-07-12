-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2024 at 12:56 PM
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
(1, 1, 1, '<p>#include &lt;stdio.h&gt;</p><p>int main() {</p><p>   // printf() displays the string inside quotation</p><p>   printf(&quot;Hello, World!&quot;);</p><p>   return 0;</p><p>}</p><p></p>', '2024-06-19 20:19:26', '2024-06-20 18:07:29'),
(2, 1, 1, '<p>dummy announcement 1</p>', '2024-06-20 18:56:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `asgn_id` int(10) NOT NULL,
  `asgn_class_id` int(10) NOT NULL,
  `asgn_teacher_id` int(10) NOT NULL,
  `asgn_title` varchar(255) NOT NULL,
  `asgn_description` text NOT NULL,
  `asgn_points` varchar(50) NOT NULL,
  `asgn_due_date` varchar(50) NOT NULL,
  `asgn_accept_status` tinyint(4) NOT NULL DEFAULT 1,
  `asgn_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `asgn_edited_at` date DEFAULT NULL
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
(1, 'Data Structures', 'BCS 3A', 'Computer Science', 'excotq', 1, '2024-06-19 19:27:46');

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
(1, 2, 1, '2024-07-12 11:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(10) NOT NULL,
  `grade_class_id` int(10) NOT NULL,
  `grade_asgn_id` int(10) NOT NULL,
  `grade_student_id` int(10) NOT NULL,
  `grade_value` varchar(20) NOT NULL,
  `grade_created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `grade_edited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `material_id` int(10) NOT NULL,
  `material_class_id` int(10) NOT NULL,
  `material_user_id` int(10) NOT NULL,
  `material_an_id` int(10) DEFAULT NULL,
  `material_asgn_id` int(10) DEFAULT NULL,
  `material_file` varchar(255) NOT NULL,
  `material_uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `sub_id` int(10) NOT NULL,
  `sub_class_id` int(10) NOT NULL,
  `sub_asgn_id` int(10) NOT NULL,
  `sub_student_id` int(10) NOT NULL,
  `sub_file` text NOT NULL,
  `sub_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, '1052 Niladri Basak', 'nilaronaldo007@gmail.com', '', '2024-06-16 02:59:34', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKbi-FQonx-bxnZ-LFDo6Ms-1Raafc8REl8Ev8K-UxLcHCCrnFP=s96-c', '117905127042921495538'),
(2, 'test1', 'test1@test.com', '$2y$10$69hellomotherfucker69uctR5y9PDolnAn0qmkLYfFRfHhZgnSEa', '2024-07-12 11:47:59', NULL, NULL, '');

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
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`sub_id`);

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
  MODIFY `an_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `asgn_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `sub_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
