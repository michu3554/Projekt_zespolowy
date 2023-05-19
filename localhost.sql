-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 17, 2023 at 03:51 PM
-- Server version: 8.0.33
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Baza_danych_projekt`
--
CREATE DATABASE IF NOT EXISTS `Baza_danych_projekt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `Baza_danych_projekt`;

-- --------------------------------------------------------

--
-- Table structure for table `Dane_transfer_phpdirect`
--

CREATE TABLE `Dane_transfer_phpdirect` (
  `id` int NOT NULL,
  `number1` int NOT NULL,
  `number2` float(6,2) NOT NULL,
  `text` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `timestamp` decimal(16,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Dane_transfer_rabbitmq`
--

CREATE TABLE `Dane_transfer_rabbitmq` (
  `id` int NOT NULL,
  `number1` int NOT NULL,
  `number2` float(6,2) NOT NULL,
  `text` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `timestamp` decimal(16,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Dane_wygenerowane`
--

CREATE TABLE `Dane_wygenerowane` (
  `id` int NOT NULL,
  `number1` int NOT NULL,
  `number2` float(6,2) NOT NULL,
  `text` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Dane_transfer_phpdirect`
--
ALTER TABLE `Dane_transfer_phpdirect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Dane_transfer_rabbitmq`
--
ALTER TABLE `Dane_transfer_rabbitmq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Dane_wygenerowane`
--
ALTER TABLE `Dane_wygenerowane`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
