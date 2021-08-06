-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2021 at 05:26 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mintly_codecanyon`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_reward`
--

CREATE TABLE `activity_reward` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `min` int(11) NOT NULL DEFAULT 1,
  `max` int(11) NOT NULL DEFAULT 100,
  `active` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `activity_reward`
--

INSERT INTO `activity_reward` (`id`, `name`, `min`, `max`, `active`) VALUES
(1, 'Day 1', 50, 100, 1),
(2, 'Day 2', 45, 95, 1),
(3, 'Day 3', 40, 90, 1),
(4, 'Day 4', 35, 85, 1),
(5, 'Day 5', 30, 80, 1),
(6, 'Day 6', 25, 75, 1),
(7, 'Day 7', 20, 70, 1),
(8, 'Day 8', 15, 65, 1),
(9, 'Day 9', 10, 60, 1),
(10, 'Day 10', 5, 55, 1);

-- --------------------------------------------------------

--
-- Table structure for table `banned_users`
--

CREATE TABLE `banned_users` (
  `id` int(11) NOT NULL,
  `device_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gateway`
--

CREATE TABLE `gateway` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 100,
  `amount` varchar(50) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gateway`
--

INSERT INTO `gateway` (`id`, `category`, `quantity`, `amount`, `points`) VALUES
(14, 2, 19, '$10', 1000),
(15, 2, 10, '$50', 50000),
(20, 3, 29, '$10', 1000),
(21, 3, 10, '$30', 3000),
(22, 3, 15, '$50', 5000),
(23, 3, 5, '$100', 10000),
(24, 3, 2, '$150', 15000);

-- --------------------------------------------------------

--
-- Table structure for table `gate_category`
--

CREATE TABLE `gate_category` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `image` text NOT NULL,
  `input_desc` text NOT NULL,
  `input_type` int(1) NOT NULL DEFAULT 2,
  `country` varchar(190) NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gate_category`
--

INSERT INTO `gate_category` (`id`, `name`, `image`, `input_desc`, `input_type`, `country`) VALUES
(2, 'Amazon Gift Card', 'https://mintly.mintsoft.org/public/uploads/1623563157.png', 'Enter your email address. We will deliver the card directly to this email address.', 1, 'all'),
(3, 'Google Play Gift Card', 'https://mintly.mintsoft.org/public/uploads/1623563403.png', 'Enter your email address. We will deliver Google Play card number directly to this email address.', 2, 'all');

-- --------------------------------------------------------

--
-- Table structure for table `gate_request`
--

CREATE TABLE `gate_request` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `g_name` varchar(190) NOT NULL,
  `points` int(11) NOT NULL,
  `to_acc` varchar(190) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'us',
  `is_completed` int(11) NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `guess_word`
--

CREATE TABLE `guess_word` (
  `id` int(11) NOT NULL,
  `image` text NOT NULL,
  `info` varchar(190) NOT NULL,
  `word` varchar(50) NOT NULL,
  `country` varchar(190) NOT NULL,
  `max_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `guess_word`
--

INSERT INTO `guess_word` (`id`, `image`, `info`, `word`, `country`, `max_time`) VALUES
(2, 'https://mintly.mintsoft.org/public/uploads/1601880722.png', 'Name of the app', 'MINTCASH', 'all', 50),
(5, 'https://mintly.mintsoft.org/public/uploads/1601880722.png', 'Name of the app?', 'MINTCASH', 'all', 50),
(6, 'none', 'In what part of the body would you find the fibula?', 'LEGS', 'all', 50),
(7, 'none', 'In what US State is the city Nashville?', 'TENNESSEE', 'all', 50),
(8, 'none', 'At which venue is the British Grand Prix held?', 'SILVERSTONE', 'all', 60),
(9, 'none', 'What is the name of the fictional borough of Melbourne where Australian soap Neighbours is set?', 'ERINSBOROUGH', 'all', 80),
(10, 'none', 'ঢাকা কোন দেশের রাজধানী?', 'BANGLADESH', 'bd', 120);

-- --------------------------------------------------------

--
-- Table structure for table `guess_word_player`
--

CREATE TABLE `guess_word_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `last_id` int(11) NOT NULL,
  `word` varchar(50) NOT NULL,
  `rewarded` int(1) NOT NULL DEFAULT 0,
  `retry` int(11) NOT NULL DEFAULT 0,
  `hint` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hist_activities`
--

CREATE TABLE `hist_activities` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `network` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_lead` int(11) NOT NULL DEFAULT 0,
  `is_custom` int(1) NOT NULL DEFAULT 0,
  `offerid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '-',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `points` int(11) NOT NULL,
  `note` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hist_game`
--

CREATE TABLE `hist_game` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `game` varchar(100) NOT NULL,
  `points` int(11) NOT NULL,
  `deducted` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `html_game`
--

CREATE TABLE `html_game` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` text NOT NULL,
  `filename` text NOT NULL,
  `orientation` int(1) NOT NULL,
  `noads` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `html_game`
--

INSERT INTO `html_game` (`id`, `name`, `image`, `filename`, `orientation`, `noads`) VALUES
(8, 'Element Blocks', 'https://mintly.mintsoft.org/api/game/html/image?img=ElementBlocks.jpg', 'https://play.famobi.com/element-blocks', 1, 1),
(12, 'Wheelie 8', 'https://mintly.mintsoft.org/api/game/html/image?img=wheelie_8.png', 'https://html5.gamedistribution.com/213012cbef744a529cf3e1cc70fa8913/?', 1, 1),
(13, 'Math Search', 'https://mintly.mintsoft.org/api/game/html/image?img=math_search.jpg', 'https://cdn.htmlgames.com/MathSearch/', 1, 1),
(14, 'Micro Jewel', 'https://mintly.mintsoft.org/api/game/html/image?img=micro_jewel.jpeg', 'https://html5.gamedistribution.com/edffc32e7aa34a489a07ba14e47186a6/', 1, 1),
(16, 'Ludo Hero', 'https://mintly.mintsoft.org/api/game/html/image?img=ludo_hero.jpeg', 'https://html5.gamedistribution.com/951d399995cc4ea5a528c9b7e873066b/', 0, 1),
(17, 'HexGL Game', 'https://mintly.mintsoft.org/api/game/html/image?img=hexgl_icon.png', 'hexgl', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `image_puzzle`
--

CREATE TABLE `image_puzzle` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `image_puzzle`
--

INSERT INTO `image_puzzle` (`id`, `category`, `image`) VALUES
(1, 1, 'https://mintly.mintsoft.org/public/uploads/1602517989.jpg'),
(4, 3, 'https://mintly.mintsoft.org/public/uploads/1602603549.png'),
(5, 3, 'https://mintly.mintsoft.org/public/uploads/1602603593.jpg'),
(6, 3, 'https://mintly.mintsoft.org/public/uploads/1602603615.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `ip_category`
--

CREATE TABLE `ip_category` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `cost` int(11) NOT NULL,
  `reward` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `row` int(2) NOT NULL DEFAULT 3,
  `col` int(2) NOT NULL DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ip_category`
--

INSERT INTO `ip_category` (`id`, `title`, `cost`, `reward`, `time`, `row`, `col`) VALUES
(1, 'Basic Puzzle', 150, 500, 1600, 6, 5),
(3, 'Complex', 300, 800, 2000, 10, 6);

-- --------------------------------------------------------

--
-- Table structure for table `ip_player`
--

CREATE TABLE `ip_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `played` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `enc` varchar(10) NOT NULL,
  `rewarded` int(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `jpz_category`
--

CREATE TABLE `jpz_category` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `cost` int(11) NOT NULL,
  `reward` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `row` int(2) NOT NULL DEFAULT 3,
  `col` int(2) NOT NULL DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jpz_category`
--

INSERT INTO `jpz_category` (`id`, `title`, `cost`, `reward`, `time`, `row`, `col`) VALUES
(1, 'Basic Jigsaw', 100, 250, 600, 7, 5);

-- --------------------------------------------------------

--
-- Table structure for table `jpz_image`
--

CREATE TABLE `jpz_image` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jpz_image`
--

INSERT INTO `jpz_image` (`id`, `category`, `image`) VALUES
(2, 1, 'https://mintly.mintsoft.org/public/uploads/1603199593.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `jpz_player`
--

CREATE TABLE `jpz_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `played` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `enc` varchar(10) NOT NULL,
  `rewarded` int(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `score_cur` int(11) NOT NULL DEFAULT 0,
  `date_cur` varchar(5) DEFAULT NULL,
  `score_prv` int(11) NOT NULL DEFAULT 0,
  `date_prv` varchar(5) DEFAULT NULL,
  `rank` int(11) NOT NULL DEFAULT 0,
  `date_rank` varchar(20) DEFAULT NULL,
  `reward` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lotto_player`
--

CREATE TABLE `lotto_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lotto_data_1` text DEFAULT NULL,
  `lotto_data_2` text DEFAULT NULL,
  `lotto_date_1` varchar(10) NOT NULL DEFAULT '00-00-0000',
  `lotto_date_2` varchar(10) NOT NULL DEFAULT '00-00-0000',
  `lotto_rewarded` varchar(10) NOT NULL DEFAULT '00-00-0000',
  `lotto_won` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `misc`
--

CREATE TABLE `misc` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `misc`
--

INSERT INTO `misc` (`id`, `name`, `data`) VALUES
(1, 'slot_rows', '5'),
(2, 'slot_cols', '4'),
(4, 'leaderboard', '15-06'),
(6, 'lotto_winner', '2543233336'),
(7, 'lotto_draw_date', '16-06-2021'),
(8, 'global_msg', 'a:3:{s:5:\"title\";s:20:\"This is a test title\";s:4:\"desc\";s:190:\"When I create a request, there\'s a validation that checks the user. if the field user_id exists (i.e if user_id exists in the users table), then there\'s no need to require email and password\";s:3:\"mid\";s:5:\"lb6QA\";}');

-- --------------------------------------------------------

--
-- Table structure for table `notif_id`
--

CREATE TABLE `notif_id` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `offers_ppv`
--

CREATE TABLE `offers_ppv` (
  `id` int(11) NOT NULL,
  `title` varchar(190) NOT NULL,
  `url` text NOT NULL,
  `seconds` int(5) NOT NULL,
  `points` int(9) NOT NULL,
  `country` varchar(190) NOT NULL DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offers_ppv`
--

INSERT INTO `offers_ppv` (`id`, `title`, `url`, `seconds`, `points`, `country`, `created_at`) VALUES
(1, 'Google', 'https://google.com', 30, 10, 'all', '2020-08-02 06:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `offers_yt`
--

CREATE TABLE `offers_yt` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(190) NOT NULL,
  `points` int(9) NOT NULL,
  `country` varchar(190) NOT NULL DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offers_yt`
--

INSERT INTO `offers_yt` (`id`, `code`, `title`, `points`, `country`, `created_at`) VALUES
(1, 'aSiDu3Ywi8E', 'F9 - Official Trailer [HD', 10, 'all', '2020-07-30 03:22:09'),
(5, 'IGQBtbKSVhY', 'Dubstep Bird (Original 5 Sec Video)', 150, 'sg', '2020-08-16 11:02:40'),
(6, 'HagVnWAeGcM', 'Mr Bean in Room 426 | Episode 8 | Widescreen Version | Classic Mr Bean', 50, 'all', '2021-05-26 08:00:33'),
(7, 'G8wxsGl-rMA', 'ETERNALS Official Trailer (2021)', 20, 'all', '2021-05-26 08:02:01'),
(9, 'Jm0MLlE4x0U', 'The Fox and the Bird - CGI short film by Fred and Sam Guillaume', 20, 'all', '2021-05-26 08:02:34'),
(10, '-ezfi6FQ8Ds', 'VENOM: LET THERE BE CARNAGE - Official Trailer (HD)', 40, 'all', '2021-05-26 08:02:53'),
(11, '9mAEEHVFK1Q', 'The Wind Guardians.Full Film. Guarding hero the ways of the world. 风语咒 全片', 45, 'all', '2021-05-26 08:03:24'),
(12, 'rrwBnlYOp4g', 'VENOM 2 Official Trailer (2021)', 25, 'all', '2021-05-26 08:03:45'),
(13, 'm6xSTVy7N9o', 'Harry Potter and the Cursed Child (2022) Concept Trailer', 12, 'all', '2021-05-26 08:04:02'),
(14, 'bvXXQxcq5-4', 'X-Men: Apocalypse - Quicksilver Saves All...But One', 22, 'all', '2021-05-26 08:04:30'),
(15, 'SEsGagFwMLg', 'VFX SIDE QUEST | Sub-Zero Ice Blade Effect!', 32, 'all', '2021-05-26 08:05:02'),
(16, 'OmD_ykd6azw', 'How To Make Your Desktop Look Cool', 41, 'all', '2021-05-26 08:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `offerwalls`
--

CREATE TABLE `offerwalls` (
  `id` int(11) NOT NULL,
  `type` int(1) NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT 1,
  `name` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `title` varchar(30) NOT NULL DEFAULT 'Untitled',
  `description` varchar(120) NOT NULL DEFAULT 'No description provided',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offerwalls`
--

INSERT INTO `offerwalls` (`id`, `type`, `enabled`, `name`, `data`, `title`, `description`, `created_at`) VALUES
(1, 1, 1, 'tapjoy', '[{\"name\":\"SDK Key\",\"slug\":\"sdk_key\",\"value\":\"AAKsFlqxQt2y0-qEVNBxPgECGANDWW7fo2Z6hdXnQjA1ZSgLRuiWVxqbtxJO\"},{\"name\":\"Placement name\",\"slug\":\"placement_name\",\"value\":\"AppOfferwall\"}]', 'Tapjoy', 'No description provided', '2020-06-29 07:07:58'),
(2, 1, 1, 'ayetstudios', '[{\"name\":\"App Key\",\"slug\":\"app_key\",\"value\":\"348e1047432a1588233c525af29396e0\"},{\"name\":\"Slot name\",\"slug\":\"slot_name\",\"value\":\"mintly\"}]', 'Ayetstudios', 'No description provided', '2020-06-29 08:01:31'),
(3, 1, 1, 'fyber', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"write your app ID here\"},{\"name\":\"Security token\",\"slug\":\"security_token\",\"value\":\"write down your security token here\"}]', 'Fyber', 'No description provided', '2020-06-29 08:10:30'),
(4, 1, 1, 'personaly', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"Mint Cash\"},{\"name\":\"Placement Name\",\"slug\":\"placement_name\",\"value\":\"Default Placement\"},{\"name\":\"Offer Placement ID\",\"slug\":\"placement_id\",\"value\":\"cbea790a92c5d8aa44a806c1e6fb0927\"}]', 'Personaly', 'No description provided', '2020-06-29 08:13:57'),
(5, 1, 1, 'offertoro', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"8885\"},{\"name\":\"App secret key\",\"slug\":\"app_secret\",\"value\":\"cc8cbb1c2189df30fbaa9b92b21deb42\"}]', 'Offertoro', 'No description provided', '2020-06-29 08:17:38'),
(6, 1, 1, 'ironsrc_c', '[{\"name\":\"App Key\",\"slug\":\"app_key\",\"value\":\"6c7d3605\"},{\"name\":\"Offerwall Placement\",\"slug\":\"offerwall_placement\",\"value\":\"DefaultOfferWall\"}]', 'Ironsrc', 'No description provided', '2020-06-29 08:22:54'),
(7, 3, 2, 'admob', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"ca-app-pub-3940256099942544~3347511713\"},{\"name\":\"Banner slot\",\"slug\":\"banner_slot\",\"value\":\"ca-app-pub-3940256099942544\\/6300978111\"},{\"name\":\"Interstitial slot\",\"slug\":\"interstitial_slot\",\"value\":\"ca-app-pub-3940256099942544\\/1033173712\"},{\"name\":\"Rewarded slot\",\"slug\":\"rewarded_slot\",\"value\":\"ca-app-pub-3940256099942544\\/5224354917\"}]', 'Admob', 'No description provide', '2020-06-30 08:46:16'),
(8, 3, 1, 'adcolony', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"app42e31504d2cb4ef5a9\"},{\"name\":\"Zone ID\",\"slug\":\"zone_id\",\"value\":\"vz37f1b06a02b841c8af\"}]', 'Adcolony', 'No description provided', '2020-06-30 08:53:39'),
(9, 3, 1, 'ironsrc_v', '[{\"name\":\"App Key\",\"slug\":\"app_key\",\"value\":\"6c7d3605\"},{\"name\":\"Video Placement\",\"slug\":\"offerwall_placement\",\"value\":\"DefaultOfferWall\"}]', 'Ironsrc', 'No description provided', '2020-06-29 08:22:54'),
(10, 3, 1, 'chartboost', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"5b0560267afbf41042e9e1c2\"},{\"name\":\"App signature\",\"slug\":\"app_signature\",\"value\":\"718f7876399035d39eaf1acf9057c96bb7ca2ede\"}]', 'Chartboost', 'No description provided', '2020-06-29 08:22:54'),
(11, 3, 1, 'vungle', '[{\"name\":\"App ID\",\"slug\":\"app_id\",\"value\":\"5b064cf982ee08381f72fb24\"},{\"name\":\"Placement\",\"slug\":\"placement\",\"value\":\"MINTCASH_DEFAULT-3245677\"}]', 'Vungle', 'No description provided', '2020-06-29 08:22:54'),
(12, 3, 1, 'applovin', '[{\"name\":\"SDK Key\",\"slug\":\"sdk_key\",\"value\":\"u6DX8PdC2oSH_DmG2CX_15E2PhPIRG3JMHsVgThiG8_FLmWOy2N-8c0XJQrlhu33AwTsOEZeNTvkIP6FSTDa5E\"}]', 'Applovin', 'No description provided', '2020-06-29 08:22:54'),
(13, 2, 1, 'cpalead', '{\"offerwall_type\":\"1\",\"offer_api_url\":\"https:\\/\\/cpalead.com\\/dashboard\\/reports\\/campaign_json.php?id=814901&format=json&country=[app_country]&offer_type=mobile&device=android&subid3=[app_uid]&gaid=[app_gaid]\",\"json_array_key\":\"offers\",\"offer_id_key\":\"campid\",\"offer_title_key\":\"title\",\"offer_description_key\":\"description\",\"reward_amount_key\":\"amount\",\"icon_url_key\":\"previews,0,url\",\"offer_url_key\":\"link\",\"offer_url_suffix\":\"&sid=[app_uid]\"}', 'CPALead', 'No description provided', '2020-07-01 06:55:44'),
(16, 2, 1, 'ogads', '{\"offerwall_type\":\"1\",\"offer_api_url\":\"https:\\/\\/mobverify.com\\/api\\/v1\\/?affiliateid=106980&device=android&ctype=1&country=[app_country]&aff_sub5=[app_uid]\",\"json_array_key\":\"offers\",\"offer_id_key\":\"offerid\",\"offer_title_key\":\"name_short\",\"offer_description_key\":\"adcopy\",\"reward_amount_key\":\"payout\",\"icon_url_key\":\"picture\",\"offer_url_key\":\"link\",\"offer_url_suffix\":\"-none-\"}', 'Untitled', 'No description provided', '2020-09-06 16:39:29'),
(17, 4, 1, 'webtest', 'https://google.com', 'Web test', 'This is offerwall description', '2020-11-05 12:10:36'),
(18, 3, 1, 'fbook', '[{\"name\":\"Placement ID\",\"slug\":\"placement_id\",\"value\":\"YOUR_PLACEMENT_ID\"}]', 'Facebook', 'Setup from backend', '2021-07-12 08:02:27');

-- --------------------------------------------------------

--
-- Table structure for table `offerwall_c`
--

CREATE TABLE `offerwall_c` (
  `id` int(11) NOT NULL,
  `offer_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'p',
  `type` int(1) NOT NULL DEFAULT 1,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No instruction given',
  `points` int(11) NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max` int(11) NOT NULL,
  `completed` int(11) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offerwall_c`
--

INSERT INTO `offerwall_c` (`id`, `offer_id`, `type`, `country`, `title`, `description`, `points`, `image`, `url`, `max`, `completed`, `date`) VALUES
(2, 'p2', 1, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 07:41:11'),
(15, 'p15', 1, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 13:38:53'),
(16, 'p16', 1, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 13:38:53'),
(17, 'p17', 1, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 13:38:53'),
(18, 'p18', 1, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 13:38:53'),
(19, 'p19', 2, 'all', 'Mintly', 'Install the app to get rewards', 100, 'https://mintly.mintsoft.org/public/uploads/1595077945.png', 'market://details?id=ltd.mintservice.mintly&uid=', 200, 0, '2020-08-23 16:07:36'),
(20, 'p20', 1, 'all', 'Host Editor', 'Install the app to get rewards', 110, 'https://mintly.mintsoft.org/public/uploads/1599738885.png', 'market://details?id=com.nilhcem.hostseditor&uid=', 50, 1, '2020-09-10 18:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `online_users`
--

CREATE TABLE `online_users` (
  `id` int(11) NOT NULL,
  `country_iso` char(3) NOT NULL,
  `country_name` varchar(80) NOT NULL,
  `visitors` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `online_users`
--

INSERT INTO `online_users` (`id`, `country_iso`, `country_name`, `visitors`) VALUES
(1, 'AD', 'Andorra', 0),
(2, 'AE', 'United Arab Emirates', 0),
(3, 'AF', 'Afghanistan', 0),
(4, 'AG', 'Antigua and Barbuda', 0),
(5, 'AI', 'Anguilla', 0),
(6, 'AL', 'Albania', 0),
(7, 'AM', 'Armenia', 0),
(8, 'AN', 'Netherlands Antilles', 0),
(9, 'AO', 'Angola', 0),
(10, 'AQ', 'Antarctica', 0),
(11, 'AR', 'Argentina', 0),
(12, 'AS', 'American Samoa', 0),
(13, 'AT', 'Austria', 0),
(14, 'AU', 'Australia', 0),
(15, 'AW', 'Aruba', 0),
(16, 'AZ', 'Azerbaijan', 0),
(17, 'BA', 'Bosnia and Herzegovina', 0),
(18, 'BB', 'Barbados', 0),
(19, 'BD', 'Bangladesh', 0),
(20, 'BE', 'Belgium', 0),
(21, 'BF', 'Burkina Faso', 0),
(22, 'BG', 'Bulgaria', 0),
(23, 'BH', 'Bahrain', 0),
(24, 'BI', 'Burundi', 0),
(25, 'BJ', 'Benin', 0),
(26, 'BM', 'Bermuda', 0),
(27, 'BN', 'Brunei Darussalam', 0),
(28, 'BO', 'Bolivia', 0),
(29, 'BR', 'Brazil', 0),
(30, 'BS', 'Bahamas', 0),
(31, 'BT', 'Bhutan', 0),
(32, 'BV', 'Bouvet Island', 0),
(33, 'BW', 'Botswana', 0),
(34, 'BY', 'Belarus', 0),
(35, 'BZ', 'Belize', 0),
(36, 'CA', 'Canada', 0),
(37, 'CC', 'Cocos (Keeling) Islands', 0),
(38, 'CD', 'Congo, the Democratic Republic of the', 0),
(39, 'CF', 'Central African Republic', 0),
(40, 'CG', 'Congo', 0),
(41, 'CH', 'Switzerland', 0),
(42, 'CI', 'Cote D\'Ivoire', 0),
(43, 'CK', 'Cook Islands', 0),
(44, 'CL', 'Chile', 0),
(45, 'CM', 'Cameroon', 0),
(46, 'CN', 'China', 0),
(47, 'CO', 'Colombia', 0),
(48, 'CR', 'Costa Rica', 0),
(49, 'CS', 'Serbia and Montenegro', 0),
(50, 'CU', 'Cuba', 0),
(51, 'CV', 'Cape Verde', 0),
(52, 'CX', 'Christmas Island', 0),
(53, 'CY', 'Cyprus', 0),
(54, 'CZ', 'Czech Republic', 0),
(55, 'DE', 'Germany', 0),
(56, 'DJ', 'Djibouti', 0),
(57, 'DK', 'Denmark', 0),
(58, 'DM', 'Dominica', 0),
(59, 'DO', 'Dominican Republic', 0),
(60, 'DZ', 'Algeria', 0),
(61, 'EC', 'Ecuador', 0),
(62, 'EE', 'Estonia', 0),
(63, 'EG', 'Egypt', 0),
(64, 'EH', 'Western Sahara', 0),
(65, 'ER', 'Eritrea', 0),
(66, 'ES', 'Spain', 0),
(67, 'ET', 'Ethiopia', 0),
(68, 'FI', 'Finland', 0),
(69, 'FJ', 'Fiji', 0),
(70, 'FK', 'Falkland Islands (Malvinas)', 0),
(71, 'FM', 'Micronesia, Federated States of', 0),
(72, 'FO', 'Faroe Islands', 0),
(73, 'FR', 'France', 0),
(74, 'GA', 'Gabon', 0),
(75, 'GB', 'United Kingdom', 0),
(76, 'GD', 'Grenada', 0),
(77, 'GE', 'Georgia', 0),
(78, 'GF', 'French Guiana', 0),
(79, 'GH', 'Ghana', 0),
(80, 'GI', 'Gibraltar', 0),
(81, 'GL', 'Greenland', 0),
(82, 'GM', 'Gambia', 0),
(83, 'GN', 'Guinea', 0),
(84, 'GP', 'Guadeloupe', 0),
(85, 'GQ', 'Equatorial Guinea', 0),
(86, 'GR', 'Greece', 0),
(87, 'GS', 'South Georgia and the South Sandwich Islands', 0),
(88, 'GT', 'Guatemala', 0),
(89, 'GU', 'Guam', 0),
(90, 'GW', 'Guinea-Bissau', 0),
(91, 'GY', 'Guyana', 0),
(92, 'HK', 'Hong Kong', 0),
(93, 'HM', 'Heard Island and Mcdonald Islands', 0),
(94, 'HN', 'Honduras', 0),
(95, 'HR', 'Croatia', 0),
(96, 'HT', 'Haiti', 0),
(97, 'HU', 'Hungary', 0),
(98, 'ID', 'Indonesia', 0),
(99, 'IE', 'Ireland', 0),
(100, 'IL', 'Israel', 0),
(101, 'IN', 'India', 0),
(102, 'IO', 'British Indian Ocean Territory', 0),
(103, 'IQ', 'Iraq', 0),
(104, 'IR', 'Iran, Islamic Republic of', 0),
(105, 'IS', 'Iceland', 0),
(106, 'IT', 'Italy', 0),
(107, 'JM', 'Jamaica', 0),
(108, 'JO', 'Jordan', 0),
(109, 'JP', 'Japan', 0),
(110, 'KE', 'Kenya', 0),
(111, 'KG', 'Kyrgyzstan', 0),
(112, 'KH', 'Cambodia', 0),
(113, 'KI', 'Kiribati', 0),
(114, 'KM', 'Comoros', 0),
(115, 'KN', 'Saint Kitts and Nevis', 0),
(116, 'KP', 'Korea, Democratic People\'s Republic of', 0),
(117, 'KR', 'Korea, Republic of', 0),
(118, 'KW', 'Kuwait', 0),
(119, 'KY', 'Cayman Islands', 0),
(120, 'KZ', 'Kazakhstan', 0),
(121, 'LA', 'Lao People\'s Democratic Republic', 0),
(122, 'LB', 'Lebanon', 0),
(123, 'LC', 'Saint Lucia', 0),
(124, 'LI', 'Liechtenstein', 0),
(125, 'LK', 'Sri Lanka', 0),
(126, 'LR', 'Liberia', 0),
(127, 'LS', 'Lesotho', 0),
(128, 'LT', 'Lithuania', 0),
(129, 'LU', 'Luxembourg', 0),
(130, 'LV', 'Latvia', 0),
(131, 'LY', 'Libyan Arab Jamahiriya', 0),
(132, 'MA', 'Morocco', 0),
(133, 'MC', 'Monaco', 0),
(134, 'MD', 'Moldova, Republic of', 0),
(135, 'MG', 'Madagascar', 0),
(136, 'MH', 'Marshall Islands', 0),
(137, 'MK', 'Macedonia, the Former Yugoslav Republic of', 0),
(138, 'ML', 'Mali', 0),
(139, 'MM', 'Myanmar', 0),
(140, 'MN', 'Mongolia', 0),
(141, 'MO', 'Macao', 0),
(142, 'MP', 'Northern Mariana Islands', 0),
(143, 'MQ', 'Martinique', 0),
(144, 'MR', 'Mauritania', 0),
(145, 'MS', 'Montserrat', 0),
(146, 'MT', 'Malta', 0),
(147, 'MU', 'Mauritius', 0),
(148, 'MV', 'Maldives', 0),
(149, 'MW', 'Malawi', 0),
(150, 'MX', 'Mexico', 0),
(151, 'MY', 'Malaysia', 0),
(152, 'MZ', 'Mozambique', 0),
(153, 'NA', 'Namibia', 0),
(154, 'NC', 'New Caledonia', 0),
(155, 'NE', 'Niger', 0),
(156, 'NF', 'Norfolk Island', 0),
(157, 'NG', 'Nigeria', 0),
(158, 'NI', 'Nicaragua', 0),
(159, 'NL', 'Netherlands', 0),
(160, 'NO', 'Norway', 0),
(161, 'NP', 'Nepal', 0),
(162, 'NR', 'Nauru', 0),
(163, 'NU', 'Niue', 0),
(164, 'NZ', 'New Zealand', 0),
(165, 'OM', 'Oman', 0),
(166, 'PA', 'Panama', 0),
(167, 'PE', 'Peru', 0),
(168, 'PF', 'French Polynesia', 0),
(169, 'PG', 'Papua New Guinea', 0),
(170, 'PH', 'Philippines', 0),
(171, 'PK', 'Pakistan', 0),
(172, 'PL', 'Poland', 0),
(173, 'PM', 'Saint Pierre and Miquelon', 0),
(174, 'PN', 'Pitcairn', 0),
(175, 'PR', 'Puerto Rico', 0),
(176, 'PS', 'Palestinian Territory, Occupied', 0),
(177, 'PT', 'Portugal', 0),
(178, 'PW', 'Palau', 0),
(179, 'PY', 'Paraguay', 0),
(180, 'QA', 'Qatar', 0),
(181, 'RE', 'Reunion', 0),
(182, 'RO', 'Romania', 0),
(183, 'RU', 'Russian Federation', 0),
(184, 'RW', 'Rwanda', 0),
(185, 'SA', 'Saudi Arabia', 0),
(186, 'SB', 'Solomon Islands', 0),
(187, 'SC', 'Seychelles', 0),
(188, 'SD', 'Sudan', 0),
(189, 'SE', 'Sweden', 0),
(190, 'SG', 'Singapore', 0),
(191, 'SH', 'Saint Helena', 0),
(192, 'SI', 'Slovenia', 0),
(193, 'SJ', 'Svalbard and Jan Mayen', 0),
(194, 'SK', 'Slovakia', 0),
(195, 'SL', 'Sierra Leone', 0),
(196, 'SM', 'San Marino', 0),
(197, 'SN', 'Senegal', 0),
(198, 'SO', 'Somalia', 0),
(199, 'SR', 'Suriname', 0),
(200, 'ST', 'Sao Tome and Principe', 0),
(201, 'SV', 'El Salvador', 0),
(202, 'SY', 'Syrian Arab Republic', 0),
(203, 'SZ', 'Swaziland', 0),
(204, 'TC', 'Turks and Caicos Islands', 0),
(205, 'TD', 'Chad', 0),
(206, 'TF', 'French Southern Territories', 0),
(207, 'TG', 'Togo', 0),
(208, 'TH', 'Thailand', 0),
(209, 'TJ', 'Tajikistan', 0),
(210, 'TK', 'Tokelau', 0),
(211, 'TL', 'Timor-Leste', 0),
(212, 'TM', 'Turkmenistan', 0),
(213, 'TN', 'Tunisia', 0),
(214, 'TO', 'Tonga', 0),
(215, 'TR', 'Turkey', 0),
(216, 'TT', 'Trinidad and Tobago', 0),
(217, 'TV', 'Tuvalu', 0),
(218, 'TW', 'Taiwan, Province of China', 0),
(219, 'TZ', 'Tanzania, United Republic of', 0),
(220, 'UA', 'Ukraine', 0),
(221, 'UG', 'Uganda', 0),
(222, 'UM', 'United States Minor Outlying Islands', 0),
(223, 'US', 'United States', 0),
(224, 'UY', 'Uruguay', 0),
(225, 'UZ', 'Uzbekistan', 0),
(226, 'VA', 'Holy See (Vatican City State)', 0),
(227, 'VC', 'Saint Vincent and the Grenadines', 0),
(228, 'VE', 'Venezuela', 0),
(229, 'VG', 'Virgin Islands, British', 0),
(230, 'VI', 'Virgin Islands, U.s.', 0),
(231, 'VN', 'Viet Nam', 0),
(232, 'VU', 'Vanuatu', 0),
(233, 'WF', 'Wallis and Futuna', 0),
(234, 'WS', 'Samoa', 0),
(235, 'YE', 'Yemen', 0),
(236, 'YT', 'Mayotte', 0),
(237, 'ZA', 'South Africa', 0),
(238, 'ZM', 'Zambia', 0),
(239, 'ZW', 'Zimbabwe', 0),
(240, 'ALL', 'Unknown', 0);

-- --------------------------------------------------------

--
-- Table structure for table `postbacks`
--

CREATE TABLE `postbacks` (
  `id` int(11) NOT NULL,
  `offerwall_id` int(11) NOT NULL,
  `postback_type` int(1) NOT NULL,
  `network_name` varchar(20) NOT NULL,
  `network_slug` varchar(20) NOT NULL,
  `network_image` varchar(191) NOT NULL,
  `param_tok` varchar(40) NOT NULL,
  `param_amount` varchar(40) NOT NULL,
  `param_userid` varchar(40) NOT NULL,
  `param_offerid` varchar(40) NOT NULL,
  `param_ip` varchar(40) NOT NULL,
  `param_verify` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `postbacks`
--

INSERT INTO `postbacks` (`id`, `offerwall_id`, `postback_type`, `network_name`, `network_slug`, `network_image`, `param_tok`, `param_amount`, `param_userid`, `param_offerid`, `param_ip`, `param_verify`) VALUES
(1, 1, 2, 'Tapjoy', 'tapjoy_o', 'https://mintly.mintsoft.org/public/uploads/tapjoy.png', 'kjasahda7s89d7ausd', 'currency=', 'snuid=', 'offer_id=tjid', 'ip=blank', NULL),
(2, 2, 1, 'Ayetstudios', 'ayetstudios_o', 'https://mintly.mintsoft.org/public/uploads/ayet.png', 'kjsad87689ausd', 'points={currency_amount}', 'uid={uid}', 'offerid={offer_id}', 'ip={ip} ', NULL),
(3, 3, 2, 'Fyber', 'fyber_o', 'https://mintly.mintsoft.org/public/uploads/fyber.png', 'kjasad98asd', 'amount=', 'uid=', 'offer_id=blank', 'ip=blank', NULL),
(4, 4, 1, 'Personaly', 'personaly_o', 'https://mintly.mintsoft.org/public/uploads/personaly.png', 'jhasgd78a6s', 'payout={amount}', 'userid={user_id}', 'offer_id={package_id}', 'ip=blank', NULL),
(5, 5, 1, 'Offertoro', 'offertoro_o', 'https://mintly.mintsoft.org/public/uploads/offertoro.png', 'jksagd8a7s6d', 'payout={amount}', 'userid={user_id}', 'offer_id={oid}', 'ip=blank', NULL),
(6, 6, 1, 'Ironsrc', 'ironsrc_o', 'https://mintly.mintsoft.org/public/uploads/ironsrc.png', 'kjasagd87as6d', 'payout=[REWARDS]', 'userid=[USER_ID]', 'offer_id=[EVENT_ID]', 'ip=blank', NULL),
(7, 7, 1, 'Admob', 'admob_v', 'https://mintly.mintsoft.org/public/uploads/admob.png', 'jhsdgfsdf7sjdfg87', 'amount=', 'uid=', 'offer_id=blank', 'ip=blank', NULL),
(8, 8, 1, 'Adcolony', 'adcolony_v', 'https://mintly.mintsoft.org/public/uploads/adcolony.png', 'jksuyda89766asiu90as', 'amount=[CURRENCY_AMOUNT]', 'userid=[CUSTOM_ID]', 'offer_id=blank', 'ip=blank', NULL),
(9, 9, 1, 'Ironsrc', 'ironsrc_v', 'https://mintly.mintsoft.org/public/uploads/ironsrc.png', 'kjasagd87as6d', 'payout=[REWARDS]', 'userid=[USER_ID]', 'offer_id=[EVENT_ID]', 'ip=blank', NULL),
(10, 10, 2, 'Chartboost', 'chartboost_v', 'https://mintly.mintsoft.org/public/uploads/chartboost.png', 'Jhjasd897ajkh2jhff987', 'amount={Award Amount}', 'userid={Custom ID}', 'offer_id=blank', 'ip=blank', NULL),
(11, 11, 1, 'Vungle', 'vungle_v', 'https://mintly.mintsoft.org/public/uploads/vungle.png', 'j3hy2hjsdf897', 'amount=1', 'userid=%user%', 'offer_id=blank', 'ip=blank', NULL),
(12, 12, 1, 'Applovin', 'applovin_v', 'https://mintly.mintsoft.org/public/uploads/applovin.png', 'usyd8762jk3h9d', 'amount={AMOUNT}', 'userid={USER_ID}', 'offer_id=blank', 'ip=blank', NULL),
(13, 13, 1, 'CPALead', 'cpalead_a', 'https://mintly.mintsoft.org/public/uploads/1593586544.png', 'kjsahkdjhaksjad87', 'payout={payout}', 'userid={subid3}', 'offer_id={campaing_id}', 'ip={ip_address}', NULL),
(14, 16, 1, 'OGAds', 'ogads_a', 'https://mintly.mintsoft.org/public/uploads/1599410369.jpg', 'kjsahkdjhaksjad87', 'payout={payout}', 'userid={aff_sub5}', 'offer_id={offerid}', 'ip=blank', NULL),
(15, 17, 1, 'Web test', 'webtest_a', 'https://mintly.mintsoft.org/public/uploads/1604578236.png', 'kldjahsas897d', 'payout={payout}', 'userid={aff_sub5}', 'offer_id={campaing_id}', 'ip=blank', NULL),
(16, 18, 1, 'Facebook', 'fb_v', 'https://mintly.mintsoft.org/public/uploads/fbads.png', 'jhastd879a6s8d', 'pc=REWARD_ID', 'uid=USER_ID', 'offer_id=blank', 'ip=blank', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `category` int(9) NOT NULL,
  `question` text NOT NULL,
  `functions` text DEFAULT NULL,
  `answer` int(2) DEFAULT NULL,
  `country` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `category`, `question`, `functions`, `answer`, `country`) VALUES
(6, 2, 'This is a test question, the answer will be line 3', 'Test ans 1||Test ans 2||Test ans 3||Test ans 4', 3, 'all'),
(7, 2, 'This is a second test which answer will be second one', 'First ans||Second ans||Third ans', 2, 'us,gb'),
(8, 1, 'What is the result of ($a+$b+$c)?', '$a = 10; $b = 10; $c = 10; $function = $a+$b+$c; $result = 30;', NULL, 'all'),
(9, 1, 'What is the 15% of ($a+$b+$c)?', '$a = 10;  $b = 10;  $c = 10;  $function = ($a+$b+$c)*15/100;  $result = 4.5;', NULL, 'all'),
(10, 1, 'What is the summation of $d and $e?', '$e=12;  $d=10;  $function=($d+$e);  $result=22;', NULL, 'all'),
(11, 3, 'What is the capital of USA?', 'California||New York||Washington D.C.', 3, 'us'),
(12, 2, 'This is a image test<br>\r\nhttps://mintly.mintsoft.org/public/img/offerwall.png', 'Test ans 1||Test ans 2||Test ans 3', 2, 'all');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_category`
--

CREATE TABLE `quiz_category` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `description` varchar(190) NOT NULL,
  `image` text NOT NULL,
  `reward` int(9) NOT NULL DEFAULT 0,
  `quiz_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quiz_category`
--

INSERT INTO `quiz_category` (`id`, `title`, `description`, `image`, `reward`, `quiz_time`) VALUES
(1, 'Mathmatics', 'This is math quiz description.', 'https://mintly.mintsoft.org/public/uploads/1598516288.png', 10, 30),
(2, 'General', 'This is a test description this is a test desc this is a test desc this is a test desc.', 'https://mintly.mintsoft.org/public/uploads/1598507168.jpg', 10, 10),
(3, 'Geography', 'How much you know about this universe?', 'https://mintly.mintsoft.org/public/uploads/1598714019.jpg', 4, 50);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_player`
--

CREATE TABLE `quiz_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `category` int(11) NOT NULL,
  `o_count` int(2) NOT NULL,
  `answer` int(2) NOT NULL,
  `rewarded` int(1) NOT NULL,
  `wrong` int(5) NOT NULL,
  `fifty` int(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `scratcher_game`
--

CREATE TABLE `scratcher_game` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `cost` int(11) NOT NULL DEFAULT 1,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `difficulty` int(2) NOT NULL DEFAULT 4,
  `card` text NOT NULL,
  `image` text NOT NULL,
  `coord` varchar(190) NOT NULL,
  `days` int(6) NOT NULL DEFAULT 365,
  `can_purchase` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scratcher_game`
--

INSERT INTO `scratcher_game` (`id`, `name`, `cost`, `min`, `max`, `difficulty`, `card`, `image`, `coord`, `days`, `can_purchase`) VALUES
(1, 'Premium Card', 1, 100, 160, 0, 'https://mintly.mintsoft.org/public/uploads/1622267867.png', 'https://mintly.mintsoft.org/public/uploads/1622267866.png', '13.000,87.000,47.000,79.000', 50, 0),
(7, 'Big Cash', 10, 1, 100, 5, 'https://mintly.mintsoft.org/public/uploads/1622262242.png', 'https://mintly.mintsoft.org/public/uploads/1622262241.png', '8.333,91.935,42.550,83.871', 30, 1),
(9, 'Test card', 50, 500, 2000, 3, 'https://mintly.mintsoft.org/public/uploads/1622716853.png', 'https://mintly.mintsoft.org/public/uploads/1622716852.png', '11.290,90.323,11.060,95.084', 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `scratcher_player`
--

CREATE TABLE `scratcher_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `card_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `slot_game`
--

CREATE TABLE `slot_game` (
  `id` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `line_array` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `slot_game`
--

INSERT INTO `slot_game` (`id`, `active`, `line_array`) VALUES
(1, 1, '0,1,2,3'),
(2, 1, '4,5,6,7'),
(3, 1, '8,9,10,11'),
(4, 1, '12,13,14,15'),
(5, 1, '16,17,18,19'),
(6, 1, '8,9,6,3'),
(7, 1, '8,9,14,19'),
(8, 1, '0,5,10,11'),
(9, 1, '16,13,10,11'),
(10, 1, '2,6,10,14,18'),
(11, 0, '0,5,6,7'),
(12, 0, '9,10,11,12'),
(13, 0, '8,13,14,15'),
(14, 0, '1,2,3,4'),
(15, 0, '0,1,2,7'),
(16, 0, '11,12,13,14'),
(17, 0, '10,11,12,13'),
(18, 0, '0,1,6,7'),
(19, 0, '2,3,4,5'),
(20, 0, '8,9,14,15');

-- --------------------------------------------------------

--
-- Table structure for table `slot_player`
--

CREATE TABLE `slot_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `round` int(5) NOT NULL DEFAULT 0,
  `free` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `support`
--

CREATE TABLE `support` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text NOT NULL,
  `is_staff` int(1) NOT NULL DEFAULT 0,
  `replied` int(1) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL,
  `updated_at` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `support_faq`
--

CREATE TABLE `support_faq` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `support_faq`
--

INSERT INTO `support_faq` (`id`, `question`, `answer`) VALUES
(1, 'This is a test question.', 'This is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer.'),
(4, 'This is a test question. This is a test question. This is a test question.', 'This is a test answer is a test answer is a test answer is a test answer is a test answer is a test answer i');

-- --------------------------------------------------------

--
-- Table structure for table `tour_player`
--

CREATE TABLE `tour_player` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `avatar` text NOT NULL,
  `qa` text NOT NULL,
  `correct` int(11) NOT NULL DEFAULT 0,
  `marks` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tour_question`
--

CREATE TABLE `tour_question` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `options` text NOT NULL,
  `answer` int(2) NOT NULL,
  `time` int(11) NOT NULL DEFAULT 10,
  `score` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tour_question`
--

INSERT INTO `tour_question` (`id`, `question`, `options`, `answer`, `time`, `score`) VALUES
(1, 'MOSST is difficult to use', 'True;;False;;Don\'t know', 2, 50, 10),
(2, 'What color is an orange?', 'Purple;;Orange;;An orange has no color. It\'s transparent.;;Don\'t know', 2, 30, 12),
(3, 'Please, let me ______!', 'make;;think;;want;;put;;have', 2, 40, 10),
(4, 'Is that boy Mary\'s son?', 'Yes, name is Robert.;;Yes, he is.;;No, he is Marys nephew.;;Yes, those are Mary\'s son.;;Yes, he are.', 2, 50, 10),
(5, 'Where is the post office?', 'It is in the corner of Main Street and Washington Avenue.;;Is near the bank.;;It\'s between Main Street.;;The post office is at 534 Washington Avenue.;;They are between the bank and the supermarket.', 4, 120, 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userid` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` int(11) NOT NULL DEFAULT 0,
  `pending` int(11) NOT NULL DEFAULT 0,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `avatar` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `country` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'us',
  `refby` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `done_ar` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_notification` int(1) NOT NULL DEFAULT 0,
  `vpn` int(11) NOT NULL DEFAULT 0,
  `ref_state` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userid`, `email`, `balance`, `pending`, `name`, `password`, `remember_token`, `device_id`, `avatar`, `ip`, `country`, `refby`, `done_ar`, `has_notification`, `vpn`, `ref_state`, `created_at`, `updated_at`) VALUES
(1, '5EC8C27BB275B', 'admin@mintsoft.org', 0, 0, 'Mintly Admin', '$2y$10$f8nF9/WmOMMS8taUahelKeG97bTKP.9wO2Ew5lvoHp4bER1LDPPuS', 'SI2O4pG3XjTSs5FvrMlu5eQKgwcq0u6teDsNN5hfSfG7eVfjaXJJoRHdZceY', 'none', NULL, '0.0.0.0', 'us', 'none', NULL, 0, 0, 0, '2020-07-15 05:47:25', '2020-05-05 03:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `vpn_monitor`
--

CREATE TABLE `vpn_monitor` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `name` varchar(50) NOT NULL,
  `avatar` text NOT NULL DEFAULT '',
  `attempted` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `wheel`
--

CREATE TABLE `wheel` (
  `id` int(11) NOT NULL,
  `text` varchar(7) NOT NULL,
  `bg` varchar(7) NOT NULL,
  `message` text NOT NULL,
  `difficulty` int(1) NOT NULL DEFAULT 2,
  `card_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `wheel`
--

INSERT INTO `wheel` (`id`, `text`, `bg`, `message`, `difficulty`, `card_id`) VALUES
(1, 'FREE', '#6f21e7', 'You received a free chance!', 4, 1),
(2, 'CARD', '#ff0909', 'You got 1 scratch card!', 1, 1),
(3, '50', '#6f21e7', 'Congrats, you won 50 coins.', 5, 0),
(4, '-100', '#f2f2f2', 'You lost 100 coins', 5, 0),
(5, '70', '#6f21e7', 'Congrats, you won 70 coins.', 5, 0),
(6, 'CARD', '#e69322', 'You received 1 Big Cash card', 1, 7),
(7, '100', '#6f21e7', '100 coins rewarded, keep playing.', 5, 0),
(8, '-30', '#f2f2f2', 'You lost 30 coins', 5, 0),
(9, '150', '#6f21e7', 'Congrats, you won 150 coins.', 5, 0),
(10, 'CARD', '#b543c5', 'You got 200 coins', 5, 1),
(11, '-150', '#6f21e7', 'Ahh! you lost 150 coins', 5, 0),
(12, '500', '#f2f2f2', 'You won 500 coins!', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wheel_player`
--

CREATE TABLE `wheel_player` (
  `id` int(11) NOT NULL,
  `userid` varchar(13) NOT NULL,
  `free` int(3) NOT NULL DEFAULT 0,
  `played` int(11) NOT NULL,
  `date` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_reward`
--
ALTER TABLE `activity_reward`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banned_users`
--
ALTER TABLE `banned_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateway`
--
ALTER TABLE `gateway`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gate_category`
--
ALTER TABLE `gate_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gate_request`
--
ALTER TABLE `gate_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guess_word`
--
ALTER TABLE `guess_word`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guess_word_player`
--
ALTER TABLE `guess_word_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hist_activities`
--
ALTER TABLE `hist_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hist_game`
--
ALTER TABLE `hist_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `html_game`
--
ALTER TABLE `html_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `image_puzzle`
--
ALTER TABLE `image_puzzle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_category`
--
ALTER TABLE `ip_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_player`
--
ALTER TABLE `ip_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jpz_category`
--
ALTER TABLE `jpz_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jpz_image`
--
ALTER TABLE `jpz_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jpz_player`
--
ALTER TABLE `jpz_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lotto_player`
--
ALTER TABLE `lotto_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `misc`
--
ALTER TABLE `misc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notif_id`
--
ALTER TABLE `notif_id`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `offers_ppv`
--
ALTER TABLE `offers_ppv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers_yt`
--
ALTER TABLE `offers_yt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offerwalls`
--
ALTER TABLE `offerwalls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `offerwall_c`
--
ALTER TABLE `offerwall_c`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offer_id` (`offer_id`);

--
-- Indexes for table `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postbacks`
--
ALTER TABLE `postbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_category`
--
ALTER TABLE `quiz_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `quiz_player`
--
ALTER TABLE `quiz_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratcher_game`
--
ALTER TABLE `scratcher_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scratcher_player`
--
ALTER TABLE `scratcher_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slot_game`
--
ALTER TABLE `slot_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slot_player`
--
ALTER TABLE `slot_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_faq`
--
ALTER TABLE `support_faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_player`
--
ALTER TABLE `tour_player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_question`
--
ALTER TABLE `tour_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vpn_monitor`
--
ALTER TABLE `vpn_monitor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wheel`
--
ALTER TABLE `wheel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wheel_player`
--
ALTER TABLE `wheel_player`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_reward`
--
ALTER TABLE `activity_reward`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `banned_users`
--
ALTER TABLE `banned_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gateway`
--
ALTER TABLE `gateway`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gate_category`
--
ALTER TABLE `gate_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gate_request`
--
ALTER TABLE `gate_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guess_word`
--
ALTER TABLE `guess_word`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guess_word_player`
--
ALTER TABLE `guess_word_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hist_activities`
--
ALTER TABLE `hist_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hist_game`
--
ALTER TABLE `hist_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `html_game`
--
ALTER TABLE `html_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `image_puzzle`
--
ALTER TABLE `image_puzzle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ip_category`
--
ALTER TABLE `ip_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ip_player`
--
ALTER TABLE `ip_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jpz_category`
--
ALTER TABLE `jpz_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jpz_image`
--
ALTER TABLE `jpz_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jpz_player`
--
ALTER TABLE `jpz_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lotto_player`
--
ALTER TABLE `lotto_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `misc`
--
ALTER TABLE `misc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notif_id`
--
ALTER TABLE `notif_id`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offers_ppv`
--
ALTER TABLE `offers_ppv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offers_yt`
--
ALTER TABLE `offers_yt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `offerwalls`
--
ALTER TABLE `offerwalls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `offerwall_c`
--
ALTER TABLE `offerwall_c`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `postbacks`
--
ALTER TABLE `postbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `quiz_category`
--
ALTER TABLE `quiz_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_player`
--
ALTER TABLE `quiz_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scratcher_game`
--
ALTER TABLE `scratcher_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `scratcher_player`
--
ALTER TABLE `scratcher_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slot_game`
--
ALTER TABLE `slot_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `slot_player`
--
ALTER TABLE `slot_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_faq`
--
ALTER TABLE `support_faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tour_player`
--
ALTER TABLE `tour_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_question`
--
ALTER TABLE `tour_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vpn_monitor`
--
ALTER TABLE `vpn_monitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wheel`
--
ALTER TABLE `wheel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wheel_player`
--
ALTER TABLE `wheel_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
