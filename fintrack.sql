-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 04:35 PM
-- Server version: 8.0.41
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fintrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `month` int DEFAULT NULL,
  `year` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `user_id`, `amount`, `month`, `year`, `created_at`) VALUES
(10, 5, 40000.00, 3, 2026, '2026-03-20 11:22:39'),
(12, 2, 12000.00, 3, 2026, '2026-03-21 10:10:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`, `created_at`) VALUES
(1, 1, 'Transportation', '2026-02-21 15:39:20'),
(4, 1, 'Entertainment', '2026-02-22 12:55:13'),
(5, 1, 'Education', '2026-02-22 12:59:50'),
(6, 2, 'Transportation', '2026-03-02 14:04:58'),
(7, 2, 'Entertainment', '2026-03-02 14:05:10'),
(8, 2, 'Bills', '2026-03-02 14:38:15'),
(11, 4, 'Entertainment', '2026-03-10 17:02:01'),
(12, 4, 'Food', '2026-03-10 17:02:21'),
(13, 4, 'Bills', '2026-03-10 17:04:59'),
(14, 2, 'Shopping', '2026-03-14 15:43:10'),
(16, 2, 'Education', '2026-03-16 16:12:58'),
(17, 5, 'Shopping', '2026-03-20 11:24:14'),
(18, 5, 'Bills', '2026-03-20 11:25:43'),
(19, 5, 'Transportation', '2026-03-20 11:26:34'),
(20, 5, 'Education', '2026-03-20 11:26:45'),
(21, 5, 'Food', '2026-03-20 11:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `title`, `amount`, `category`, `type`, `expense_date`, `created_at`) VALUES
(1, 1, 'Grocery', 1200.00, 'Food', 'expense', '2026-02-03', '2026-02-21 15:08:25'),
(2, 1, 'Trip', 1000.00, 'Entertainment', 'expense', '2025-12-14', '2026-02-21 15:09:19'),
(3, 1, 'Grocery', 3000.00, 'Food', 'expense', '2025-12-24', '2026-02-22 12:57:47'),
(4, 1, 'Online Course', 12000.00, 'Education', 'expense', '2026-02-22', '2026-02-22 12:58:57'),
(7, 1, 'Grocery', 5000.00, 'Food', 'expense', '2026-01-08', '2026-03-10 15:41:33'),
(8, 4, 'Grocery', 5000.00, 'Food', 'expense', '2026-03-01', '2026-03-10 16:54:40'),
(9, 4, 'Trip', 8000.00, 'Entertainment', 'expense', '2026-03-10', '2026-03-10 17:01:07'),
(10, 4, 'Electricity Bill', 5000.00, 'Bills', 'expense', '2026-02-13', '2026-03-10 17:04:25'),
(11, 2, 'Medicines', 2000.00, 'Bills', 'expense', '2026-03-02', '2026-03-14 15:34:06'),
(12, 2, 'Grocery', 5000.00, 'Shopping', 'expense', '2026-03-06', '2026-03-14 15:35:01'),
(13, 2, 'Trip', 10000.00, 'Entertainment', 'expense', '2026-02-25', '2026-03-14 15:45:53'),
(15, 2, 'Books', 5000.00, 'Education', 'expense', '2026-02-02', '2026-03-16 16:12:04'),
(17, 2, 'Airpods', 2000.00, 'Shopping', 'expense', '2025-10-10', '2026-03-19 14:49:18'),
(18, 5, 'Clothes', 2000.00, 'Shopping', 'expense', '2025-10-08', '2026-03-20 11:23:53'),
(19, 5, 'Electricity', 5000.00, 'Bills', 'expense', '2025-10-19', '2026-03-20 11:25:23'),
(20, 5, 'Trip', 6000.00, 'Transportation', 'expense', '2025-10-21', '2026-03-20 11:26:21'),
(21, 5, 'Online course', 8000.00, 'Education', 'expense', '2025-10-28', '2026-03-20 11:27:13'),
(22, 5, 'Cafe date', 2000.00, 'Food', 'expense', '2026-03-10', '2026-03-20 11:28:19'),
(23, 5, 'Footwear', 3400.00, 'Shopping', 'expense', '2025-11-18', '2026-03-20 11:30:38'),
(24, 5, 'Bed', 20000.00, 'Shopping', 'expense', '2025-12-24', '2026-03-20 11:31:04'),
(25, 5, 'Movie', 500.00, 'Entertainment', 'expense', '2026-01-08', '2026-03-20 11:31:42'),
(26, 5, 'Examination fees', 3000.00, 'Education', 'expense', '2026-02-16', '2026-03-20 11:32:22'),
(27, 2, 'Date', 1000.00, 'Food', 'expense', '2026-03-02', '2026-03-21 09:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Alka', 'alkashah@gmail.com', '$2y$10$OvWNMN.GKFyLJFpIfxK3x.ndKaQXDUqN9cBdczaATwpAvqvBlagoq', '2026-02-21 14:23:22'),
(2, 'Ritu', 'ritu123@gmail.com', '$2y$10$SZkKxInEdW/lvfg3iK5/.uN1O7JAdDGEf/4TkddX614E4UMAL0DYu', '2026-03-02 14:03:12'),
(4, 'ritu', 'ritu@gmail.com', '$2y$10$4Hr3PsrIE64496mvpXHY3OtCNtXHhckHM5/uTLH2IIxFHIA3GD/A.', '2026-03-10 16:39:30'),
(5, 'Ankit', 'ankit@gmail.com', '$2y$10$RbClV9L8rCPqy44hE9eEReoKVkbUDaHuBQYNofu8mHHJHfvKXeg1m', '2026-03-20 11:22:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
