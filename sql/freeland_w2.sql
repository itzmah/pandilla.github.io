-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: d52452.mysql.zone.ee
-- Generation Time: Jan 14, 2016 at 09:16 PM
-- Server version: 10.0.22-MariaDB-log
-- PHP Version: 5.5.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `d52452sd100721`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_transfers`
--

CREATE TABLE `bank_transfers` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `reciver` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `credit_history`
--

CREATE TABLE `credit_history` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `flc` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `crime_list`
--

CREATE TABLE `crime_list` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `money` int(11) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fight_requests`
--

CREATE TABLE `fight_requests` (
  `id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `o_user_id` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang`
--

CREATE TABLE `gang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `logo_url` text NOT NULL,
  `leader` int(11) NOT NULL,
  `building_level` int(11) NOT NULL DEFAULT '1',
  `money` bigint(20) NOT NULL,
  `score` bigint(20) NOT NULL,
  `points` int(11) NOT NULL,
  `defence` bigint(20) NOT NULL,
  `offence` bigint(20) NOT NULL,
  `wep_1` int(11) NOT NULL,
  `wep_2` int(11) NOT NULL,
  `wep_3` int(11) NOT NULL,
  `wep_4` int(11) NOT NULL,
  `wep_5` int(11) NOT NULL,
  `wep_6` int(11) NOT NULL,
  `access` text NOT NULL,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang_buildings`
--

CREATE TABLE `gang_buildings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `max_members` int(11) NOT NULL,
  `money` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang_forum_post`
--

CREATE TABLE `gang_forum_post` (
  `id` int(11) NOT NULL,
  `topic_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first` int(1) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang_forum_topic`
--

CREATE TABLE `gang_forum_topic` (
  `id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang_logs`
--

CREATE TABLE `gang_logs` (
  `id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gang_members`
--

CREATE TABLE `gang_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `money` bigint(20) NOT NULL,
  `points` int(11) NOT NULL,
  `joined` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `house_interior`
--

CREATE TABLE `house_interior` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `money` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `education` int(11) NOT NULL,
  `limit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `house_interior`
--

INSERT INTO `house_interior` (`id`, `name`, `money`, `score`, `education`, `limit`) VALUES
(1, 'Vaip', 500, 150, 20, 200),
(2, 'Tool', 3500, 450, 750, 180),
(3, 'Laud', 9900, 1100, 2500, 160),
(4, 'Laualamp', 19000, 1900, 7500, 140),
(5, 'Seinalamp', 36000, 2500, 16000, 120),
(6, 'Lauatelfeon', 62000, 4900, 27000, 100),
(7, 'Nutitelefon', 95000, 7500, 36000, 80),
(8, 'LED Teler', 150000, 11000, 60000, 60),
(9, 'Kodukino', 250000, 15000, 95000, 40),
(10, 'Lauaarvuti', 375000, 22000, 125000, 20);

-- --------------------------------------------------------

--
-- Table structure for table `house_levels`
--

CREATE TABLE `house_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `money` bigint(20) NOT NULL,
  `land` int(11) NOT NULL,
  `weed_limit` int(11) NOT NULL,
  `greenhouse_land` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `house_levels`
--

INSERT INTO `house_levels` (`id`, `name`, `money`, `land`, `weed_limit`, `greenhouse_land`) VALUES
(1, 'Kuur level 1', 25000, 150, 200, 50),
(2, 'Kuur level 2', 200000, 200, 300, 100),
(3, 'Kuur level 3', 675000, 250, 450, 200),
(4, 'Kuur level 4', 1600000, 300, 800, 300),
(5, 'Kuur level 5', 3125000, 350, 1250, 400),
(6, 'Kuur level 6', 5400000, 400, 1800, 600),
(7, 'Kuur level 7', 8575000, 450, 2450, 800),
(8, 'Kuur level 8', 12800000, 500, 3200, 1000),
(9, 'Kuur level 9', 18225000, 550, 4050, 1350),
(10, 'Kuur level 10', 25000000, 600, 5000, 1700),
(11, 'Korter level 1', 33275000, 650, 6050, 2000),
(12, 'Korter level 2', 43200000, 700, 7200, 2400),
(13, 'Korter level 3', 54925000, 750, 8450, 2800),
(14, 'Korter level 4', 68600000, 800, 9800, 3200),
(15, 'Korter level 5', 84375000, 850, 11250, 3600),
(16, 'Korter level 6', 102400000, 900, 12800, 4000),
(17, 'Korter level 7', 122825000, 950, 14450, 4500),
(18, 'Korter level 8', 145800000, 1000, 16200, 5000),
(19, 'Korter level 9', 171475000, 1050, 18050, 5500),
(20, 'Korter level 10', 200000000, 1100, 20000, 6000),
(21, 'Talumaja level 1', 231525000, 1150, 22050, 6600),
(22, 'Talumaja level 2', 266200000, 1200, 24200, 7200),
(23, 'Talumaja level 3', 304175000, 1250, 26450, 7800),
(24, 'Talumaja level 4', 345600000, 1300, 28800, 8500),
(25, 'Talumaja level 5', 390625000, 1350, 31250, 9200),
(26, 'Talumaja level 6', 439400000, 1400, 33800, 10000),
(27, 'Talumaja level 7', 492075000, 1450, 36450, 10900),
(28, 'Talumaja level 8', 548800000, 1500, 39200, 12000),
(29, 'Talumaja level 9', 609725000, 1550, 42050, 13000),
(30, 'Talumaja level 10', 675000000, 1600, 45000, 14250),
(31, 'Villa level 1', 744775000, 1650, 48050, 15500),
(32, 'Villa level 2', 819200000, 1700, 51200, 17000),
(33, 'Villa level 3', 898425000, 1750, 54450, 18500),
(34, 'Villa level 4', 982600000, 1800, 57800, 20000),
(35, 'Villa level 5', 1071875000, 1850, 61250, 21500),
(36, 'Villa level 6', 1166400000, 1900, 64800, 23250),
(37, 'Villa level 7', 1266325000, 1950, 68450, 25000),
(38, 'Villa level 8', 1371800000, 2000, 72200, 26800),
(39, 'Villa level 9', 1482975000, 2050, 76050, 29000),
(40, 'Villa level 10', 1600000000, 2100, 80000, 32000);

-- --------------------------------------------------------

--
-- Table structure for table `lottery_bets`
--

CREATE TABLE `lottery_bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lottery_number` bigint(20) NOT NULL,
  `bet` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lottery_winners`
--

CREATE TABLE `lottery_winners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `money` bigint(20) NOT NULL,
  `time` int(11) NOT NULL,
  `active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `id` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `new` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_recovery`
--

CREATE TABLE `password_recovery` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activation_code` varchar(255) NOT NULL,
  `newpassword` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `session` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `birth` date NOT NULL,
  `gender` int(11) NOT NULL,
  `last_active` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `joined` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referer` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '2',
  `activation_code` varchar(255) NOT NULL,
  `groups` int(11) NOT NULL,
  `ban_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE `users_data` (
  `id` int(11) NOT NULL,
  `flc` int(11) NOT NULL,
  `toetaja` int(1) NOT NULL,
  `toetaja_time` datetime NOT NULL,
  `bank_level` int(11) NOT NULL DEFAULT '1',
  `money` bigint(20) NOT NULL DEFAULT '5000',
  `money_bank` bigint(20) NOT NULL DEFAULT '30000',
  `vault` int(11) NOT NULL,
  `vault_money` bigint(20) NOT NULL,
  `loan` bigint(20) NOT NULL,
  `stocks` bigint(20) NOT NULL,
  `score` bigint(20) NOT NULL DEFAULT '50',
  `turns` int(11) NOT NULL DEFAULT '700',
  `education` int(11) NOT NULL DEFAULT '15',
  `food` bigint(20) NOT NULL DEFAULT '200',
  `speed` int(11) NOT NULL DEFAULT '15',
  `strength` int(11) NOT NULL DEFAULT '15',
  `stamina` int(11) NOT NULL DEFAULT '15',
  `fight_fond` bigint(20) NOT NULL,
  `job` int(11) NOT NULL DEFAULT '1',
  `crime_level` int(11) NOT NULL DEFAULT '1',
  `crime_xp` int(11) NOT NULL,
  `crime_last` datetime NOT NULL,
  `lottery_last` int(1) NOT NULL,
  `gym` int(1) NOT NULL,
  `gym_time` int(11) NOT NULL,
  `gym_points` int(11) NOT NULL,
  `gang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_data_house`
--

CREATE TABLE `users_data_house` (
  `id` int(11) NOT NULL,
  `defence_level` int(11) NOT NULL DEFAULT '1',
  `defence_man` int(11) NOT NULL,
  `offence_level` int(11) NOT NULL DEFAULT '1',
  `offence_man` int(11) NOT NULL,
  `wep_1` int(11) NOT NULL,
  `wep_2` int(11) NOT NULL,
  `wep_3` int(11) NOT NULL,
  `wep_4` int(11) NOT NULL,
  `wep_5` int(11) NOT NULL,
  `wep_6` int(11) NOT NULL,
  `weed` bigint(20) NOT NULL,
  `weed_seed` bigint(20) NOT NULL,
  `seed` bigint(20) NOT NULL,
  `foods` bigint(20) NOT NULL,
  `house_level` int(11) NOT NULL DEFAULT '1',
  `land` int(11) NOT NULL,
  `items` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_data_resto`
--

CREATE TABLE `users_data_resto` (
  `id` int(11) NOT NULL,
  `created` int(1) NOT NULL,
  `created_time` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `reputation` bigint(20) NOT NULL,
  `money` bigint(20) NOT NULL,
  `area` int(11) NOT NULL,
  `area_total` int(11) NOT NULL DEFAULT '50',
  `foods` bigint(20) NOT NULL,
  `work_hours` int(11) DEFAULT '8',
  `work_hours_time` datetime NOT NULL,
  `waiter` int(11) NOT NULL,
  `waiter_morale` int(11) NOT NULL,
  `waiter_level` int(11) NOT NULL DEFAULT '1',
  `waiter_salary` int(11) NOT NULL,
  `waiter_salary_change` datetime NOT NULL,
  `chef` int(11) NOT NULL,
  `chef_morale` int(11) NOT NULL,
  `chef_level` int(11) NOT NULL DEFAULT '1',
  `chef_salary` int(11) NOT NULL,
  `chef_salary_change` datetime NOT NULL,
  `kitchen_level` int(11) NOT NULL DEFAULT '1',
  `furniture_level` int(11) NOT NULL DEFAULT '1',
  `income_today` bigint(20) NOT NULL,
  `outcome_today` bigint(20) NOT NULL,
  `food_make_today` int(11) NOT NULL,
  `food_make_limit` int(11) NOT NULL,
  `food_sell_today` int(11) NOT NULL,
  `food_sell_limit` int(11) NOT NULL,
  `food_1` int(11) NOT NULL,
  `food_1_price` int(11) NOT NULL DEFAULT '4000',
  `food_1_price_edit` datetime NOT NULL,
  `food_1_orders` int(11) NOT NULL,
  `food_1_start` datetime NOT NULL,
  `food_1_end` datetime NOT NULL,
  `food_1_amount` int(11) NOT NULL,
  `food_2` int(11) NOT NULL,
  `food_2_price` int(11) NOT NULL DEFAULT '5600',
  `food_2_price_edit` datetime NOT NULL,
  `food_2_orders` int(11) NOT NULL,
  `food_2_start` datetime NOT NULL,
  `food_2_end` datetime NOT NULL,
  `food_2_amount` int(11) NOT NULL,
  `food_3` int(11) NOT NULL,
  `food_3_price` int(11) NOT NULL DEFAULT '4400',
  `food_3_price_edit` datetime NOT NULL,
  `food_3_orders` int(11) NOT NULL,
  `food_3_start` datetime NOT NULL,
  `food_3_end` datetime NOT NULL,
  `food_3_amount` int(11) NOT NULL,
  `food_4` int(11) NOT NULL,
  `food_4_price` int(11) NOT NULL DEFAULT '9200',
  `food_4_price_edit` datetime NOT NULL,
  `food_4_orders` int(11) NOT NULL,
  `food_4_start` datetime NOT NULL,
  `food_4_end` datetime NOT NULL,
  `food_4_amount` int(11) NOT NULL,
  `food_5` int(11) NOT NULL,
  `food_5_price` int(11) NOT NULL DEFAULT '7200',
  `food_5_price_edit` datetime NOT NULL,
  `food_5_orders` int(11) NOT NULL,
  `food_5_start` datetime NOT NULL,
  `food_5_end` datetime NOT NULL,
  `food_5_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(11) NOT NULL,
  `body` text NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_transfers`
--
ALTER TABLE `bank_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_history`
--
ALTER TABLE `credit_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crime_list`
--
ALTER TABLE `crime_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fight_requests`
--
ALTER TABLE `fight_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang`
--
ALTER TABLE `gang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang_buildings`
--
ALTER TABLE `gang_buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang_forum_post`
--
ALTER TABLE `gang_forum_post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang_forum_topic`
--
ALTER TABLE `gang_forum_topic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang_logs`
--
ALTER TABLE `gang_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gang_members`
--
ALTER TABLE `gang_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `house_interior`
--
ALTER TABLE `house_interior`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `house_levels`
--
ALTER TABLE `house_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lottery_bets`
--
ALTER TABLE `lottery_bets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lottery_winners`
--
ALTER TABLE `lottery_winners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_recovery`
--
ALTER TABLE `password_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_data_house`
--
ALTER TABLE `users_data_house`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_data_resto`
--
ALTER TABLE `users_data_resto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_transfers`
--
ALTER TABLE `bank_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1401;
--
-- AUTO_INCREMENT for table `credit_history`
--
ALTER TABLE `credit_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `crime_list`
--
ALTER TABLE `crime_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=958390;
--
-- AUTO_INCREMENT for table `fight_requests`
--
ALTER TABLE `fight_requests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `gang`
--
ALTER TABLE `gang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `gang_buildings`
--
ALTER TABLE `gang_buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `gang_forum_post`
--
ALTER TABLE `gang_forum_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `gang_forum_topic`
--
ALTER TABLE `gang_forum_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `gang_logs`
--
ALTER TABLE `gang_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13376;
--
-- AUTO_INCREMENT for table `gang_members`
--
ALTER TABLE `gang_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT for table `house_interior`
--
ALTER TABLE `house_interior`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `house_levels`
--
ALTER TABLE `house_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `lottery_bets`
--
ALTER TABLE `lottery_bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=896;
--
-- AUTO_INCREMENT for table `lottery_winners`
--
ALTER TABLE `lottery_winners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;
--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=487;
--
-- AUTO_INCREMENT for table `password_recovery`
--
ALTER TABLE `password_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `users_data`
--
ALTER TABLE `users_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `users_data_house`
--
ALTER TABLE `users_data_house`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `users_data_resto`
--
ALTER TABLE `users_data_resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22292;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
