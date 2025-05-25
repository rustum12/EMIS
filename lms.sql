

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE `classes` (
  `CID` int(11) NOT NULL,
  `class_name` varchar(256) NOT NULL,
  `class_short` varchar(12) NOT NULL,
  `class_status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
  `session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`CID`, `class_name`, `class_short`, `class_status`, `session_id`) VALUES
(1, 'First Year', 'XI', 'active', 3),
(2, 'Second Year', 'XII', 'active', 3),
(3, 'First Year', 'XI', 'active', 4);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(9) DEFAULT NULL,
  `starting_date` date NOT NULL,
  `status` enum('active','inactive','suspended','deleted') NOT NULL,
  `remarks` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `session_name`, `starting_date`, `status`, `remarks`) VALUES
(3, '2025-2026', '2025-09-01', 'deleted', 'For Inter Mediate Classes'),
(4, '2026-2027', '2025-04-01', 'active', ''),
(5, '2026-2027', '2025-04-11', 'active', '2026-2027');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(256) NOT NULL,
  `father_name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','admitted','banned','suspended','deleted') DEFAULT 'registered',
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `student_cnic` varchar(15) DEFAULT NULL,
  `father_cnic` varchar(15) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `father_name`, `email`, `phone`, `address`, `gender`, `dob`, `class`, `registration_date`, `status`, `city`, `state`, `photo`, `student_cnic`, `father_cnic`, `session_id`) VALUES
(1, 'Muddasar Khan', 'Sultan Muqqarab Khan', 'niazi587@gmail.com', '03361000830', 'FG Liaqat Ali Degree College, Peshawar Road, Rawalpindi\r\nRawalpindi', 'Male', '1981-11-12', 1, '2025-04-22 11:35:08', 'registered', 'Rawalpindi', 'Punjab', '1745321708_muddasar.khan.jpg', '33100-0333226-5', '33100-0724226-5', 3);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `cnic` varchar(15) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `highest_qualification` varchar(100) NOT NULL,
  `bps` int(11) DEFAULT NULL,
  `department` enum('Computer Science','Physics','Chemistry','Mathematics') NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `job_nature` enum('Permanent','Contract','Visiting') DEFAULT 'Contract',
  `joining` date NOT NULL,
  `job_status` enum('Active','Resigned','Retired','Suspended') DEFAULT 'Active',
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_name`, `cnic`, `photo`, `designation`, `highest_qualification`, `bps`, `department`, `subject`, `job_nature`, `joining`, `job_status`, `status`) VALUES
(1, 'Muddasar Khan', '3310007242265', 'uploads/muddasar.khan.jpg', 'Computer Instructor', 'MSCS', 16, 'Computer Science', 'Computer Science', 'Permanent', '2025-04-08', 'Active', 0),
(2, 'Rustam Ali', '3310007242245', 'uploads/B.Ed.jpg', 'Computer Instructor', 'MSCS', 34, 'Computer Science', 'Computer Science', 'Visiting', '2025-04-02', 'Active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_classes`
--

CREATE TABLE `teacher_classes` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `status` enum('Active','Deleted') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_classes`
--

INSERT INTO `teacher_classes` (`id`, `class_id`, `teacher_id`, `session_id`, `status`) VALUES
(1, 1, 1, 3, 'Active'),
(2, 2, 1, 3, 'Active'),
(3, 1, 2, 3, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `uname` varchar(256) NOT NULL,
  `uemail` varchar(256) NOT NULL,
  `CNIC` varchar(13) DEFAULT NULL,
  `upassword` varchar(256) NOT NULL,
  `urole` enum('Admin','Teacher','Student','Parents') DEFAULT NULL,
  `creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uname`, `uemail`, `CNIC`, `upassword`, `urole`, `creationdate`, `status`) VALUES
(1, 'Muddasar Khan Niazi', 'niazi587@gmail.com', '3310007242265', '$2y$10$4ay0NJON0VplKHi1jwF/8erpZCUo1QlN1uwbeMPamRrOxa9I5JT.q', 'Admin', '2025-04-22 11:06:53', 'active'),
(2, 'Agha', 'Agha5387@gmail.com', '3630232101309', '$2y$10$30epl9kjFuhVFjGgNq0JCuSj7QLvDpGGDS1t4tlgj..dgzs9IeBji', 'Teacher', '2025-04-30 05:45:49', 'active'),
(3, 'Muddasar Khan Niazi', 'niazi5e4347@gmail.com', '3310034724226', '$2y$10$p7/WE135kvR7jWY4FL81z.oRVEHdzE4ChQfi8Q3uHXsrVjUPb4Nva', 'Student', '2025-04-30 05:48:54', 'active'),
(4, 'Muddasar Khan', 'niazi5847@gmail.com', '3310004242265', '$2y$10$wBcs2/7cbjjg0C2Du4ZXruNWiUMMaQArhaQ9sy/KqTfnRPddmQViC', 'Parents', '2025-04-30 06:01:20', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`CID`),
  ADD KEY `fk_session` (`session_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `cnic` (`cnic`);

--
-- Indexes for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uemail` (`uemail`),
  ADD UNIQUE KEY `CNIC` (`CNIC`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `CID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
