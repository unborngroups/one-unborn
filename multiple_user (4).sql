-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 01:09 PM
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
-- Database: `multiple_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_code` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `business_display_name` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `billing_spoc_name` varchar(255) DEFAULT NULL,
  `billing_spoc_contact` varchar(20) DEFAULT NULL,
  `billing_spoc_email` varchar(255) DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `invoice_email` varchar(255) DEFAULT NULL,
  `invoice_cc` varchar(255) DEFAULT NULL,
  `support_spoc_name` varchar(255) DEFAULT NULL,
  `support_spoc_mobile` varchar(20) DEFAULT NULL,
  `support_spoc_email` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_code`, `client_name`, `business_display_name`, `address1`, `address2`, `address3`, `city`, `state`, `country`, `pincode`, `billing_spoc_name`, `billing_spoc_contact`, `billing_spoc_email`, `gstin`, `invoice_email`, `invoice_cc`, `support_spoc_name`, `support_spoc_mobile`, `support_spoc_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CL0001', 'operationsm', 'MNC', 'Harur, tamil nadu', 'Harur, tamil nadu', 'Harur, tamil nadu', 'Bangalore', 'Karnataka', 'India', '636903', NULL, '09025012401', 'operationst.it21@scew.org', NULL, 'operationst.it21@scew.org', NULL, 'd', '09025012401', NULL, 'Active', '2025-10-17 04:51:50', '2025-10-17 05:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `cin_llpin` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `email_1` varchar(255) DEFAULT NULL,
  `email_2` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `billing_logo` varchar(255) DEFAULT NULL,
  `billing_sign_normal` varchar(255) DEFAULT NULL,
  `billing_sign_digital` varchar(255) DEFAULT NULL,
  `gst_no` varchar(255) DEFAULT NULL,
  `pan_number` varchar(255) DEFAULT NULL,
  `tan_number` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#333333',
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `company_name`, `cin_llpin`, `contact_no`, `phone_no`, `email_1`, `email_2`, `address`, `billing_logo`, `billing_sign_normal`, `billing_sign_digital`, `gst_no`, `pan_number`, `tan_number`, `color`, `logo`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MNC', NULL, '9025012401', '09025012401', 'operationst.it21@scew.org', 'operationst.it21@scew.org', 'uthangarai, tamilnadu', 'logos/ntow3QbfKW4F8J1aZ8nXOVO8pvYhb13mYEjZe7nM.png', NULL, NULL, NULL, NULL, NULL, '#333333', NULL, 'Active', '2025-10-17 04:51:15', '2025-10-17 04:51:15'),
(2, 'unborn technology', NULL, '9025012401', '09025012401', 'operationsofficial2124@gmail.com', 'operationsofficial2124@gmail.com', 'Harur, tamil nadu\r\nHarur, tamil nadu\r\nHarur, tamil nadu', NULL, NULL, NULL, NULL, NULL, NULL, '#333333', NULL, 'Active', '2025-10-17 04:51:37', '2025-10-17 04:51:37'),
(3, 'palle technologym', '784541', '78945421', '23423543546', 'operationsofficial2124@gmail.com', 'operationsofficial2124@gmail.com', 'operationst.itdsfjj fh hfbc fhdsb kfjedsnjaknsssssssssjerwebd ehrrrh eirhuewihruihhnirubbefhjgerg ywwrgwh ww rweehi', 'logos/D2O9W5nLvSL2tKjjeSAG2Zu7dIWGKObXFCP63NK1.jpg', 'signs/UbVLLWYK7wiWEH75k8lMYXo1bZfaFLTQSo04LIFQ.jpg', 'signs/FHbYDiU9kQNM2dSHWMUFbFvllsCYDYXWaHMkQo6X.jpg', '1235684', '458784', '8845454', '#333333', NULL, 'Active', '2025-10-17 05:15:21', '2025-10-17 05:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gst_number` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `mail_mailer` varchar(255) DEFAULT NULL,
  `mail_host` varchar(255) DEFAULT NULL,
  `mail_port` varchar(255) DEFAULT NULL,
  `mail_username` varchar(255) DEFAULT NULL,
  `mail_password` varchar(255) DEFAULT NULL,
  `mail_encryption` varchar(255) DEFAULT NULL,
  `mail_from_address` varchar(255) DEFAULT NULL,
  `mail_from_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_id`, `company_name`, `company_email`, `contact_no`, `website`, `address`, `gst_number`, `company_logo`, `linkedin_url`, `facebook_url`, `instagram_url`, `whatsapp_number`, `is_default`, `mail_mailer`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`, `mail_from_address`, `mail_from_name`, `created_at`, `updated_at`) VALUES
(1, NULL, 'unborn technology', 'unborn@gmail.com', '9025012401', NULL, NULL, NULL, 'logos/FwtFyHjMt0PVJVRZtm4ODn9q2Qoxib7I6Cehvmm1.jpg', NULL, NULL, NULL, '9025012401', 0, 'smtp', 'smtp.gmail.com', '587', 'operationsoffical4@gmail.com', 'qwmrmwxiwwmmyuua', 'tls', 'operationsofficial4@gmail.com', 'unborn technology', '2025-10-17 04:52:58', '2025-10-17 05:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `company_user`
--

CREATE TABLE `company_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_user`
--

INSERT INTO `company_user` (`id`, `user_id`, `company_id`, `created_at`, `updated_at`) VALUES
(1, 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `header` text DEFAULT NULL,
  `footer` text DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `company_id`, `subject`, `body`, `status`, `header`, `footer`, `email_from`, `email_to`, `created_at`, `updated_at`) VALUES
(1, 'User Created', 0, 'Welcome {name} to {company_name}', 'Hello {name},<br><br>Welcome to {company_name}!<br>Your account has been created with the email: {email}.<br>Joining Date: {joining_date}<br><br>Thank you,<br>Team {company_name}', 'Active', NULL, NULL, NULL, NULL, '2025-10-17 04:50:34', '2025-10-17 04:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `route` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `can_menu` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_view` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `module_name`, `user_type`, `name`, `route`, `icon`, `can_menu`, `can_add`, `can_edit`, `can_delete`, `can_view`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'superadmin', 'Dashboard', 'welcome', 'bi bi-speedometer2', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(2, 'Dashboard', 'admin', 'Dashboard', 'welcome', 'bi bi-speedometer2', 1, 1, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(3, 'Dashboard', 'users', 'Dashboard', 'welcome', 'bi bi-speedometer2', 1, 0, 0, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(4, 'User Management', 'superadmin', 'Manage User', 'users.index', 'bi bi-people', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(5, 'User Management', 'superadmin', 'User Type', 'usertype.index', 'bi bi-person-lines-fill', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(6, 'User Management', 'admin', 'Manage User', 'users.index', 'bi bi-people', 1, 1, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(7, 'Company', 'superadmin', 'Company Details', 'company.index', 'bi bi-building', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(8, 'Company', 'admin', 'Company Details', 'company.index', 'bi bi-building', 1, 0, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(9, 'Master', 'superadmin', 'Template Master', 'template.index', 'bi bi-file-earmark-text', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(10, 'Master', 'superadmin', 'Client Master', 'client.index', 'bi bi-person-badge', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(11, 'Master', 'superadmin', 'Vendor Master', 'vendor.index', 'bi bi-truck', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(12, 'Master', 'admin', 'Template Master', 'template.index', 'bi bi-file-earmark-text', 1, 1, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(13, 'Master', 'admin', 'Client Master', 'client.index', 'bi bi-person-badge', 1, 1, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(14, 'Master', 'admin', 'Vendor Master', 'vendor.index', 'bi bi-truck', 1, 1, 1, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(15, 'Settings', 'superadmin', 'Common Settings', 'settings.index', 'bi bi-gear', 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(16, 'Master', 'users', 'Client Master', 'client.index', 'bi bi-person-badge', 1, 0, 0, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(17, 'Master', 'users', 'Vendor Master', 'vendor.index', 'bi bi-truck', 1, 0, 0, 0, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_09_23_095415_create_sessions_table', 1),
(2, '2025_09_24_055852_create_cache_table', 1),
(3, '2025_09_27_060357_create_users_and_user_types_tables', 1),
(4, '2025_09_27_100920_create_password_reset_tokens_table', 1),
(5, '2025_09_29_085946_create_companies_user_types_and_pivot_tables', 1),
(6, '2025_09_29_103240_add_is_superuser_to_users_table', 1),
(7, '2025_10_02_105115_create_email_templates_table', 1),
(8, '2025_10_03_084421_add_email_template_id_to_users_table', 1),
(9, '2025_10_03_094302_clients', 1),
(10, '2025_10_03_175314_create_vendor_table', 1),
(11, '2025_10_07_053254_create_company_settings_table', 1),
(12, '2025_10_07_053943_create_tax_invoice_settings_table', 1),
(13, '2025_10_07_055840_create_system_settings_table', 1),
(14, '2025_10_11_093202_create_profiles_table', 1),
(15, '2025_10_14_043351_create_menus_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `Date_of_Birth` date DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `phone2` varchar(255) DEFAULT NULL,
  `aadhaar_number` varchar(255) DEFAULT NULL,
  `aadhaar_upload` varchar(255) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `pan_upload` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `fname`, `lname`, `designation`, `Date_of_Birth`, `phone1`, `phone2`, `aadhaar_number`, `aadhaar_upload`, `pan`, `pan_upload`, `bank_name`, `branch`, `bank_account_no`, `ifsc_code`, `created_at`, `updated_at`) VALUES
(1, 2, 'operations', 'T', 'user', '2025-10-15', '09025012401', '09025012401', '7885454421484', 'uploads/aadhaar/hYuTgpLg6k51aKdB5rKUEw82NNrKuWHPwufbioQ1.png', '987987264654', 'uploads/pan/Le9nu0EnOjBSJPS0ZCEFXlVhjylFvK5JE2oTrlqq.png', 'SBI', 'harur', '49528464987', 'sdfs54654', '2025-10-17 05:18:35', '2025-10-17 05:18:35');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'Asia/Kolkata',
  `date_format` varchar(255) NOT NULL DEFAULT 'DD-MM-YYYY',
  `language` varchar(255) NOT NULL DEFAULT 'English',
  `currency_symbol` varchar(255) NOT NULL DEFAULT '₹',
  `fiscal_start_month` varchar(255) NOT NULL DEFAULT 'April',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_invoice_settings`
--

CREATE TABLE `tax_invoice_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_prefix` varchar(255) NOT NULL DEFAULT 'INV',
  `invoice_start_no` int(11) NOT NULL DEFAULT 1,
  `currency_symbol` varchar(255) NOT NULL DEFAULT '₹',
  `currency_code` varchar(255) NOT NULL DEFAULT 'INR',
  `tax_percentage` decimal(5,2) DEFAULT NULL,
  `billing_terms` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `Date_of_Birth` date DEFAULT NULL,
  `Date_of_Joining` date DEFAULT NULL,
  `official_email` varchar(255) DEFAULT NULL,
  `personal_email` varchar(255) DEFAULT NULL,
  `email_template` varchar(255) DEFAULT NULL,
  `profile_created` tinyint(1) NOT NULL DEFAULT 0,
  `user_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL DEFAULT 1,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `email_template_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `mobile`, `Date_of_Birth`, `Date_of_Joining`, `official_email`, `personal_email`, `email_template`, `profile_created`, `user_type_id`, `status`, `created_at`, `updated_at`, `remember_token`, `is_superuser`, `role`, `email_template_id`) VALUES
(1, 'Super Admin', 'superadmin@example.com', NULL, '$2y$12$Nmo4vn2UnKTbe61vp5wG6eWy0AEKmeWQ7SAGaftk0P/8PiKl6Xgyy', NULL, '2000-01-01', NULL, NULL, NULL, NULL, 1, 1, 'Active', '2025-10-17 04:50:34', '2025-10-17 04:50:34', NULL, 1, 'superadmin', NULL),
(2, 'operationst', 'operationsofficial2124@gmail.com', NULL, '$2y$12$u4qlxZpYiwSU18oZBugjveFElr7uedt7fPWPvR5cn/xxNTQZIb7du', '09398306293', '2025-05-20', '2025-05-20', 'operationsofficial2124@gmail.com', 'operationsofficial2124@gmail.com', NULL, 1, 2, 'Active', '2025-10-17 05:16:35', '2025-10-17 05:18:35', NULL, 1, 'user', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_menu_privileges`
--

CREATE TABLE `user_menu_privileges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `can_menu` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_menu_privileges`
--

INSERT INTO `user_menu_privileges` (`id`, `user_id`, `menu_id`, `can_menu`, `can_add`, `can_edit`, `can_delete`, `can_view`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(2, 1, 2, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(3, 1, 3, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(4, 1, 4, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(5, 1, 5, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(6, 1, 6, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(7, 1, 7, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(8, 1, 8, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(9, 1, 9, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(10, 1, 10, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(11, 1, 11, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(12, 1, 12, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(13, 1, 13, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(14, 1, 14, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(15, 1, 15, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(16, 1, 16, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(17, 1, 17, 1, 1, 1, 1, 1, '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(30, 2, 1, 1, 1, 1, 1, 1, '2025-10-17 05:21:00', '2025-10-17 05:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`, `Description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', NULL, 'Active', '2025-10-17 04:50:34', '2025-10-17 04:50:34'),
(2, 'user', NULL, 'Active', '2025-10-17 04:50:34', '2025-10-17 04:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_code` varchar(255) NOT NULL,
  `vendor_name` varchar(255) NOT NULL,
  `business_display_name` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `contact_person_mobile` varchar(20) DEFAULT NULL,
  `contact_person_email` varchar(255) DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `bank_account_no` varchar(30) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_code`, `vendor_name`, `business_display_name`, `address1`, `address2`, `address3`, `city`, `state`, `country`, `pincode`, `contact_person_name`, `contact_person_mobile`, `contact_person_email`, `gstin`, `pan_no`, `bank_account_no`, `ifsc_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'V0001', 'operationsd', 'MNC', 'Harur, tamil nadu', 'Harur, tamil nadu', 'Harur, tamil nadu', NULL, NULL, NULL, '636903', NULL, '09025012401', 'operationst.it21@scew.org', '123123', NULL, NULL, NULL, 'Active', '2025-10-17 04:52:01', '2025-10-17 05:24:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_client_code_unique` (`client_code`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_user`
--
ALTER TABLE `company_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_user_user_id_company_id_unique` (`user_id`,`company_id`),
  ADD KEY `company_user_company_id_foreign` (`company_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD KEY `password_reset_tokens_email_index` (`email`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_invoice_settings`
--
ALTER TABLE `tax_invoice_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_official_email_unique` (`official_email`),
  ADD KEY `users_user_type_id_foreign` (`user_type_id`),
  ADD KEY `users_email_template_id_foreign` (`email_template_id`);

--
-- Indexes for table `user_menu_privileges`
--
ALTER TABLE `user_menu_privileges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_menu_privileges_user_id_foreign` (`user_id`),
  ADD KEY `user_menu_privileges_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_vendor_code_unique` (`vendor_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_user`
--
ALTER TABLE `company_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_invoice_settings`
--
ALTER TABLE `tax_invoice_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_menu_privileges`
--
ALTER TABLE `user_menu_privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_user`
--
ALTER TABLE `company_user`
  ADD CONSTRAINT `company_user_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_email_template_id_foreign` FOREIGN KEY (`email_template_id`) REFERENCES `email_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_menu_privileges`
--
ALTER TABLE `user_menu_privileges`
  ADD CONSTRAINT `user_menu_privileges_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_menu_privileges_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
