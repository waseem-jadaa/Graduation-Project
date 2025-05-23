-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: May 23, 2025 at 03:06 PM
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
(18, NULL, 20, 'accepted', 9),
(19, NULL, 12, 'accepted', 9),
(22, NULL, 19, 'accepted', 9),
(25, NULL, 21, 'pending', 22),
(26, NULL, 19, 'accepted', 23),
(27, 15, 19, 'accepted', NULL),
(28, NULL, 12, 'accepted', 23),
(29, NULL, 20, 'accepted', 23),
(30, NULL, 21, 'pending', 23),
(31, NULL, 20, 'accepted', 23),
(32, 16, 21, 'accepted', NULL),
(33, NULL, 20, 'accepted', 24),
(34, NULL, 12, 'pending', 23),
(35, 17, 20, 'accepted', NULL),
(36, 15, 12, 'accepted', NULL);

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
  `salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`job_ID`, `employer_ID`, `title`, `description`, `location`, `salary`) VALUES
(15, 24, 'فرقة سفرجية ', 'الشروط: \r\nاخلاق واحترام متبادل مع الناس', 'نابلس', 1500.00),
(16, 23, 'دهان وديكورات جبص ', 'ان يمتلك خبرة جيدة في العمل ', 'راس عطية', 5000.00),
(17, 24, 'صانعة حلوى ', 'لديها ذوق رفيع في تصميمات الجاتوه ', 'نابلس', 3500.00);

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
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(6, 19, 'تم قبول طلبك لوظيفة: كهربائي', 'jobs.php', 0, '2025-05-15 15:27:38'),
(8, 19, 'تم رفض طلبك لوظيفة: بستاني ', 'jobs.php', 1, '2025-05-15 15:37:36'),
(11, 20, 'تم قبول طلبك لوظيفة: شوفير تكسي ', 'jobs.php', 0, '2025-05-15 22:18:17'),
(13, 12, 'تم قبول طلبك لوظيفة: تركيب كرميد ', 'jobs.php', 1, '2025-05-16 16:06:28'),
(23, 19, 'تم قبول طلبك لوظيفة: موسرجي', 'jobs.php', 0, '2025-05-16 17:29:04'),
(24, 20, 'تم قبول طلبك لوظيفة: موسرجي', 'jobs.php', 1, '2025-05-16 17:29:07'),
(25, 20, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-16 18:11:23'),
(26, 12, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-16 18:12:18'),
(38, 12, 'تم قبول طلبك لوظيفة: موسرجي', 'jobs.php', 1, '2025-05-16 18:20:22'),
(41, 20, 'تم قبول طلبك لوظيفة: بستاني ', 'jobs.php', 1, '2025-05-16 18:44:03'),
(42, 19, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-16 18:44:58'),
(45, 19, 'تم قبول طلبك لوظيفة: شوفير تكسي ', 'jobs.php', 1, '2025-05-16 18:47:02'),
(47, 12, 'تم قبول طلبك لوظيفة: بستاني ', 'jobs.php', 1, '2025-05-16 18:50:18'),
(48, 21, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-05-16 19:10:53'),
(49, 19, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-17 00:17:49'),
(50, 24, 'تقدم شخص جديد لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 1, '2025-05-17 00:28:02'),
(51, 19, 'تم قبول طلبك لوظيفة: فرقة سفرجية ', 'jobs.php', 1, '2025-05-17 00:30:06'),
(52, 12, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-17 22:44:31'),
(53, 23, 'تم قبول طلبك من المهني.', '', 1, '2025-05-17 22:48:43'),
(54, 20, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-17 22:49:18'),
(55, 23, 'تم قبول طلبك من المهني.', '', 1, '2025-05-17 22:49:31'),
(56, 21, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-18 01:14:37'),
(57, 20, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-18 01:14:45'),
(58, 23, 'تقدم شخص جديد لوظيفة: دهان وديكورات جبص . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=16', 1, '2025-05-18 01:15:37'),
(59, 21, 'تم قبول طلبك لوظيفة: دهان وديكورات جبص ', 'jobs.php', 0, '2025-05-18 01:17:00'),
(60, 20, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-18 21:46:32'),
(61, 24, 'تم قبول طلبك من المهني.', '', 1, '2025-05-18 21:46:52'),
(62, 23, 'تم قبول طلبك من المهني.', '', 1, '2025-05-18 21:47:06'),
(63, 12, 'لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-05-18 22:17:07'),
(64, 24, 'تقدم شخص جديد لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 1, '2025-05-18 22:27:15'),
(65, 20, 'تم قبول طلبك لوظيفة: صانعة حلوى ', 'jobs.php', 1, '2025-05-18 22:29:33'),
(66, 23, 'تم قبول طلبك من المهني.', '', 1, '2025-05-19 19:30:59'),
(67, 24, 'تقدم شخص جديد لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 1, '2025-05-19 20:53:43'),
(68, 12, 'تم قبول طلبك لوظيفة: فرقة سفرجية ', 'jobs.php', 1, '2025-05-19 20:54:17'),
(69, 19, 'تم رفض طلب توثيق حسابك. سبب الرفض: عدم تطابق المعلومات ', NULL, 1, '2025-05-23 16:04:45');

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
(3, 20, 24, 4, '', '2025-05-18 18:47:38');

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
(3, 12, 'mosab ', 'mashaqi', 'قصارة', 'فولخ , حجر', 'ياصيد', '5', NULL),
(10, 19, 'mayar', 'elyan', 'مصممة ازياء ', 'تصميم بدلات اعراس , تصميم ستايلات حديثة للجنسين ', 'جيوس', '7', 'uploads/profile_photos/19_profile.jpg'),
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

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE `skill` (
  `skill_ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill`
--

INSERT INTO `skill` (`skill_ID`, `name`) VALUES
(1, 'PHP'),
(2, 'MySQL'),
(3, 'UI/UX Design');

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
(12, 'mosab mashaqi', 's12029151@stu.najah.edu', '$2y$10$dH3On4IRQ.bfKIRksuNRLuVutz2PlpxyMXKnE3r999MeQzQ/0Kd.G', 'job_seeker', 'not_verified', NULL),
(19, 'mayar elyan', 'mayarelyan2@gmail.com', '$2y$10$VqSZM1G6/RGevQJILWHUIuEgcEO8XD39DOEbzeBo025YiYK2bV/oS', 'job_seeker', 'rejected', 'عدم تطابق المعلومات '),
(20, 'Ayman Qadome', 'adnansleem370@gmail.com', '$2y$10$elgk53iYroeFKKZl4v0mMe9oYpdq5v70MjGZxQRH16HTa6J7Ous3K', 'job_seeker', 'not_verified', NULL),
(21, 'مالك جدع', 'malek.jada@gmail.com', '$2y$10$x/.gWKGcsj6JPOU8Z5REUO.1mDMKojAuJOzErCAEF1AZkVJs/at06', 'job_seeker', 'verified', NULL),
(23, 'waseem Jadaa', 'rowaid.jadaa@gmail.com', '$2y$10$gKfYflfEUaOSltcS2u3dSu7gHwKGE9k/jreVebNVEmHYND85L15ZG', 'employer', 'verified', NULL),
(24, 'محمد محمد', 'mohamad.790@gmail.com', '$2y$10$4Rj.05sfSG3nIfAjbzcmi.rw/Vz046YY4IAFlhamyKfKYz5rxNUpu', 'employer', 'not_verified', NULL),
(25, 'Admin', 'admin@forsa-pal.com', '$2y$10$OCL4HUKtKi0GRKvUNbg5Ku4vWKUB1MqZQLvCoyBPYZpHtIaJAWnXi', 'admin', 'verified', NULL),
(29, 'كمال خليل', 'kamal.100@gmail.com', '$2y$10$J.ea9eOxYfY07eP1RnpGPefzMADc4TTO0kuD6qYcBRv01Sw6wXveK', 'employer', 'not_verified', NULL),
(30, 'رامي جدع', 'rami.jadaa@gmail.com', '$2y$10$Te0AEM/BcVs7JX24fYEnieI1JZx5yoa3vHLpDI20vzFm0Ez77hs2C', 'job_seeker', 'not_verified', NULL);

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
(4, 19, 'ID', 'uploads/verification_docs/19_1748005456.jpg', 'rejected', 'عدم تطابق المعلومات ', '2025-05-23 16:04:16', '2025-05-23 16:04:45', 'ميار عليان');

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
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `application_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `keyword`
--
ALTER TABLE `keyword`
  MODIFY `keyword_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `professional_ratings`
--
ALTER TABLE `professional_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profile_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
