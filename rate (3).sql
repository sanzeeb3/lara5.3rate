-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2016 at 04:50 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rate`
--

-- --------------------------------------------------------

--
-- Table structure for table `bands`
--

CREATE TABLE `bands` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `views` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bands`
--

INSERT INTO `bands` (`id`, `name`, `views`) VALUES
(1, 'Guns N'' Roses', 1),
(3, 'Pink Floyd', 6),
(4, 'The Beatles', 7),
(6, 'No band selected', 7);

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `name` text,
  `band_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `link` text,
  `mp3` text,
  `added_by` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `name`, `band_id`, `views`, `link`, `mp3`, `added_by`) VALUES
(1, 'Shine on you crazy diamond', 3, 12, 'https://www.youtube.com/watch?v=R0sw2CgysWY', '1478515670.mp3', NULL),
(2, 'Estranged', 1, 28, 'https://www.youtube.com/watch?v=dpmAY059TTY', '1478515670.mp3', NULL),
(10, 'Fearless', 3, 5, 'https://www.youtube.com/watch?v=TeyHPAdxuy0', '1478515670.mp3', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `password` text,
  `verified` varchar(30) DEFAULT NULL,
  `remember_token` text,
  `avatar` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bands`
--
ALTER TABLE `bands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bands`
--
ALTER TABLE `bands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
