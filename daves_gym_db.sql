-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 03:33 PM
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
-- Database: `daves_gym_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `membership_start` date NOT NULL,
  `membership_end` date NOT NULL,
  `status` enum('Active','Expired') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `fullname`, `contact_number`, `address`, `membership_start`, `membership_end`, `status`) VALUES
(5, 'Goku', '09123354123', 'Planet Namek', '2026-10-28', '2027-10-28', 'Active'),
(6, 'Saitama', '0923574178', 'Japan Tokyo', '2026-10-28', '2027-04-28', 'Active'),
(7, 'Zenitsu', '09124573178', 'hidden Mountain', '2026-10-28', '2026-11-28', 'Active'),
(8, 'Manny Pacquioa', '09547853146', 'Bisaya', '2026-10-28', '2027-01-28', 'Active'),
(9, 'Cardo', '09345631981', 'Manila', '2026-10-28', '2026-11-28', 'Active'),
(10, 'golden Eagle', '09345642145', 'Planet Mercury', '2026-10-29', '2026-11-29', 'Active'),
(11, 'Asta', '09234567812', 'Planet Earth', '2026-10-29', '2026-11-29', 'Active'),
(12, 'Asta', '09234567812', 'Planet Earth', '2026-10-29', '2026-11-29', 'Active'),
(13, 'Naruto', '09345875642', 'Leaf Village', '2026-10-29', '2026-11-29', 'Active'),
(14, 'Naruto', '09345875642', 'Leaf Village', '2026-10-29', '2026-11-29', 'Active'),
(15, 'Sasuke', '09341823456', 'Hidden Village', '2026-10-29', '2026-11-29', 'Active'),
(16, 'Sasuke', '09341823456', 'Hidden Village', '2026-10-29', '2026-11-05', 'Active'),
(17, 'Curry', '09345876123', 'United States', '2026-10-29', '2026-11-05', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `pin_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `role`, `pin_code`) VALUES
(3, 'Bryan', 'Admin', '$2y$10$9LM3YQ28jMLDmPsElnO9vejjVRbmCIu7pBxWmwrXxlr5F02Ly6.qi'),
(4, 'Turas', 'Staff', '$2y$10$k6JP0fXaANZ3LBSf.m9rKOyTF6kh2Hc0PYoRvUnU50s8.GVFDmOtC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
