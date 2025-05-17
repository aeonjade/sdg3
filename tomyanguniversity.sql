-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 01:16 AM
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
-- Database: `tomyanguniversity`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `applicantID` int(11) NOT NULL,
  `applicantName` varchar(50) NOT NULL,
  `firstChoice` enum('Bachelor of Science in Information Technology','Bachelor of Science in Civil Engineering','Bachelor of Science in Electrical Engineering') NOT NULL,
  `secondChoice` enum('Bachelor of Science in Information Technology','Bachelor of Science in Civil Engineering','Bachelor of Science in Electrical Engineering') NOT NULL,
  `applicantType` enum('Bachelor-Program','Graduate-Program') NOT NULL DEFAULT 'Bachelor-Program',
  `requirementsStatus` enum('pending','submitted','accomplished','incomplete') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicantID`, `applicantName`, `firstChoice`, `secondChoice`, `applicantType`, `requirementsStatus`) VALUES
(1, 'Mrs. Galve-Abad', 'Bachelor of Science in Information Technology', 'Bachelor of Science in Civil Engineering', 'Bachelor-Program', 'submitted'),
(2, 'Mr. Ubana', 'Bachelor of Science in Civil Engineering', 'Bachelor of Science in Electrical Engineering', 'Graduate-Program', 'pending'),
(3, 'Mr. Octubre', 'Bachelor of Science in Electrical Engineering', 'Bachelor of Science in Information Technology', 'Bachelor-Program', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `documentID` int(11) NOT NULL,
  `applicantID` int(11) DEFAULT NULL,
  `documentName` varchar(255) DEFAULT NULL,
  `documentType` varchar(100) DEFAULT NULL,
  `documentStatus` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `uploadDate` datetime DEFAULT current_timestamp(),
  `rejectReason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicantID`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`documentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `documentID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
