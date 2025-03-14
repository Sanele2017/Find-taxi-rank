-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2025 at 09:58 AM
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
-- Database: `taxi-rank`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'imanathi17@gmail.com', '$2y$10$qKAwnOXtVKTdep8w.7LBNeH5lqHVeYgjcB28/gcsLkc8sVdjhKzCu', '2025-01-02 13:46:45'),
(3, 'Imanathi', 'sanelelindokuhle231@gmail.com', '$2y$10$ix.BvURnk1FlwQpveSCUWuvYor8kfIdESL6UIZjumkDlAJcuczOCy', '2025-01-02 14:09:41'),
(5, '@dmin', 'imanathi1@gmail.com', '$2y$10$qUsfMgvHWlq3D1p2dyVhx.NtCltHSkfIWDA4qVoqYo8o3RRdGU1fG', '2025-01-03 10:05:42'),
(11, 'Mr Test', 'test@gmail.com', '$2y$10$gcHm9HpIrj8MtWtCWJNaberj7NSXXK/hNQh.mPpU/TCqmyQ0kt4p6', '2025-03-14 08:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE `ranks` (
  `ranks_id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `rank_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `association` varchar(255) NOT NULL,
  `operating_hours` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`ranks_id`, `city`, `rank_name`, `location`, `association`, `operating_hours`) VALUES
(2, 'Kaalfontein', 'Kaalfontein Taxi Rank', 'Gauteng', 'iPTA', '05:00-22:00'),
(3, 'Tembisa', 'ivory 2 Taxi Rank', 'Gauteng', 'iPTA', '05:00-20:00'),
(6, 'Centurion', 'Centurion Taxi rank', 'CenturionMall', 'iPTA', '05:00-22:00'),
(7, 'Alex', 'Gomora Taxi rank', 'Alex', 'AAA', '05:00-22:00');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `ranks_id` int(11) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `fare` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `ranks_id`, `route_name`, `fare`) VALUES
(31, 3, 'Pretoria', 23.00),
(32, 3, 'Joburg', 25.00),
(33, 7, 'Randburg', 20.00),
(34, 7, 'Midrand', 18.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`ranks_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `ranks_id` (`ranks_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ranks`
--
ALTER TABLE `ranks`
  MODIFY `ranks_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`ranks_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
