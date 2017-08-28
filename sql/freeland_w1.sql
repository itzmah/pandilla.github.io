-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: d52452.mysql.zone.ee
-- Generation Time: Jan 14, 2016 at 09:15 PM
-- Server version: 10.0.22-MariaDB-log
-- PHP Version: 5.5.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `d52452sd100619`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_test`
--

CREATE TABLE `bank_test` (
  `id` int(11) NOT NULL,
  `sender` text NOT NULL,
  `body` text NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Table structure for table `contact_inbox`
--

CREATE TABLE `contact_inbox` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `contact_types`
--

CREATE TABLE `contact_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `priority_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contact_types`
--

INSERT INTO `contact_types` (`id`, `name`, `priority_level`) VALUES
(1, 'MÃ¤ngu viga', 1),
(2, 'Probleem tasuliste teenustega', 1),
(3, 'Soovin mÃ¤ngu kohta infot', 0),
(4, 'Kasutaja blokeeritud', 0),
(5, 'Moderaatori valimine', 0);

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
-- Table structure for table `forum_post`
--

CREATE TABLE `forum_post` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first` int(1) NOT NULL,
  `world` int(11) NOT NULL DEFAULT '1',
  `body` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forum_theard`
--

CREATE TABLE `forum_theard` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `forum_theard`
--

INSERT INTO `forum_theard` (`id`, `name`) VALUES
(1, 'Üldine'),
(2, 'Ideed / Ettepanekud'),
(3, 'Abi ja õpetused'),
(4, 'Vead'),
(5, 'Kaebused'),
(6, 'Vaba teema');

-- --------------------------------------------------------

--
-- Table structure for table `forum_topic`
--

CREATE TABLE `forum_topic` (
  `id` int(11) NOT NULL,
  `theard_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed` int(1) NOT NULL,
  `deleted` int(1) NOT NULL
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

--
-- Dumping data for table `gang_buildings`
--

INSERT INTO `gang_buildings` (`id`, `name`, `max_members`, `money`) VALUES
(1, 'Haagiselamu', 5, 500000),
(2, 'Väike maja metsas', 7, 9000000),
(3, 'Kuur', 9, 25000000),
(4, 'Laohoone', 11, 75000000),
(5, 'Korterelamu', 13, 125000000),
(6, 'Talumaja', 15, 200000000);

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
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `permissions` text NOT NULL,
  `color` varchar(25) NOT NULL DEFAULT '#000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `permissions`, `color`) VALUES
(1, 'Tavakasutaja', '{"cpanel":0, "cp_general":0, "cp_group":0, "cp_rules":0, "cp_help":0, "cp_news":0, "cp_gameplay":0, "cp_deletechat":0, "cp_user_management":0, "cp_ads":0, "cp_fortumo_services":0, "cp_contact_settings":0, "contact_inbox":0, "forum_edit":0, "forum_delete":0, "forum_close":0}', '#000000'),
(2, 'MÃ¤ngu omanik', '{"cpanel":1, "cp_general":1, "cp_group":1, "cp_rules":1, "cp_help":1, "cp_news":1, "cp_gameplay":1, "cp_deletechat":1, "cp_user_management":1, "cp_ads":1, "cp_fortumo_services":1, "cp_contact_settings":1, "contact_inbox":1, "forum_edit":1, "forum_delete":1, "forum_close":1}', 'red'),
(3, 'Blokeeritud', '{"cpanel":0, "cp_general":0, "cp_group":0, "cp_rules":0, "cp_help":0, "cp_news":0, "cp_gameplay":0, "cp_deletechat":0, "cp_user_management":0, "cp_ads":0, "cp_fortumo_services":0, "cp_contact_settings":0, "contact_inbox":0, "forum_edit":0, "forum_delete":0, "forum_close":0}', 'grey'),
(4, 'Noorem moderaator', '{"cpanel":1, "cp_general":0, "cp_group":0, "cp_rules":0, "cp_help":0, "cp_news":0, "cp_gameplay":0, "cp_deletechat":1, "cp_user_management":0, "cp_ads":0, "cp_fortumo_services":0, "cp_contact_settings":0, "contact_inbox":0, "forum_edit":0, "forum_delete":0, "forum_close":1}', '#008181'),
(5, 'Vanem moderaaotor', '{"cpanel":1, "cp_general":0, "cp_group":0, "cp_rules":0, "cp_help":1, "cp_news":0, "cp_gameplay":0, "cp_deletechat":1, "cp_user_management":0, "cp_ads":0, "cp_fortumo_services":0, "cp_contact_settings":0, "contact_inbox":0, "forum_edit":1, "forum_delete":1, "forum_close":1}', 'blue');

-- --------------------------------------------------------

--
-- Table structure for table `help`
--

CREATE TABLE `help` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `help`
--

INSERT INTO `help` (`id`, `name`, `body`) VALUES
(1, 'Kuidas teenida raha?', 'Raha saamine on tegelikult vÃ¤ga lihtne.\r\nAlguses on soovitatav teenida raha kanepiga. Ostate turult kanepi seemneid, kasvatate neid elamus ning siis mÃ¼Ã¼te need turul kallimalt maha.\r\nIga 15 minuti tagant saate raha veel tÃ¶Ã¶kohast. Mida parem on tÃ¶Ã¶koht seda rohkem palka saate. Raha teenimiseks on veel muid vÃµimalusi nagu kuritegevus, kasiino, rÃ¶Ã¶vimine jne. Ãœks suurimaid raha teenimis vÃµimalusi on ka restoran, mis sai hiljuti mÃ¤ngu lisatud.'),
(2, 'Kuidas saada skoori?', 'Skoori saab enamuste asjadega aga kÃµige kiiremini saab relvadega aga nende jaoks on teil vaja ka kaitsjaid ja rÃ¼ndajaid ning muidugi ka kaitse ja rÃ¼nde levelit.\r\nSuures osas annavad veel skoori maja esemed.'),
(3, 'Mis kasu ma saan kambast?', 'Kui te liitute vÃµi loote oma kamba siis te saate kambast kaitset ja rÃ¼nnet aga kaitse ja rÃ¼nde saamise protsent sÃµltub teie kaitse vÃµi rÃ¼nde levelist. Peale kaitse ja rÃ¼nde saate veel kambast meeldivat seltskonda ja palju muud.'),
(4, 'Miks ma ei saa kasutajat rÃ¼nnata?', 'Meie praegune rÃ¼ndamis sÃ¼steem nÃ¤eb vÃ¤lja selline, et kui Ã¼hte kasutajat on rÃ¼nnatud 15 minuti jooksul 5 korda siis teda ei saa rohkem 15 minuti jooksul rÃ¶Ã¶vida.'),
(5, 'Kuidas ma saan oma kasutaja kustutada?', 'Kasutaja saab kustutada konto seadete alt. Peale kustutamist lÃ¤heb kasutaja blokeeritud staatusesse.'),
(6, 'Kasutaja pettis mind.', 'MÃ¤ngu omanik ega ka keegi muu ei vastuta mÃ¤ngijate omavaheliste kokkulepete eest. Alati on vÃµimalik kaebuseid esitada foorumisse.');

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
-- Table structure for table `job_list`
--

CREATE TABLE `job_list` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `salary` int(11) NOT NULL,
  `education` int(11) NOT NULL,
  `bank_limit` bigint(20) NOT NULL,
  `loan_percent` int(11) NOT NULL,
  `stock_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `job_list`
--

INSERT INTO `job_list` (`id`, `name`, `salary`, `education`, `bank_limit`, `loan_percent`, `stock_num`) VALUES
(1, 'Kerjus', 250, 15, 40000, 0, 50000),
(2, 'Uksehoidja', 600, 900, 90000, 0, 50000),
(3, 'Aknapesija', 1100, 2500, 270000, 0, 49000),
(4, 'Nõudepesija', 1500, 4200, 450000, 0, 48000),
(5, 'Teenija', 2200, 6700, 800000, 6, 47000),
(6, 'Koristaja', 3100, 9750, 1450000, 8, 46000),
(7, 'Keevitaja', 4000, 13000, 1900000, 10, 45000),
(8, 'Kokk', 4900, 16500, 3000000, 12, 44000),
(9, 'Peakokk', 6350, 21500, 8000000, 14, 43000),
(10, 'Tuletõrjuja', 8500, 25000, 19000000, 16, 42000),
(11, 'Arst', 11000, 31000, 30000000, 18, 41000),
(12, 'Turvamees', 14000, 36000, 45000000, 20, 40000),
(13, 'Hambaarst', 18000, 42000, 70000000, 22, 39000),
(14, 'Disainer', 25000, 51000, 115000000, 24, 38000),
(15, 'Politseinik', 29000, 62500, 150000000, 26, 37000),
(16, 'Maaler', 35000, 75000, 200000000, 28, 36000),
(17, 'Ehitaja', 42500, 89000, 300000000, 30, 35000),
(18, 'Raamatupidaja', 50000, 102000, 450000000, 32, 34000),
(19, 'Ärimees', 65000, 115000, 600000000, 34, 33000),
(20, 'Firmajuht', 74000, 135000, 850000000, 36, 32000),
(21, 'Programmeerija', 85000, 145000, 1150000000, 38, 31000),
(22, 'Projektijuht', 97000, 167000, 1400000000, 40, 30000),
(23, 'Lavastaja', 115000, 185000, 1700000000, 42, 29000),
(24, 'Piloot', 135000, 200000, 2000000000, 44, 28000),
(25, 'IT septsialist', 150000, 230000, 2400000000, 46, 27000),
(26, 'Re&#x017E;issöör', 180000, 265000, 2700000000, 48, 26000),
(27, 'Poliitik', 225000, 300000, 3000000000, 50, 25000),
(28, 'Filmistaar', 250000, 345000, 3450000000, 52, 24000),
(29, 'Minister', 275000, 375000, 4000000000, 54, 23000),
(30, 'Peaminister', 300000, 415000, 4500000000, 56, 22000),
(31, 'President', 330000, 450000, 5000000000, 58, 21000),
(32, 'Teadlane', 350000, 500000, 6000000000, 60, 20000);

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
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `user_id` int(11) NOT NULL
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
-- Table structure for table `resto_levels`
--

CREATE TABLE `resto_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `area` int(11) NOT NULL,
  `education` int(11) NOT NULL,
  `money` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resto_levels`
--

INSERT INTO `resto_levels` (`id`, `name`, `area`, `education`, `money`) VALUES
(1, 'Tase 1', 10, 16000, 5000000),
(2, 'Tase 2', 50, 32000, 20000000),
(3, 'Tase 3', 150, 48000, 50000000),
(4, 'Tase 4', 300, 64000, 100000000),
(5, 'Tase 5', 400, 80000, 250000000),
(6, 'Tase 6', 500, 96000, 400000000),
(7, 'Tase 7', 700, 112000, 750000000),
(8, 'Tase 8', 900, 128000, 1000000000),
(9, 'Tase 9', 1100, 144000, 2000000000),
(10, 'Tase 10', 1300, 160000, 3000000000),
(11, 'Tase 11', 1500, 176000, 4000000000),
(12, 'Tase 12', 1600, 192000, 5000000000),
(13, 'Tase 13', 1700, 208000, 6000000000),
(14, 'Tase 14', 1800, 224000, 7000000000),
(15, 'Tase 15', 1900, 240000, 8000000000),
(16, 'Tase 16', 2000, 256000, 9000000000),
(17, 'Tase 17', 2100, 272000, 10000000000),
(18, 'Tase 18', 2200, 288000, 12000000000),
(19, 'Tase 19', 2300, 304000, 14000000000),
(20, 'Tase 20', 2400, 320000, 16000000000),
(21, 'Tase 21', 2500, 336000, 18000000000),
(22, 'Tase 22', 2600, 352000, 20000000000),
(23, 'Tase 23', 2700, 368000, 22000000000),
(24, 'Tase 24', 2800, 384000, 24000000000),
(25, 'Tase 25', 2900, 400000, 26000000000),
(26, 'Tase 26', 3000, 416000, 28000000000),
(27, 'Tase 27', 3100, 432000, 30000000000),
(28, 'Tase 28', 3200, 448000, 34000000000),
(29, 'Tase 29', 3350, 464000, 38000000000),
(30, 'Tase 30', 3500, 472000, 40000000000);

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `rule` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`id`, `chapter_id`, `rule`) VALUES
(23, 5, 'Teenuseid kasutanud mÃ¤ngijale mÃ¤ngust eemaldamise korral raha ei tagastata.'),
(24, 5, 'Makstud raha ei tagastata, kui maksad rohkem kui soovisid, lisatakse makstud raha eest vastav kogus FLC-sid.'),
(25, 5, 'FLC-sid ostnutele reeglite rikkumise korral erandeid karistamise osas ei tehta.'),
(26, 5, 'Teenuseid ei sunni teid keegi kasutama ning te kasutate neid omal tahtel.'),
(27, 5, 'Probleemide korral vÃµtke mÃ¤ngu omanikuga Ã¼hendust.'),
(28, 2, 'MÃ¤ngu omanik ei vastuta SMS-teenuste vÃµi pangateenuste tÃ¶Ã¶tamise eest.'),
(29, 2, 'MÃ¤ngu omanikul on Ãµigus kustutada vÃµi blokeerida kasutajakonto ilma ette hoiatamata.'),
(30, 2, 'FreeLand-i serveri teenuste eest vastutab Zone Media OÃœ.'),
(31, 2, 'MÃ¤ngu omanikul on Ãµigus muuta reegleid.'),
(32, 2, 'MÃ¤ngu omanik ei vastuta mÃ¤ngus tehtud kasutajate omavaheliste kokkulepete kinnipidamise eest.'),
(33, 4, 'Ei tohi lisada kasutajatele niisama keelu.'),
(34, 4, 'Ei tohi ka sellepÃ¤rast keelu lisada et kasutaja rÃ¼ndab teid jne...'),
(35, 4, 'Kasutajatel on Ãµigus kaevata omanikule, et nad on saanud niisama keelu jne. Siis omanik vÃµib teid eemaldada moderaatori vÃµi administraatori staatuselt.'),
(36, 4, 'Kui kasutaja on rikkunud reegleid tuleb kasutajat karistada.'),
(37, 3, 'Teavitama vigadest mÃ¤ngu omanikule koheselt.'),
(38, 3, 'MÃ¤ngu vigu ei tohi keegi enda huvides Ã¤ra kasutada!'),
(39, 3, 'Teavitama koheselt mÃ¤ngu juhtkonda kui keegi on reegleid rikkunud.'),
(40, 3, 'Omanikult ei tohi kÃ¼sida endale asju juurde (nÃ¤iteks: Lisa mulle raha juurde).'),
(41, 3, 'Keelatud on kasutada erinevaid programme mis lihtsustavad mÃ¤ngimist.'),
(42, 3, 'Oma parooli ei tohi Ã¶elda KELLELEGI, isegi mÃ¤ngu hooldajatele (mÃ¤ngu hooldajad ei kÃ¼si kunagi su parooli).'),
(43, 3, 'Kontot vÃµib omada ainult Ã¼ks inimene. Kontosse ei tohi keegi peale sinu sisse logida, samuti ei tohi sina kellegi teise kontosse sisse logida, ka mitte abistamise eesmÃ¤rgil.'),
(44, 3, 'Oma kasutajat ei tohi osta, mÃ¼Ã¼a, vahetada, jagada, Ã¤ra anda ega ka vastu vÃµtta (ka mitte pÃ¤ris raha eest).'),
(45, 3, 'Ãœheski mÃ¤ngu keskkonnas ei tohi ropendada, teisi solvata ega Ãµhutada rassismi. Samuti on keelatud kasutada roppe ning rassistlike kasutajanimesid.'),
(46, 3, 'Ãœheski mÃ¤ngu keskkonnas ei tohi levitada viiruseid, keyloggereid, keelatud programme jms.'),
(47, 3, 'Ãœheski mÃ¤ngu keskkonnas ei tohi levitada veebilehekÃ¼lgi mis ei ole seotud FreeLand-iga.');

-- --------------------------------------------------------

--
-- Table structure for table `rules_chapters`
--

CREATE TABLE `rules_chapters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rules_chapters`
--

INSERT INTO `rules_chapters` (`id`, `name`) VALUES
(2, 'Ãœldised tingimused'),
(3, 'FreeLand-i kasutaja kohustused'),
(4, 'FreeLand-i meeskonna kohustused'),
(5, 'Tasuliste teenuste tingimused');

-- --------------------------------------------------------

--
-- Table structure for table `settings_game`
--

CREATE TABLE `settings_game` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `group` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings_game`
--

INSERT INTO `settings_game` (`id`, `name`, `value`, `group`) VALUES
(1, 'GAME_EMAIL', 'support@freelandplay.eu', 'general'),
(2, 'WEBSITE_ADDRESS', 'www.freelandplay.eu', 'general'),
(3, 'NEWS_PER_PAGE', '5', 'general'),
(4, 'CHAT_PER_PAGE', '50', 'chat'),
(5, 'DEFMAN_MONEY', '500', 'house_defence'),
(6, 'DEFMAN_FOOD', '200', 'house_defence'),
(7, 'DEFMAN_TURNS', '4', 'house_defence'),
(8, 'OFEMAN_MONEY', '400', 'house_offence'),
(9, 'OFEMAN_FOOD', '150', 'house_offence'),
(10, 'OFEMAN_TURNS', '3', 'house_offence'),
(11, 'WEP_1_DEF', '35', 'weapons'),
(12, 'WEP_1_OFE', '15', 'weapons'),
(13, 'WEP_2_DEF', '19', 'weapons'),
(14, 'WEP_2_OFE', '39', 'weapons'),
(15, 'WEP_3_DEF', '52', 'weapons'),
(16, 'WEP_3_OFE', '28', 'weapons'),
(17, 'WEP_4_DEF', '36', 'weapons'),
(18, 'WEP_4_OFE', '59', 'weapons'),
(19, 'WEP_5_DEF', '72', 'weapons'),
(20, 'WEP_5_OFE', '36', 'weapons'),
(21, 'WEP_6_DEF', '42', 'weapons'),
(22, 'WEP_6_OFE', '69', 'weapons'),
(23, 'WEP_1_MONEY', '2550', 'weapons'),
(24, 'WEP_2_MONEY', '3200', 'weapons'),
(25, 'WEP_3_MONEY', '9500', 'weapons'),
(26, 'WEP_4_MONEY', '12200', 'weapons'),
(27, 'WEP_5_MONEY', '19250', 'weapons'),
(28, 'WEP_6_MONEY', '21700', 'weapons'),
(29, 'SCHOOL_MONEY', '20', 'school'),
(30, 'SCHOOL_TURNS', '40', 'school'),
(31, 'SCHOOL_GET', '2', 'school'),
(32, 'STOCK_PRICE', '260', 'stocks'),
(33, 'STOCK_PRICE_MIN', '245', 'stocks'),
(34, 'STOCK_PRICE_MAX', '290', 'stocks'),
(35, 'GYM_MONEY', '800', 'gym'),
(36, 'GYM_FOOD', '300', 'gym'),
(37, 'GYM_1_DEF', '57', 'gym'),
(38, 'GYM_1_OFE', '65', 'gym'),
(39, 'GYM_2_DEF', '60', 'gym'),
(40, 'GYM_2_OFE', '62', 'gym'),
(41, 'GYM_3_DEF', '68', 'gym'),
(42, 'GYM_3_OFE', '53', 'gym'),
(43, 'GANG_CREATE_MONEY', '500000', 'gang'),
(44, 'GANG_JOIN_MONEY', '100000', 'gang'),
(45, 'GANG_MONEY_SEND_LIMIT', '100000000', 'gang'),
(46, 'GANG_MONEY_SEND_POINTS', '5', 'gang'),
(47, 'GANG_EDIT_LEADER_MONEY', '100000000', 'gang'),
(48, 'HOUSE_LAND_MONEY', '400', 'house'),
(49, 'HOUSE_LAND_TURNS', '1', 'house'),
(50, 'HOUSE_WEED_SEED', '4', 'house'),
(51, 'HOUSE_WEED_TURNS', '25', 'house'),
(52, 'ROB_TURNS', '20', 'robbery'),
(53, 'ROB_TURNS_GANG', '15', 'robbery'),
(54, 'ROB_POINTS_GANG_EARN', '1', 'robbery'),
(55, 'ROB_DEFMAN_PROTECTED', '10', 'robbery'),
(56, 'FOOD_BUY_MONEY', '9', 'market'),
(57, 'FOOD_SELL_MONEY', '4', 'market'),
(58, 'WEED_BUY_MONEY', '0', 'market'),
(59, 'WEED_SELL_MONEY', '49', 'market'),
(60, 'SEED_BUY_MONEY', '4', 'market'),
(61, 'SEED_SELL_MONEY', '1', 'market'),
(62, 'SCHOOL_FOOD', '40', 'school'),
(63, 'GAME_UPDATE', '0', 'general'),
(64, 'GAME_VERSION', 'v2.5.9', 'general'),
(65, 'HOUSE_FOOD_TURNS', '5', 'house'),
(66, 'HOUSE_FOOD_FOODS', '4', 'house'),
(67, 'HOUSE_FOOD_LIMIT', '4', 'house');

-- --------------------------------------------------------

--
-- Table structure for table `settings_group`
--

CREATE TABLE `settings_group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings_group`
--

INSERT INTO `settings_group` (`id`, `name`, `desc`) VALUES
(1, 'cpanel', 'Control panel access.'),
(2, 'cp_general', 'Access to general website settings.'),
(3, 'cp_group', 'Access to add, edit and delete groups.'),
(4, 'cp_rules', 'Access to add, edit and delete rules.'),
(5, 'cp_help', 'Access to add, edit and delete help topics.'),
(6, 'cp_news', 'Access to add, edit and delete news.'),
(7, 'cp_gameplay', 'Access to edit gameplay settings'),
(8, 'cp_deletechat', 'Access to delete chat rows.'),
(9, 'cp_user_management', 'Access to manage users.'),
(10, 'cp_ads', 'Access to manage ads system.'),
(11, 'cp_fortumo_services', 'Allow access to view and edit fortumo services. '),
(12, 'cp_contact_settings', 'Access to edit contact form settings.'),
(13, 'contact_inbox', 'Access to read and reply to contact form messages.'),
(14, 'forum_edit', 'Access to edit forum posts.'),
(15, 'forum_delete', 'Access to delete forum posts.'),
(16, 'forum_close', 'Access to lock forum topics.');

-- --------------------------------------------------------

--
-- Table structure for table `system_time`
--

CREATE TABLE `system_time` (
  `id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `system_time`
--

INSERT INTO `system_time` (`id`, `time`) VALUES
(1, '2016-01-14 21:30:00');

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
  `food_1_price` int(11) NOT NULL DEFAULT '400',
  `food_1_price_edit` datetime NOT NULL,
  `food_1_orders` int(11) NOT NULL,
  `food_1_start` datetime NOT NULL,
  `food_1_end` datetime NOT NULL,
  `food_1_amount` int(11) NOT NULL,
  `food_2` int(11) NOT NULL,
  `food_2_price` int(11) NOT NULL DEFAULT '560',
  `food_2_price_edit` datetime NOT NULL,
  `food_2_orders` int(11) NOT NULL,
  `food_2_start` datetime NOT NULL,
  `food_2_end` datetime NOT NULL,
  `food_2_amount` int(11) NOT NULL,
  `food_3` int(11) NOT NULL,
  `food_3_price` int(11) NOT NULL DEFAULT '440',
  `food_3_price_edit` datetime NOT NULL,
  `food_3_orders` int(11) NOT NULL,
  `food_3_start` datetime NOT NULL,
  `food_3_end` datetime NOT NULL,
  `food_3_amount` int(11) NOT NULL,
  `food_4` int(11) NOT NULL,
  `food_4_price` int(11) NOT NULL DEFAULT '920',
  `food_4_price_edit` datetime NOT NULL,
  `food_4_orders` int(11) NOT NULL,
  `food_4_start` datetime NOT NULL,
  `food_4_end` datetime NOT NULL,
  `food_4_amount` int(11) NOT NULL,
  `food_5` int(11) NOT NULL,
  `food_5_price` int(11) NOT NULL DEFAULT '720',
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

-- --------------------------------------------------------

--
-- Table structure for table `vault_levels`
--

CREATE TABLE `vault_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `money` bigint(20) NOT NULL,
  `max_money` bigint(20) NOT NULL,
  `education` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vault_levels`
--

INSERT INTO `vault_levels` (`id`, `name`, `money`, `max_money`, `education`) VALUES
(1, 'Seif level 1', 250000000, 500000000, 70000),
(2, 'Seif level 2', 750000000, 1500000000, 130000),
(3, 'Seif level 3', 1500000000, 3000000000, 190000),
(4, 'Seif level 4', 3000000000, 6000000000, 256000),
(5, 'Seif level 5', 4500000000, 9000000000, 320000),
(6, 'Seif level 6', 6000000000, 12000000000, 390000),
(7, 'Seif level 7', 7500000000, 15000000000, 450000),
(8, 'Seif level 8', 9000000000, 18000000000, 512000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_test`
--
ALTER TABLE `bank_test`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `contact_inbox`
--
ALTER TABLE `contact_inbox`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_types`
--
ALTER TABLE `contact_types`
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
-- Indexes for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_theard`
--
ALTER TABLE `forum_theard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_topic`
--
ALTER TABLE `forum_topic`
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
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `help`
--
ALTER TABLE `help`
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
-- Indexes for table `job_list`
--
ALTER TABLE `job_list`
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
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_recovery`
--
ALTER TABLE `password_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resto_levels`
--
ALTER TABLE `resto_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rules_chapters`
--
ALTER TABLE `rules_chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_game`
--
ALTER TABLE `settings_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_group`
--
ALTER TABLE `settings_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_time`
--
ALTER TABLE `system_time`
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
-- Indexes for table `vault_levels`
--
ALTER TABLE `vault_levels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_test`
--
ALTER TABLE `bank_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `bank_transfers`
--
ALTER TABLE `bank_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2265;
--
-- AUTO_INCREMENT for table `contact_inbox`
--
ALTER TABLE `contact_inbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `contact_types`
--
ALTER TABLE `contact_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `credit_history`
--
ALTER TABLE `credit_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `crime_list`
--
ALTER TABLE `crime_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1835886;
--
-- AUTO_INCREMENT for table `fight_requests`
--
ALTER TABLE `fight_requests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;
--
-- AUTO_INCREMENT for table `forum_post`
--
ALTER TABLE `forum_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;
--
-- AUTO_INCREMENT for table `forum_theard`
--
ALTER TABLE `forum_theard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `forum_topic`
--
ALTER TABLE `forum_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `gang`
--
ALTER TABLE `gang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `gang_buildings`
--
ALTER TABLE `gang_buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `gang_forum_post`
--
ALTER TABLE `gang_forum_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `gang_forum_topic`
--
ALTER TABLE `gang_forum_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `gang_logs`
--
ALTER TABLE `gang_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16582;
--
-- AUTO_INCREMENT for table `gang_members`
--
ALTER TABLE `gang_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `help`
--
ALTER TABLE `help`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
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
-- AUTO_INCREMENT for table `job_list`
--
ALTER TABLE `job_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `lottery_bets`
--
ALTER TABLE `lottery_bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=419;
--
-- AUTO_INCREMENT for table `lottery_winners`
--
ALTER TABLE `lottery_winners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;
--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1482;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `password_recovery`
--
ALTER TABLE `password_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `resto_levels`
--
ALTER TABLE `resto_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `rules_chapters`
--
ALTER TABLE `rules_chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `settings_game`
--
ALTER TABLE `settings_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
--
-- AUTO_INCREMENT for table `settings_group`
--
ALTER TABLE `settings_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `system_time`
--
ALTER TABLE `system_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;
--
-- AUTO_INCREMENT for table `users_data`
--
ALTER TABLE `users_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;
--
-- AUTO_INCREMENT for table `users_data_house`
--
ALTER TABLE `users_data_house`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;
--
-- AUTO_INCREMENT for table `users_data_resto`
--
ALTER TABLE `users_data_resto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;
--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27023;
--
-- AUTO_INCREMENT for table `vault_levels`
--
ALTER TABLE `vault_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
