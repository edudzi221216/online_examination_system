-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2023 at 05:15 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_acad`
--

CREATE TABLE `tbl_acad` (
  `ay_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_registered` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_acad`
--

INSERT INTO `tbl_acad` (`ay_id`, `name`, `date_registered`) VALUES
('AY6424', '2022/2023', '08-07-2023');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_assessment_records`
--

CREATE TABLE `tbl_assessment_records` (
  `record_id` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `exam_name` varchar(255) NOT NULL,
  `exam_id` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `score` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `next_retake` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `rstatus` varchar(255) NOT NULL DEFAULT 'Result not published',
  `fstatus` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_assessment_records`
--

INSERT INTO `tbl_assessment_records` (`record_id`, `student_id`, `student_name`, `class`, `exam_name`, `exam_id`, `subject`, `score`, `status`, `next_retake`, `date`, `rstatus`, `fstatus`) VALUES
('RS64579263660914', 'OES32557', 'Ayuba Isaac', 'ICT L100', 'Quiz', 'EX429988', 'Element Of Programing', '0', 'FAIL', '07/08/2023', '07/08/2023', 'Result not published', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bulk_uploads`
--

CREATE TABLE `tbl_bulk_uploads` (
  `upload_id` varchar(255) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `user_type` enum('admin','teacher') NOT NULL,
  `exam_id` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `total_questions` int(11) DEFAULT 0,
  `successful_uploads` int(11) DEFAULT 0,
  `failed_uploads` int(11) DEFAULT 0,
  `upload_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Processing','Completed','Failed') DEFAULT 'Processing',
  `error_log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_classes`
--

CREATE TABLE `tbl_classes` (
  `class_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ay` varchar(255) NOT NULL,
  `date_registered` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_classes`
--

INSERT INTO `tbl_classes` (`class_id`, `name`, `ay`, `date_registered`) VALUES
('CL407217', 'ICT L100', '2022/2023', '08-07-2023');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_examinations`
--

CREATE TABLE `tbl_examinations` (
  `exam_id` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `exam_name` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `end_exam_date` varchar(20) DEFAULT NULL,
  `start_time` time DEFAULT '09:00:00',
  `end_time` time DEFAULT '17:00:00',
  `result_publish_start_date` date DEFAULT NULL,
  `result_publish_start_time` time DEFAULT NULL,
  `result_publish_end_date` date DEFAULT NULL,
  `result_publish_end_time` time DEFAULT NULL,
  `result_publish_status` enum('Not Published','Published','Scheduled') DEFAULT 'Not Published',
  `duration` int(255) NOT NULL,
  `passmark` int(255) NOT NULL,
  `full_marks` int(255) NOT NULL,
  `re_exam` int(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Inactive',
  `created_by` varchar(255) DEFAULT NULL,
  `created_by_type` enum('admin','teacher') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_examinations`
--

INSERT INTO `tbl_examinations` (`exam_id`, `class`, `subject`, `exam_name`, `date`, `end_exam_date`, `start_time`, `end_time`, `result_publish_start_date`, `result_publish_start_time`, `result_publish_end_date`, `result_publish_end_time`, `result_publish_status`, `duration`, `passmark`, `full_marks`, `re_exam`, `status`, `created_by`, `created_by_type`) VALUES
('EX142419', 'ICT L100', 'Element Of Programing', 'Trail Exam', '07/30/2025', '07/30/2025', '20:25:00', '20:30:00', '0000-00-00', '20:30:00', '0000-00-00', '20:35:00', 'Scheduled', 20, 15, 20, 0, 'Inactive', 'admin', 'admin'),
('EX212578', 'BTECH ICT', 'Coding', 'NETO', '08/10/2025', '08/10/2025', '20:15:00', '20:20:00', NULL, NULL, NULL, NULL, 'Not Published', 40, 20, 40, 0, 'Inactive', 'admin', 'admin'),
('EX429988', 'ICT L100', 'Element Of Programing', 'Quiz', '07/08/2023', '07/08/2023', '09:00:00', '17:00:00', NULL, NULL, NULL, NULL, 'Not Published', 1, 60, 100, 0, 'Active', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notice`
--

CREATE TABLE `tbl_notice` (
  `notice` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_questions`
--

CREATE TABLE `tbl_questions` (
  `question_id` varchar(255) NOT NULL,
  `exam_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `question_type` enum('MC','FB','TF') DEFAULT 'MC',
  `question` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Qmarks` int(255) NOT NULL,
  `option1` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `option2` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `option3` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `option4` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '-',
  `answer` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_questions`
--

INSERT INTO `tbl_questions` (`question_id`, `exam_id`, `type`, `question_type`, `question`, `Qmarks`, `option1`, `option2`, `option3`, `option4`, `answer`) VALUES
('QS-157822', 'EX429988', 'MC', 'MC', '<p>what is the full meaning of <strong>php</strong>?</p>\r\n', 20, 'PHP', 'C++', 'Java', 'JS', 'option1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subjects`
--

CREATE TABLE `tbl_subjects` (
  `subject_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ay` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `date_registered` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_subjects`
--

INSERT INTO `tbl_subjects` (`subject_id`, `name`, `ay`, `class`, `date_registered`) VALUES
('SB-959645', 'Element Of Programing', '2022/2023', '----', '08-07-2023');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_teacher`
--

CREATE TABLE `tbl_teacher` (
  `teacher_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL DEFAULT 'teachersjs',
  `role` varchar(255) NOT NULL DEFAULT 'teacher',
  `acc_stat` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_teacher`
--

INSERT INTO `tbl_teacher` (`teacher_id`, `first_name`, `last_name`, `gender`, `email`, `login`, `role`, `acc_stat`) VALUES
('ACC7529', 'Kojo', 'Ayuba', 'Male', 'ayuba@gmail.com', '3a8f44d17c5cfca6f4141e6ae44c9d82', 'accountant', '1'),
('admin', 'admin', 'admin', 'm', 'admin@gmaill.com', '0e7517141fb53f21ee439b355b5a1d0a', 'admin', '1'),
('TCHR6891', 'Ayuba', 'Isaac', 'Male', 'agbobliisaackwadzo@gmail.com', '711238b6208b67dc2a24213842736ffb', 'teacher', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `ay` varchar(255) NOT NULL DEFAULT '-',
  `class` varchar(255) NOT NULL DEFAULT '-',
  `login` varchar(255) NOT NULL DEFAULT 'studentsjs',
  `role` varchar(255) NOT NULL DEFAULT 'student',
  `acc_stat` varchar(255) NOT NULL DEFAULT 'Active',
  `fees` varchar(255) NOT NULL DEFAULT 'Paid',
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_device` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `first_name`, `last_name`, `gender`, `email`, `contact`, `ay`, `class`, `login`, `role`, `acc_stat`, `fees`, `last_login`, `login_device`) VALUES
('OES32557', 'Ayuba', 'Isaac', 'Male', 'agbobliisaackwadzo@gmail.com', '', '2022/2023', 'ICT L100', '26b12d91dda9498ac8469b960c8b817b', 'student', 'Active', 'Paid', '2023-07-08 21:41:11', 'Desktop/Laptop');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_acad`
--
ALTER TABLE `tbl_acad`
  ADD PRIMARY KEY (`ay_id`);

--
-- Indexes for table `tbl_assessment_records`
--
ALTER TABLE `tbl_assessment_records`
  ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `tbl_bulk_uploads`
--
ALTER TABLE `tbl_bulk_uploads`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `tbl_classes`
--
ALTER TABLE `tbl_classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `tbl_examinations`
--
ALTER TABLE `tbl_examinations`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `idx_exam_dates` (`date`, `end_exam_date`),
  ADD KEY `idx_exam_times` (`start_time`, `end_time`),
  ADD KEY `idx_result_publish` (`result_publish_start_date`, `result_publish_end_date`);

--
-- Indexes for table `tbl_questions`
--
ALTER TABLE `tbl_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `tbl_subjects`
--
ALTER TABLE `tbl_subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `tbl_teacher`
--
ALTER TABLE `tbl_teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
