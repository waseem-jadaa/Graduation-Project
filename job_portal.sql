-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 07:08 PM
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
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `application_ID` int(11) NOT NULL,
  `job_ID` int(11) DEFAULT NULL,
  `user_ID` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `employer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`application_ID`, `job_ID`, `user_ID`, `status`, `employer_id`) VALUES
(66, NULL, 19, 'pending', 23),
(68, 15, 19, 'pending', NULL),
(69, 17, 19, 'pending', NULL),
(72, 16, 19, 'accepted', NULL),
(73, NULL, 12, 'pending', 23);

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `job_ID` int(11) NOT NULL,
  `employer_ID` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('published','pending_review','rejected') DEFAULT 'published',
  `rejection_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`job_ID`, `employer_ID`, `title`, `description`, `location`, `salary`, `status`, `rejection_note`, `created_at`) VALUES
(15, 24, 'فرقة سفرجية ', 'الشروط: \r\nاخلاق واحترام متبادل مع الناس', 'نابلس', 1500.00, 'published', NULL, '2025-06-02 00:23:14'),
(16, 23, 'دهان وديكورات جبص ', 'ان يمتلك خبرة جيدة في العمل ', 'راس عطية', 5000.00, 'published', NULL, '2025-06-02 00:23:14'),
(17, 24, 'صانعة حلوى ', 'لديها ذوق رفيع في تصميمات الجاتوه ', 'نابلس', 3500.00, 'published', NULL, '2025-06-02 00:23:14'),
(20, 23, 'خدمات لوجستية', 'مطلوب موظف إداري للعمل بدوام كامل في شركة خدمات لوجستية في رام الله. المهام تشمل تنظيم الجداول، إدارة البريد الإلكتروني، والتنسيق مع الأقسام المختلفة. يشترط إتقان برامج الأوفيس وخبرة لا تقل عن سنة.\r\n', 'جبع - رام الله', 100.00, 'published', NULL, '2025-06-02 12:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `job_keyword`
--

CREATE TABLE `job_keyword` (
  `job_ID` int(11) NOT NULL,
  `keyword_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_skill`
--

CREATE TABLE `job_skill` (
  `job_ID` int(11) NOT NULL,
  `skill_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

CREATE TABLE `keyword` (
  `keyword_ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_ID` int(11) NOT NULL,
  `sender_ID` int(11) DEFAULT NULL,
  `receiver_ID` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_ID`, `sender_ID`, `receiver_ID`, `content`, `seen`, `created_at`) VALUES
(4, 12, 20, 'السلام عليكم', 1, '2025-05-26 18:23:41'),
(5, 20, 12, 'وعليكم السلام', 1, '2025-05-26 18:28:54'),
(6, 12, 20, 'كيف حالك', 1, '2025-05-26 18:39:56'),
(9, 12, 21, 'سلام', 1, '2025-05-27 00:32:53'),
(10, 23, 21, 'هلووو', 1, '2025-05-27 00:47:30'),
(11, 23, 21, 'كيف الحال', 1, '2025-05-27 00:47:35'),
(13, 21, 23, 'اهلاً', 1, '2025-05-27 02:03:16'),
(14, 23, 19, 'مرحباً', 1, '2025-05-27 02:58:13'),
(15, 19, 23, 'اهلاً', 1, '2025-05-27 03:12:14'),
(16, 19, 23, 'كيف بقدر اساعدك؟', 1, '2025-05-27 03:17:28'),
(17, 21, 23, 'تفضل', 1, '2025-05-27 04:09:16'),
(38, 23, 21, 'مساء الخير', 1, '2025-06-02 01:51:49'),
(39, 19, 23, 'مرحبا', 1, '2025-06-02 18:00:28'),
(40, 23, 19, 'هلا', 1, '2025-06-02 18:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `sender_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(98, 23, 21, 'مالك جدع قام بقبول طلبك.', '', 1, '2025-06-01 23:40:57'),
(104, 24, 21, 'مالك جدع تقدم لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 0, '2025-06-02 13:16:51'),
(105, 21, 29, 'لديك طلب جديد من كمال خليل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-06-02 13:18:44'),
(106, 24, 20, 'Ayman Qadome تقدم لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 0, '2025-06-02 13:21:38'),
(128, 24, 19, 'Mayar Elyan تقدم لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 0, '2025-06-02 17:22:15'),
(129, 24, 19, 'Mayar Elyan تقدم لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 0, '2025-06-02 17:22:57'),
(133, 23, 19, 'Mayar Elyan تقدم لوظيفة: دهان وديكورات جبص . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=16', 1, '2025-06-02 18:00:13'),
(134, 19, 23, 'waseem Jadaa قبل طلبك لوظيفة: دهان وديكورات جبص ', 'jobs.php', 1, '2025-06-02 18:00:53'),
(135, 12, 23, 'لديك طلب جديد من waseem Jadaa. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-06-02 19:00:22');

-- --------------------------------------------------------

--
-- Table structure for table `professional_ratings`
--

CREATE TABLE `professional_ratings` (
  `id` int(11) NOT NULL,
  `professional_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professional_ratings`
--

INSERT INTO `professional_ratings` (`id`, `professional_id`, `employer_id`, `rating`, `review`, `created_at`) VALUES
(1, 12, 23, 4, '', '2025-05-18 16:54:56'),
(2, 20, 23, 3, '', '2025-05-18 18:42:30'),
(3, 20, 24, 4, '', '2025-05-18 18:47:38'),
(4, 30, 23, 5, '', '2025-05-24 13:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profile_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profile_ID`, `User_ID`, `first_name`, `last_name`, `bio`, `skills`, `location`, `experience`, `profile_photo`) VALUES
(3, 12, 'Musab ', 'Mashaqi', 'قصارة', 'فولخ , حجر', 'نابلس', '5', 'uploads/profile_photos/12_profile.jpg'),
(10, 19, 'Mayar', 'Elyan', 'مصممة ازياء ', 'تصميم بدلات اعراس , تصميم ستايلات حديثة للجنسين ', 'جيوس', '7', 'uploads/profile_photos/19_profile.jpg'),
(11, 20, 'Ayman', 'Qadome', 'سياحة وسفر ', 'سياحة وسفر ', 'كفر قدوم', '21', 'uploads/profile_photos/20_profile.jpg'),
(12, 21, 'مالك', 'جدع', 'حلاق', 'حلاقة شعر لجميع الفئات العمرية , حلاقة عريس , تنظيف بشرة , سشوار , تحديد لحية ', 'قلقيلية - حبلة', '5', 'uploads/profile_photos/21_profile.jpg'),
(14, 23, 'waseem', 'Jadaa', 'مقاول اعمال حرة , جميع المجالات', '', 'جبع - رام الله', '0', 'uploads/profile_photos/23_profile.jpg'),
(15, 24, 'محمد', 'محمد', 'ادارة اعراس ', '', 'جنين', '0', 'uploads/profile_photos/24_profile.jpg'),
(16, 29, 'كمال', 'خليل', 'مقاول اعمال حرة ', '', 'قلقيلية', '0', 'uploads/profile_photos/29_profile.png'),
(17, 30, 'رامي', 'جدع', 'الزراعة', 'رش المبيدات الحشرية , رش المزروعات , تتبع الري للمزروعات ', 'حبلة', '25', 'uploads/profile_photos/30_profile.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `user_id`, `job_id`, `saved_at`) VALUES
(27, 23, 20, '2025-06-02 13:11:14'),
(29, 19, 15, '2025-06-02 14:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE `skill` (
  `skill_ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('job_seeker','employer','admin') NOT NULL,
  `verification_status` enum('not_verified','pending','verified','rejected') DEFAULT 'not_verified',
  `verification_note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_ID`, `name`, `email`, `password`, `role`, `verification_status`, `verification_note`) VALUES
(12, 'Musab Mashaqi', 's12029151@stu.najah.edu', '$2y$10$dH3On4IRQ.bfKIRksuNRLuVutz2PlpxyMXKnE3r999MeQzQ/0Kd.G', 'job_seeker', 'not_verified', NULL),
(19, 'Mayar Elyan', 'mayarelyan2@gmail.com', '$2y$10$VqSZM1G6/RGevQJILWHUIuEgcEO8XD39DOEbzeBo025YiYK2bV/oS', 'job_seeker', 'rejected', 'انتي '),
(20, 'Ayman Qadome', 'adnansleem370@gmail.com', '$2y$10$elgk53iYroeFKKZl4v0mMe9oYpdq5v70MjGZxQRH16HTa6J7Ous3K', 'job_seeker', 'not_verified', NULL),
(21, 'مالك جدع', 'malek.jada@gmail.com', '$2y$10$x/.gWKGcsj6JPOU8Z5REUO.1mDMKojAuJOzErCAEF1AZkVJs/at06', 'job_seeker', 'verified', NULL),
(23, 'waseem Jadaa', 'rowaid.jadaa@gmail.com', '$2y$10$gKfYflfEUaOSltcS2u3dSu7gHwKGE9k/jreVebNVEmHYND85L15ZG', 'employer', 'verified', NULL),
(24, 'محمد محمد', 'mohamad.790@gmail.com', '$2y$10$4Rj.05sfSG3nIfAjbzcmi.rw/Vz046YY4IAFlhamyKfKYz5rxNUpu', 'employer', 'verified', NULL),
(25, 'Admin', 'admin@forsa-pal.com', '$2y$10$OCL4HUKtKi0GRKvUNbg5Ku4vWKUB1MqZQLvCoyBPYZpHtIaJAWnXi', 'admin', 'verified', NULL),
(29, 'كمال خليل', 'kamal.100@gmail.com', '$2y$10$J.ea9eOxYfY07eP1RnpGPefzMADc4TTO0kuD6qYcBRv01Sw6wXveK', 'employer', 'not_verified', NULL),
(30, 'رامي جدع', 'rami.jadaa@gmail.com', '$2y$10$Te0AEM/BcVs7JX24fYEnieI1JZx5yoa3vHLpDI20vzFm0Ez77hs2C', 'job_seeker', 'verified', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_skill`
--

CREATE TABLE `user_skill` (
  `user_ID` int(11) NOT NULL,
  `skill_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_verification`
--

CREATE TABLE `user_verification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_type` varchar(20) NOT NULL,
  `doc_path` varchar(255) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `legal_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_verification`
--

INSERT INTO `user_verification` (`id`, `user_id`, `doc_type`, `doc_path`, `status`, `note`, `created_at`, `reviewed_at`, `legal_name`) VALUES
(1, 23, 'ID', 'uploads/verification_docs/23_1747928179.jpg', 'accepted', NULL, '2025-05-22 18:36:19', '2025-05-22 19:28:22', ''),
(2, 21, 'ID', 'uploads/verification_docs/21_1748002601.jpg', 'accepted', NULL, '2025-05-23 15:16:41', '2025-05-23 15:23:03', 'مالك جميل عبدرحيم جدع'),
(4, 19, 'ID', 'uploads/verification_docs/19_1748005456.jpg', 'rejected', 'عدم تطابق المعلومات ', '2025-05-23 16:04:16', '2025-05-23 16:04:45', 'ميار عليان'),
(5, 30, 'ID', 'uploads/verification_docs/30_1748091771.jpg', 'accepted', NULL, '2025-05-24 16:02:51', '2025-05-24 16:15:48', 'رامي محمد جدع'),
(6, 24, 'Passport', 'uploads/verification_docs/24_1748176246.jpg', 'accepted', NULL, '2025-05-25 15:30:46', '2025-05-25 15:32:11', 'محمد عبد الرؤوف جدع'),
(7, 19, 'ID', 'uploads/verification_docs/19_1748525967.png', 'rejected', 'انتي ', '2025-05-29 16:39:27', '2025-05-29 16:40:47', 'ميار عليان');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`application_ID`),
  ADD KEY `job_ID` (`job_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`job_ID`),
  ADD KEY `employer_ID` (`employer_ID`);

--
-- Indexes for table `job_keyword`
--
ALTER TABLE `job_keyword`
  ADD PRIMARY KEY (`job_ID`,`keyword_ID`),
  ADD KEY `keyword_ID` (`keyword_ID`);

--
-- Indexes for table `job_skill`
--
ALTER TABLE `job_skill`
  ADD PRIMARY KEY (`job_ID`,`skill_ID`),
  ADD KEY `skill_ID` (`skill_ID`);

--
-- Indexes for table `keyword`
--
ALTER TABLE `keyword`
  ADD PRIMARY KEY (`keyword_ID`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_ID`),
  ADD KEY `sender_ID` (`sender_ID`),
  ADD KEY `receiver_ID` (`receiver_ID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_notifications_sender` (`sender_id`);

--
-- Indexes for table `professional_ratings`
--
ALTER TABLE `professional_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professional_id` (`professional_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`profile_ID`),
  ADD UNIQUE KEY `User_ID` (`User_ID`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_saved` (`user_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`skill_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_skill`
--
ALTER TABLE `user_skill`
  ADD PRIMARY KEY (`user_ID`,`skill_ID`),
  ADD KEY `skill_ID` (`skill_ID`);

--
-- Indexes for table `user_verification`
--
ALTER TABLE `user_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `application_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `keyword`
--
ALTER TABLE `keyword`
  MODIFY `keyword_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `professional_ratings`
--
ALTER TABLE `professional_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profile_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `skill`
--
ALTER TABLE `skill`
  MODIFY `skill_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user_verification`
--
ALTER TABLE `user_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`job_ID`) REFERENCES `job` (`job_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `job`
--
ALTER TABLE `job`
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`employer_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `job_keyword`
--
ALTER TABLE `job_keyword`
  ADD CONSTRAINT `job_keyword_ibfk_1` FOREIGN KEY (`job_ID`) REFERENCES `job` (`job_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_keyword_ibfk_2` FOREIGN KEY (`keyword_ID`) REFERENCES `keyword` (`keyword_ID`) ON DELETE CASCADE;

--
-- Constraints for table `job_skill`
--
ALTER TABLE `job_skill`
  ADD CONSTRAINT `job_skill_ibfk_1` FOREIGN KEY (`job_ID`) REFERENCES `job` (`job_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_skill_ibfk_2` FOREIGN KEY (`skill_ID`) REFERENCES `skill` (`skill_ID`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_sender` FOREIGN KEY (`sender_id`) REFERENCES `user` (`User_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `professional_ratings`
--
ALTER TABLE `professional_ratings`
  ADD CONSTRAINT `professional_ratings_ibfk_1` FOREIGN KEY (`professional_id`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `professional_ratings_ibfk_2` FOREIGN KEY (`employer_id`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_ID`);

--
-- Constraints for table `user_skill`
--
ALTER TABLE `user_skill`
  ADD CONSTRAINT `user_skill_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_skill_ibfk_2` FOREIGN KEY (`skill_ID`) REFERENCES `skill` (`skill_ID`) ON DELETE CASCADE;

--
-- Constraints for table `user_verification`
--
ALTER TABLE `user_verification`
  ADD CONSTRAINT `user_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
