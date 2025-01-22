-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2025 at 01:57 AM
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
-- Database: `payrollsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `check_in`, `check_out`) VALUES
(1, 2, '0000-00-00 00:00:00', '2025-01-22 08:35:26'),
(2, 2, '2025-01-22 09:43:22', '2025-01-22 09:43:25'),
(3, 2, '2025-01-22 09:50:42', '2025-01-22 09:50:47'),
(4, 2, '2025-01-22 09:51:07', '2025-01-22 09:51:22'),
(5, 2, '2025-01-22 09:52:00', '2025-01-22 09:54:01'),
(6, 2, '2025-01-22 09:52:50', '2025-01-22 09:53:58'),
(7, 2, '2025-01-22 09:53:09', '2025-01-22 09:53:10'),
(8, 2, '2025-01-22 09:53:11', '2025-01-22 09:53:46'),
(9, 2, '2025-01-22 09:53:12', '2025-01-22 09:53:26'),
(10, 2, '2025-01-22 09:53:19', '2025-01-22 09:53:20'),
(11, 2, '2025-01-22 09:53:22', '2025-01-22 09:53:23'),
(12, 2, '2025-01-22 09:53:29', '2025-01-22 09:53:38'),
(13, 2, '2025-01-22 09:53:47', '2025-01-22 09:53:48'),
(14, 2, '2025-01-22 09:54:13', '2025-01-22 09:54:17'),
(15, 2, '2025-01-22 09:55:26', '2025-01-22 09:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `is_admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `password`, `role`, `hourly_rate`, `is_admin`) VALUES
(1, 'brendo', 'brendo1@gmail.com', '$2y$10$ATy.jb3YjsNo.cghmDpzhOe79/3SUXNOyAW5cn6A8so.a45DqQyIm', 'admin', 25.00, 0),
(2, 'brendo2', 'brendo2@gmail.com', '$2y$10$Zj/zFQ1ldq1KlWPlamOLueXGkQ6aNK7Qgv2DZ0jFnuicWkim/5PjO', 'employee', 25.00, 0),
(3, 'brendo3', 'brendo3@gmail.om', '$2y$10$ePTxYadWIhWQWKs.idRvg.j.lV6obVpgnBYj9s0v/LkxfEW0gR.Sq', 'admin', 25.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `pay_period_start` date NOT NULL,
  `pay_period_end` date NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL,
  `tax_deduction` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attendance_employee` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payroll_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
