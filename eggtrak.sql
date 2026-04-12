-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2026 at 03:58 AM
-- Server version: 11.4.10-MariaDB-cll-lve
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `serbvhad_eggtrak`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(10) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` varchar(50) NOT NULL,
  `changes` text DEFAULT NULL,
  `action_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `admin_id`, `user_id`, `action`, `table_name`, `record_id`, `changes`, `action_time`) VALUES
(151, 8, NULL, 'add', 'admin', '12', '{\"admin_name\":\"Pia Abella\",\"email\":\"piaabella19@gmail.com\",\"created_by\":\"8\"}', '2025-10-28 02:03:11'),
(152, 8, NULL, 'add', 'egg_inventory', '51', '{\"egg_type\":\"Regular\",\"size\":\"Medium\",\"quality\":\"Grade A\",\"stock_quantity\":\"20\",\"production_date\":\"2025-10-29\",\"expiry_date\":\"2025-11-28\"}', '2025-10-29 07:00:57'),
(153, 8, NULL, 'add', 'egg_inventory', '52', '{\"egg_type\":\"Free-Range\",\"size\":\"Small\",\"quality\":\"Grade A\",\"stock_quantity\":\"12\",\"production_date\":\"2025-10-29\",\"expiry_date\":\"2025-11-28\"}', '2025-10-29 07:01:05'),
(154, 8, NULL, 'update', 'egg_inventory', '52', '{\"stock_quantity\":\"123\"}', '2025-10-29 07:01:19'),
(155, 8, NULL, 'delete', 'egg_inventory', '51', '{\"egg_type\":\"Regular\",\"size\":\"Medium\",\"quality\":\"Grade A\",\"stock_quantity\":20,\"production_date\":\"2025-10-29\",\"expiry_date\":\"2025-11-28\"}', '2025-10-29 07:01:22'),
(156, 8, NULL, 'add', 'expenses', '25', '{\"date\":\"2025-10-29\",\"category\":\"Egg\",\"description\":\"50\",\"amount\":\"10\"}', '2025-10-29 07:02:09'),
(157, 8, NULL, 'delete', 'expenses', '25', '{\"date\":\"2025-10-29\",\"category\":\"Egg\",\"description\":\"50\",\"amount\":\"10.00\"}', '2025-10-29 07:02:30'),
(158, NULL, NULL, 'add', 'sales', '34', '{\"product_name\":\"Egg\",\"quantity\":\"100\",\"price\":\"10\",\"total\":1000,\"sale_date\":\"2025-10-29\"}', '2025-10-29 07:02:43'),
(159, 8, NULL, 'update', 'sales', '34', '{\"quantity\":\"1001\",\"total\":10010}', '2025-10-29 07:02:52'),
(160, 8, NULL, 'delete', 'sales', '34', '{\"product_name\":\"Egg\",\"quantity\":1001,\"price\":\"10.00\",\"total\":\"10010.00\",\"sale_date\":\"2025-10-29\"}', '2025-10-29 07:03:00'),
(161, 8, NULL, 'add', 'return_stocks', '22', '{\"egg_type\":\"Regular\",\"quantity\":\"100\",\"return_reason\":\"Expired\",\"return_date\":\"2025-10-29\"}', '2025-10-29 07:03:17'),
(162, 8, NULL, 'update', 'return_stocks', '22', '{\"quantity\":\"1001\"}', '2025-10-29 07:03:23'),
(163, 8, NULL, 'delete', 'return_stocks', '22', '{\"egg_type\":\"Regular\",\"quantity\":1001,\"return_reason\":\"Expired\",\"return_date\":\"2025-10-29\"}', '2025-10-29 07:03:26'),
(164, 8, NULL, 'delete', 'egg_inventory', '52', '{\"egg_type\":\"Free-Range\",\"size\":\"Small\",\"quality\":\"Grade A\",\"stock_quantity\":123,\"production_date\":\"2025-10-29\",\"expiry_date\":\"2025-11-28\"}', '2025-10-29 07:04:34'),
(165, 8, NULL, 'add', 'admin', '13', '{\"admin_name\":\"Ann joshua tresvalles\",\"email\":\"anndeuswa@gmail.com\",\"created_by\":\"8\"}', '2025-11-13 06:37:46'),
(166, 8, NULL, 'delete', 'admin', '13', '{\"admin_name\":\"Ann joshua tresvalles\",\"email\":\"anndeuswa@gmail.com\",\"deleted_by\":\"8\"}', '2025-11-13 06:44:14'),
(167, 8, NULL, 'add', 'admin', '14', '{\"admin_name\":\"Ann\",\"email\":\"anndeuswa@gmail.com\",\"created_by\":\"8\"}', '2025-11-13 06:44:34'),
(168, 8, NULL, 'add', 'egg_inventory', '53', '{\"egg_type\":\"Regular\",\"size\":\"Small\",\"quality\":\"Grade A\",\"stock_quantity\":\"28\",\"production_date\":\"2025-11-14\",\"expiry_date\":\"2025-12-14\"}', '2025-11-13 22:14:39'),
(169, 8, NULL, 'add', 'egg_inventory', '54', '{\"egg_type\":\"Regular\",\"size\":\"Small\",\"quality\":\"Grade A\",\"stock_quantity\":\"28\",\"production_date\":\"2025-11-14\",\"expiry_date\":\"2025-12-14\"}', '2025-11-13 22:14:40'),
(170, 8, NULL, 'delete', 'egg_inventory', '54', '{\"egg_type\":\"Regular\",\"size\":\"Small\",\"quality\":\"Grade A\",\"stock_quantity\":28,\"production_date\":\"2025-11-14\",\"expiry_date\":\"2025-12-14\"}', '2025-11-13 22:14:51'),
(171, 14, NULL, 'add', 'egg_inventory', '55', '{\"egg_type\":\"Regular\",\"size\":\"Large\",\"quality\":\"Grade B\",\"stock_quantity\":\"120\",\"production_date\":\"2026-03-21\",\"expiry_date\":\"2026-04-20\"}', '2026-03-21 02:21:54'),
(172, 14, NULL, 'add', 'users', '31', '{\"full_name\":\"Alexander John Caligan\",\"email\":\"almo.caligan.ui@phinmaed.com\"}', '2026-03-27 21:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `created_by`, `admin_name`, `email`, `password`, `role`, `profile_picture`, `created_at`, `updated_at`) VALUES
(8, NULL, 'Jay-R Robles', 'jayrrobles088@gmail.com', '$2y$12$tGLw5zxcd8b97Vyj2kJ8AepYzQ6WDRVtvTRSgqyrDHT4eqnR95OPW', 'admin', 'uploads/profile_690ced76a7c740.81742783.png', '2025-06-27 08:35:59', '2026-03-06 01:16:02'),
(12, 8, 'Pia Abella', 'piaabella19@gmail.com', '$2y$10$jNjiHlUXMh2IaNwrql2wZuVs4R7BlJLx7rq6r8JOAqZbP1OfixruG', 'admin', NULL, '2025-10-28 06:03:11', '2025-11-06 18:00:26'),
(14, 8, 'Ann', 'anndeuswa@gmail.com', '$2y$12$F8hvDqliwH0xId7uxRmgN.31gU.G/O7BUfgJ2v1vnuZBChXuQEwF2', 'admin', NULL, '2025-11-13 11:44:34', '2026-03-06 01:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `consumers`
--

CREATE TABLE `consumers` (
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `last_purchase` date DEFAULT NULL,
  `next_delivery` date DEFAULT NULL,
  `restock_reminder` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumers`
--

INSERT INTO `consumers` (`supplier_id`, `user_id`, `admin_id`, `supplier_name`, `contact_number`, `email`, `address`, `last_purchase`, `next_delivery`, `restock_reminder`, `created_at`, `updated_at`) VALUES
(14, NULL, 8, 'Jay-r Robles', '09821569079', 'jayrrobles088@gmail.com', 'Brgy sta felomina', '2025-10-28', '2025-10-28', '0000-00-00', '2025-10-28 05:34:53', '2025-10-28 01:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `egg_inventory`
--

CREATE TABLE `egg_inventory` (
  `batch_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `egg_type` varchar(50) NOT NULL,
  `size` varchar(20) NOT NULL,
  `quality` varchar(20) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `production_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `egg_inventory`
--

INSERT INTO `egg_inventory` (`batch_id`, `user_id`, `admin_id`, `egg_type`, `size`, `quality`, `stock_quantity`, `production_date`, `expiry_date`, `updated_at`) VALUES
(53, NULL, 8, 'Regular', 'Small', 'Grade A', 28, '2025-11-14', '2025-12-14', '2025-11-13 22:14:39'),
(55, NULL, 14, 'Regular', 'Large', 'Grade B', 120, '2026-03-21', '2026-04-20', '2026-03-21 02:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `history_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_datetime` datetime NOT NULL,
  `signout_datetime` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('Success','Failed') NOT NULL,
  `attempt_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`history_id`, `admin_id`, `user_id`, `login_datetime`, `signout_datetime`, `ip_address`, `user_agent`, `status`, `attempt_password`) VALUES
(25, 8, NULL, '2025-10-28 13:57:31', NULL, '203.84.189.233', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Failed', 'Qwerty123'),
(26, 8, NULL, '2025-10-28 13:57:41', NULL, '203.84.189.233', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Failed', 'Qwerty123456'),
(27, 8, NULL, '2025-10-28 13:58:36', NULL, '203.84.189.233', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(28, 8, NULL, '2025-10-28 14:15:12', NULL, '124.217.31.21', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(29, 8, NULL, '2025-10-28 14:16:10', NULL, '124.217.31.21', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Success', NULL),
(30, NULL, 29, '2025-10-29 11:01:54', '2025-10-28 23:02:25', '203.84.189.248', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(31, 8, NULL, '2025-10-29 11:03:06', '2025-10-28 23:04:18', '203.84.189.251', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(32, 8, NULL, '2025-10-29 19:00:03', '2025-10-29 07:05:11', '143.44.196.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Success', NULL),
(33, 8, NULL, '2025-10-29 19:44:18', NULL, '143.44.196.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Success', NULL),
(34, 8, NULL, '2025-11-02 19:35:55', NULL, '203.84.189.250', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(38, 8, NULL, '2025-11-07 02:47:27', '2025-11-06 13:49:51', '143.44.196.86', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(39, 8, NULL, '2025-11-07 02:54:38', NULL, '143.44.196.86', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(40, 8, NULL, '2025-11-13 13:17:38', NULL, '143.44.196.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Failed', '123456'),
(41, 8, NULL, '2025-11-13 13:17:48', NULL, '143.44.196.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Failed', '123456678'),
(42, 8, NULL, '2025-11-13 13:17:53', NULL, '143.44.196.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Failed', '123456'),
(43, 8, NULL, '2025-11-13 13:18:19', NULL, '143.44.196.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(44, 8, NULL, '2025-11-13 19:28:26', NULL, '143.44.196.146', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(45, 14, NULL, '2025-11-13 20:46:55', NULL, '221.121.99.233', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(46, 8, NULL, '2025-11-13 22:50:25', NULL, '143.44.196.146', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Failed', '123456'),
(47, 8, NULL, '2025-11-13 22:51:00', NULL, '143.44.196.146', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(48, NULL, 29, '2025-11-14 00:48:58', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Failed', '12345678'),
(49, NULL, 29, '2025-11-14 00:49:11', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Failed', '12345678'),
(50, NULL, 29, '2025-11-14 00:50:28', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Failed', '12345678'),
(51, NULL, 29, '2025-11-14 00:51:01', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Failed', '12345678'),
(52, NULL, 29, '2025-11-14 00:52:20', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Failed', '842170'),
(53, 8, NULL, '2025-11-14 01:11:13', NULL, '143.44.196.49', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Success', NULL),
(54, 14, NULL, '2025-11-14 06:50:30', NULL, '221.121.99.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(55, 8, NULL, '2025-11-14 08:58:34', NULL, '136.158.240.130', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_0_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/394.1.829830871 Mobile/15E148 Safari/604.1', 'Success', NULL),
(56, 8, NULL, '2025-11-14 11:13:08', NULL, '136.158.240.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Success', NULL),
(57, 8, NULL, '2025-11-22 18:10:43', NULL, '175.176.76.190', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(58, 8, NULL, '2026-03-06 09:07:25', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '123456'),
(59, 8, NULL, '2026-03-06 09:07:35', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '1234567'),
(60, 8, NULL, '2026-03-06 09:07:46', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '123456789'),
(61, 8, NULL, '2026-03-06 09:07:54', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', 'Qwerty123'),
(62, 8, NULL, '2026-03-06 09:07:54', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', 'Qwerty123'),
(63, 8, NULL, '2026-03-06 09:08:19', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '123456'),
(64, 8, NULL, '2026-03-06 09:08:40', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '123456'),
(65, 8, NULL, '2026-03-06 09:08:59', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '123456'),
(66, 8, NULL, '2026-03-06 09:09:34', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Failed', '1234567'),
(67, 8, NULL, '2026-03-06 09:17:12', NULL, '175.176.77.58', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(68, 14, NULL, '2026-03-06 09:42:38', NULL, '112.208.78.217', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(69, 14, NULL, '2026-03-12 12:54:59', NULL, '112.208.78.217', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(70, 14, NULL, '2026-03-21 14:16:13', NULL, '180.190.238.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'Failed', 'AnnJAdmin123'),
(71, 14, NULL, '2026-03-21 14:18:36', NULL, '180.190.238.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'Success', NULL),
(72, 14, NULL, '2026-03-21 14:19:20', NULL, '180.190.238.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'Success', NULL),
(73, 8, NULL, '2026-03-27 21:33:09', '2026-03-27 09:34:01', '103.84.177.35', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Success', NULL),
(74, 14, NULL, '2026-03-28 09:27:14', NULL, '136.158.240.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Success', NULL),
(75, NULL, 31, '2026-03-28 09:31:28', NULL, '136.158.240.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'Success', NULL),
(76, 8, NULL, '2026-03-29 12:29:15', NULL, '175.176.72.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'Success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `user_id`, `admin_id`, `email`, `otp`, `created_at`, `expires_at`, `verified`) VALUES
(93, NULL, 8, 'jayrrobles088@gmail.com', '215166d49ac2ae5f5d04554404f9234792ec388b87fa5150c510ad5ce493ec1c', '2025-11-06 18:46:58', '2025-11-06 18:47:27', 1),
(94, NULL, 8, 'jayrrobles088@gmail.com', '1578fdf0f0f40b719d9f30267867b6a4a18ed04c068ce7c0ce9a200d1c2040ef', '2025-11-06 18:49:58', '2025-11-06 18:54:38', 1),
(95, NULL, 8, 'Jayrrobles088@gmail.com', 'bafe03d993a0270a726d3bfd96d99e45e206636132007e84e5fc9b6730e2853c', '2025-11-13 05:18:00', '2025-11-13 05:18:19', 1),
(96, NULL, 8, 'jayrrobles088@gmail.com', '1e471a55263147df7d7dc5680ba9528bd2e6b73f721a827e62b36cca4fd29589', '2025-11-13 11:28:06', '2025-11-13 11:28:26', 1),
(97, NULL, 14, 'anndeuswa@gmail.com', 'eeae603058213b0ae4ffe1a5bd36309b68a008b0dd2ac87ad176b15130e4f4d1', '2025-11-13 12:46:24', '2025-11-13 12:46:55', 1),
(98, NULL, 8, 'jayrrobles088@gmail.com', 'd01ebd6223b26eeaa0f7804b98bea65fc0b05660a6795f101b8c044fb8454a18', '2025-11-13 14:50:35', '2025-11-13 14:51:00', 1),
(99, NULL, 8, 'jayrrobles088@gmail.com', '248a90b0367bc4f7ff9615151c3269bb77026b7f4404a57c2aa4898e9e7df1a0', '2025-11-13 16:45:50', '2025-11-14 05:50:50', 0),
(100, 29, NULL, 'jayrrobles088@gmail.com', 'ccb14dbc89bf725ccfeb4f81a22eeccfca66146ae0be7a5fbfad5e160705bf52', '2025-11-13 16:49:44', '2025-11-14 05:54:44', 0),
(101, 29, NULL, 'jayrrobles088@gmail.com', '5f8c97c7e341992ad4b38fa260b06c31626d16dbe5bc18c27314603db433b4e6', '2025-11-13 16:49:49', '2025-11-14 05:54:49', 0),
(102, 29, NULL, 'jayrrobles088@gmail.com', 'b9bf9347b55a412954435a36bb33de01269e2e59a56174f73d9c898c9837a3eb', '2025-11-13 16:49:54', '2025-11-14 05:54:54', 0),
(103, 29, NULL, 'jayrrobles088@gmail.com', '641b9313577abd3413e179760772de8e9ae28e76ca0907ffd4c7381ec1552132', '2025-11-13 16:50:09', '2025-11-14 05:55:09', 0),
(104, 29, NULL, 'jayrrobles088@gmail.com', '2a4081714d20042dd77b5b3a6c6011a0c8ad06256809d00d9f8df49df84229e3', '2025-11-13 16:50:13', '2025-11-14 05:55:13', 0),
(105, 29, NULL, 'jayrrobles088@gmail.com', 'fc18d6b0645a935516055c8e29fa018ceff392ea5be1d2653f226f4ee100e7f3', '2025-11-13 16:50:18', '2025-11-14 05:55:18', 0),
(106, NULL, 8, 'jayrrobles088@gmail.com', '8e66f3caeeeddbf88bd02876d8193d1ad1a5e2fec025e4c2d0796dc4512e46d5', '2025-11-13 16:58:11', '2025-11-14 06:03:11', 0),
(107, NULL, 8, 'jayrrobles088@gmail.com', 'deabcd5d4b811b417bc78fcbfd9de9b122838b369152263a46be1512f2099298', '2025-11-13 17:04:45', '2025-11-14 06:09:45', 0),
(108, NULL, 8, 'jayrrobles088@gmail.com', 'e0e19ab3a1e89ab6e7a6256d823803951c8d8403c939c4a7f6281b9c4d742e9a', '2025-11-13 17:04:48', '2025-11-14 06:09:48', 0),
(109, NULL, 8, 'jayrrobles088@gmail.com', 'b2d12d166d061f8bc0743f6bede462efed2030a1d1673b455d5270ca1c12738c', '2025-11-13 17:04:52', '2025-11-14 06:09:52', 0),
(110, NULL, 8, 'jayrrobles088@gmail.com', '30ec1d0465e05a8a1049041171f46a1a15dfa8f211bcf542b3a8adae1d86ae1c', '2025-11-13 17:04:55', '2025-11-14 06:09:55', 0),
(111, NULL, 8, 'jayrrobles088@gmail.com', '10b20f23420e95ba436016c9035483ae3fddffbeacdc35d9d1aeabbacff32b5e', '2025-11-13 17:04:58', '2025-11-14 06:09:58', 0),
(112, NULL, 8, 'jayrrobles088@gmail.com', '9f1a6ffcc21a151e2653a8e6f59788245a1f7d199e8c5bf60ab55b7261b63212', '2025-11-13 17:07:55', '2025-11-14 06:12:55', 0),
(113, NULL, 8, 'jayrrobles088@gmail.com', 'b16ffc10fc13f1b1104591334d238cb8ce57553fb6d72c2882a5ccc72a01bd41', '2025-11-13 17:08:01', '2025-11-14 06:13:01', 0),
(114, NULL, 8, 'jayrrobles088@gmail.com', '20066223ac606ce9c2b472fd74928310b8f48b9a739f1aa4fd96048f93357087', '2025-11-13 17:08:03', '2025-11-14 06:13:03', 0),
(115, NULL, 8, 'jayrrobles088@gmail.com', '383975b739c3b1159c0b4f025d338b6561f043af95ebe79a0c6d95a470d508c7', '2025-11-13 17:08:06', '2025-11-14 06:13:06', 0),
(116, NULL, 8, 'jayrrobles088@gmail.com', '58b1a1e4198a05560f4720f352840a1e91612139ec8df67299a714e7025501cf', '2025-11-13 17:08:14', '2025-11-14 06:13:14', 0),
(117, NULL, 8, 'jayrrobles088@gmail.com', 'ecff2bec33fd8ada53450a0babf6f10609b52c0034a12b9806ce8b98c0075ea7', '2025-11-13 17:08:17', '2025-11-14 06:13:17', 0),
(118, NULL, 8, 'jayrrobles088@gmail.com', '578cf5620b4199c5ab97b386069b640cff5a4149e3833e03594ccdf01a8f87eb', '2025-11-13 17:08:20', '2025-11-14 06:13:20', 0),
(119, NULL, 8, 'jayrrobles088@gmail.com', 'c8c5dc7f33820fe2611bb84d9261b0855c91c1e46b98b6b6e2e44d3802a0698a', '2025-11-13 17:08:23', '2025-11-14 06:13:23', 0),
(120, NULL, 8, 'jayrrobles088@gmail.com', '5c6e4b910ce26f8f2e2eb4d03baa5db187cf32e66428a0bcd4e32ec8ee86c755', '2025-11-13 17:08:27', '2025-11-14 06:13:27', 0),
(121, NULL, 8, 'jayrrobles088@gmail.com', '59bfedad105449638b62548abe063775d80ddbe14f0174bd80eab799f5f09462', '2025-11-13 17:08:31', '2025-11-14 06:13:31', 0),
(122, NULL, 8, 'jayrrobles088@gmail.com', '04a30eb8ecd32a10acac7767334ddd636c2449eb6c1d5a3384622d28268bfeb9', '2025-11-13 17:08:34', '2025-11-14 06:13:34', 0),
(123, NULL, 8, 'jayrrobles088@gmail.com', '1675aa1e7716af7df1a2e14e1f0e566a91537a85e29f2455eb1024e18f782cc5', '2025-11-13 17:08:37', '2025-11-14 06:13:37', 0),
(124, NULL, 8, 'jayrrobles088@gmail.com', '49477b4a740c2fada7becfdac966f39524f7e659d074bf288b326ed1e3361813', '2025-11-13 17:08:42', '2025-11-14 06:13:42', 0),
(125, NULL, 8, 'jayrrobles088@gmail.com', '99d5eacbb576e0e0c165245fc89b3f6c7560da317dca1a02d471f30c02b8a4c5', '2025-11-13 17:08:46', '2025-11-14 06:13:46', 0),
(126, NULL, 8, 'jayrrobles088@gmail.com', 'fcb1c513f80551d57d540f58712c589e3bb736d7ee8d2cd04d1308ded6e616d4', '2025-11-13 17:08:50', '2025-11-14 06:13:50', 0),
(127, NULL, 8, 'jayrrobles088@gmail.com', 'c8faa97ed9f4952a95334bdedd0d380389a581120e48de65ef81bc4383f95beb', '2025-11-13 17:08:55', '2025-11-14 06:13:55', 0),
(128, NULL, 8, 'jayrrobles088@gmail.com', '1302d3e1998d11657e40954671ffaedcca524c920e560b405506681bbdd33bd2', '2025-11-13 17:08:58', '2025-11-14 06:13:58', 0),
(129, NULL, 8, 'jayrrobles088@gmail.com', '437839560a26f03c7553909ad53c8529a2471f84b43a2bd734740175d90e6e70', '2025-11-13 17:09:02', '2025-11-14 06:14:02', 0),
(130, NULL, 8, 'jayrrobles088@gmail.com', '801ee22bb6d98673520970976bc07be91b75b6ace71c74d6e3810ca346f82198', '2025-11-13 17:09:06', '2025-11-14 06:14:06', 0),
(131, NULL, 8, 'jayrrobles088@gmail.com', '00caccaf020e21169a0bcadd968bf527abe318c9c5066af386f9ab4fcc774ce5', '2025-11-13 17:09:09', '2025-11-14 06:14:09', 0),
(132, NULL, 8, 'jayrrobles088@gmail.com', '5a8fb2f8e1796c2403b5cf18dccc5b6da44c1c8a1da27f9bc44ef56e61d3d2dd', '2025-11-13 17:10:25', '2025-11-13 17:11:13', 1),
(133, NULL, 14, 'anndeuswa@gmail.com', '88441af8eef118593a08bead46e4634780d6502b8df0760a434b4220ee518916', '2025-11-13 22:49:06', '2025-11-13 22:50:30', 1),
(134, NULL, 8, 'jayrrobles088@gmail.com', '1dfdc3fa37bfcbfb046edfe9a4b5662fbe128e5726cc031b17ccfd0b08a3778b', '2025-11-14 00:57:30', '2025-11-14 00:58:34', 1),
(135, NULL, 8, 'jayrrobles088@gmail.com', '6762d82c1dbfdb6267924a36eb8fad0733e4aa2dfb29e4d39e11e714d4191871', '2025-11-14 03:12:41', '2025-11-14 03:13:08', 1),
(136, NULL, 8, 'jayrrobles088@gmail.com', '06c51ca1b60c6b4f5c6b4478c6109c7ca3f787d39f123698223f23f8dc9d74af', '2025-11-22 10:10:17', '2025-11-22 10:10:43', 1),
(137, NULL, 8, 'jayrrobles088@gmail.com', 'fdbe71d0e063db8a81d0d5166b2d64c9a1176481fb062a216efcc30facceb574', '2026-03-06 01:16:47', '2026-03-06 01:17:12', 1),
(138, NULL, 14, 'anndeuswa@gmail.com', '24ddd41beea3e147eb64269e08c088f7e7bb0e27fe25bcac21a2227df838a137', '2026-03-06 01:22:47', '2026-03-06 14:27:47', 0),
(139, NULL, 14, 'anndeuswa@gmail.com', '10ab9dd540de225dad800e4a293bf209a56bc466b21898812934ac3e2ee1b9d6', '2026-03-06 01:23:42', '2026-03-06 14:28:42', 0),
(140, NULL, 14, 'anndeuswa@gmail.com', 'd43bbe0da45d88c809be62e806030cd1e1e71a9206138160356866ff8d6e1301', '2026-03-06 01:23:49', '2026-03-06 14:28:49', 0),
(141, NULL, 14, 'anndeuswa@gmail.com', '7df0e29a483b1c8fb7d8daf4bbc6fc2e348b7387b6453b5a52fa8ea0c379d072', '2026-03-06 01:24:58', '2026-03-06 14:29:58', 0),
(142, NULL, 14, 'anndeuswa@gmail.com', '86276c39403ed3e62233e1982fdddc4671fe738d5888617ce2e233c5fdf06bff', '2026-03-06 01:25:04', '2026-03-06 14:30:04', 0),
(143, NULL, 14, 'anndeuswa@gmail.com', '807554e5f4afe201a1a055c39fbf554e4ddeebf96555f8c0b7c1473b09cc4e53', '2026-03-06 01:41:34', '2026-03-06 01:42:38', 1),
(144, NULL, 14, 'anndeuswa@gmail.com', '0aba1c3642e641acd718cec1a2df809c73633add3dfccd74273122be2744d177', '2026-03-12 04:44:18', '2026-03-12 16:49:18', 0),
(145, NULL, 14, 'anndeuswa@gmail.com', '3044c23591d3c786efea3b5d24a6459cc66ce39c59f0f8d7356f723951446daa', '2026-03-12 04:44:24', '2026-03-12 16:49:24', 0),
(146, NULL, 14, 'anndeuswa@gmail.com', '8d27d130dfa6237997e37260083ef2d341695de0fab414c46872fa114ab1b57e', '2026-03-12 04:45:20', '2026-03-12 16:50:20', 0),
(147, NULL, 14, 'anndeuswa@gmail.com', 'a95b4045ad284c8285dd1013e6c4c0b5cf54e4213b5b30cf0bd0d322b50c2ca5', '2026-03-12 04:54:34', '2026-03-12 04:54:59', 1),
(148, NULL, 14, 'anndeuswa@gmail.com', '4269d6c09b22f8117eb80e8e05a486c8b3c7d651217c9cc92a0e7d8dbcc8b87d', '2026-03-21 06:17:29', '2026-03-21 06:18:36', 1),
(149, NULL, 14, 'anndeuswa@gmail.com', 'f62bebaffbf2a6360526e000098c5ef786b2d42a9843513feb47877575ca8067', '2026-03-21 06:18:50', '2026-03-21 06:19:20', 1),
(150, NULL, 8, 'jayrrobles088@gmail.com', '91c82a58ac0e61cdea5254ee5d6e256af354dd8c05c88d876e9caddbd13e19ea', '2026-03-27 13:32:45', '2026-03-27 13:33:09', 1),
(151, NULL, 14, 'anndeuswa@gmail.com', '3a06f42e1c1e4399203ca3415e357ac45461e49fac1b014fa438ed84d97c211f', '2026-03-28 01:26:34', '2026-03-28 01:27:14', 1),
(152, 31, NULL, 'almo.caligan.ui@phinmaed.com', 'a48fe471fba9cd51cb9a1be7ee0558d30393f5b94fc5545ae9ec6dcb92113e79', '2026-03-28 01:30:46', '2026-03-28 01:31:28', 1),
(153, NULL, 8, 'jayrrobles088@gmail.com', '23b1453c3d677ad78701ac941fe6e5672b66730d8a54b5b589e95778ad057a16', '2026-03-29 04:28:07', '2026-03-29 04:29:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `return_stocks`
--

CREATE TABLE `return_stocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `batch_id` int(11) NOT NULL,
  `egg_type` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_reason` text NOT NULL,
  `return_date` date NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `sale_date` date NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('manager','staff') NOT NULL,
  `password` varchar(255) NOT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_attempt_time` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `admin_id`, `full_name`, `email`, `role`, `password`, `failed_attempts`, `last_failed_attempt_time`, `is_active`, `deleted_at`) VALUES
(29, 8, 'Jay-R Robles', 'jayrrobles088@gmail.com', 'manager', '$2y$10$O8ePuAi348DblhVOfBITPOXSUaReDzf611ODuA.56dU7Uc2R3mBgS', 0, NULL, 1, NULL),
(31, 14, 'Alexander John Caligan', 'almo.caligan.ui@phinmaed.com', 'manager', '$2y$10$zHVRrQSRUZO1tIvS14d0qO0KFd6oUDlp4fjkgEfYZQLPTN0ypgAxy', 0, NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `activity_log_ibfk_2` (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_created_by` (`created_by`);

--
-- Indexes for table `consumers`
--
ALTER TABLE `consumers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `fk_suppliers_user` (`user_id`),
  ADD KEY `fk_suppliers_admin` (`admin_id`);

--
-- Indexes for table `egg_inventory`
--
ALTER TABLE `egg_inventory`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `fk_egg_inventory_user` (`user_id`),
  ADD KEY `fk_egg_inventory_admin` (`admin_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_expenses_user` (`user_id`),
  ADD KEY `fk_expenses_admin` (`admin_id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `fk_login_history_user` (`user_id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_otp_user` (`user_id`),
  ADD KEY `fk_otp_verifications_admin` (`admin_id`);

--
-- Indexes for table `return_stocks`
--
ALTER TABLE `return_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_return_stocks_user` (`user_id`),
  ADD KEY `fk_return_stocks_admin` (`admin_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_user` (`user_id`),
  ADD KEY `fk_sales_admin` (`admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admin` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `consumers`
--
ALTER TABLE `consumers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `egg_inventory`
--
ALTER TABLE `egg_inventory`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `return_stocks`
--
ALTER TABLE `return_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_created_by` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`);

--
-- Constraints for table `consumers`
--
ALTER TABLE `consumers`
  ADD CONSTRAINT `fk_suppliers_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `fk_suppliers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `egg_inventory`
--
ALTER TABLE `egg_inventory`
  ADD CONSTRAINT `fk_egg_inventory_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `fk_egg_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `fk_login_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_otp_verifications_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);

--
-- Constraints for table `return_stocks`
--
ALTER TABLE `return_stocks`
  ADD CONSTRAINT `fk_return_stocks_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `fk_return_stocks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
