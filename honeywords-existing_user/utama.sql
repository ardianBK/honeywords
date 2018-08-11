-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2018 at 01:38 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci_ta`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_user`
--

CREATE TABLE `data_user` (
  `no` int(233) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `IP_terakhir` varchar(45) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `aktif` tinyint(1) NOT NULL,
  `token_reg` varchar(255) NOT NULL,
  `sudah_aktivasi` varchar(5) NOT NULL,
  `token_forgot` varchar(255) NOT NULL,
  `date_forgot` date NOT NULL,
  `token_crack` varchar(255) NOT NULL,
  `date_cracking` date NOT NULL,
  `konfirmasi` varchar(255) NOT NULL,
  `tgl_konfirmasi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token_brute` varchar(255) NOT NULL,
  `cookies` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hitung_login`
--

CREATE TABLE `hitung_login` (
  `id` int(255) NOT NULL,
  `IP` varchar(22) NOT NULL,
  `login` varchar(255) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabel1`
--

CREATE TABLE `tabel1` (
  `no1` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `honeyindeks` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabel2`
--

CREATE TABLE `tabel2` (
  `indeks_pass` int(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_user`
--
ALTER TABLE `data_user`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `hitung_login`
--
ALTER TABLE `hitung_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tabel1`
--
ALTER TABLE `tabel1`
  ADD PRIMARY KEY (`no1`);

--
-- Indexes for table `tabel2`
--
ALTER TABLE `tabel2`
  ADD PRIMARY KEY (`indeks_pass`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_user`
--
ALTER TABLE `data_user`
  MODIFY `no` int(233) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `hitung_login`
--
ALTER TABLE `hitung_login`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tabel1`
--
ALTER TABLE `tabel1`
  MODIFY `no1` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
