-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 06:04 PM
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
-- Database: `invent`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `suffix` varchar(12) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `phone`, `created_at`) VALUES
(1, 'Test', '', 'Test', '', 'test@gmail.com', '09815133675', '2025-04-11 15:08:47');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','delivered','read') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `status`) VALUES
(26, 3, 1, 'wat', '2025-02-09 13:14:33', 'sent'),
(27, 3, 1, 'wata', '2025-02-09 13:36:34', 'sent'),
(28, 3, 1, 'watata', '2025-02-09 13:36:41', 'sent'),
(29, 6, 1, 'aaaa', '2025-02-09 14:21:14', 'sent'),
(30, 6, 1, 'aaaaa', '2025-02-09 14:21:17', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `user_id` int(2) NOT NULL,
  `total_price` float NOT NULL,
  `status` varchar(100) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(30, '67f9371cd6f04', 9, 320, 'Completed', '2025-04-11 15:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(44, '67f9371cd6f04', 33, 1, 100.00),
(45, '67f9371cd6f04', 34, 1, 120.00),
(46, '67f9371cd6f04', 45, 1, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `image_path` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `expiration_date`, `image_path`) VALUES
(33, 'Pinangat Original', 100.00, 6, '2025-05-09', '../uploads/pinangat-ng-bicol-main.webp'),
(34, 'Pinangat Spicy', 120.00, 2, '2025-05-22', '../uploads/674b74444c30d.jpg'),
(45, 'Pinangat Small', 100.00, 3, '2025-04-25', '../uploads/images.jpg'),
(46, 'Pili Nut Candy Originals 1kg', 2000.00, 9, '2025-02-15', '../uploads/img_67aa0a5ed1325.jpg'),
(47, 'Pili Nut Candy Originals 200g', 450.00, 9, '2025-02-15', '../uploads/img_67aa0a93bd944.jpg'),
(48, 'Pili Tart Original', 120.00, 9, '2025-02-15', '../uploads/img_67aa0acfdb08e.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `store_owners`
--

CREATE TABLE `store_owners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_address` varchar(255) NOT NULL,
  `store_phone` varchar(15) NOT NULL,
  `store_email` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `owner_phone` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_owners`
--

INSERT INTO `store_owners` (`id`, `user_id`, `store_name`, `store_address`, `store_phone`, `store_email`, `owner_name`, `owner_email`, `owner_phone`, `username`, `password`, `role`) VALUES
(4, 7, 'Sample Store', 'Legazpi City', '09934259546', 'store12@email.com', 'Angelo Bufete', 'angelo@email.com', '09815133675', 'Angelo', '123456', 'store_owner');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `phone`, `password`, `role`, `last_activity`) VALUES
(1, 'admin', 'admin@email.com', '09154856442', 'admin', 'admin', '2025-04-11 16:01:33'),
(7, 'Angelo', 'angelo@email.com', '09815133675', '123456', 'user', '2025-04-11 15:12:42'),
(9, 'test', 'test@gmail.com', '09815133675', 'password', 'customer', '2025-04-11 15:37:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `owner_email` (`email`),
  ADD UNIQUE KEY `owner_phone` (`phone`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `store_owners`
--
ALTER TABLE `store_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_email` (`store_email`),
  ADD UNIQUE KEY `owner_email` (`owner_email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `store_owners`
--
ALTER TABLE `store_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
