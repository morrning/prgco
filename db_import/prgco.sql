-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2019 at 10:09 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prgco`
--

-- --------------------------------------------------------

--
-- Table structure for table `sys_group`
--

CREATE TABLE `sys_group` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bundle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `des` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_group`
--

INSERT INTO `sys_group` (`id`, `group_name`, `label`, `bundle`, `des`, `pid`, `options`) VALUES
(1, 'superAdmin', 'داشبورد سرپرست سامانه', 'CORE', NULL, NULL, NULL),
(2, 'newsPublish', 'مدیریت نشر خبر و اطلاعیه', 'CORE', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sys_position`
--

CREATE TABLE `sys_position` (
  `id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upper_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `public_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `groups` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_position`
--

INSERT INTO `sys_position` (`id`, `label`, `upper_id`, `user_id`, `public_label`, `is_default`, `groups`) VALUES
(1, 'سرپرست کل سامانه', NULL, 1, 'سرپرست کل سامانه', 1, '1,2'),
(2, 'مدیر ICT پروژه کربلا', NULL, 1, 'بابک علی زاده - مدیر ICT پروژه کربلا', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sys_user`
--

CREATE TABLE `sys_user` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_num` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_user`
--

INSERT INTO `sys_user` (`id`, `fullname`, `username`, `password`, `unique_id`, `mobile_num`) VALUES
(1, 'سرپرست سامانه', 'admin', '90deff4b32c134f32e3f0d7e8a2aad92', '6dejto570d616dh2cmnd0hdosp', '+989183282405');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sys_group`
--
ALTER TABLE `sys_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_position`
--
ALTER TABLE `sys_position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_user`
--
ALTER TABLE `sys_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sys_group`
--
ALTER TABLE `sys_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sys_position`
--
ALTER TABLE `sys_position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sys_user`
--
ALTER TABLE `sys_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
