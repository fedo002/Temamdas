-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 06 Mar 2025, 17:59:16
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kazanc_platformu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','moderator','support') DEFAULT 'admin',
  `status` enum('active','blocked') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$nLJY0mruoM0bRX9bQC3LM.d2oGVdnm7cPzQzk1hQd.s3kGXrLNpBO', 'admin', 'active', NULL, '2025-03-06 13:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(20,6) NOT NULL,
  `fee` decimal(20,6) DEFAULT 0.000000,
  `status` enum('pending','confirmed','failed') DEFAULT 'pending',
  `payment_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'nowpayments',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `deposits`
--

INSERT INTO `deposits` (`id`, `user_id`, `amount`, `fee`, `status`, `payment_id`, `order_id`, `transaction_hash`, `payment_method`, `created_at`, `updated_at`) VALUES
(1, 1, 2000.000000, 0.000000, 'pending', '5125476861', '0', NULL, 'nowpayments', '2025-03-06 13:32:35', '2025-03-06 13:32:35'),
(2, 1, 1000.000000, 0.000000, 'pending', '4327060757', '0', NULL, 'nowpayments', '2025-03-06 13:34:05', '2025-03-06 13:34:05'),
(3, 2, 1000.000000, 0.000000, 'confirmed', '4962909011', '0', NULL, 'nowpayments', '2025-03-06 14:02:45', '2025-03-06 14:03:03');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `game_attempts`
--

CREATE TABLE `game_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempt_result` enum('win','lose','retry') DEFAULT NULL,
  `stake_amount` decimal(10,2) DEFAULT 0.00,
  `win_amount` decimal(10,2) DEFAULT 0.00,
  `stage` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `game_attempts`
--

INSERT INTO `game_attempts` (`id`, `user_id`, `attempt_result`, `stake_amount`, `win_amount`, `stage`, `created_at`) VALUES
(1, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:39:52'),
(2, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:05'),
(3, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:15'),
(4, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:23'),
(5, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:30'),
(6, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:35'),
(7, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:40:40'),
(8, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:40:46'),
(9, 1, '', 5.00, 10.00, 2, '2025-03-06 14:41:48'),
(10, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:44:40'),
(11, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:44:42'),
(12, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:46:00'),
(13, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:49:12'),
(14, 1, 'win', 5.00, 7.00, 3, '2025-03-06 14:49:22'),
(15, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:51:31'),
(16, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:51:36'),
(17, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:51:42'),
(18, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 14:51:52'),
(19, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:52:01'),
(20, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:52:23'),
(21, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:52:29'),
(22, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:52:37'),
(23, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:52:43'),
(24, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 14:52:47'),
(25, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 14:54:08'),
(26, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:54:13'),
(27, 1, 'lose', 5.00, 7.00, 3, '2025-03-06 14:56:32'),
(28, 1, 'win', 5.00, 10.00, 1, '2025-03-06 14:58:15'),
(29, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 14:58:21'),
(30, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:01:10'),
(31, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:01:15'),
(32, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:01:21'),
(33, 1, 'lose', 5.00, 3.00, 3, '2025-03-06 15:01:25'),
(34, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:01:31'),
(35, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 15:01:54'),
(36, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:05:17'),
(37, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:05:21'),
(38, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:05:28'),
(39, 1, 'lose', 5.00, 3.00, 3, '2025-03-06 15:05:36'),
(40, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:05:42'),
(41, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:05:47'),
(42, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 15:05:51'),
(43, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:06:03'),
(44, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:07:00'),
(45, 1, 'win', 5.00, 10.00, 3, '2025-03-06 15:07:06'),
(46, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:07:13'),
(47, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:07:19'),
(48, 1, 'win', 5.00, 7.00, 3, '2025-03-06 15:07:23'),
(49, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:07:30'),
(50, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:07:35'),
(51, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:07:40'),
(52, 1, 'lose', 5.00, 7.00, 3, '2025-03-06 15:07:45'),
(53, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:07:52'),
(54, 1, 'win', 5.00, 10.00, 3, '2025-03-06 15:07:57'),
(55, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:08'),
(56, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:13'),
(57, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:19'),
(58, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:08:24'),
(59, 1, 'win', 5.00, 10.00, 3, '2025-03-06 15:08:27'),
(60, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:33'),
(61, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:08:34'),
(62, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:38'),
(63, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:39'),
(64, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:08:44'),
(65, 1, 'lose', 5.00, 7.00, 3, '2025-03-06 15:08:53'),
(66, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:08:59'),
(67, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:04'),
(68, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:09'),
(69, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:09:15'),
(70, 1, 'lose', 5.00, 3.00, 3, '2025-03-06 15:09:18'),
(71, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:24'),
(72, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:29'),
(73, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:35'),
(74, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:09:40'),
(75, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:09:46'),
(76, 1, 'win', 5.00, 3.00, 3, '2025-03-06 15:09:51'),
(77, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:10:00'),
(78, 1, 'lose', 5.00, 3.00, 3, '2025-03-06 15:10:03'),
(79, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:10:15'),
(80, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:10:21'),
(81, 1, 'win', 5.00, 10.00, 3, '2025-03-06 15:10:36'),
(82, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:11:27'),
(83, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:11:33'),
(84, 1, 'lose', 5.00, 3.00, 3, '2025-03-06 15:11:36'),
(85, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:11:43'),
(86, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:11:48'),
(87, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:11:54'),
(88, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:11:59'),
(89, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:05'),
(90, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:10'),
(91, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:16'),
(92, 1, 'win', 5.00, 10.00, 1, '2025-03-06 15:12:22'),
(93, 1, 'lose', 5.00, 10.00, 3, '2025-03-06 15:12:28'),
(94, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:38'),
(95, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:43'),
(96, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:49'),
(97, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:54'),
(98, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:12:59'),
(99, 1, 'lose', 5.00, 0.00, 1, '2025-03-06 15:13:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `game_rewards`
--

CREATE TABLE `game_rewards` (
  `id` int(11) NOT NULL,
  `vip_level` int(11) NOT NULL,
  `stage` int(11) NOT NULL,
  `base_reward` decimal(10,2) NOT NULL,
  `prize1_amount` decimal(10,2) NOT NULL,
  `prize1_chance` decimal(5,2) NOT NULL,
  `prize2_amount` decimal(10,2) NOT NULL,
  `prize2_chance` decimal(5,2) NOT NULL,
  `prize3_amount` decimal(10,2) NOT NULL,
  `prize3_chance` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `game_rewards`
--

INSERT INTO `game_rewards` (`id`, `vip_level`, `stage`, `base_reward`, `prize1_amount`, `prize1_chance`, `prize2_amount`, `prize2_chance`, `prize3_amount`, `prize3_chance`, `is_active`, `created_at`) VALUES
(1, 1, 2, 5.00, 3.00, 50.00, 7.00, 35.00, 10.00, 15.00, 1, '2025-03-06 15:51:00'),
(2, 2, 2, 10.00, 3.00, 40.00, 7.00, 40.00, 10.00, 20.00, 1, '2025-03-06 15:51:00'),
(3, 3, 2, 20.00, 3.00, 30.00, 7.00, 45.00, 10.00, 25.00, 1, '2025-03-06 15:51:00'),
(4, 4, 2, 50.00, 3.00, 20.00, 7.00, 50.00, 10.00, 30.00, 1, '2025-03-06 15:51:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mining_earnings`
--

CREATE TABLE `mining_earnings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_mining_id` int(11) NOT NULL,
  `hash_rate` decimal(10,2) DEFAULT NULL,
  `revenue` decimal(10,6) DEFAULT NULL,
  `electricity_cost` decimal(10,6) DEFAULT NULL,
  `net_revenue` decimal(10,6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `processed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mining_packages`
--

CREATE TABLE `mining_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `hash_rate` decimal(10,2) NOT NULL,
  `electricity_cost` decimal(10,4) NOT NULL,
  `daily_revenue_rate` decimal(6,4) NOT NULL,
  `package_price` decimal(20,6) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `mining_packages`
--

INSERT INTO `mining_packages` (`id`, `name`, `hash_rate`, `electricity_cost`, `daily_revenue_rate`, `package_price`, `is_active`, `description`, `created_at`) VALUES
(1, 'Başlangıç Paketi', 10.00, 0.1000, 0.0200, 100.000000, 1, 'Başlangıç seviyesi mining paketi', '2025-03-06 13:01:19'),
(2, 'Orta Seviye Paket', 50.00, 0.3000, 0.0500, 500.000000, 1, 'Orta seviye mining paketi', '2025-03-06 13:01:19'),
(3, 'Profesyonel Paket', 100.00, 0.6000, 0.1000, 1000.000000, 1, 'Profesyonel seviye mining paketi', '2025-03-06 13:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'nowpayments_api_key', 'X1NJW52-NRY4EPF-GYBEKM6-MVZJG6A', 'NOWPayments API Anahtarı', '2025-03-06 13:23:22'),
(2, 'nowpayments_ipn_secret', 'cCoz43Pl1Ckay8Ym6iRv7MvUL4i7pIlO', 'NOWPayments IPN Secret Anahtarı', '2025-03-06 13:23:35'),
(3, 'nowpayments_test_mode', '0', 'NOWPayments Test Modu (0: Kapalı, 1: Açık)', '2025-03-06 13:01:19'),
(4, 'min_deposit_amount', '10', 'Minimum yatırım tutarı (USDT)', '2025-03-06 13:01:19'),
(5, 'min_withdraw_amount', '20', 'Minimum çekim tutarı (USDT)', '2025-03-06 13:01:19'),
(6, 'withdraw_fee', '2', 'Çekim ücreti (%)', '2025-03-06 13:01:19'),
(7, 'trc20_address', '', 'Platform TRC-20 USDT Cüzdan Adresi', '2025-03-06 13:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'Kazanç Platformu', 'Site Adı', '2025-03-06 13:01:19'),
(2, 'site_description', 'Birlikte Kazan, Birlikte Büyü', 'Site Açıklaması', '2025-03-06 13:01:19'),
(3, 'referral_active', '1', 'Referans sistemi aktif mi?', '2025-03-06 13:01:19'),
(4, 'referral_tier1_rate', '0.05', 'Tier 1 referans oranı', '2025-03-06 13:01:19'),
(5, 'referral_tier2_rate', '0.02', 'Tier 2 referans oranı', '2025-03-06 13:01:19'),
(6, 'mining_active', '1', 'Mining sistemi aktif mi?', '2025-03-06 13:01:19'),
(7, 'daily_game_active', '1', 'Günlük ödül oyunu aktif mi?', '2025-03-06 13:01:19'),
(8, 'max_win_chance', '0.15', 'Maksimum kazanma şansı (oyun)', '2025-03-06 13:01:19'),
(9, 'support_email', 'support@example.com', 'Destek e-posta adresi', '2025-03-06 13:01:19'),
(10, 'contact_email', 'contact@example.com', 'İletişim e-posta adresi', '2025-03-06 13:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_user_message` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','closed') DEFAULT 'open',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `type` enum('deposit','withdraw','referral','game','mining','bonus','transfer','other') NOT NULL,
  `amount` decimal(20,6) NOT NULL,
  `before_balance` decimal(20,6) DEFAULT NULL,
  `after_balance` decimal(20,6) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'completed',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `trc20_address` varchar(100) DEFAULT NULL,
  `balance` decimal(20,6) DEFAULT 0.000000,
  `referral_balance` decimal(20,6) DEFAULT 0.000000,
  `referral_code` varchar(20) DEFAULT NULL,
  `referrer_id` int(11) DEFAULT NULL,
  `vip_level` int(11) DEFAULT 0,
  `total_deposit` decimal(20,6) DEFAULT 0.000000,
  `total_withdraw` decimal(20,6) DEFAULT 0.000000,
  `total_earnings` decimal(20,6) DEFAULT 0.000000,
  `status` enum('active','blocked','pending') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `trc20_address`, `balance`, `referral_balance`, `referral_code`, `referrer_id`, `vip_level`, `total_deposit`, `total_withdraw`, `total_earnings`, `status`, `last_login`, `created_at`) VALUES
(1, 'pranga', 'h22221737@gmail.com', '$2y$10$94mwgpGsmaoRySb3TLPpo.bw77GMbsxSMCnRKtSWrBUfNbAqzesL2', NULL, NULL, 10297.000000, 0.000000, 'CRC64VV1', NULL, 1, 0.000000, 0.000000, 0.000000, 'active', NULL, '2025-03-06 13:13:17'),
(2, 'pranga01', 'hilalsonbahar789@gmail.com', '$2y$10$2Yk3yRbI5cQc/sgBAUF.C.5KUOo6PKYlCrzFa9Ns4c5.Ch/z/SaCi', NULL, NULL, 1000.000000, 0.000000, '7ZPBNXLR', 1, 0, 1000.000000, 0.000000, 0.000000, 'active', NULL, '2025-03-06 13:58:37');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_mining_packages`
--

CREATE TABLE `user_mining_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','expired','paused') DEFAULT 'active',
  `total_earned` decimal(20,6) DEFAULT 0.000000,
  `total_electricity_cost` decimal(20,6) DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vip_packages`
--

CREATE TABLE `vip_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(20,6) NOT NULL,
  `daily_game_limit` int(11) DEFAULT 5,
  `game_max_win_chance` decimal(5,4) DEFAULT 0.1500,
  `referral_rate` decimal(5,4) DEFAULT 0.0500,
  `mining_bonus_rate` decimal(5,4) DEFAULT 0.0000,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `vip_packages`
--

INSERT INTO `vip_packages` (`id`, `name`, `price`, `daily_game_limit`, `game_max_win_chance`, `referral_rate`, `mining_bonus_rate`, `description`, `created_at`, `is_active`) VALUES
(1, 'Standart', 0.000000, 999, 0.1500, 0.0500, 0.0000, 'Ücretsiz Standart Paket', '2025-03-06 13:01:19', 1),
(2, 'Silver', 50.000000, 7, 0.2000, 0.0700, 0.0500, 'Günlük oyun limitini artır ve daha yüksek kazanma şansı', '2025-03-06 13:01:19', 1),
(3, 'Gold', 200.000000, 10, 0.2500, 0.1000, 0.1000, 'Daha fazla mining geliri ve referans bonusu', '2025-03-06 13:01:19', 1),
(4, 'Platinum', 500.000000, 15, 0.3000, 0.1500, 0.1500, 'En yüksek kazanç ve bonuslar', '2025-03-06 13:01:19', 1),
(5, 'Pro Elite', 700.000000, 99, 0.4000, 0.1500, 0.1500, 'En yüksek kazanç ve bonuslar +%10', '2025-03-06 13:01:19', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(20,6) NOT NULL,
  `fee` decimal(20,6) DEFAULT 0.000000,
  `status` enum('pending','processing','completed','failed','cancelled') DEFAULT 'pending',
  `trc20_address` varchar(255) NOT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Tablo için indeksler `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `game_attempts`
--
ALTER TABLE `game_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `game_rewards`
--
ALTER TABLE `game_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `mining_earnings`
--
ALTER TABLE `mining_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_mining_id` (`user_mining_id`);

--
-- Tablo için indeksler `mining_packages`
--
ALTER TABLE `mining_packages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Tablo için indeksler `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Tablo için indeksler `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `referrer_id` (`referrer_id`);

--
-- Tablo için indeksler `user_mining_packages`
--
ALTER TABLE `user_mining_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Tablo için indeksler `vip_packages`
--
ALTER TABLE `vip_packages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `game_attempts`
--
ALTER TABLE `game_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Tablo için AUTO_INCREMENT değeri `game_rewards`
--
ALTER TABLE `game_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `mining_earnings`
--
ALTER TABLE `mining_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `mining_packages`
--
ALTER TABLE `mining_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `user_mining_packages`
--
ALTER TABLE `user_mining_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `vip_packages`
--
ALTER TABLE `vip_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

--
-- Tablo kısıtlamaları `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `game_attempts`
--
ALTER TABLE `game_attempts`
  ADD CONSTRAINT `game_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `mining_earnings`
--
ALTER TABLE `mining_earnings`
  ADD CONSTRAINT `mining_earnings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `mining_earnings_ibfk_2` FOREIGN KEY (`user_mining_id`) REFERENCES `user_mining_packages` (`id`);

--
-- Tablo kısıtlamaları `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`),
  ADD CONSTRAINT `support_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `user_mining_packages`
--
ALTER TABLE `user_mining_packages`
  ADD CONSTRAINT `user_mining_packages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_mining_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `mining_packages` (`id`);

--
-- Tablo kısıtlamaları `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `withdrawals_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
