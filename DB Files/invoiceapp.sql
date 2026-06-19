-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 09:16 AM
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
-- Database: `invoiceapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `logo_path` varchar(255) DEFAULT 'assets/images/nandai-events-logo.png',
  `theme_name` varchar(40) NOT NULL DEFAULT 'espresso',
  `primary_color` varchar(20) NOT NULL DEFAULT '#7a5638',
  `secondary_color` varchar(20) NOT NULL DEFAULT '#24170f',
  `accent_color` varchar(20) NOT NULL DEFAULT '#b8915d',
  `soft_color` varchar(20) NOT NULL DEFAULT '#f3e5d3',
  `page_bg_color` varchar(20) NOT NULL DEFAULT '#f5efe8',
  `font_color` varchar(20) NOT NULL DEFAULT '#261b14',
  `popup_color` varchar(20) NOT NULL DEFAULT '#f3e5d3',
  `button_color` varchar(20) NOT NULL DEFAULT '#7a5638',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `logo_path`, `theme_name`, `primary_color`, `secondary_color`, `accent_color`, `soft_color`, `page_bg_color`, `font_color`, `popup_color`, `button_color`, `updated_at`) VALUES
(1, 'uploads/logos/logo-20260619054913-4128.jpg', 'custom', '#7a5638', '#24170f', '#b8915d', '#ffe2bd', '#fcecdc', '#261b14', '#ffe2bd', '#7a5638', '2026-06-19 03:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(140) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `email` varchar(160) DEFAULT NULL,
  `event_type` varchar(120) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `name`, `phone`, `email`, `event_type`, `event_date`, `budget`, `message`, `status`, `created_at`) VALUES
(2, 'kunal more', '08805545888', 'atharvagujar.789@gmail.com', 'Anniversary', '2026-06-16', 5000.00, 'jfyjmb', 'new', '2026-06-19 05:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`) VALUES
(1, 'Birthday', 'birthday', 'Birthday Partys', 1, '2026-06-19 04:54:41'),
(2, 'Wedding', 'wedding', 'Marriage', 1, '2026-06-19 04:55:19'),
(3, 'Haldi Image', 'haldi-image', '', 1, '2026-06-19 05:06:15');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(160) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(80) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `category_id`, `title`, `file_path`, `original_name`, `mime_type`, `file_size`, `created_at`) VALUES
(5, 3, '186af444e1a288c7a1823c1fb5970f39', 'uploads/category/haldi-image/186af444e1a288c7a1823c1fb5970f39-20260619090451-6518.jpg', '186af444e1a288c7a1823c1fb5970f39.jpg', 'image/jpeg', 76727, '2026-06-19 07:04:51'),
(6, 1, 'image0_birthday-set-up-ideas_birthday-set-up-ideas', 'uploads/category/birthday/image0-birthday-set-up-ideas-birthday-set-up-ideas-20260619090457-7614.jpg', 'image0_birthday-set-up-ideas_birthday-set-up-ideas.jpg', 'image/jpeg', 169402, '2026-06-19 07:04:57'),
(7, 2, 'OIP', 'uploads/category/wedding/oip-20260619090503-8248.jpg', 'OIP.jpg', 'image/jpeg', 62994, '2026-06-19 07:05:03');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_no` varchar(40) NOT NULL,
  `gst_no` varchar(40) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `sender_name` varchar(160) NOT NULL,
  `sender_tagline` varchar(255) DEFAULT NULL,
  `sender_phone` varchar(80) DEFAULT NULL,
  `sender_email` varchar(160) DEFAULT NULL,
  `invoice_to` varchar(160) NOT NULL,
  `client_email` varchar(160) DEFAULT NULL,
  `client_phone` varchar(80) DEFAULT NULL,
  `venue_address` varchar(255) DEFAULT NULL,
  `event_name` varchar(160) DEFAULT NULL,
  `account_no` varchar(80) DEFAULT NULL,
  `account_name` varchar(160) DEFAULT NULL,
  `bank_name` varchar(160) DEFAULT NULL,
  `ifsc_code` varchar(40) DEFAULT NULL,
  `branch_address` varchar(180) DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `advance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_no`, `gst_no`, `invoice_date`, `sender_name`, `sender_tagline`, `sender_phone`, `sender_email`, `invoice_to`, `client_email`, `client_phone`, `venue_address`, `event_name`, `account_no`, `account_name`, `bank_name`, `ifsc_code`, `branch_address`, `signature_path`, `notes`, `subtotal`, `advance`, `total`, `balance`, `created_at`, `updated_at`) VALUES
(4, 'INV-20260618-121332-729', '', '2026-06-18', 'Nandai Events', 'We turn ideas into action', '+91 7719948722 / +91 8446847989', 'nandaievents@gmail.com', 'KHS Pre primary school', '', '+91 9325798058', 'Ganeshnagar, Pandurang Colony, Erandwane, Pune,', 'Ballon Decoration', '38618100000128', 'Kunal Bhanudas More', 'Bank of Baroda', 'BARB0KOTHRUD', 'Kothrud, Pune 411038', 'uploads/signatures/signature-20260618190426-8469.png', 'Thank you for your business.', 5000.00, 0.00, 5000.00, 5000.00, '2026-06-18 10:13:35', '2026-06-18 17:04:26'),
(5, 'INV-20260618-155802-645', '12345678910', '2026-05-07', 'Nandai Events', 'We turn ideas into action', '+91 8446847989 / +91 7620956830', 'nandaievents@gmail.com', 'Atharva Gujar', 'atharvagujar.789@gmail.com', '+91 8805545888', 'Pune', 'Birthday', '38618100000128', 'Kunal Bhanudas More', 'Bank of Baroda', 'BARB0KOTHRUD', 'Kothrud, Pune 411038', 'uploads/signatures/signature-20260619080737-1763.png', 'Thank you for your business.', 200000.00, 0.00, 200000.00, 200000.00, '2026-06-18 13:59:28', '2026-06-19 06:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_email_logs`
--

CREATE TABLE `invoice_email_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `sender_email` varchar(160) DEFAULT NULL,
  `client_email` varchar(160) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'failed',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_email_logs`
--

INSERT INTO `invoice_email_logs` (`id`, `invoice_id`, `sender_email`, `client_email`, `email_subject`, `email_body`, `status`, `error_message`, `sent_at`) VALUES
(2, 4, '', '', 'Invoice email failed', '', 'failed', 'Client email is empty or invalid. Add client email first, then send invoice.', '2026-06-18 10:26:48'),
(3, 5, 'nandaievents@gmail.com', 'atharvagujar.789@gmail.com', 'Your invoice INV-20260618-155802-645 from Nandai Events', '\n<!DOCTYPE html>\n<html>\n<head>\n<meta charset=\"UTF-8\">\n<style>\nbody {\n  margin: 0;\n  padding: 0;\n  background: #f5efe8;\n  font-family: Arial, Helvetica, sans-serif;\n  color: #261b14;\n}\n\n/* MAIN CONTAINER */\n.invoice-sheet {\n  max-width: 650px;\n  margin: auto;\n  background: #ffffff;\n  border: 1px solid #dfd0c0;\n  box-shadow: 0 10px 30px rgba(36, 23, 15, 0.1);\n}\n\n/* HEADER */\n.invoice-hero {\n  background: #2b1a10;\n  color: #ffffff;\n  padding: 20px;\n}\n\n.logo-lockup {\n  display: flex;\n  align-items: center;\n  gap: 12px;\n}\n\n.logo-lockup img {\n  width: 60px;\n  height: 60px;\n  background: #f6e7d8;\n}\n\n.brand {\n  font-size: 16px;\n  font-weight: bold;\n  margin: 0;\n}\n\n/* TITLE */\n.invoice-title h1 {\n  margin: 0;\n  font-size: 28px;\n}\n\n.invoice-title p {\n  margin: 0;\n  font-size: 12px;\n}\n\n/* META SECTIONS */\n.invoice-meta {\n  padding: 20px;\n}\n\n.invoice-meta h2 {\n  font-size: 16px;\n  margin-bottom: 10px;\n}\n\n.invoice-meta p {\n  font-size: 12px;\n  margin: 4px 0;\n}\n\n/* BOXES */\n.date-box {\n  background: #f3e5d3;\n  padding: 12px;\n  margin-bottom: 15px;\n}\n\n/* TABLE */\n.print-items {\n  width: 100%;\n  border-collapse: collapse;\n  margin: 0 0 20px 0;\n}\n\n.print-items th {\n  background: #24170f;\n  color: #ffffff;\n  padding: 10px;\n  font-size: 12px;\n  text-align: left;\n}\n\n.print-items td {\n  border-bottom: 1px solid #dfd0c0;\n  padding: 10px;\n  font-size: 12px;\n}\n\n/* TOTALS */\n.print-totals {\n  background: #f3e5d3;\n  padding: 12px;\n  font-size: 12px;\n}\n\n.print-totals p {\n  display: flex;\n  justify-content: space-between;\n  margin: 6px 0;\n}\n\n/* ACCOUNT BOX */\n.account-box {\n  border-left: 4px solid #b8915d;\n  padding-left: 12px;\n  margin-bottom: 15px;\n}\n\n.account-box h3 {\n  font-size: 13px;\n  margin-bottom: 10px;\n}\n\n/* FOOTER */\nfooter {\n  text-align: center;\n  padding: 20px;\n  font-size: 11px;\n  border-top: 1px solid #dfd0c0;\n}\n\n.signature-block img {\n  max-width: 120px;\n}\n</style>\n</head>\n\n<body style=\"margin:0;padding:0;background:#f5efe8;font-family:Arial,sans-serif;\">\n\n<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#f5efe8;padding:20px;\">\n<tr>\n<td align=\"center\">\n\n<table width=\"650\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#fff;border:1px solid #dfd0c0;\">\n\n<!-- HEADER -->\n<tr>\n<td style=\"background:#2b1a10;color:#fff;padding:20px;\">\n\n<table width=\"100%\">\n<tr>\n<td style=\"vertical-align:middle;\">\n    <img src=\"https://beeimg.com/images/d43463561674.png\" style=\"width:60px;height:60px;background:#f6e7d8;\">\n</td>\n\n<td style=\"text-align:left;padding-left:10px;\">\n    <div style=\"font-size:16px;font-weight:bold;\">Nandai Events</div>\n    <div style=\"font-size:12px;\">We turn ideas into action</div>\n</td>\n\n<td style=\"text-align:right;\">\n    <div style=\"font-size:26px;font-weight:bold;\">INVOICE</div>\n    <div style=\"font-size:12px;\">INV-20260618-155802-645</div>\n</td>\n</tr>\n</table>\n\n</td>\n</tr>\n\n<!-- CLIENT INFO -->\n<tr>\n<td style=\"padding:20px;\">\n\n<table width=\"100%\">\n<tr>\n\n<td width=\"50%\" style=\"vertical-align:top;\">\n    <h3 style=\"margin:0 0 10px 0;\">Invoice From</h3>\n    <p style=\"margin:4px 0;\">Nandai Events</p>\n    <p style=\"margin:4px 0;\">+91 8446847989 / +91 7620956830</p>\n    <p style=\"margin:4px 0;\">nandaievents@gmail.com</p>\n</td>\n\n<td width=\"50%\" style=\"vertical-align:top;\">\n    <h3 style=\"margin:0 0 10px 0;\">Invoice To</h3>\n    <p style=\"margin:4px 0;\">Atharva Gujar</p>\n    <p style=\"margin:4px 0;\">+91 8805545888</p>\n    <p style=\"margin:4px 0;\">atharvagujar.789@gmail.com</p>\n    <p style=\"margin:4px 0;\">Pune</p>\n    <h4\">Event Name: Birthday</h4>\n</td>\n\n</tr>\n</table>\n\n</td>\n</tr>\n\n<!-- TABLE -->\n<tr>\n<td style=\"padding:0 20px 20px 20px;\">\n\n<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;\">\n\n<tr style=\"background:#24170f;color:#fff;\">\n    <th style=\"padding:10px;text-align:left;\">Description</th>\n    <th style=\"padding:10px;\">Qty</th>\n    <th style=\"padding:10px;text-align:right;\">Rate</th>\n    <th style=\"padding:10px;text-align:right;\">Amount</th>\n</tr>\n\n\n    <tr>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;\">\n            Ballon Decor\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:center;\">\n            2.00\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;\">\n            Rs 100,000.00\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;\">\n            Rs 200,000.00\n        </td>\n    </tr>\n\n</table>\n\n</td>\n</tr>\n\n<!-- TOTALS -->\n<tr>\n<td style=\"padding:0 20px 20px 20px;\">\n\n<table width=\"100%\" style=\"background:#f3e5d3;padding:10px;\">\n<tr><td>Sub Total</td><td align=\"right\">Rs 200,000.00</td></tr>\n<tr><td>Advance</td><td align=\"right\">Rs 0.00</td></tr>\n<tr><td>Total</td><td align=\"right\">Rs 200,000.00</td></tr>\n<tr><td>Balance</td><td align=\"right\">Rs 200,000.00</td></tr>\n</table>\n\n</td>\n</tr>\n\n<!-- FOOTER -->\n<tr>\n<td style=\"padding:20px;border-top:1px solid #dfd0c0;text-align:center;\">\n\n<p style=\"margin-top:10px;font-size:12px;\">\nThank you for your business.\n</p>\n\n</td>\n</tr>\n\n</table>\n\n</td>\n</tr>\n</table>\n\n</body>\n</html>\n', 'sent', NULL, '2026-06-18 13:59:54'),
(4, 5, 'nandaievents@gmail.com', 'atharvagujar.789@gmail.com', 'Your invoice INV-20260618-155802-645 from Nandai Events', '\n<!DOCTYPE html>\n<html>\n<head>\n<meta charset=\"UTF-8\">\n<style>\nbody {\n  margin: 0;\n  padding: 0;\n  background: #f5efe8;\n  font-family: Arial, Helvetica, sans-serif;\n  color: #261b14;\n}\n\n/* MAIN CONTAINER */\n.invoice-sheet {\n  max-width: 650px;\n  margin: auto;\n  background: #ffffff;\n  border: 1px solid #dfd0c0;\n  box-shadow: 0 10px 30px rgba(36, 23, 15, 0.1);\n}\n\n/* HEADER */\n.invoice-hero {\n  background: #2b1a10;\n  color: #ffffff;\n  padding: 20px;\n}\n\n.logo-lockup {\n  display: flex;\n  align-items: center;\n  gap: 12px;\n}\n\n.logo-lockup img {\n  width: 60px;\n  height: 60px;\n  background: #f6e7d8;\n}\n\n.brand {\n  font-size: 16px;\n  font-weight: bold;\n  margin: 0;\n}\n\n/* TITLE */\n.invoice-title h1 {\n  margin: 0;\n  font-size: 28px;\n}\n\n.invoice-title p {\n  margin: 0;\n  font-size: 12px;\n}\n\n/* META SECTIONS */\n.invoice-meta {\n  padding: 20px;\n}\n\n.invoice-meta h2 {\n  font-size: 16px;\n  margin-bottom: 10px;\n}\n\n.invoice-meta p {\n  font-size: 12px;\n  margin: 4px 0;\n}\n\n/* BOXES */\n.date-box {\n  background: #f3e5d3;\n  padding: 12px;\n  margin-bottom: 15px;\n}\n\n/* TABLE */\n.print-items {\n  width: 100%;\n  border-collapse: collapse;\n  margin: 0 0 20px 0;\n}\n\n.print-items th {\n  background: #24170f;\n  color: #ffffff;\n  padding: 10px;\n  font-size: 12px;\n  text-align: left;\n}\n\n.print-items td {\n  border-bottom: 1px solid #dfd0c0;\n  padding: 10px;\n  font-size: 12px;\n}\n\n/* TOTALS */\n.print-totals {\n  background: #f3e5d3;\n  padding: 12px;\n  font-size: 12px;\n}\n\n.print-totals p {\n  display: flex;\n  justify-content: space-between;\n  margin: 6px 0;\n}\n\n/* ACCOUNT BOX */\n.account-box {\n  border-left: 4px solid #b8915d;\n  padding-left: 12px;\n  margin-bottom: 15px;\n}\n\n.account-box h3 {\n  font-size: 13px;\n  margin-bottom: 10px;\n}\n\n/* FOOTER */\nfooter {\n  text-align: center;\n  padding: 20px;\n  font-size: 11px;\n  border-top: 1px solid #dfd0c0;\n}\n\n.signature-block img {\n  max-width: 120px;\n}\n</style>\n</head>\n\n<body style=\"margin:0;padding:0;background:#f5efe8;font-family:Arial,sans-serif;\">\n\n<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#f5efe8;padding:20px;\">\n<tr>\n<td align=\"center\">\n\n<table width=\"650\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#fff;border:1px solid #dfd0c0;\">\n\n<!-- HEADER -->\n<tr>\n<td style=\"background:#2b1a10;color:#fff;padding:20px;\">\n\n<table width=\"100%\">\n<tr>\n<td style=\"vertical-align:middle;\">\n    <img src=\"https://beeimg.com/images/d43463561674.png\" style=\"width:60px;height:60px;background:#f6e7d8;\">\n</td>\n\n<td style=\"text-align:left;padding-left:10px;\">\n    <div style=\"font-size:16px;font-weight:bold;\">Nandai Events</div>\n    <div style=\"font-size:12px;\">We turn ideas into action</div>\n</td>\n\n<td style=\"text-align:right;\">\n    <div style=\"font-size:26px;font-weight:bold;\">INVOICE</div>\n    <div style=\"font-size:12px;\">INV-20260618-155802-645</div>\n</td>\n</tr>\n</table>\n\n</td>\n</tr>\n\n<!-- CLIENT INFO -->\n<tr>\n<td style=\"padding:20px;\">\n\n<table width=\"100%\">\n<tr>\n\n<td width=\"50%\" style=\"vertical-align:top;\">\n    <h3 style=\"margin:0 0 10px 0;\">Invoice From</h3>\n    <p style=\"margin:4px 0;\">Nandai Events</p>\n    <p style=\"margin:4px 0;\">+91 8446847989 / +91 7620956830</p>\n    <p style=\"margin:4px 0;\">nandaievents@gmail.com</p>\n</td>\n\n<td width=\"50%\" style=\"vertical-align:top;\">\n    <h3 style=\"margin:0 0 10px 0;\">Invoice To</h3>\n    <p style=\"margin:4px 0;\">Atharva Gujar</p>\n    <p style=\"margin:4px 0;\">+91 8805545888</p>\n    <p style=\"margin:4px 0;\">atharvagujar.789@gmail.com</p>\n    <p style=\"margin:4px 0;\">Pune</p>\n    <h4\">Event Name: Birthday</h4>\n</td>\n\n</tr>\n</table>\n\n</td>\n</tr>\n\n<!-- TABLE -->\n<tr>\n<td style=\"padding:0 20px 20px 20px;\">\n\n<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;\">\n\n<tr style=\"background:#24170f;color:#fff;\">\n    <th style=\"padding:10px;text-align:left;\">Description</th>\n    <th style=\"padding:10px;\">Qty</th>\n    <th style=\"padding:10px;text-align:right;\">Rate</th>\n    <th style=\"padding:10px;text-align:right;\">Amount</th>\n</tr>\n\n\n    <tr>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;\">\n            Ballon Decor\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:center;\">\n            2.00\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;\">\n            Rs 100,000.00\n        </td>\n        <td style=\"padding:10px;border-bottom:1px solid #dfd0c0;text-align:right;\">\n            Rs 200,000.00\n        </td>\n    </tr>\n\n</table>\n\n</td>\n</tr>\n\n<!-- TOTALS -->\n<tr>\n<td style=\"padding:0 20px 20px 20px;\">\n\n<table width=\"100%\" style=\"background:#f3e5d3;padding:10px;\">\n<tr><td>Sub Total</td><td align=\"right\">Rs 200,000.00</td></tr>\n<tr><td>Advance</td><td align=\"right\">Rs 0.00</td></tr>\n<tr><td>Total</td><td align=\"right\">Rs 200,000.00</td></tr>\n<tr><td>Balance</td><td align=\"right\">Rs 200,000.00</td></tr>\n</table>\n\n</td>\n</tr>\n\n<!-- FOOTER -->\n<tr>\n<td style=\"padding:20px;border-top:1px solid #dfd0c0;text-align:center;\">\n\n<p style=\"margin-top:10px;font-size:12px;\">\nThank you for your business.\n</p>\n\n</td>\n</tr>\n\n</table>\n\n</td>\n</tr>\n</table>\n\n</body>\n</html>\n', 'sent', NULL, '2026-06-18 14:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `quantity`, `unit_price`, `line_total`, `sort_order`) VALUES
(36, 4, 'Balloons', 1000.00, 4.00, 4000.00, 0),
(37, 4, 'Balloons', 250.00, 4.00, 1000.00, 1),
(48, 5, 'Ballon Decor', 2.00, 100000.00, 200000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_whatsapp_logs`
--

CREATE TABLE `invoice_whatsapp_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED NOT NULL,
  `client_phone` varchar(40) NOT NULL,
  `media_id` varchar(120) DEFAULT NULL,
  `message_id` varchar(160) DEFAULT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'failed',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_whatsapp_logs`
--

INSERT INTO `invoice_whatsapp_logs` (`id`, `invoice_id`, `client_phone`, `media_id`, `message_id`, `status`, `error_message`, `sent_at`) VALUES
(2, 4, '919325798058', NULL, NULL, 'failed', 'WhatsApp API HTTP 401: Authentication Error', '2026-06-18 15:34:32'),
(4, 5, '918805545888', '718870751320380', 'wamid.HBgMOTE4ODA1NTQ1ODg4FQIAERgSRTU2RkQ2NjY0MzQ2NTdGRkEyAA==', 'sent', NULL, '2026-06-18 16:20:06'),
(5, 5, '918805545888', '1526146702324671', 'wamid.HBgMOTE4ODA1NTQ1ODg4FQIAERgSRUI0MDc2ODE0MjRCNzZGMEIwAA==', 'sent', NULL, '2026-06-18 16:52:40'),
(6, 4, '919325798058', NULL, NULL, 'failed', 'WhatsApp API HTTP 401: Authentication Error', '2026-06-18 17:04:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `display_name` varchar(120) NOT NULL,
  `role` enum('superadmin','staff') NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `display_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$sVCRigY.MSlgCfQKNNvUUOcMCWRx3aukziSLvjHBvgbwYAt2u0w1y', 'Administrator', 'superadmin', 1, '2026-06-18 04:10:01'),
(2, 'atharvagujar.789@gmail.com', '$2y$10$WpnnRZgWXCWH9hupOqZ2XeUX3qLbbBfPxAZn/tkDS.TPR./oySpN6', 'Atharva Gujar', 'superadmin', 1, '2026-06-18 16:17:17'),
(4, 'kmore5811@gmail.com', '$2y$10$IpFxcODaz9ei5Hct3yTfC..nFs5.wpWqouAaddkNDySTgjApt9Un.', 'Kunal More', 'staff', 1, '2026-06-19 06:56:22'),
(5, 'example@gmail.com', '$2y$10$0OWkwIG73ELiOGTffnzM5.VV1a2rVvLkWQWQm/PwEg/sc7aVEoNmS', 'Demo User', 'staff', 1, '2026-06-19 07:14:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gallery_images_category` (`category_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`);

--
-- Indexes for table `invoice_email_logs`
--
ALTER TABLE `invoice_email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_invoice_email_logs_invoice` (`invoice_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_invoice_items_invoice` (`invoice_id`);

--
-- Indexes for table `invoice_whatsapp_logs`
--
ALTER TABLE `invoice_whatsapp_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_invoice_whatsapp_logs_invoice` (`invoice_id`);

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
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoice_email_logs`
--
ALTER TABLE `invoice_email_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `invoice_whatsapp_logs`
--
ALTER TABLE `invoice_whatsapp_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `fk_gallery_images_category` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_email_logs`
--
ALTER TABLE `invoice_email_logs`
  ADD CONSTRAINT `fk_invoice_email_logs_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_whatsapp_logs`
--
ALTER TABLE `invoice_whatsapp_logs`
  ADD CONSTRAINT `fk_invoice_whatsapp_logs_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
