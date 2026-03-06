-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 06:36 AM
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
-- Database: `8th_mile_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Cleaning Materials', 'Supplies for general cleaning', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(2, 'Consumable Materials', 'Items intended for single use', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(3, 'Electrical Materials', 'Wiring and breakers', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(4, 'Electrical Tools', 'Tools for electrical installation', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(5, 'Engineering Tools', 'Precision instruments', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(6, 'Fabrication Tools', 'Tools for fabrication', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(7, 'Fabrication Materials', 'Materials for fabrication', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(9, 'Machine Equipment', 'Heavy and light machinery', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(10, 'Machinist Tools', 'Tools for machining', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(11, 'Mechanical Tools', 'Wrenches and gear', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(12, 'Painting Materials', 'Brushes and paint', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(13, 'Power Tools', 'Electric powered tools', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(14, 'Premier (Tanauan)', 'Site specific equipment', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(15, 'Safety Equipment', 'PPE and safety gear', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(16, 'Scaffolding', 'Poles and clamps', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(17, 'Special Tools', 'Unique specialized equipment', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(18, 'Tools Equipment', 'General workshop equipment', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(19, 'Trucking', 'Vehicle related items', '2026-02-14 09:53:27', '2026-02-14 09:53:27'),
(20, 'PPE', '', '2026-02-14 10:12:33', '2026-02-14 10:12:33'),
(469, 'IT Equipment', '', '2026-02-23 05:53:21', '2026-02-23 05:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `contact_person`, `email`, `phone`, `address`, `created_at`) VALUES
(2, 'Flour Mill MNC', '', '', '', 'Santa Rosa', '2026-02-16 06:47:15'),
(5, 'Calaca(MNC)', '', '', '', '', '2026-02-19 02:25:31'),
(6, 'Noodles Plant (MNC)', '', '', '', '', '2026-02-19 02:26:38'),
(7, 'Cake Plant (MNC)', '', '', '', '', '2026-02-19 02:27:09'),
(8, 'Malvar Plant (MNC)', '', '', '', '', '2026-02-19 02:27:24'),
(9, 'Brixton (MNC)', '', '', '', '', '2026-02-19 02:27:58'),
(10, 'M.I.S (MNC)', '', '', '', '', '2026-02-19 02:28:14'),
(11, 'Porac (MNC)', '', '', '', '', '2026-02-19 02:28:35'),
(12, 'Wafer Plant (MNC)', '', '', '', '', '2026-02-19 02:28:53'),
(13, 'Seasoning Plant (MNC)', '', '', '', '', '2026-02-19 02:29:09'),
(14, 'Engineering (MNC)', '', '', '', '', '2026-02-19 02:29:28'),
(15, 'IT DEPT', 'Mr. Ruel Nunez', '', '', '', '2026-02-19 02:29:55'),
(16, 'ASTRO', 'Engenette', '', '', '', '2026-03-06 03:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `designation`, `contact_no`, `email`) VALUES
(4, 'Samuel', 'Base', 'Supervisor', '123456', 'sam@gmail.com'),
(5, 'Ruel', 'Nunez', 'IT', '123456', 'nunezru@8th-mile.com');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit_type` varchar(20) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `return_id` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `item_holder` varchar(100) NOT NULL,
  `stockman_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_condition` varchar(50) DEFAULT 'Good',
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_items`
--

CREATE TABLE `return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `condition` varchar(30) NOT NULL,
  `action_taken` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `stockID` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `movement_type` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_out`
--

CREATE TABLE `stock_out` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `holder_name` varchar(100) NOT NULL,
  `holder_id_number` varchar(50) NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `date_out` datetime DEFAULT current_timestamp(),
  `ClientID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `contact`, `email`) VALUES
(3, 'Mega South', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'Staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'System Admin', 'admin', 'admin123', 'Administrator', '2026-02-14 08:45:28'),
(2, 'Kyle ', 'IT1', '123123', 'Staff', '2026-02-14 08:56:50'),
(3, 'Ruel Nunez', 'Ruel', '123', 'Staff', '2026-02-16 06:39:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_out`
--
ALTER TABLE `stock_out`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ClientID` (`ClientID`),
  ADD KEY `ClientID_2` (`ClientID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=470;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_out`
--
ALTER TABLE `stock_out`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
