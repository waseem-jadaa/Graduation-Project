-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jun 07, 2025 at 11:11 PM
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
(72, 16, 19, 'accepted', NULL),
(73, NULL, 12, 'accepted', 23),
(81, 16, 21, 'rejected', NULL),
(83, 16, 21, 'rejected', NULL),
(85, 16, 21, 'rejected', NULL),
(92, 16, 21, 'pending', NULL),
(96, 32, 21, 'accepted', NULL),
(99, NULL, 20, 'pending', 23),
(100, 52, 21, 'accepted', NULL),
(101, 26, 12, 'accepted', NULL),
(102, 49, 12, 'pending', NULL),
(103, 46, 12, 'pending', NULL),
(104, 34, 12, 'pending', NULL),
(105, 42, 12, 'pending', NULL),
(106, 29, 12, 'accepted', NULL),
(107, 40, 12, 'pending', NULL),
(109, 55, 12, 'pending', NULL),
(110, 57, 12, 'pending', NULL),
(111, 33, 12, 'pending', NULL),
(112, 27, 12, 'pending', NULL),
(113, NULL, 21, 'pending', 23),
(114, 42, 21, 'pending', NULL),
(115, 53, 21, 'pending', NULL),
(116, NULL, 21, 'pending', 24),
(117, 65, 12, 'pending', NULL),
(118, NULL, 12, 'pending', 23),
(119, 16, 12, 'accepted', NULL),
(120, 56, 19, 'rejected', NULL),
(121, 50, 19, 'accepted', NULL),
(122, 56, 19, 'pending', NULL),
(123, 58, 19, 'pending', NULL),
(124, 42, 19, 'pending', NULL),
(125, 34, 19, 'pending', NULL),
(126, 39, 19, 'pending', NULL),
(127, 41, 19, 'pending', NULL),
(128, 73, 12, 'pending', NULL);

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
  `status` enum('published','pending_review','rejected','filled') DEFAULT 'published',
  `rejection_note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`job_ID`, `employer_ID`, `title`, `description`, `location`, `salary`, `status`, `rejection_note`, `created_at`) VALUES
(16, 23, 'دهان وديكورات جبص ', 'ان يمتلك خبرة جيدة في العمل ', 'راس عطية', 5000.00, 'filled', NULL, '2025-06-02 00:23:14'),
(22, 23, 'مسيرة فلسطينية ضد الاحتلال', 'مطلوب متظاهرين للمشاركة في الاحتجاجات والمظاهرات السلمية. يشترط الالتزام بالتعليمات والانضباط أثناء الفعالية. يجب توفر القدرة على الوقوف لفترات طويلة والعمل ضمن فريق.', 'جنين', 0.00, 'rejected', 'عدم تطابق المعلومات ', '2025-06-03 08:42:48'),
(26, 29, 'عامل حجر', 'مطلوب عامل حجر للعمل في مشاريع بناء.', 'نابلس', 2400.00, 'filled', NULL, '2025-06-05 11:30:22'),
(27, 24, 'مصممة أزياء', 'مطلوب مصممة أزياء لتصميم ملابس عصرية وحديثة.', 'جنين', 3000.00, 'published', NULL, '2025-06-05 11:30:22'),
(28, 24, 'مصممة بدلات أعراس', 'مطلوب مصممة بدلات أعراس للعمل في مشغل نسائي.', 'جنين', 3200.00, 'published', NULL, '2025-06-05 11:30:22'),
(29, 23, 'مدرب سباحة', 'مطلوب مدرب سباحة محترف للعمل في نادي رياضي.', 'كفر قدوم', 2800.00, 'filled', NULL, '2025-06-05 11:30:22'),
(30, 29, 'سائق ونش', 'مطلوب سائق ونش لنقل المعدات الثقيلة.', 'كفر قدوم', 2700.00, 'published', NULL, '2025-06-05 11:30:22'),
(32, 24, 'حلاق عرسان', 'مطلوب حلاق متخصص في تجهيز العرسان.', 'حجة', 300.00, 'published', NULL, '2025-06-05 11:30:22'),
(33, 23, 'مقاول أعمال بناء', 'مطلوب مقاول أعمال بناء لمشاريع سكنية وتجارية.', 'رام الله', 4000.00, 'published', NULL, '2025-06-05 11:30:22'),
(34, 23, 'مقاول تشطيبات', 'مطلوب مقاول تشطيبات داخلية وخارجية.', 'جبع', 3800.00, 'published', NULL, '2025-06-05 11:30:22'),
(35, 24, 'منسق أعراس', 'مطلوب منسق أعراس لإدارة وتنظيم حفلات الزفاف.', 'جنين', 3500.00, 'published', NULL, '2025-06-05 11:30:22'),
(36, 24, 'مدير قاعة أفراح', 'مطلوب مدير قاعة أفراح بخبرة في إدارة المناسبات.', 'جنين', 3400.00, 'published', NULL, '2025-06-05 11:30:22'),
(37, 29, 'مقاول مشاريع صغيرة', 'مطلوب مقاول لإدارة مشاريع صغيرة ومتوسطة.', 'قلقيلية', 3600.00, 'published', NULL, '2025-06-05 11:30:22'),
(39, 23, 'عامل رش مبيدات', 'مطلوب عامل رش مبيدات لمزارع الخضروات.', 'حجة', 2100.00, 'published', NULL, '2025-06-05 11:30:22'),
(40, 29, 'عامل زراعة', 'مطلوب عامل زراعة للعمل في مزارع حجة.', 'حجة', 2000.00, 'published', NULL, '2025-06-05 11:30:22'),
(41, 23, 'مهندس زراعي', 'مطلوب مهندس زراعي للإشراف على مزارع حجة.', 'حجة', 3200.00, 'published', NULL, '2025-06-05 11:30:22'),
(42, 23, 'عامل بناء', 'مطلوب عامل بناء لمشاريع في نابلس.', 'نابلس', 2200.00, 'published', NULL, '2025-06-05 11:30:22'),
(43, 24, 'عامل نظافة', 'مطلوب عامل نظافة لمؤسسة تعليمية.', 'جنين', 1500.00, 'published', NULL, '2025-06-05 11:30:22'),
(44, 29, 'سائق شاحنة', 'مطلوب سائق شاحنة لنقل البضائع.', 'قلقيلية', 2500.00, 'published', NULL, '2025-06-05 11:30:22'),
(45, 24, 'كهربائي منازل', 'مطلوب كهربائي لتركيب وصيانة كهرباء المنازل.', 'جنين', 2300.00, 'published', NULL, '2025-06-05 11:30:22'),
(46, 23, 'عامل تركيب حجر', 'مطلوب عامل ماهر في تركيب جميع أنواع حجر البناء.', 'نابلس', 550.00, 'published', NULL, '2025-06-05 12:04:31'),
(47, 29, 'فني فوالج وصيانة', 'مطلوب فني للعمل في صيانة وتركيب الفوالج.', 'نابلس', 2400.00, 'published', NULL, '2025-06-05 12:04:31'),
(48, 24, 'خياطة فساتين سهرة', 'مطلوب خياطة ماهرة في تفصيل وخياطة فساتين السهرة.', 'جنين', 2900.00, 'published', NULL, '2025-06-05 12:04:31'),
(49, 24, 'مساعدة مصممة أزياء', 'مطلوب مساعدة مصممة للعمل في ورشة تصميم أزياء.', 'جنين', 2000.00, 'published', NULL, '2025-06-05 12:04:31'),
(50, 23, 'مشغل ونش بناء', 'مطلوب مشغل ونش بخبرة في مواقع البناء.', 'كفر قدوم', 3000.00, 'filled', NULL, '2025-06-05 12:04:31'),
(51, 29, 'منقذ سباحة', 'مطلوب منقذ سباحة للعمل في مسبح عام.', 'كفر قدوم', 2200.00, 'published', NULL, '2025-06-05 12:04:31'),
(52, 24, 'حلاق في صالون رجالي', 'مطلوب حلاق بخبرة في أحدث قصات الشعر.', 'قلقيلية', 2400.00, 'published', NULL, '2025-06-05 12:04:31'),
(53, 23, 'متخصص حلاقة عرسان', 'مطلوب حلاق متخصص في تجهيز العرسان والاهتمام بالتفاصيل.', 'حجة', 2500.00, 'published', NULL, '2025-06-05 12:04:31'),
(54, 24, 'مشرف موقع بناء', 'مطلوب مشرف موقع لمتابعة سير العمل في المشاريع.', 'رام الله', 3700.00, 'published', NULL, '2025-06-05 12:04:31'),
(55, 29, 'مدير مشاريع تشييد', 'مطلوب مدير مشاريع للإشراف على مشاريع إنشائية كبيرة.', 'جبع - رام الله', 4200.00, 'published', NULL, '2025-06-05 12:04:31'),
(56, 23, 'منسق فعاليات ومناسبات', 'مطلوب منسق لتنظيم وإدارة مختلف الفعاليات والمناسبات.', 'جنين', 3100.00, 'published', NULL, '2025-06-05 12:04:31'),
(57, 29, 'مساعد مدير قاعة', 'مطلوب مساعد لمدير قاعة أفراح في جنين.', 'جنين', 2600.00, 'published', NULL, '2025-06-05 12:04:31'),
(58, 23, 'مراقب جودة بناء', 'مطلوب مراقب جودة لمتابعة تطبيق المعايير في مشاريع البناء.', 'قلقيلية', 3500.00, 'published', NULL, '2025-06-05 12:04:31'),
(59, 24, 'مقاول تشطيبات داخلية', 'مطلوب مقاول متخصص في أعمال التشطيبات الداخلية.', 'قلقيلية', 3400.00, 'published', NULL, '2025-06-05 12:04:31'),
(60, 23, 'أخصائي مكافحة آفات', 'مطلوب أخصائي لرش ومكافحة الآفات الزراعية.', 'حجة', 2300.00, 'published', NULL, '2025-06-05 12:04:31'),
(61, 24, 'عامل زراعي بخبرة', 'مطلوب عامل زراعي بخبرة في التعامل مع المحاصيل.', 'حجة', 2100.00, 'published', NULL, '2025-06-05 12:04:31'),
(63, 23, 'مسيرة فلسطينية', 'مطلوب شباب للمشاركة في مسيرة سلمية لدعم الحقوق الفلسطينية، مع ضرورة الالتزام بالتصرف المسؤول والانضباط أثناء الفعالية.', 'نابلس ', 100.00, 'pending_review', NULL, '2025-06-05 15:36:49'),
(64, 23, 'مسيرة', 'مطلوب شباب للمشاركة في مظاهرة سلمية ضد الاحتلال، مع الالتزام بالتعليمات والتصرف بشكل مسؤول خلال الحدث.', 'نابلس', 0.00, 'pending_review', NULL, '2025-06-06 14:45:11'),
(65, 23, 'تمديدات كهربائية', 'مطلوب كهربائي لتوصيل وتمديد الكهرباء لبيت جديد قيد الإنشاء. يشترط الخبرة في أعمال التمديدات الكهربائية للمباني الجديدة والقدرة على قراءة المخططات وتنفيذ الأعمال بدقة وأمان.', 'نابلس ', 3500.00, 'published', NULL, '2025-06-07 13:22:39'),
(66, 24, 'مكيفات ', 'مطلوب فني محترف لتركيب مكيفات الهواء. يشترط الخبرة في تثبيت وتشغيل أجهزة التكييف، وفهم أساسيات الكهرباء والسلامة المهنية. يجب الالتزام بالجودة والدقة في العمل. الوظيفة متاحة للرجال فقط.', 'نابلس', 400.00, 'published', NULL, '2025-06-07 14:07:21'),
(67, 24, 'جنان ', 'مطلوب عامل للعناية بالحدائق وترتيبها بانتظام، يشترط الخبرة في تنظيم النباتات وصيانة المساحات الخضراء والقدرة على الحفاظ على نظافة وترتيب الحديقة.', 'رام الله', 1500.00, 'published', NULL, '2025-06-07 16:37:03'),
(68, 23, 'خباز', 'اريد شخص لديه خبرة سابقة في العجين وخبز جميع انواع المخبوزات', 'قلقيلية', 3000.00, 'published', NULL, '2025-06-07 16:50:37'),
(69, 23, 'جرسون', 'مطلوب شخص لبق وحسن التعامل مع الزبائن للعمل في مطعم. يجب أن يتمتع بمهارات تواصل قوية والقدرة على التعامل مع مختلف الأشخاص بأدب واحترام.', 'نابلس ', 3000.00, 'published', NULL, '2025-06-07 17:13:36'),
(70, 24, 'طباخ اعراس', 'اريد طباخ اعراس ماهر ', 'ياصيد', 1500.00, 'published', NULL, '2025-06-07 17:36:42'),
(71, 24, 'ميكانيكي', 'تعطلت سيارتي في نصف الطريق', 'نابلس ', 1000.00, 'published', NULL, '2025-06-07 17:43:29'),
(72, 24, 'قهوجي', 'صنع القهوة للاعراس ', 'نابلس ', 1500.00, 'published', NULL, '2025-06-07 17:55:14'),
(73, 24, 'تزيين صالات افراح', 'اريد فرقة متخصصة في تزيين الاعراس ', 'نابلس', 1000.00, 'published', NULL, '2025-06-07 17:58:42');

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
(40, 23, 19, 'هلا', 1, '2025-06-02 18:01:01'),
(41, 12, 23, 'مرحبا', 1, '2025-06-04 19:35:49'),
(42, 23, 12, 'اهلين', 1, '2025-06-04 19:36:49'),
(43, 23, 12, 'شو الاخبار', 1, '2025-06-04 19:37:03'),
(44, 12, 23, 'الحمد الله', 1, '2025-06-04 19:38:03'),
(45, 23, 21, 'اين انت', 0, '2025-06-05 15:27:12');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `employer_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `sender_id`, `message`, `link`, `is_read`, `created_at`, `employer_photo`) VALUES
(98, 23, 21, 'مالك جدع قام بقبول طلبك.', '', 1, '2025-06-01 23:40:57', NULL),
(104, 24, 21, 'مالك جدع تقدم لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 0, '2025-06-02 13:16:51', NULL),
(106, 24, 20, 'Ayman Qadome تقدم لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 0, '2025-06-02 13:21:38', NULL),
(128, 24, 19, 'Mayar Elyan تقدم لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 0, '2025-06-02 17:22:15', NULL),
(129, 24, 19, 'Mayar Elyan تقدم لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 0, '2025-06-02 17:22:57', NULL),
(133, 23, 19, 'Mayar Elyan تقدم لوظيفة: دهان وديكورات جبص . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=16', 1, '2025-06-02 18:00:13', NULL),
(134, 19, 23, 'waseem Jadaa قبل طلبك لوظيفة: دهان وديكورات جبص ', 'jobs.php', 1, '2025-06-02 18:00:53', NULL),
(135, 12, 23, 'لديك طلب جديد من waseem Jadaa. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 1, '2025-06-02 19:00:22', NULL),
(143, 24, 12, 'Musab Mashaqi تقدم لوظيفة: صانعة حلوى . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=17', 1, '2025-06-03 11:48:38', NULL),
(144, 23, 21, 'مالك جدع تقدم لوظيفة: حلاقة عريس. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=24', 1, '2025-06-05 01:00:09', NULL),
(145, 21, 23, 'waseem Jadaa رفض طلبك لوظيفة: حلاقة عريس', 'jobs.php', 1, '2025-06-05 01:04:46', NULL),
(148, 23, 21, 'مالك جدع تقدم لوظيفة: حلاقة عريس. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=24', 1, '2025-06-05 03:23:15', NULL),
(149, 23, 21, 'مالك جدع تقدم لوظيفة: دهان وديكورات جبص . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=16', 1, '2025-06-05 03:23:27', NULL),
(152, 23, 21, 'مالك جدع تقدم لوظيفة: حلاقة عريس. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=24', 1, '2025-06-05 03:23:57', NULL),
(164, 23, 21, 'مالك جدع تقدم لوظيفة: حلاقة عريس. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=24', 1, '2025-06-05 03:32:35', NULL),
(165, 23, 21, 'مالك جدع تقدم لوظيفة: تصليح سيارة. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=23', 1, '2025-06-05 03:32:43', NULL),
(166, 24, 21, 'مالك جدع تقدم لوظيفة: فرقة سفرجية . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=15', 1, '2025-06-05 03:32:48', NULL),
(170, 23, 21, 'مالك جدع تقدم لوظيفة: تصليح سيارة. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=23', 1, '2025-06-05 03:33:42', NULL),
(180, 23, 21, 'مالك جدع تقدم لوظيفة: خدمات لوجستية. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=20', 1, '2025-06-05 04:18:35', NULL),
(181, 21, 23, 'waseem Jadaa قبل طلبك لوظيفة: خدمات لوجستية', 'jobs.php', 1, '2025-06-05 04:18:59', NULL),
(182, 21, 24, 'محمد محمد قبل طلبك لوظيفة: صانعة حلوى ', 'jobs.php', 1, '2025-06-05 04:23:38', NULL),
(188, 23, 12, 'Musab Mashaqi قام بقبول طلبك.', '', 0, '2025-06-05 04:53:24', NULL),
(196, 24, 21, 'مالك جدع تقدم لوظيفة: حلاق عرسان. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=32', 1, '2025-06-05 14:31:20', NULL),
(197, 21, 24, 'محمد محمد قبل طلبك لوظيفة: حلاق عرسان', 'jobs.php', 1, '2025-06-05 14:31:37', NULL),
(198, 23, 12, 'Musab Mashaqi تقدم لوظيفة: عامل فوالج. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=25', 1, '2025-06-05 14:33:49', NULL),
(199, 19, 24, 'محمد محمد قبل طلبك لوظيفة: فرقة سفرجية ', 'jobs.php', 0, '2025-06-05 14:34:15', NULL),
(200, 21, 24, 'محمد محمد قبل طلبك لوظيفة: فرقة سفرجية ', 'jobs.php', 1, '2025-06-05 14:34:15', NULL),
(201, 12, 23, 'waseem Jadaa قبل طلبك لوظيفة: عامل فوالج', 'jobs.php', 1, '2025-06-05 14:35:39', NULL),
(202, 12, 23, 'تم تحديث تفاصيل الوظيفة التي تقدمت لها', 'job.php?job_id=25', 0, '2025-06-05 14:36:30', NULL),
(203, 12, 23, 'تم إغلاق الوظيفة التي تقدمت لها', 'job.php?job_id=25', 0, '2025-06-05 14:36:34', NULL),
(207, 24, 21, 'مالك جدع تقدم لوظيفة: حلاق رجالي. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=31', 1, '2025-06-05 15:00:07', NULL),
(208, 21, 24, 'محمد محمد قبل طلبك لوظيفة: حلاق رجالي', 'jobs.php', 0, '2025-06-05 15:00:32', NULL),
(209, 21, 24, 'تم تحديث تفاصيل الوظيفة التي تقدمت لها', 'job.php?job_id=31', 0, '2025-06-05 15:00:45', 'uploads/profile_photos/24_profile.jpg'),
(210, 21, 24, 'تم إغلاق الوظيفة التي تقدمت لها', 'job.php?job_id=31', 0, '2025-06-05 15:02:13', 'uploads/profile_photos/24_profile.jpg'),
(211, 20, 23, 'لديك طلب جديد من waseem Jadaa. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-06-05 15:17:15', NULL),
(212, 24, 21, 'مالك جدع تقدم لوظيفة: حلاق في صالون رجالي. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=52', 1, '2025-06-05 15:55:49', NULL),
(213, 21, 24, 'محمد محمد قبل طلبك لوظيفة: حلاق في صالون رجالي', 'jobs.php', 0, '2025-06-05 16:14:10', NULL),
(214, 29, 12, 'Musab Mashaqi تقدم لوظيفة: عامل حجر. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=26', 1, '2025-06-05 16:25:44', NULL),
(215, 12, 29, 'كمال خليل قبل طلبك لوظيفة: عامل حجر', 'jobs.php', 0, '2025-06-05 16:26:28', NULL),
(216, 24, 12, 'Musab Mashaqi تقدم لوظيفة: مساعدة مصممة أزياء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=49', 0, '2025-06-05 16:29:01', NULL),
(217, 23, 12, 'Musab Mashaqi تقدم لوظيفة: عامل تركيب حجر. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=46', 0, '2025-06-05 16:33:14', NULL),
(218, 23, 12, 'Musab Mashaqi تقدم لوظيفة: مقاول تشطيبات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=34', 0, '2025-06-05 16:33:17', NULL),
(219, 23, 12, 'Musab Mashaqi تقدم لوظيفة: عامل بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=42', 1, '2025-06-05 16:33:47', NULL),
(220, 23, 12, 'Musab Mashaqi تقدم لوظيفة: مدرب سباحة. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=29', 1, '2025-06-05 17:32:22', NULL),
(221, 12, 23, 'waseem Jadaa قبل طلبك لوظيفة: مدرب سباحة', 'jobs.php', 1, '2025-06-05 17:32:37', NULL),
(222, 29, 12, 'Musab Mashaqi تقدم لوظيفة: عامل زراعة. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=40', 0, '2025-06-05 17:33:50', NULL),
(223, 29, 12, 'Musab Mashaqi تقدم لوظيفة: مقاول ترميمات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=38', 0, '2025-06-05 17:34:01', NULL),
(224, 12, 29, 'تم تحديث تفاصيل الوظيفة التي تقدمت لها', 'job.php?job_id=38', 1, '2025-06-05 17:34:44', 'uploads/profile_photos/29_profile.png'),
(225, 12, 29, 'تم إغلاق الوظيفة التي تقدمت لها', 'job.php?job_id=38', 1, '2025-06-05 17:34:54', 'uploads/profile_photos/29_profile.png'),
(226, 29, 12, 'Musab Mashaqi تقدم لوظيفة: مدير مشاريع تشييد. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=55', 0, '2025-06-05 17:35:16', NULL),
(227, 12, 29, 'تم تحديث تفاصيل الوظيفة التي تقدمت لها', 'job.php?job_id=55', 1, '2025-06-05 17:35:39', 'uploads/profile_photos/29_profile.png'),
(228, 29, 12, 'Musab Mashaqi تقدم لوظيفة: مساعد مدير قاعة. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=57', 0, '2025-06-05 17:44:40', NULL),
(229, 23, 12, 'Musab Mashaqi تقدم لوظيفة: مقاول أعمال بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=33', 0, '2025-06-05 17:44:48', NULL),
(230, 12, 23, 'تم تحديث تفاصيل الوظيفة التي تقدمت لها', 'job.php?job_id=46', 0, '2025-06-05 18:27:05', 'uploads/profile_photos/23_profile.jpg'),
(231, 24, 12, 'Musab Mashaqi تقدم لوظيفة: مصممة أزياء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=27', 0, '2025-06-05 20:10:06', NULL),
(232, 21, 23, 'لديك طلب جديد من waseem Jadaa. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-06-07 13:36:46', NULL),
(233, 23, 21, 'مالك جدع تقدم لوظيفة: عامل بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=42', 0, '2025-06-07 15:28:32', NULL),
(234, 23, 21, 'مالك جدع تقدم لوظيفة: متخصص حلاقة عرسان. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=53', 0, '2025-06-07 15:34:12', NULL),
(235, 24, 25, 'تمت الموافقة على نشر وظيفتك: \"ميكانيكي\" من قِبل صفحة الأدمن.', NULL, 0, '2025-06-07 20:44:11', NULL),
(236, 21, 24, 'لديك طلب جديد من محمد محمد. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-06-07 21:13:02', NULL),
(237, 23, 12, 'Musab Mashaqi تقدم لوظيفة: تمديدات كهربائية. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=65', 0, '2025-06-07 21:19:01', NULL),
(238, 12, 23, 'لديك طلب جديد من waseem Jadaa. يمكنك قبول أو رفض الطلب.', 'manage_professional_requests.php', 0, '2025-06-07 21:34:09', NULL),
(239, 19, 25, 'تم رفض طلب توثيق حسابك.', NULL, 1, '2025-06-07 22:04:40', NULL),
(240, 29, 25, 'تمت الموافقة على توثيق حسابك.', NULL, 0, '2025-06-07 22:09:57', NULL),
(241, 19, 25, 'تم رفض طلب توثيق حسابك.', NULL, 0, '2025-06-07 22:18:17', NULL),
(242, 19, 25, 'تمت الموافقة على توثيق حسابك.', NULL, 0, '2025-06-07 22:20:07', NULL),
(243, 19, 25, 'تمت الموافقة على توثيق حسابك.', NULL, 0, '2025-06-07 22:24:07', NULL),
(244, 23, 12, 'Musab Mashaqi تقدم لوظيفة: دهان وديكورات جبص . يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=16', 1, '2025-06-07 22:40:52', NULL),
(245, 12, 23, 'waseem Jadaa قبل طلبك لوظيفة: دهان وديكورات جبص ', 'jobs.php', 1, '2025-06-07 22:41:13', NULL),
(246, 23, 19, 'Mayar Elyan تقدم لوظيفة: منسق فعاليات ومناسبات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=56', 1, '2025-06-07 22:45:57', NULL),
(247, 23, 19, 'Mayar Elyan تقدم لوظيفة: مشغل ونش بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=50', 1, '2025-06-07 22:46:14', NULL),
(248, 19, 23, 'waseem Jadaa قبل طلبك لوظيفة: مشغل ونش بناء', 'jobs.php', 0, '2025-06-07 22:47:20', NULL),
(249, 19, 23, 'waseem Jadaa رفض طلبك لوظيفة: منسق فعاليات ومناسبات', 'jobs.php', 0, '2025-06-07 22:47:31', NULL),
(250, 23, 19, 'Mayar Elyan تقدم لوظيفة: منسق فعاليات ومناسبات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=56', 0, '2025-06-07 22:48:20', NULL),
(251, 23, 19, 'Mayar Elyan تقدم لوظيفة: مراقب جودة بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=58', 0, '2025-06-07 22:53:54', NULL),
(252, 23, 19, 'Mayar Elyan تقدم لوظيفة: عامل بناء. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=42', 0, '2025-06-07 22:55:08', NULL),
(253, 23, 19, 'Mayar Elyan تقدم لوظيفة: مقاول تشطيبات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=34', 0, '2025-06-07 22:57:24', NULL),
(254, 23, 19, 'Mayar Elyan تقدم لوظيفة: عامل رش مبيدات. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=39', 0, '2025-06-07 22:57:44', NULL),
(255, 23, 19, 'Mayar Elyan تقدم لوظيفة: مهندس زراعي. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=41', 0, '2025-06-07 23:00:09', NULL),
(256, 24, 12, 'Musab Mashaqi تقدم لوظيفة: تزيين صالات افراح. يمكنك قبول أو رفض الطلب.', 'manage_applications.php?job_id=73', 0, '2025-06-07 23:31:23', NULL);

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
  `profile_photo` varchar(255) DEFAULT NULL,
  `work_image_1` varchar(255) DEFAULT NULL,
  `work_image_2` varchar(255) DEFAULT NULL,
  `work_image_3` varchar(255) DEFAULT NULL,
  `work_image_4` varchar(255) DEFAULT NULL,
  `work_image_5` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profile_ID`, `User_ID`, `first_name`, `last_name`, `bio`, `skills`, `location`, `experience`, `profile_photo`, `work_image_1`, `work_image_2`, `work_image_3`, `work_image_4`, `work_image_5`) VALUES
(3, 12, 'Musab', 'Mashaqi', 'قصارة', 'فولخ , حجر', 'نابلس', '5', 'uploads/profile_photos/12_profile.jpg', NULL, NULL, NULL, NULL, NULL),
(10, 19, 'Mayar', 'Elyan', 'مصممة ازياء ', 'تصميم بدلات اعراس , تصميم ستايلات حديثة للجنسين ', 'جيوس', '7', 'uploads/profile_photos/19_profile.jpg', NULL, NULL, NULL, NULL, NULL),
(11, 20, 'Ayman', 'Qadome', 'سياحة وسفر ', 'سياحة وسفر ', 'كفر قدوم', '21', 'uploads/profile_photos/20_profile.jpg', NULL, NULL, NULL, NULL, NULL),
(12, 21, 'مالك', 'جدع', 'حلاق', 'حلاقة شعر لجميع الفئات العمرية , حلاقة عريس , تنظيف بشرة , سشوار , تحديد لحية ', 'قلقيلية - حبلة', '5', 'uploads/profile_photos/21_profile.jpg', '/forsa-pal/uploads/work_images/68442e28b8937.png', '/forsa-pal/uploads/work_images/68440b0047b2b.png', '/forsa-pal/uploads/work_images/68440a463f7cf.png', '/forsa-pal/uploads/work_images/68440a595f420.png', '/forsa-pal/uploads/work_images/684407a488395.png'),
(14, 23, 'waseem', 'Jadaa', 'مقاول اعمال حرة , جميع المجالات', '', 'جبع - رام الله', '0', 'uploads/profile_photos/23_profile.jpg', NULL, NULL, NULL, NULL, NULL),
(15, 24, 'محمد', 'محمد', 'ادارة اعراس ', '', 'جنين', '0', 'uploads/profile_photos/24_profile.jpg', NULL, NULL, NULL, NULL, NULL),
(16, 29, 'كمال', 'خليل', 'مقاول اعمال حرة ', '', 'قلقيلية', '0', 'uploads/profile_photos/29_profile.png', NULL, NULL, NULL, NULL, NULL),
(17, 30, 'رامي', 'جدع', 'الزراعة', 'رش المبيدات الحشرية , رش المزروعات , تتبع الري للمزروعات ', 'حبلة', '25', 'uploads/profile_photos/30_profile.jpg', NULL, NULL, NULL, NULL, NULL);

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
(30, 23, 16, '2025-06-05 12:24:00'),
(31, 23, 41, '2025-06-06 14:46:14'),
(32, 21, 34, '2025-06-07 12:26:10'),
(34, 21, 53, '2025-06-07 12:34:05'),
(36, 19, 34, '2025-06-07 20:04:54'),
(37, 12, 73, '2025-06-07 20:31:27');

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
  `verification_note` varchar(255) DEFAULT NULL,
  `email_notifications` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_ID`, `name`, `email`, `password`, `role`, `verification_status`, `verification_note`, `email_notifications`) VALUES
(12, 'Musab Mashaqi', 's12029151@stu.najah.edu', '$2y$10$VPTB3293BFYuRgUpLkVA6.L3FbsTEsFw9BKq6YrYECI6NTRREnWxK', 'job_seeker', 'verified', NULL, 1),
(19, 'Mayar Elyan', 'mayarelyan2@gmail.com', '$2y$10$VqSZM1G6/RGevQJILWHUIuEgcEO8XD39DOEbzeBo025YiYK2bV/oS', 'job_seeker', 'verified', NULL, 1),
(20, 'Ayman Qadome', 'adnansleem370@gmail.com', '$2y$10$elgk53iYroeFKKZl4v0mMe9oYpdq5v70MjGZxQRH16HTa6J7Ous3K', 'job_seeker', 'not_verified', NULL, 1),
(21, 'مالك جدع', 'malek.jada@gmail.com', '$2y$10$x/.gWKGcsj6JPOU8Z5REUO.1mDMKojAuJOzErCAEF1AZkVJs/at06', 'job_seeker', 'verified', NULL, 1),
(23, 'waseem Jadaa', 'rowaid.jadaa@gmail.com', '$2y$10$gKfYflfEUaOSltcS2u3dSu7gHwKGE9k/jreVebNVEmHYND85L15ZG', 'employer', 'verified', NULL, 1),
(24, 'محمد محمد', 'mohamad.790@gmail.com', '$2y$10$4Rj.05sfSG3nIfAjbzcmi.rw/Vz046YY4IAFlhamyKfKYz5rxNUpu', 'employer', 'verified', NULL, 1),
(25, 'Admin', 'admin@forsa-pal.com', '$2y$10$OCL4HUKtKi0GRKvUNbg5Ku4vWKUB1MqZQLvCoyBPYZpHtIaJAWnXi', 'admin', 'verified', NULL, 1),
(29, 'كمال خليل', 'kamal.100@gmail.com', '$2y$10$J.ea9eOxYfY07eP1RnpGPefzMADc4TTO0kuD6qYcBRv01Sw6wXveK', 'employer', 'verified', NULL, 1),
(30, 'رامي جدع', 'rami.jadaa@gmail.com', '$2y$10$Te0AEM/BcVs7JX24fYEnieI1JZx5yoa3vHLpDI20vzFm0Ez77hs2C', 'job_seeker', 'verified', NULL, 1);

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
(7, 19, 'ID', 'uploads/verification_docs/19_1748525967.png', 'rejected', 'انتي ', '2025-05-29 16:39:27', '2025-05-29 16:40:47', 'ميار عليان'),
(8, 12, 'ID', 'uploads/verification_docs/12_1749322909.jpg', 'accepted', NULL, '2025-06-07 22:01:49', '2025-06-07 22:02:09', 'مصعب حسن أسعد مشاقي'),
(9, 19, 'Licence', 'uploads/verification_docs/19_1749323063.jpg', 'rejected', '', '2025-06-07 22:04:23', '2025-06-07 22:04:40', 'ميار عليان'),
(10, 29, 'ID', 'uploads/verification_docs/29_1749323381.jpg', 'accepted', NULL, '2025-06-07 22:09:41', '2025-06-07 22:09:57', 'كمال خليل شهوان'),
(11, 19, 'ID', 'uploads/verification_docs/19_1749323848.jpg', 'rejected', '', '2025-06-07 22:17:28', '2025-06-07 22:18:17', 'ميار عليان '),
(12, 19, 'ID', 'uploads/verification_docs/19_1749323989.jpg', 'accepted', NULL, '2025-06-07 22:19:49', '2025-06-07 22:20:07', 'ميار عليان'),
(13, 19, 'Passport', 'uploads/verification_docs/19_1749324222.jpg', 'accepted', NULL, '2025-06-07 22:23:42', '2025-06-07 22:24:07', 'ميار عليان');

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
  MODIFY `application_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `keyword`
--
ALTER TABLE `keyword`
  MODIFY `keyword_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
