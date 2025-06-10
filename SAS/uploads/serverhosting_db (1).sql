-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 05:28 PM
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
-- Database: `serverhosting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `hosting_packages`
--

CREATE TABLE `hosting_packages` (
  `package_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `storage_gb` int(11) NOT NULL,
  `bandwidth_gb` int(11) NOT NULL,
  `price_per_month` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hosting_packages`
--

INSERT INTO `hosting_packages` (`package_id`, `name`, `storage_gb`, `bandwidth_gb`, `price_per_month`) VALUES
(9001, 'Singapore', 3, 2, 2.00),
(9002, 'Japan', 2, 3, 0.00),
(9003, 'Jakarta', 5, 4, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','paid','overdue') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `user_id`, `account_id`, `amount`, `issued_date`, `due_date`, `status`) VALUES
(5001, 2001, 4001, 0.00, '2025-06-04', '2025-06-07', 'paid'),
(5002, 2002, 4002, 2.00, '2025-06-03', '2025-06-07', 'unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `server_id` int(11) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('active','maintenance','offline') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servers`
--

INSERT INTO `servers` (`server_id`, `hostname`, `ip_address`, `location`, `status`) VALUES
(1, 'NotMe', '239.243.15.42', 'Singapore', ''),
(2, 'Zeeyane', '98.227.68.141', 'Jakarta', 'offline'),
(3, 'Ayane', '111.131.245.224', 'Japan', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(2001, 'Gilang', 'gilang43@gmail.com', 'poligon12', '2025-06-03 00:21:49', 'user'),
(2002, 'Kiko', 'jambut@gmail.com', 'iopal2', '2025-05-31 21:26:29', 'user'),
(2003, 'Nanda', 'nandagaming@gmail.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2025-06-08 12:04:05', 'admin'),
(2004, 'Jaykunister', 'Jayden@gmail.com', 'a061bb28eafeebea4be70ea2a33577cff044912853100ba0a497041fb65a0074', '2025-06-08 12:05:06', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_hosting_accounts`
--

CREATE TABLE `user_hosting_accounts` (
  `account_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `domain_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_hosting_accounts`
--

INSERT INTO `user_hosting_accounts` (`account_id`, `user_id`, `package_id`, `server_id`, `domain_name`, `start_date`, `expiry_date`) VALUES
(4001, 2001, 9002, 3, 'Japan Goes Crazy', '2025-06-04', '2025-09-01'),
(4002, 2002, 9001, 1, 'Singapore', '2025-06-03', '2025-09-02');

--
-- Triggers `user_hosting_accounts`
--
DELIMITER $$
CREATE TRIGGER `set_server_status_used` AFTER INSERT ON `user_hosting_accounts` FOR EACH ROW BEGIN
    UPDATE servers
    SET status = 'used'
    WHERE server_id = NEW.server_id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hosting_packages`
--
ALTER TABLE `hosting_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`server_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_hosting_accounts`
--
ALTER TABLE `user_hosting_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `server_id` (`server_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hosting_packages`
--
ALTER TABLE `hosting_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9004;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5003;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `server_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2005;

--
-- AUTO_INCREMENT for table `user_hosting_accounts`
--
ALTER TABLE `user_hosting_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4003;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `user_hosting_accounts` (`account_id`);

--
-- Constraints for table `user_hosting_accounts`
--
ALTER TABLE `user_hosting_accounts`
  ADD CONSTRAINT `user_hosting_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_hosting_accounts_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `hosting_packages` (`package_id`),
  ADD CONSTRAINT `user_hosting_accounts_ibfk_3` FOREIGN KEY (`server_id`) REFERENCES `servers` (`server_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
