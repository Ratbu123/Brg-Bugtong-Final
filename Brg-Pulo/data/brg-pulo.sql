  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Host: 127.0.0.1
  -- Generation Time: Jul 23, 2025 at 09:44 AM
  -- Server version: 10.4.32-MariaDB
  -- PHP Version: 8.0.30

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";


  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
  /*!40101 SET NAMES utf8mb4 */;

  --
  -- Database: `brg-pulo`
  --

  -- --------------------------------------------------------

  --
  -- Table structure for table `announcements`
  --

  CREATE TABLE `announcements` (
    `id` int(11) NOT NULL,
    `title` varchar(225) NOT NULL,
    `content` text NOT NULL,
    `date_posted` datetime NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `announcements`
  --

  INSERT INTO `announcements` (`id`, `title`, `content`, `date_posted`) VALUES
  (19, 'nurse', '', '0000-00-00 00:00:00'),
  (20, 'test', '', '0000-00-00 00:00:00'),
  (21, 'Libre Tuli', 'Patuli na kayo sayang ang tite walang mapipipe', '2025-07-23 15:31:27');

  -- --------------------------------------------------------

  --
  -- Table structure for table `b-official`
  --

  CREATE TABLE `b-official` (
    `fname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `mname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `lname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `number` varchar(50) DEFAULT NULL,
    `barangay` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `position` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `age` int(50) DEFAULT NULL,
    `dateofbirth` int(50) DEFAULT NULL,
    `address` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `picture` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `email` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `password` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `id` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `b-official`
  --

  INSERT INTO `b-official` (`fname`, `mname`, `lname`, `number`, `barangay`, `position`, `age`, `dateofbirth`, `address`, `picture`, `email`, `password`, `id`) VALUES
  ('pablo', 'D', 'escobar', '1234567890', 'Bembang', 'Kagawad', 78, 9999, 'Tadlac ES, Tadlac , Alitagtag', 'data/uploads/Adobe Express - file.png', 'evangeline.guce@deped.gov.ph', '123', 8);

  -- --------------------------------------------------------

  --
  -- Table structure for table `job_listings`
  --

  CREATE TABLE `job_listings` (
    `id` int(11) NOT NULL,
    `title` varchar(225) NOT NULL,
    `description` varchar(225) NOT NULL,
    `salary` varchar(100) NOT NULL,
    `date_posted` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `job_listings`
  --

  INSERT INTO `job_listings` (`id`, `title`, `description`, `salary`, `date_posted`) VALUES
  (16, 'nurse', 'gufdgucugrusdvcrgdsbvuigefdsgfdhgsdshdvhsajsjsvsanmbnvxbcnxasvsaojhjdhfvcsjkndbnd', '25,000', '2025-07-21 05:11:00'),
  (17, 'test', 'dfgkhstrdjytcgvhgyfktdyrstehdzxgfchgvjhgftdrthxc', '7,000', '2025-07-23 07:27:36');

  -- --------------------------------------------------------

  --
  -- Table structure for table `mainadmin`
  --

  CREATE TABLE `mainadmin` (
    `username` varchar(50) NOT NULL,
    `password` varchar(50) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  INSERT INTO `mainadmin` (`username`, `password`) VALUES
('admin', 'admin123');


  -- --------------------------------------------------------

  --
  -- Table structure for table `request`
  --

  CREATE TABLE `request` (
    `id` int(25) NOT NULL,
    `name` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
    `date` date DEFAULT NULL,
    `status` enum('approved','declined') DEFAULT NULL,
    `certificate_type` varchar(225) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `request`
  --

  INSERT INTO `request` (`id`, `name`, `date`, `status`, `certificate_type`) VALUES
  (18, 'tite', '2025-07-21', 'approved', ''),
  (19, 'test', '2025-07-21', 'declined', 'Certificate of residency'),
  (20, 'etits', '2025-07-21', 'declined', 'Certificate of indigency');

  -- --------------------------------------------------------

  --
  -- Table structure for table `res-info`
  --

  CREATE TABLE `res-info` (
    `id` int(11) NOT NULL,
    `fname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `mname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `lname` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `age` int(50) NOT NULL,
    `number` varchar(50) NOT NULL,
    `c-status` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `dob` date NOT NULL,
    `profile` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `housen` int(50) NOT NULL,
    `purok` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    `gender` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `announcements`
  --
  ALTER TABLE `announcements`
    ADD PRIMARY KEY (`id`);

  --
  -- Indexes for table `b-official`
  --
  ALTER TABLE `b-official`
    ADD PRIMARY KEY (`id`);

  --
  -- Indexes for table `job_listings`
  --
  ALTER TABLE `job_listings`
    ADD PRIMARY KEY (`id`);

  --
  -- Indexes for table `request`
  --
  ALTER TABLE `request`
    ADD PRIMARY KEY (`id`);

  --
  -- Indexes for table `res-info`
  --
  ALTER TABLE `res-info`
    ADD PRIMARY KEY (`id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `announcements`
  --
  ALTER TABLE `announcements`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

  --
  -- AUTO_INCREMENT for table `b-official`
  --
  ALTER TABLE `b-official`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

  --
  -- AUTO_INCREMENT for table `job_listings`
  --
  ALTER TABLE `job_listings`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

  --
  -- AUTO_INCREMENT for table `request`
  --
  ALTER TABLE `request`
    MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

  --
  -- AUTO_INCREMENT for table `res-info`
  --
  ALTER TABLE `res-info`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
  COMMIT;

  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
