-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2026 at 06:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `globalexchange_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `recid` int(11) NOT NULL,
  `login_id` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `site_name` varchar(255) NOT NULL DEFAULT '',
  `site_slogan` varchar(100) NOT NULL,
  `site_url` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_info` varchar(50) NOT NULL,
  `email_mail` varchar(50) NOT NULL,
  `email_support` varchar(50) NOT NULL,
  `email_sales` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `pincode` varchar(10) NOT NULL DEFAULT '',
  `tds` decimal(11,2) NOT NULL,
  `service_tax` decimal(11,2) NOT NULL,
  `service` decimal(11,2) NOT NULL,
  `use_type` varchar(10) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `working_status` tinyint(4) NOT NULL,
  `sms_count` int(11) NOT NULL,
  `visitors` int(11) NOT NULL,
  `bv_value` varchar(50) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `capping_binary` int(11) NOT NULL DEFAULT 0,
  `capping_gift` int(11) NOT NULL DEFAULT 0,
  `user_sms_format` text NOT NULL,
  `api_type` tinyint(4) NOT NULL DEFAULT 0,
  `b_rate` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `s_rate` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `coin_rate` decimal(20,8) NOT NULL DEFAULT 1.00000000,
  `coin_rate_bnb` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `otp` tinyint(4) NOT NULL DEFAULT 0,
  `bot_liquidity` int(11) DEFAULT NULL,
  `bot_profit` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`recid`, `login_id`, `password`, `name`, `site_name`, `site_slogan`, `site_url`, `email`, `email_info`, `email_mail`, `email_support`, `email_sales`, `phone`, `mobile`, `address`, `city`, `state`, `country`, `pincode`, `tds`, `service_tax`, `service`, `use_type`, `status`, `working_status`, `sms_count`, `visitors`, `bv_value`, `logo`, `capping_binary`, `capping_gift`, `user_sms_format`, `api_type`, `b_rate`, `s_rate`, `coin_rate`, `coin_rate_bnb`, `otp`, `bot_liquidity`, `bot_profit`) VALUES
(1, 'administrator', 'fc942d89d295b0a600a534d2ccfdd82a33fa876b', 'globalexchange.live', 'globalexchange.live', '', 'globalexchange.live', 'support@globalexchange.live', 'info@globalexchange.live', 'info@globalexchange.live', 'info@globalexchange.live', 'info@globalexchange.live', '1234567890', '0000000000', 'Pune', 'Pune', 'MH', 'IN', '310001', 0.00, 0.00, 5.00, 'Admin', 0, 0, 0, 8, '1', '', 100, 100, 'Dear sir,successful  call [MOBILE]', 0, 1.00000000, 1.00000000, 1.00000000, 0.00000000, 0, 12344, 12);

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_detail`
--

CREATE TABLE `admin_login_detail` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms`
--

CREATE TABLE `cms` (
  `recid` int(11) NOT NULL,
  `mid` int(11) NOT NULL DEFAULT 0,
  `cid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_categories`
--

CREATE TABLE `cms_categories` (
  `recid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0,
  `mid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `datetime` datetime DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu`
--

CREATE TABLE `cms_menu` (
  `recid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `is_image` tinyint(4) NOT NULL DEFAULT 0,
  `is_category` tinyint(4) NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_menu`
--

INSERT INTO `cms_menu` (`recid`, `pid`, `title`, `description`, `is_image`, `is_category`, `datetime`, `type`, `status`) VALUES
(1, 0, 'News', 'News', 0, 0, '2020-06-20 21:29:58', 0, 0),
(2, 0, 'Achiever', 'Achiever', 0, 0, '2020-06-20 21:29:58', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `recid` int(11) NOT NULL,
  `country_id` char(2) DEFAULT NULL,
  `short_name` varchar(80) NOT NULL DEFAULT '',
  `long_name` varchar(80) NOT NULL DEFAULT '',
  `iso3` char(3) DEFAULT NULL,
  `numcode` varchar(6) DEFAULT NULL,
  `un_member` varchar(12) DEFAULT NULL,
  `calling_code` varchar(8) DEFAULT NULL,
  `cctld` varchar(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`recid`, `country_id`, `short_name`, `long_name`, `iso3`, `numcode`, `un_member`, `calling_code`, `cctld`) VALUES
(1, 'AF', 'Afghanistan', 'Islamic Republic of Afghanistan', 'AFG', '004', 'yes', '93', '.af'),
(2, 'AX', 'Aland Islands', '&Aring;land Islands', 'ALA', '248', 'no', '358', '.ax'),
(3, 'AL', 'Albania', 'Republic of Albania', 'ALB', '008', 'yes', '355', '.al'),
(4, 'DZ', 'Algeria', 'People\'s Democratic Republic of Algeria', 'DZA', '012', 'yes', '213', '.dz'),
(5, 'AS', 'American Samoa', 'American Samoa', 'ASM', '016', 'no', '1+684', '.as'),
(6, 'AD', 'Andorra', 'Principality of Andorra', 'AND', '020', 'yes', '376', '.ad'),
(7, 'AO', 'Angola', 'Republic of Angola', 'AGO', '024', 'yes', '244', '.ao'),
(8, 'AI', 'Anguilla', 'Anguilla', 'AIA', '660', 'no', '1+264', '.ai'),
(9, 'AQ', 'Antarctica', 'Antarctica', 'ATA', '010', 'no', '672', '.aq'),
(10, 'AG', 'Antigua and Barbuda', 'Antigua and Barbuda', 'ATG', '028', 'yes', '1+268', '.ag'),
(11, 'AR', 'Argentina', 'Argentine Republic', 'ARG', '032', 'yes', '54', '.ar'),
(12, 'AM', 'Armenia', 'Republic of Armenia', 'ARM', '051', 'yes', '374', '.am'),
(13, 'AW', 'Aruba', 'Aruba', 'ABW', '533', 'no', '297', '.aw'),
(14, 'AU', 'Australia', 'Commonwealth of Australia', 'AUS', '036', 'yes', '61', '.au'),
(15, 'AT', 'Austria', 'Republic of Austria', 'AUT', '040', 'yes', '43', '.at'),
(16, 'AZ', 'Azerbaijan', 'Republic of Azerbaijan', 'AZE', '031', 'yes', '994', '.az'),
(17, 'BS', 'Bahamas', 'Commonwealth of The Bahamas', 'BHS', '044', 'yes', '1+242', '.bs'),
(18, 'BH', 'Bahrain', 'Kingdom of Bahrain', 'BHR', '048', 'yes', '973', '.bh'),
(19, 'BD', 'Bangladesh', 'People\'s Republic of Bangladesh', 'BGD', '050', 'yes', '880', '.bd'),
(20, 'BB', 'Barbados', 'Barbados', 'BRB', '052', 'yes', '1+246', '.bb'),
(21, 'BY', 'Belarus', 'Republic of Belarus', 'BLR', '112', 'yes', '375', '.by'),
(22, 'BE', 'Belgium', 'Kingdom of Belgium', 'BEL', '056', 'yes', '32', '.be'),
(23, 'BZ', 'Belize', 'Belize', 'BLZ', '084', 'yes', '501', '.bz'),
(24, 'BJ', 'Benin', 'Republic of Benin', 'BEN', '204', 'yes', '229', '.bj'),
(25, 'BM', 'Bermuda', 'Bermuda Islands', 'BMU', '060', 'no', '1+441', '.bm'),
(26, 'BT', 'Bhutan', 'Kingdom of Bhutan', 'BTN', '064', 'yes', '975', '.bt'),
(27, 'BO', 'Bolivia', 'Plurinational State of Bolivia', 'BOL', '068', 'yes', '591', '.bo'),
(28, 'BQ', 'Bonaire, Sint Eustatius and Saba', 'Bonaire, Sint Eustatius and Saba', 'BES', '535', 'no', '599', '.bq'),
(29, 'BA', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'BIH', '070', 'yes', '387', '.ba'),
(30, 'BW', 'Botswana', 'Republic of Botswana', 'BWA', '072', 'yes', '267', '.bw'),
(31, 'BV', 'Bouvet Island', 'Bouvet Island', 'BVT', '074', 'no', 'NONE', '.bv'),
(32, 'BR', 'Brazil', 'Federative Republic of Brazil', 'BRA', '076', 'yes', '55', '.br'),
(33, 'IO', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'IOT', '086', 'no', '246', '.io'),
(34, 'BN', 'Brunei', 'Brunei Darussalam', 'BRN', '096', 'yes', '673', '.bn'),
(35, 'BG', 'Bulgaria', 'Republic of Bulgaria', 'BGR', '100', 'yes', '359', '.bg'),
(36, 'BF', 'Burkina Faso', 'Burkina Faso', 'BFA', '854', 'yes', '226', '.bf'),
(37, 'BI', 'Burundi', 'Republic of Burundi', 'BDI', '108', 'yes', '257', '.bi'),
(38, 'KH', 'Cambodia', 'Kingdom of Cambodia', 'KHM', '116', 'yes', '855', '.kh'),
(39, 'CM', 'Cameroon', 'Republic of Cameroon', 'CMR', '120', 'yes', '237', '.cm'),
(40, 'CA', 'Canada', 'Canada', 'CAN', '124', 'yes', '1', '.ca'),
(41, 'CV', 'Cape Verde', 'Republic of Cape Verde', 'CPV', '132', 'yes', '238', '.cv'),
(42, 'KY', 'Cayman Islands', 'The Cayman Islands', 'CYM', '136', 'no', '1+345', '.ky'),
(43, 'CF', 'Central African Republic', 'Central African Republic', 'CAF', '140', 'yes', '236', '.cf'),
(44, 'TD', 'Chad', 'Republic of Chad', 'TCD', '148', 'yes', '235', '.td'),
(45, 'CL', 'Chile', 'Republic of Chile', 'CHL', '152', 'yes', '56', '.cl'),
(46, 'CN', 'China', 'People\'s Republic of China', 'CHN', '156', 'yes', '86', '.cn'),
(47, 'CX', 'Christmas Island', 'Christmas Island', 'CXR', '162', 'no', '61', '.cx'),
(48, 'CC', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'CCK', '166', 'no', '61', '.cc'),
(49, 'CO', 'Colombia', 'Republic of Colombia', 'COL', '170', 'yes', '57', '.co'),
(50, 'KM', 'Comoros', 'Union of the Comoros', 'COM', '174', 'yes', '269', '.km'),
(51, 'CG', 'Congo', 'Republic of the Congo', 'COG', '178', 'yes', '242', '.cg'),
(52, 'CK', 'Cook Islands', 'Cook Islands', 'COK', '184', 'some', '682', '.ck'),
(53, 'CR', 'Costa Rica', 'Republic of Costa Rica', 'CRI', '188', 'yes', '506', '.cr'),
(54, 'CI', 'Cote d\'ivoire (Ivory Coast)', 'Republic of C&ocirc;te D\'Ivoire (Ivory Coast)', 'CIV', '384', 'yes', '225', '.ci'),
(55, 'HR', 'Croatia', 'Republic of Croatia', 'HRV', '191', 'yes', '385', '.hr'),
(56, 'CU', 'Cuba', 'Republic of Cuba', 'CUB', '192', 'yes', '53', '.cu'),
(57, 'CW', 'Curacao', 'Cura&ccedil;ao', 'CUW', '531', 'no', '599', '.cw'),
(58, 'CY', 'Cyprus', 'Republic of Cyprus', 'CYP', '196', 'yes', '357', '.cy'),
(59, 'CZ', 'Czech Republic', 'Czech Republic', 'CZE', '203', 'yes', '420', '.cz'),
(60, 'CD', 'Democratic Republic of the Congo', 'Democratic Republic of the Congo', 'COD', '180', 'yes', '243', '.cd'),
(61, 'DK', 'Denmark', 'Kingdom of Denmark', 'DNK', '208', 'yes', '45', '.dk'),
(62, 'DJ', 'Djibouti', 'Republic of Djibouti', 'DJI', '262', 'yes', '253', '.dj'),
(63, 'DM', 'Dominica', 'Commonwealth of Dominica', 'DMA', '212', 'yes', '1+767', '.dm'),
(64, 'DO', 'Dominican Republic', 'Dominican Republic', 'DOM', '214', 'yes', '1+809, 8', '.do'),
(65, 'EC', 'Ecuador', 'Republic of Ecuador', 'ECU', '218', 'yes', '593', '.ec'),
(66, 'EG', 'Egypt', 'Arab Republic of Egypt', 'EGY', '818', 'yes', '20', '.eg'),
(67, 'SV', 'El Salvador', 'Republic of El Salvador', 'SLV', '222', 'yes', '503', '.sv'),
(68, 'GQ', 'Equatorial Guinea', 'Republic of Equatorial Guinea', 'GNQ', '226', 'yes', '240', '.gq'),
(69, 'ER', 'Eritrea', 'State of Eritrea', 'ERI', '232', 'yes', '291', '.er'),
(70, 'EE', 'Estonia', 'Republic of Estonia', 'EST', '233', 'yes', '372', '.ee'),
(71, 'ET', 'Ethiopia', 'Federal Democratic Republic of Ethiopia', 'ETH', '231', 'yes', '251', '.et'),
(72, 'FK', 'Falkland Islands (Malvinas)', 'The Falkland Islands (Malvinas)', 'FLK', '238', 'no', '500', '.fk'),
(73, 'FO', 'Faroe Islands', 'The Faroe Islands', 'FRO', '234', 'no', '298', '.fo'),
(74, 'FJ', 'Fiji', 'Republic of Fiji', 'FJI', '242', 'yes', '679', '.fj'),
(75, 'FI', 'Finland', 'Republic of Finland', 'FIN', '246', 'yes', '358', '.fi'),
(76, 'FR', 'France', 'French Republic', 'FRA', '250', 'yes', '33', '.fr'),
(77, 'GF', 'French Guiana', 'French Guiana', 'GUF', '254', 'no', '594', '.gf'),
(78, 'PF', 'French Polynesia', 'French Polynesia', 'PYF', '258', 'no', '689', '.pf'),
(79, 'TF', 'French Southern Territories', 'French Southern Territories', 'ATF', '260', 'no', NULL, '.tf'),
(80, 'GA', 'Gabon', 'Gabonese Republic', 'GAB', '266', 'yes', '241', '.ga'),
(81, 'GM', 'Gambia', 'Republic of The Gambia', 'GMB', '270', 'yes', '220', '.gm'),
(82, 'GE', 'Georgia', 'Georgia', 'GEO', '268', 'yes', '995', '.ge'),
(83, 'DE', 'Germany', 'Federal Republic of Germany', 'DEU', '276', 'yes', '49', '.de'),
(84, 'GH', 'Ghana', 'Republic of Ghana', 'GHA', '288', 'yes', '233', '.gh'),
(85, 'GI', 'Gibraltar', 'Gibraltar', 'GIB', '292', 'no', '350', '.gi'),
(86, 'GR', 'Greece', 'Hellenic Republic', 'GRC', '300', 'yes', '30', '.gr'),
(87, 'GL', 'Greenland', 'Greenland', 'GRL', '304', 'no', '299', '.gl'),
(88, 'GD', 'Grenada', 'Grenada', 'GRD', '308', 'yes', '1+473', '.gd'),
(89, 'GP', 'Guadaloupe', 'Guadeloupe', 'GLP', '312', 'no', '590', '.gp'),
(90, 'GU', 'Guam', 'Guam', 'GUM', '316', 'no', '1+671', '.gu'),
(91, 'GT', 'Guatemala', 'Republic of Guatemala', 'GTM', '320', 'yes', '502', '.gt'),
(92, 'GG', 'Guernsey', 'Guernsey', 'GGY', '831', 'no', '44', '.gg'),
(93, 'GN', 'Guinea', 'Republic of Guinea', 'GIN', '324', 'yes', '224', '.gn'),
(94, 'GW', 'Guinea-Bissau', 'Republic of Guinea-Bissau', 'GNB', '624', 'yes', '245', '.gw'),
(95, 'GY', 'Guyana', 'Co-operative Republic of Guyana', 'GUY', '328', 'yes', '592', '.gy'),
(96, 'HT', 'Haiti', 'Republic of Haiti', 'HTI', '332', 'yes', '509', '.ht'),
(97, 'HM', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', 'HMD', '334', 'no', 'NONE', '.hm'),
(98, 'HN', 'Honduras', 'Republic of Honduras', 'HND', '340', 'yes', '504', '.hn'),
(99, 'HK', 'Hong Kong', 'Hong Kong', 'HKG', '344', 'no', '852', '.hk'),
(100, 'HU', 'Hungary', 'Hungary', 'HUN', '348', 'yes', '36', '.hu'),
(101, 'IS', 'Iceland', 'Republic of Iceland', 'ISL', '352', 'yes', '354', '.is'),
(102, 'IN', 'India', 'Republic of India', 'IND', '356', 'yes', '91', '.in'),
(103, 'ID', 'Indonesia', 'Republic of Indonesia', 'IDN', '360', 'yes', '62', '.id'),
(104, 'IR', 'Iran', 'Islamic Republic of Iran', 'IRN', '364', 'yes', '98', '.ir'),
(105, 'IQ', 'Iraq', 'Republic of Iraq', 'IRQ', '368', 'yes', '964', '.iq'),
(106, 'IE', 'Ireland', 'Ireland', 'IRL', '372', 'yes', '353', '.ie'),
(107, 'IM', 'Isle of Man', 'Isle of Man', 'IMN', '833', 'no', '44', '.im'),
(108, 'IL', 'Israel', 'State of Israel', 'ISR', '376', 'yes', '972', '.il'),
(109, 'IT', 'Italy', 'Italian Republic', 'ITA', '380', 'yes', '39', '.jm'),
(110, 'JM', 'Jamaica', 'Jamaica', 'JAM', '388', 'yes', '1+876', '.jm'),
(111, 'JP', 'Japan', 'Japan', 'JPN', '392', 'yes', '81', '.jp'),
(112, 'JE', 'Jersey', 'The Bailiwick of Jersey', 'JEY', '832', 'no', '44', '.je'),
(113, 'JO', 'Jordan', 'Hashemite Kingdom of Jordan', 'JOR', '400', 'yes', '962', '.jo'),
(114, 'KZ', 'Kazakhstan', 'Republic of Kazakhstan', 'KAZ', '398', 'yes', '7', '.kz'),
(115, 'KE', 'Kenya', 'Republic of Kenya', 'KEN', '404', 'yes', '254', '.ke'),
(116, 'KI', 'Kiribati', 'Republic of Kiribati', 'KIR', '296', 'yes', '686', '.ki'),
(117, 'XK', 'Kosovo', 'Republic of Kosovo', '---', '---', 'some', '381', ''),
(118, 'KW', 'Kuwait', 'State of Kuwait', 'KWT', '414', 'yes', '965', '.kw'),
(119, 'KG', 'Kyrgyzstan', 'Kyrgyz Republic', 'KGZ', '417', 'yes', '996', '.kg'),
(120, 'LA', 'Laos', 'Lao People\'s Democratic Republic', 'LAO', '418', 'yes', '856', '.la'),
(121, 'LV', 'Latvia', 'Republic of Latvia', 'LVA', '428', 'yes', '371', '.lv'),
(122, 'LB', 'Lebanon', 'Republic of Lebanon', 'LBN', '422', 'yes', '961', '.lb'),
(123, 'LS', 'Lesotho', 'Kingdom of Lesotho', 'LSO', '426', 'yes', '266', '.ls'),
(124, 'LR', 'Liberia', 'Republic of Liberia', 'LBR', '430', 'yes', '231', '.lr'),
(125, 'LY', 'Libya', 'Libya', 'LBY', '434', 'yes', '218', '.ly'),
(126, 'LI', 'Liechtenstein', 'Principality of Liechtenstein', 'LIE', '438', 'yes', '423', '.li'),
(127, 'LT', 'Lithuania', 'Republic of Lithuania', 'LTU', '440', 'yes', '370', '.lt'),
(128, 'LU', 'Luxembourg', 'Grand Duchy of Luxembourg', 'LUX', '442', 'yes', '352', '.lu'),
(129, 'MO', 'Macao', 'The Macao Special Administrative Region', 'MAC', '446', 'no', '853', '.mo'),
(130, 'MK', 'Macedonia', 'The Former Yugoslav Republic of Macedonia', 'MKD', '807', 'yes', '389', '.mk'),
(131, 'MG', 'Madagascar', 'Republic of Madagascar', 'MDG', '450', 'yes', '261', '.mg'),
(132, 'MW', 'Malawi', 'Republic of Malawi', 'MWI', '454', 'yes', '265', '.mw'),
(133, 'MY', 'Malaysia', 'Malaysia', 'MYS', '458', 'yes', '60', '.my'),
(134, 'MV', 'Maldives', 'Republic of Maldives', 'MDV', '462', 'yes', '960', '.mv'),
(135, 'ML', 'Mali', 'Republic of Mali', 'MLI', '466', 'yes', '223', '.ml'),
(136, 'MT', 'Malta', 'Republic of Malta', 'MLT', '470', 'yes', '356', '.mt'),
(137, 'MH', 'Marshall Islands', 'Republic of the Marshall Islands', 'MHL', '584', 'yes', '692', '.mh'),
(138, 'MQ', 'Martinique', 'Martinique', 'MTQ', '474', 'no', '596', '.mq'),
(139, 'MR', 'Mauritania', 'Islamic Republic of Mauritania', 'MRT', '478', 'yes', '222', '.mr'),
(140, 'MU', 'Mauritius', 'Republic of Mauritius', 'MUS', '480', 'yes', '230', '.mu'),
(141, 'YT', 'Mayotte', 'Mayotte', 'MYT', '175', 'no', '262', '.yt'),
(142, 'MX', 'Mexico', 'United Mexican States', 'MEX', '484', 'yes', '52', '.mx'),
(143, 'FM', 'Micronesia', 'Federated States of Micronesia', 'FSM', '583', 'yes', '691', '.fm'),
(144, 'MD', 'Moldava', 'Republic of Moldova', 'MDA', '498', 'yes', '373', '.md'),
(145, 'MC', 'Monaco', 'Principality of Monaco', 'MCO', '492', 'yes', '377', '.mc'),
(146, 'MN', 'Mongolia', 'Mongolia', 'MNG', '496', 'yes', '976', '.mn'),
(147, 'ME', 'Montenegro', 'Montenegro', 'MNE', '499', 'yes', '382', '.me'),
(148, 'MS', 'Montserrat', 'Montserrat', 'MSR', '500', 'no', '1+664', '.ms'),
(149, 'MA', 'Morocco', 'Kingdom of Morocco', 'MAR', '504', 'yes', '212', '.ma'),
(150, 'MZ', 'Mozambique', 'Republic of Mozambique', 'MOZ', '508', 'yes', '258', '.mz'),
(151, 'MM', 'Myanmar (Burma)', 'Republic of the Union of Myanmar', 'MMR', '104', 'yes', '95', '.mm'),
(152, 'NA', 'Namibia', 'Republic of Namibia', 'NAM', '516', 'yes', '264', '.na'),
(153, 'NR', 'Nauru', 'Republic of Nauru', 'NRU', '520', 'yes', '674', '.nr'),
(154, 'NP', 'Nepal', 'Federal Democratic Republic of Nepal', 'NPL', '524', 'yes', '977', '.np'),
(155, 'NL', 'Netherlands', 'Kingdom of the Netherlands', 'NLD', '528', 'yes', '31', '.nl'),
(156, 'NC', 'New Caledonia', 'New Caledonia', 'NCL', '540', 'no', '687', '.nc'),
(157, 'NZ', 'New Zealand', 'New Zealand', 'NZL', '554', 'yes', '64', '.nz'),
(158, 'NI', 'Nicaragua', 'Republic of Nicaragua', 'NIC', '558', 'yes', '505', '.ni'),
(159, 'NE', 'Niger', 'Republic of Niger', 'NER', '562', 'yes', '227', '.ne'),
(160, 'NG', 'Nigeria', 'Federal Republic of Nigeria', 'NGA', '566', 'yes', '234', '.ng'),
(161, 'NU', 'Niue', 'Niue', 'NIU', '570', 'some', '683', '.nu'),
(162, 'NF', 'Norfolk Island', 'Norfolk Island', 'NFK', '574', 'no', '672', '.nf'),
(163, 'KP', 'North Korea', 'Democratic People\'s Republic of Korea', 'PRK', '408', 'yes', '850', '.kp'),
(164, 'MP', 'Northern Mariana Islands', 'Northern Mariana Islands', 'MNP', '580', 'no', '1+670', '.mp'),
(165, 'NO', 'Norway', 'Kingdom of Norway', 'NOR', '578', 'yes', '47', '.no'),
(166, 'OM', 'Oman', 'Sultanate of Oman', 'OMN', '512', 'yes', '968', '.om'),
(167, 'PK', 'Pakistan', 'Islamic Republic of Pakistan', 'PAK', '586', 'yes', '92', '.pk'),
(168, 'PW', 'Palau', 'Republic of Palau', 'PLW', '585', 'yes', '680', '.pw'),
(169, 'PS', 'Palestine', 'State of Palestine (or Occupied Palestinian Territory)', 'PSE', '275', 'some', '970', '.ps'),
(170, 'PA', 'Panama', 'Republic of Panama', 'PAN', '591', 'yes', '507', '.pa'),
(171, 'PG', 'Papua New Guinea', 'Independent State of Papua New Guinea', 'PNG', '598', 'yes', '675', '.pg'),
(172, 'PY', 'Paraguay', 'Republic of Paraguay', 'PRY', '600', 'yes', '595', '.py'),
(173, 'PE', 'Peru', 'Republic of Peru', 'PER', '604', 'yes', '51', '.pe'),
(174, 'PH', 'Phillipines', 'Republic of the Philippines', 'PHL', '608', 'yes', '63', '.ph'),
(175, 'PN', 'Pitcairn', 'Pitcairn', 'PCN', '612', 'no', 'NONE', '.pn'),
(176, 'PL', 'Poland', 'Republic of Poland', 'POL', '616', 'yes', '48', '.pl'),
(177, 'PT', 'Portugal', 'Portuguese Republic', 'PRT', '620', 'yes', '351', '.pt'),
(178, 'PR', 'Puerto Rico', 'Commonwealth of Puerto Rico', 'PRI', '630', 'no', '1+939', '.pr'),
(179, 'QA', 'Qatar', 'State of Qatar', 'QAT', '634', 'yes', '974', '.qa'),
(180, 'RE', 'Reunion', 'R&eacute;union', 'REU', '638', 'no', '262', '.re'),
(181, 'RO', 'Romania', 'Romania', 'ROU', '642', 'yes', '40', '.ro'),
(182, 'RU', 'Russia', 'Russian Federation', 'RUS', '643', 'yes', '7', '.ru'),
(183, 'RW', 'Rwanda', 'Republic of Rwanda', 'RWA', '646', 'yes', '250', '.rw'),
(184, 'BL', 'Saint Barthelemy', 'Saint Barth&eacute;lemy', 'BLM', '652', 'no', '590', '.bl'),
(185, 'SH', 'Saint Helena', 'Saint Helena, Ascension and Tristan da Cunha', 'SHN', '654', 'no', '290', '.sh'),
(186, 'KN', 'Saint Kitts and Nevis', 'Federation of Saint Christopher and Nevis', 'KNA', '659', 'yes', '1+869', '.kn'),
(187, 'LC', 'Saint Lucia', 'Saint Lucia', 'LCA', '662', 'yes', '1+758', '.lc'),
(188, 'MF', 'Saint Martin', 'Saint Martin', 'MAF', '663', 'no', '590', '.mf'),
(189, 'PM', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'SPM', '666', 'no', '508', '.pm'),
(190, 'VC', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', 'VCT', '670', 'yes', '1+784', '.vc'),
(191, 'WS', 'Samoa', 'Independent State of Samoa', 'WSM', '882', 'yes', '685', '.ws'),
(192, 'SM', 'San Marino', 'Republic of San Marino', 'SMR', '674', 'yes', '378', '.sm'),
(193, 'ST', 'Sao Tome and Principe', 'Democratic Republic of S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'STP', '678', 'yes', '239', '.st'),
(194, 'SA', 'Saudi Arabia', 'Kingdom of Saudi Arabia', 'SAU', '682', 'yes', '966', '.sa'),
(195, 'SN', 'Senegal', 'Republic of Senegal', 'SEN', '686', 'yes', '221', '.sn'),
(196, 'RS', 'Serbia', 'Republic of Serbia', 'SRB', '688', 'yes', '381', '.rs'),
(197, 'SC', 'Seychelles', 'Republic of Seychelles', 'SYC', '690', 'yes', '248', '.sc'),
(198, 'SL', 'Sierra Leone', 'Republic of Sierra Leone', 'SLE', '694', 'yes', '232', '.sl'),
(199, 'SG', 'Singapore', 'Republic of Singapore', 'SGP', '702', 'yes', '65', '.sg'),
(200, 'SX', 'Sint Maarten', 'Sint Maarten', 'SXM', '534', 'no', '1+721', '.sx'),
(201, 'SK', 'Slovakia', 'Slovak Republic', 'SVK', '703', 'yes', '421', '.sk'),
(202, 'SI', 'Slovenia', 'Republic of Slovenia', 'SVN', '705', 'yes', '386', '.si'),
(203, 'SB', 'Solomon Islands', 'Solomon Islands', 'SLB', '090', 'yes', '677', '.sb'),
(204, 'SO', 'Somalia', 'Somali Republic', 'SOM', '706', 'yes', '252', '.so'),
(205, 'ZA', 'South Africa', 'Republic of South Africa', 'ZAF', '710', 'yes', '27', '.za'),
(206, 'GS', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', 'SGS', '239', 'no', '500', '.gs'),
(207, 'KR', 'South Korea', 'Republic of Korea', 'KOR', '410', 'yes', '82', '.kr'),
(208, 'SS', 'South Sudan', 'Republic of South Sudan', 'SSD', '728', 'yes', '211', '.ss'),
(209, 'ES', 'Spain', 'Kingdom of Spain', 'ESP', '724', 'yes', '34', '.es'),
(210, 'LK', 'Sri Lanka', 'Democratic Socialist Republic of Sri Lanka', 'LKA', '144', 'yes', '94', '.lk'),
(211, 'SD', 'Sudan', 'Republic of the Sudan', 'SDN', '729', 'yes', '249', '.sd'),
(212, 'SR', 'Suriname', 'Republic of Suriname', 'SUR', '740', 'yes', '597', '.sr'),
(213, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard and Jan Mayen', 'SJM', '744', 'no', '47', '.sj'),
(214, 'SZ', 'Swaziland', 'Kingdom of Swaziland', 'SWZ', '748', 'yes', '268', '.sz'),
(215, 'SE', 'Sweden', 'Kingdom of Sweden', 'SWE', '752', 'yes', '46', '.se'),
(216, 'CH', 'Switzerland', 'Swiss Confederation', 'CHE', '756', 'yes', '41', '.ch'),
(217, 'SY', 'Syria', 'Syrian Arab Republic', 'SYR', '760', 'yes', '963', '.sy'),
(218, 'TW', 'Taiwan', 'Republic of China (Taiwan)', 'TWN', '158', 'former', '886', '.tw'),
(219, 'TJ', 'Tajikistan', 'Republic of Tajikistan', 'TJK', '762', 'yes', '992', '.tj'),
(220, 'TZ', 'Tanzania', 'United Republic of Tanzania', 'TZA', '834', 'yes', '255', '.tz'),
(221, 'TH', 'Thailand', 'Kingdom of Thailand', 'THA', '764', 'yes', '66', '.th'),
(222, 'TL', 'Timor-Leste (East Timor)', 'Democratic Republic of Timor-Leste', 'TLS', '626', 'yes', '670', '.tl'),
(223, 'TG', 'Togo', 'Togolese Republic', 'TGO', '768', 'yes', '228', '.tg'),
(224, 'TK', 'Tokelau', 'Tokelau', 'TKL', '772', 'no', '690', '.tk'),
(225, 'TO', 'Tonga', 'Kingdom of Tonga', 'TON', '776', 'yes', '676', '.to'),
(226, 'TT', 'Trinidad and Tobago', 'Republic of Trinidad and Tobago', 'TTO', '780', 'yes', '1+868', '.tt'),
(227, 'TN', 'Tunisia', 'Republic of Tunisia', 'TUN', '788', 'yes', '216', '.tn'),
(228, 'TR', 'Turkey', 'Republic of Turkey', 'TUR', '792', 'yes', '90', '.tr'),
(229, 'TM', 'Turkmenistan', 'Turkmenistan', 'TKM', '795', 'yes', '993', '.tm'),
(230, 'TC', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'TCA', '796', 'no', '1+649', '.tc'),
(231, 'TV', 'Tuvalu', 'Tuvalu', 'TUV', '798', 'yes', '688', '.tv'),
(232, 'UG', 'Uganda', 'Republic of Uganda', 'UGA', '800', 'yes', '256', '.ug'),
(233, 'UA', 'Ukraine', 'Ukraine', 'UKR', '804', 'yes', '380', '.ua'),
(234, 'AE', 'United Arab Emirates', 'United Arab Emirates', 'ARE', '784', 'yes', '971', '.ae'),
(235, 'GB', 'United Kingdom', 'United Kingdom of Great Britain and Nothern Ireland', 'GBR', '826', 'yes', '44', '.uk'),
(236, 'US', 'United States', 'United States of America', 'USA', '840', 'yes', '1', '.us'),
(237, 'UM', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', 'UMI', '581', 'no', 'NONE', 'NONE'),
(238, 'UY', 'Uruguay', 'Eastern Republic of Uruguay', 'URY', '858', 'yes', '598', '.uy'),
(239, 'UZ', 'Uzbekistan', 'Republic of Uzbekistan', 'UZB', '860', 'yes', '998', '.uz'),
(240, 'VU', 'Vanuatu', 'Republic of Vanuatu', 'VUT', '548', 'yes', '678', '.vu'),
(241, 'VA', 'Vatican City', 'State of the Vatican City', 'VAT', '336', 'no', '39', '.va'),
(242, 'VE', 'Venezuela', 'Bolivarian Republic of Venezuela', 'VEN', '862', 'yes', '58', '.ve'),
(243, 'VN', 'Vietnam', 'Socialist Republic of Vietnam', 'VNM', '704', 'yes', '84', '.vn'),
(244, 'VG', 'Virgin Islands, British', 'British Virgin Islands', 'VGB', '092', 'no', '1+284', '.vg'),
(245, 'VI', 'Virgin Islands, US', 'Virgin Islands of the United States', 'VIR', '850', 'no', '1+340', '.vi'),
(246, 'WF', 'Wallis and Futuna', 'Wallis and Futuna', 'WLF', '876', 'no', '681', '.wf'),
(247, 'EH', 'Western Sahara', 'Western Sahara', 'ESH', '732', 'no', '212', '.eh'),
(248, 'YE', 'Yemen', 'Republic of Yemen', 'YEM', '887', 'yes', '967', '.ye'),
(249, 'ZM', 'Zambia', 'Republic of Zambia', 'ZMB', '894', 'yes', '260', '.zm'),
(250, 'ZW', 'Zimbabwe', 'Republic of Zimbabwe', 'ZWE', '716', 'yes', '263', '.zw');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_block`
--

CREATE TABLE `deposit_block` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fee` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `net_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `amount_coin` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `txid` varchar(225) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `rate` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `address` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_deduct`
--

CREATE TABLE `fund_deduct` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `remark` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fund_transfer`
--

CREATE TABLE `fund_transfer` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `from_uid` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `tamt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fee` decimal(11,2) NOT NULL DEFAULT 0.00,
  `datetime` datetime NOT NULL,
  `remark` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fund_transfer`
--

INSERT INTO `fund_transfer` (`recid`, `uid`, `from_uid`, `amount`, `tamt`, `fee`, `datetime`, `remark`, `type`, `status`) VALUES
(1, 534191, 0, 10000.00000000, 0.00000000, 0.00, '2026-08-29 09:20:44', 'hqbjda', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hot_news`
--

CREATE TABLE `hot_news` (
  `recid` int(11) NOT NULL,
  `hot_news` text NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `hot_news`
--

INSERT INTO `hot_news` (`recid`, `hot_news`, `image`, `datetime`, `status`) VALUES
(1, '<p> </p>\r\n\r\n<p> </p>\r\n', '6816228bf2415.jpeg', '2024-09-04 09:03:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `income_binary`
--

CREATE TABLE `income_binary` (
  `recid` bigint(20) NOT NULL,
  `uid` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `datetime` datetime NOT NULL,
  `pair_left` decimal(11,2) NOT NULL,
  `pair_right` decimal(11,2) NOT NULL,
  `matching` decimal(11,2) NOT NULL,
  `left_carry` decimal(11,2) NOT NULL,
  `right_carry` decimal(11,2) NOT NULL,
  `current_left` decimal(11,2) NOT NULL,
  `current_right` decimal(11,2) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `flash_out` decimal(11,2) NOT NULL,
  `self_bv` decimal(11,2) NOT NULL,
  `statusc` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_direct`
--

CREATE TABLE `income_direct` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `from_uid` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 0,
  `pool` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `ipid` int(11) NOT NULL DEFAULT 0,
  `iamount` decimal(20,8) NOT NULL DEFAULT 0.00000000
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_growth`
--

CREATE TABLE `income_growth` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `iid` int(11) NOT NULL DEFAULT 0,
  `stage` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `datetime` datetime NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `percentage` decimal(11,2) NOT NULL DEFAULT 0.00,
  `p1` decimal(10,0) NOT NULL DEFAULT 0,
  `p2` decimal(10,0) NOT NULL DEFAULT 0,
  `p3` decimal(10,0) NOT NULL DEFAULT 0,
  `p4` decimal(10,0) NOT NULL DEFAULT 0,
  `p5` decimal(10,0) NOT NULL DEFAULT 0,
  `iamount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `binary_status` tinyint(4) NOT NULL DEFAULT 0,
  `is_booster` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `income_growth`
--

INSERT INTO `income_growth` (`recid`, `uid`, `iid`, `stage`, `days`, `amount`, `datetime`, `type`, `status`, `percentage`, `p1`, `p2`, `p3`, `p4`, `p5`, `iamount`, `binary_status`, `is_booster`) VALUES
(1, 100, 3, 0, 1, 0.20000000, '2026-08-27 15:06:32', 0, 0, 0.20, 0, 0, 0, 0, 0, 100.00000000, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `income_level`
--

CREATE TABLE `income_level` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `from_uid` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `lbv` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `datetime` datetime NOT NULL,
  `level` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `c_l_datetime` datetime NOT NULL,
  `extend` tinyint(4) NOT NULL DEFAULT 0,
  `status_d` tinyint(4) NOT NULL DEFAULT 0,
  `quick_pay` tinyint(4) NOT NULL DEFAULT 0,
  `uid2` int(11) NOT NULL DEFAULT 0,
  `pool` int(11) NOT NULL DEFAULT 0,
  `wamt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `uamt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `camt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `samt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `ipid` int(11) NOT NULL DEFAULT 0,
  `iamount` decimal(20,8) NOT NULL DEFAULT 0.00000000
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_reward`
--

CREATE TABLE `income_reward` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `reward` tinyint(4) NOT NULL,
  `datetime` datetime NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_royalty`
--

CREATE TABLE `income_royalty` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `datetime` datetime NOT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `days` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `tamt` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `tid` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `ipid` int(11) NOT NULL,
  `amount` decimal(20,10) NOT NULL DEFAULT 0.0000000000,
  `amount2` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `amount_coin` decimal(20,10) NOT NULL DEFAULT 0.0000000000,
  `amount_booster` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `amount_booster_direct` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `datetime` datetime NOT NULL,
  `rdatetime` datetime NOT NULL,
  `days` int(11) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `uid2` int(11) NOT NULL DEFAULT 0,
  `binary_status` tinyint(4) NOT NULL DEFAULT 0,
  `status2` tinyint(4) NOT NULL DEFAULT 0,
  `trade_status` int(11) NOT NULL DEFAULT 0,
  `btc_hash` varchar(255) NOT NULL DEFAULT '',
  `bonus` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `wtxid` varchar(255) NOT NULL DEFAULT '',
  `wdatetime` datetime NOT NULL,
  `wstatus` tinyint(4) NOT NULL DEFAULT 0,
  `statusc` tinyint(4) NOT NULL DEFAULT 0,
  `invest_hour` int(11) DEFAULT NULL,
  `exchange_pair` varchar(50) DEFAULT NULL COMMENT 'Trading pair like BTC/USDT',
  `exchange_coin` varchar(50) DEFAULT NULL COMMENT 'Selected cryptocurrency',
  `is_closed` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`recid`, `uid`, `ipid`, `amount`, `amount2`, `amount_coin`, `amount_booster`, `amount_booster_direct`, `datetime`, `rdatetime`, `days`, `type`, `status`, `uid2`, `binary_status`, `status2`, `trade_status`, `btc_hash`, `bonus`, `wtxid`, `wdatetime`, `wstatus`, `statusc`, `invest_hour`, `exchange_pair`, `exchange_coin`, `is_closed`) VALUES
(2, 100, 1, 50.0000000000, 50.00000000, 50.0000000000, 0.00000000, 0.00000000, '2026-08-27 14:40:29', '0000-00-00 00:00:00', 0, 0, 0, 100, 0, 0, 1, '', 50.00000000, '', '0000-00-00 00:00:00', 0, 0, 0, '', '', 0),
(3, 100, 2, 100.0000000000, 100.00000000, 100.0000000000, 0.00000000, 0.00000000, '2026-08-27 15:04:22', '0000-00-00 00:00:00', 1, 0, 0, 100, 0, 0, 0, '', 100.00000000, '', '0000-00-00 00:00:00', 0, 0, 0, 'binance-bybit', 'ethereum', 0),
(4, 534191, 1, 50.0000000000, 50.00000000, 50.0000000000, 0.00000000, 0.00000000, '2026-08-29 09:21:05', '0000-00-00 00:00:00', 0, 0, 0, 534191, 0, 0, 1, '', 50.00000000, '', '0000-00-00 00:00:00', 0, 0, 0, '', '', 0),
(5, 534191, 2, 5000.0000000000, 5000.00000000, 5000.0000000000, 0.00000000, 0.00000000, '2026-08-29 09:21:39', '0000-00-00 00:00:00', 0, 0, 0, 534191, 0, 0, 1, '', 5000.00000000, '', '0000-00-00 00:00:00', 0, 0, 0, 'binance-bybit', 'ripple', 0);

-- --------------------------------------------------------

--
-- Table structure for table `investments_plan`
--

CREATE TABLE `investments_plan` (
  `recid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `line1` varchar(255) NOT NULL DEFAULT '',
  `line2` varchar(255) NOT NULL DEFAULT '',
  `amount_from` decimal(20,10) NOT NULL DEFAULT 0.0000000000,
  `amount_to` decimal(20,10) NOT NULL DEFAULT 0.0000000000,
  `percentage` decimal(11,2) NOT NULL DEFAULT 0.00,
  `percentage_to` decimal(11,2) NOT NULL DEFAULT 0.00,
  `days` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `daily` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `action` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1. active\r\n,0. Deactive'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `investments_plan`
--

INSERT INTO `investments_plan` (`recid`, `title`, `description`, `line1`, `line2`, `amount_from`, `amount_to`, `percentage`, `percentage_to`, `days`, `type`, `daily`, `status`, `action`) VALUES
(1, 'Bot Activation Account', 'Account/bot activation only. No ROI or MLM income.', 'Activation Only', 'No ROI / No MLM', 50.0000000000, 50.0000000000, 0.00, 0.00, 0, 0, 1, 0, 1),
(2, 'Silver Package', '6% Monthly Trading ROI | Max Capping 200% (300% with 6 directs if investment $100-$500)', '6% Monthly ROI', 'Capping 200%', 100.0000000000, 9999.0000000000, 6.00, 6.00, 9999, 1, 1, 0, 1),
(3, 'Gold Package', '7% Monthly Trading ROI | Max Capping 300%', '7% Monthly ROI', 'Capping 300%', 10000.0000000000, 1000000.0000000000, 7.00, 7.00, 9999, 2, 1, 0, 1),
(4, 'Bot Subscription', '', '', '', 125.0000000000, 125.0000000000, 0.00, 0.00, 0, 3, 0, 1, 0),
(5, 'Diamond Drive', '', '', '', 5001.0000000000, 5001.0000000000, 1.00, 2.60, 365, 4, 1, 1, 0),
(6, 'Legacy League', '', '', '', 10001.0000000000, 10001.0000000000, 1.50, 2.70, 365, 5, 1, 1, 0),
(7, 'Elite Pack', '', '', '', 25500.0000000000, 25500.0000000000, 1.50, 2.80, 365, 6, 1, 1, 0),
(8, 'Royal Pack', '', '', '', 51000.0000000000, 51000.0000000000, 1.50, 3.00, 365, 7, 1, 1, 0),
(9, 'Crown Pack', '', '', '', 100000.0000000000, 100000.0000000000, 1.80, 0.00, 365, 8, 1, 1, 0),
(10, 'Stacking 5000$', '', '', '', 5000.0000000000, 5000.0000000000, 12.00, 0.00, 17, 9, 1, 1, 0),
(11, 'Silver Matrix 2 - 10000 TRX', '3% Monthly ROI', '', '', 10000.0000000000, 10000.0000000000, 0.00, 0.00, 0, 10, 1, 1, 0),
(12, 'Gold Matrix - 50,000 TRX', '3% Monthly ROI', '', '', 50000.0000000000, 50000.0000000000, 0.00, 0.00, 0, 11, 1, 1, 0),
(13, 'Gold Matrix 2 - 1,00,000 TRX', '3% Monthly ROI', '', '', 100000.0000000000, 100000.0000000000, 0.00, 0.00, 0, 12, 1, 1, 0),
(14, 'Ruby Matrix - 1,00,000 TRX', '3% Monthly ROI', '', '', 100000.0000000000, 100000.0000000000, 0.00, 0.00, 0, 13, 1, 1, 0),
(15, 'Ruby Matrix 2 - 2,00,000 TRX', '3% Monthly ROI', '', '', 200000.0000000000, 200000.0000000000, 0.00, 0.00, 0, 14, 1, 1, 0),
(16, 'Royal Matrix - 5,00,000 TRX', '3% Monthly ROI', '', '', 500000.0000000000, 500000.0000000000, 0.00, 0.00, 0, 15, 1, 1, 0),
(17, 'Royal Matrix 2 - 10,00,000 TRX', '3% Monthly ROI', '', '', 1000000.0000000000, 1000000.0000000000, 0.00, 0.00, 0, 16, 1, 1, 0),
(18, 'Crown Matrix - 50,00,000 TRX', '3% Monthly ROI', '', '', 5000000.0000000000, 5000000.0000000000, 0.00, 0.00, 0, 17, 1, 1, 0),
(19, 'Crown Matrix 2 - 1,00,00,000 TRX', '3% Monthly ROI', '', '', 10000000.0000000000, 10000000.0000000000, 0.00, 0.00, 0, 18, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `recid` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `filename` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `read` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`recid`, `sender`, `receiver`, `subject`, `message`, `filename`, `datetime`, `read`, `type`, `status`) VALUES
(10, 0, 566768, 'User', 'Your user id is not active', '', '2026-04-02 13:14:04', 1, 0, 0),
(2, 0, 784794, 'Payment', 'The withdrawal will be processed between Monday and Friday dear', '', '2026-02-08 19:13:14', 1, 0, 0),
(9, 566768, 0, 'Withdrawal', 'Sir I have withdrawal amount 10 usdt but it is not credited to my binanc wallet', '', '2026-04-01 18:38:15', 1, 0, 0),
(8, 0, 251790, 'Mobile no Updated', 'Your mobile number has changed, check it on this user no TB251790', '', '2026-02-23 08:25:09', 1, 0, 0),
(7, 251790, 0, 'Change of mobile number', 'Sir I want to change my mobile number as 9957574518 instead of 7002576843.\r\n Sir please do as request thank.', '', '2026-02-22 21:00:26', 1, 0, 0),
(11, 566768, 0, 'Activate user id', 'Sir I have deposited 100$ and my id is already active', '', '2026-04-03 21:25:04', 1, 0, 0),
(12, 570511, 0, 'Matching income', 'Hello sir please check mY fist matching income not show my id 570511', '', '2026-04-06 21:07:37', 1, 0, 0),
(13, 0, 570511, 'Bonus', 'You will be updated please give it little time', '', '2026-04-07 06:17:11', 1, 0, 0),
(14, 570511, 0, 'Reword', 'Please sir my first reward not received myid 570511', '', '2026-04-07 11:28:33', 1, 0, 0),
(15, 570511, 0, 'Reword', 'Hello sir my first reward not received', '', '2026-04-08 13:32:48', 1, 0, 0),
(16, 0, 570511, 'Reward', 'You have not fulfilled the conditions.', '', '2026-04-08 22:41:48', 1, 0, 0),
(17, 570511, 0, 'Reword', 'Total bussness 3000 $ sir \r\n\r\nStrong leg 1000 $ other leg 2000 $\r\n\r\nNot ricive reword', '', '2026-04-09 10:15:44', 1, 0, 0),
(18, 570511, 0, 'Widroval', 'Sir 10$ ka widroval laga ya tha vo success full dikha raha hai par ricive nahi hua hai sir', '', '2026-04-10 08:06:40', 1, 0, 0),
(19, 683005, 0, 'Add widrowal ricive address add', 'Please sir my ricive address add\r\n\r\n0xD482559b6A0A471C8Af3c69acE958A467E5482c3\r\n\r\nMy id no 683005', '', '2026-04-10 14:20:58', 1, 0, 0),
(20, 570511, 0, 'Reword', 'Hello sir 4000$ bussness my reward not showing', '', '2026-04-12 06:31:41', 1, 0, 0),
(21, 0, 570511, 'Bonus', 'Please tak some time and update you after checking', '', '2026-04-13 05:56:29', 1, 0, 0),
(22, 405830, 0, 'Address change', 'Please sir id address change my id number 405830\r\n\r\n0x3ee789Ea230715A66AD4f84C493d7b95b7376EcE', '', '2026-04-14 20:21:00', 0, 0, 0),
(23, 570511, 0, 'Solution', 'Sir my id problem solution', '', '2026-04-14 20:53:04', 0, 0, 0),
(24, 405830, 0, 'Address change', 'Please sir my id address update \r\n\r\n0x3ee789Ea230715A66AD4f84C493d7b95b7376EcE', '', '2026-04-15 16:16:19', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `recharge`
--

CREATE TABLE `recharge` (
  `recid` bigint(20) NOT NULL,
  `uid` int(11) NOT NULL,
  `number` varchar(20) NOT NULL,
  `operator` varchar(25) NOT NULL,
  `amount` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` varchar(100) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `recharge_income` decimal(11,2) NOT NULL,
  `user_income` decimal(11,2) NOT NULL,
  `upline_income` decimal(11,2) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `api_balance` decimal(11,2) NOT NULL,
  `balance` decimal(11,2) NOT NULL,
  `customer_account_no` varchar(100) NOT NULL DEFAULT '',
  `cycle` varchar(100) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `std` varchar(255) NOT NULL DEFAULT '',
  `other` varchar(255) NOT NULL DEFAULT '',
  `other2` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smart_contract_batches`
--

CREATE TABLE `smart_contract_batches` (
  `batch_id` int(11) NOT NULL,
  `tx_hash` varchar(66) DEFAULT NULL,
  `admin_address` varchar(42) NOT NULL,
  `total_addresses` int(11) NOT NULL,
  `total_amount` decimal(20,8) NOT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '0=Pending, 1=Success, 2=Failed',
  `gas_used` varchar(20) DEFAULT NULL,
  `block_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `withdrawal_ids` text NOT NULL COMMENT 'JSON array of withdrawal recids',
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `login_id` varchar(100) NOT NULL DEFAULT '',
  `refer_id` int(11) NOT NULL,
  `placement_id` int(11) NOT NULL,
  `placement_id2` int(11) NOT NULL DEFAULT 0,
  `placement_id3` int(11) NOT NULL DEFAULT 0,
  `placement_id4` int(11) NOT NULL DEFAULT 0,
  `placement_id5` int(11) NOT NULL DEFAULT 0,
  `placement_id6` int(11) NOT NULL DEFAULT 0,
  `placement_id7` int(11) NOT NULL DEFAULT 0,
  `position` varchar(2) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(6) NOT NULL,
  `email` varchar(50) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_time` datetime DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `country` char(2) NOT NULL,
  `transaction_password` varchar(50) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `reward` tinyint(4) NOT NULL,
  `rank` tinyint(4) NOT NULL,
  `wallet` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `wallet_topup` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `wallet_token` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `balance` decimal(11,2) NOT NULL,
  `topup` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `topup_datetime` datetime NOT NULL,
  `ex_date` date NOT NULL,
  `package` int(11) NOT NULL DEFAULT 0,
  `stage` tinyint(4) NOT NULL DEFAULT 0,
  `upgrade` tinyint(4) NOT NULL DEFAULT 1,
  `wallet_promo` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `wallet_admin` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `status_recharge` tinyint(4) NOT NULL DEFAULT 0,
  `bitcoin` varchar(255) NOT NULL DEFAULT '',
  `block_address` varchar(255) NOT NULL DEFAULT '',
  `block_label` varchar(255) NOT NULL DEFAULT '',
  `block_user_id` varchar(255) NOT NULL DEFAULT '',
  `bnb_address` varchar(255) NOT NULL DEFAULT '',
  `usdt_address` varchar(255) NOT NULL DEFAULT '',
  `pool` int(11) NOT NULL DEFAULT 0,
  `pt` datetime NOT NULL,
  `pt2` datetime NOT NULL,
  `pt3` datetime NOT NULL,
  `pt4` datetime NOT NULL,
  `pt5` datetime NOT NULL,
  `pt6` datetime NOT NULL,
  `pt7` datetime NOT NULL,
  `is_duplicate` tinyint(4) NOT NULL DEFAULT 0,
  `wallet_address` varchar(255) NOT NULL DEFAULT '',
  `royalty` tinyint(4) NOT NULL DEFAULT 0,
  `royalty2` int(11) NOT NULL DEFAULT 0,
  `royalty3` int(11) NOT NULL DEFAULT 0,
  `teamb` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `teamc` int(11) NOT NULL DEFAULT 0,
  `teamb2` decimal(11,2) NOT NULL DEFAULT 0.00,
  `tbl` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `tbr` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `pay_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pay_privatekey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `got` int(11) DEFAULT 0,
  `trade_active` int(11) NOT NULL DEFAULT 0,
  `trade_status` int(11) NOT NULL DEFAULT 0,
  `verified` int(11) NOT NULL DEFAULT 0,
  `invest_count` int(11) NOT NULL DEFAULT 0,
  `trade_status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`recid`, `uid`, `login_id`, `refer_id`, `placement_id`, `placement_id2`, `placement_id3`, `placement_id4`, `placement_id5`, `placement_id6`, `placement_id7`, `position`, `password`, `name`, `dob`, `gender`, `email`, `otp`, `otp_time`, `phone`, `mobile`, `address`, `city`, `state`, `country`, `transaction_password`, `datetime`, `status`, `type`, `reward`, `rank`, `wallet`, `wallet_topup`, `wallet_token`, `balance`, `topup`, `topup_datetime`, `ex_date`, `package`, `stage`, `upgrade`, `wallet_promo`, `wallet_admin`, `status_recharge`, `bitcoin`, `block_address`, `block_label`, `block_user_id`, `bnb_address`, `usdt_address`, `pool`, `pt`, `pt2`, `pt3`, `pt4`, `pt5`, `pt6`, `pt7`, `is_duplicate`, `wallet_address`, `royalty`, `royalty2`, `royalty3`, `teamb`, `teamc`, `teamb2`, `tbl`, `tbr`, `pay_address`, `pay_privatekey`, `got`, `trade_active`, `trade_status`, `verified`, `invest_count`, `trade_status_updated_at`) VALUES
(1, 100, 'member', 0, 0, 0, 0, 0, 0, 0, 0, '', 'fc942d89d295b0a600a534d2ccfdd82a33fa876b', 'member', '2026-02-03', 'Male', 'dev3.brt@gmail.com', '', '2026-08-29 09:12:56', '', '', '', '', '', 'IN', 'fc942d89d295b0a600a534d2ccfdd82a33fa876b', '2019-03-28 00:00:00', 0, 0, 0, 0, 0.00000000, 0.00000000, 0.00000000, 0.00, 150.00000000, '2026-08-27 14:40:29', '2025-10-01', 2, 0, 0, 0.00000000, 0.00000000, 0, 'wdq', '', '', '', 'member', '', 0, '2024-05-10 22:45:24', '2024-05-18 12:10:15', '2024-05-10 04:50:58', '2024-05-10 04:51:04', '2024-05-10 04:51:10', '2024-05-10 04:51:15', '2024-05-10 04:51:19', 0, '', 0, 0, 0, 0.00000000, 0, 0.00, 0.00000000, 0.00000000, '0x32ff5ebaef38e8777f75d2d4cc1783341bbe5c71', '0xbe5e67e6a2ea4526f56ddef074634aea5ae2bd17c1ab1e248c126d37275a2219', 0, 0, 0, 0, 3, '2026-08-29 03:43:23'),
(2, 534191, 'TB534191', 100, 100, 0, 0, 0, 0, 0, 0, '', 'b096d56b9f6ae95eac15ca231027d8750a3ce4f1', 'testuser', '2026-08-29', 'Male', 'testuser@gmail.com', NULL, NULL, '', '9876543210', '', '', '', 'IN', 'b096d56b9f6ae95eac15ca231027d8750a3ce4f1', '2026-08-29 09:18:58', 0, 0, 0, 0, 0.00000000, 4950.00000000, 0.00000000, 0.00, 5050.00000000, '2026-08-29 09:21:05', '0000-00-00', 2, 0, 1, 0.00000000, 0.00000000, 0, '', '', '', '', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 0, 0, 0.00000000, 0, 0.00, 0.00000000, 0.00000000, '', '', 0, 0, 0, 0, 2, '2026-08-29 03:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `userre`
--

CREATE TABLE `userre` (
  `recid` int(11) NOT NULL,
  `placement_id` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL DEFAULT 0,
  `placement_uid` int(11) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL,
  `pool` int(11) NOT NULL DEFAULT 0,
  `is_re` tinyint(4) NOT NULL DEFAULT 0,
  `color` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_detail`
--

CREATE TABLE `user_login_detail` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `ip` varchar(50) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user_login_detail`
--

INSERT INTO `user_login_detail` (`recid`, `uid`, `datetime`, `ip`, `type`, `status`) VALUES
(1, 100, '2026-01-31 17:59:53', '42.108.237.31', 0, 0),
(2, 100, '2026-01-31 18:17:32', '42.108.237.31', 0, 0),
(3, 100, '2026-01-31 18:17:42', '42.108.237.31', 0, 0),
(4, 418062, '2026-01-31 19:59:39', '42.108.238.200', 0, 0),
(5, 418062, '2026-02-02 09:56:01', '42.108.226.15', 0, 0),
(6, 607309, '2026-02-02 10:02:45', '42.108.226.15', 0, 0),
(7, 418062, '2026-02-03 09:14:10', '42.108.233.176', 0, 0),
(8, 418062, '2026-02-03 09:21:18', '42.108.233.176', 0, 0),
(9, 418062, '2026-02-03 11:21:09', '42.108.236.76', 0, 0),
(10, 418062, '2026-02-03 11:21:29', '42.108.236.76', 0, 0),
(11, 100, '2026-02-03 12:41:27', '152.56.10.134', 0, 0),
(12, 100, '2026-02-03 12:44:11', '152.56.10.134', 0, 0),
(13, 100, '2026-02-03 12:56:42', '152.56.10.24', 0, 0),
(14, 100, '2026-02-03 15:59:04', '152.56.10.225', 0, 0),
(15, 100, '2026-02-05 13:45:25', '157.49.36.39', 0, 0),
(16, 100, '2026-02-05 14:24:33', '42.108.228.97', 0, 0),
(17, 100, '2026-02-05 14:56:36', '42.108.228.97', 0, 0),
(18, 100, '2026-02-05 15:11:16', '42.108.237.229', 0, 0),
(19, 100, '2026-02-05 15:29:01', '42.108.239.175', 0, 0),
(20, 100421, '2026-02-05 16:14:18', '42.108.239.96', 0, 0),
(21, 784794, '2026-02-05 20:16:34', '106.221.249.219', 0, 0),
(22, 784794, '2026-02-05 20:39:54', '106.221.245.219', 0, 0),
(23, 784794, '2026-02-05 21:41:04', '106.221.255.219', 0, 0),
(24, 433479, '2026-02-05 21:42:32', '223.228.145.58', 0, 0),
(25, 217260, '2026-02-06 08:13:47', '42.108.231.150', 0, 0),
(26, 784794, '2026-02-06 08:52:48', '106.221.251.243', 0, 0),
(27, 784794, '2026-02-06 09:17:22', '106.221.251.243', 0, 0),
(28, 784794, '2026-02-06 09:18:36', '106.221.251.243', 0, 0),
(29, 257861, '2026-02-06 09:28:58', '152.59.154.113', 0, 0),
(30, 257861, '2026-02-06 09:30:03', '152.59.154.113', 0, 0),
(31, 784794, '2026-02-06 10:28:49', '106.221.255.243', 0, 0),
(32, 784794, '2026-02-06 12:01:20', '106.221.244.243', 0, 0),
(33, 615868, '2026-02-06 12:17:53', '106.221.251.39', 0, 0),
(34, 217260, '2026-02-06 12:34:27', '27.97.86.33', 0, 0),
(35, 784794, '2026-02-06 14:12:55', '106.221.245.243', 0, 0),
(36, 784794, '2026-02-06 16:05:35', '106.221.242.243', 0, 0),
(37, 784794, '2026-02-06 17:16:32', '106.221.242.5', 0, 0),
(38, 217260, '2026-02-06 18:59:31', '1.187.150.8', 0, 0),
(39, 784794, '2026-02-06 20:25:23', '106.221.245.5', 0, 0),
(40, 219197, '2026-02-06 21:17:19', '47.15.86.28', 0, 0),
(41, 520874, '2026-02-06 21:28:30', '49.37.241.116', 0, 0),
(42, 784794, '2026-02-07 00:16:52', '223.190.181.210', 0, 0),
(43, 784794, '2026-02-07 00:16:53', '223.190.181.210', 0, 0),
(44, 433479, '2026-02-07 01:25:05', '106.220.59.109', 0, 0),
(45, 784794, '2026-02-07 09:15:00', '223.190.181.210', 0, 0),
(46, 784794, '2026-02-07 09:15:02', '223.190.181.210', 0, 0),
(47, 219197, '2026-02-07 09:25:29', '47.15.107.158', 0, 0),
(48, 610129, '2026-02-07 09:27:50', '1.38.44.4', 0, 0),
(49, 100, '2026-02-07 10:34:29', '157.49.36.39', 0, 0),
(50, 784794, '2026-02-07 11:08:55', '223.190.181.210', 0, 0),
(51, 217260, '2026-02-07 12:33:08', '42.108.239.243', 0, 0),
(52, 433479, '2026-02-07 13:36:42', '106.221.242.207', 0, 0),
(53, 784794, '2026-02-07 13:43:52', '223.190.181.210', 0, 0),
(54, 615868, '2026-02-07 13:48:34', '223.228.151.196', 0, 0),
(55, 217260, '2026-02-07 14:54:26', '42.108.238.60', 0, 0),
(56, 673638, '2026-02-07 15:17:27', '42.108.238.60', 0, 0),
(57, 784794, '2026-02-07 15:23:28', '223.190.181.210', 0, 0),
(58, 784794, '2026-02-07 15:37:13', '223.190.181.210', 0, 0),
(59, 257861, '2026-02-07 15:46:36', '223.190.181.210', 0, 0),
(60, 784794, '2026-02-07 15:48:34', '223.190.181.210', 0, 0),
(61, 257861, '2026-02-07 15:57:25', '223.190.181.210', 0, 0),
(62, 784794, '2026-02-07 16:21:09', '223.190.181.210', 0, 0),
(63, 257861, '2026-02-07 17:22:05', '152.56.134.42', 0, 0),
(64, 257861, '2026-02-07 17:27:50', '152.56.134.42', 0, 0),
(65, 257861, '2026-02-07 17:29:02', '152.56.134.42', 0, 0),
(66, 784794, '2026-02-07 19:03:19', '223.190.181.210', 0, 0),
(67, 784794, '2026-02-07 19:03:19', '223.190.181.210', 0, 0),
(68, 217260, '2026-02-07 20:52:19', '42.108.239.186', 0, 0),
(69, 433479, '2026-02-08 06:55:19', '106.220.58.67', 0, 0),
(70, 433479, '2026-02-08 07:21:09', '106.221.240.140', 0, 0),
(71, 257861, '2026-02-08 07:55:33', '152.59.147.212', 0, 0),
(72, 784794, '2026-02-08 08:56:31', '47.31.123.198', 0, 0),
(73, 217260, '2026-02-08 10:23:56', '42.108.236.185', 0, 0),
(74, 217260, '2026-02-08 11:23:44', '42.108.236.185', 0, 0),
(75, 784794, '2026-02-08 11:33:50', '223.237.141.2', 0, 0),
(76, 735913, '2026-02-08 12:50:34', '157.49.183.90', 0, 0),
(77, 735913, '2026-02-08 15:02:22', '157.49.182.2', 0, 0),
(78, 217260, '2026-02-08 18:28:57', '42.108.237.208', 0, 0),
(79, 433479, '2026-02-08 20:51:52', '223.176.61.127', 0, 0),
(80, 217260, '2026-02-08 22:59:21', '42.108.238.39', 0, 0),
(81, 784794, '2026-02-09 01:28:57', '223.237.130.137', 0, 0),
(82, 784794, '2026-02-09 07:37:59', '223.237.141.204', 0, 0),
(83, 217260, '2026-02-09 08:53:58', '42.108.236.26', 0, 0),
(84, 784794, '2026-02-09 09:19:08', '223.237.134.204', 0, 0),
(85, 615868, '2026-02-09 11:17:32', '106.192.111.200', 0, 0),
(86, 582950, '2026-02-09 11:43:01', '152.59.169.145', 0, 0),
(87, 217260, '2026-02-09 11:53:45', '42.108.238.127', 0, 0),
(88, 784794, '2026-02-09 12:02:54', '223.237.143.204', 0, 0),
(89, 784794, '2026-02-09 13:51:52', '223.237.132.204', 0, 0),
(90, 217260, '2026-02-09 17:01:23', '42.108.239.250', 0, 0),
(91, 217260, '2026-02-09 19:04:26', '42.108.237.193', 0, 0),
(92, 784794, '2026-02-09 20:57:07', '223.237.142.146', 0, 0),
(93, 147405, '2026-02-09 21:06:37', '152.58.187.154', 0, 0),
(94, 217260, '2026-02-09 22:58:54', '42.108.237.195', 0, 0),
(95, 735913, '2026-02-10 00:14:12', '157.49.53.221', 0, 0),
(96, 217260, '2026-02-10 02:12:03', '42.108.237.247', 0, 0),
(97, 433479, '2026-02-10 02:27:34', '223.228.159.22', 0, 0),
(98, 217260, '2026-02-10 06:23:39', '42.108.236.179', 0, 0),
(99, 147405, '2026-02-10 07:18:11', '223.237.130.99', 0, 0),
(100, 784794, '2026-02-10 07:18:51', '223.237.130.99', 0, 0),
(101, 433479, '2026-02-10 07:20:57', '223.228.154.231', 0, 0),
(102, 784794, '2026-02-10 09:30:35', '223.237.134.0', 0, 0),
(103, 147405, '2026-02-10 09:32:18', '223.237.134.0', 0, 0),
(104, 147405, '2026-02-10 11:53:26', '223.237.134.0', 0, 0),
(105, 784794, '2026-02-10 12:02:09', '223.237.134.0', 0, 0),
(106, 147405, '2026-02-10 12:03:48', '223.237.134.0', 0, 0),
(107, 784794, '2026-02-10 12:36:14', '223.237.138.0', 0, 0),
(108, 784794, '2026-02-10 13:01:33', '223.237.134.0', 0, 0),
(109, 147405, '2026-02-10 14:09:28', '223.237.136.0', 0, 0),
(110, 147405, '2026-02-10 14:09:28', '223.237.136.0', 0, 0),
(111, 784794, '2026-02-10 14:11:23', '223.237.136.0', 0, 0),
(112, 735913, '2026-02-10 15:49:57', '157.49.61.109', 0, 0),
(113, 147405, '2026-02-10 16:18:01', '223.235.101.48', 0, 0),
(114, 784794, '2026-02-10 17:11:13', '223.235.101.48', 0, 0),
(115, 784794, '2026-02-10 17:12:50', '223.235.101.48', 0, 0),
(116, 217260, '2026-02-10 17:29:44', '42.108.235.254', 0, 0),
(117, 735913, '2026-02-10 19:15:30', '157.49.65.171', 0, 0),
(118, 520874, '2026-02-10 20:48:52', '152.57.113.77', 0, 0),
(119, 784794, '2026-02-10 21:00:58', '223.237.138.92', 0, 0),
(120, 784794, '2026-02-10 21:00:59', '223.237.138.92', 0, 0),
(121, 784794, '2026-02-11 07:09:45', '223.237.142.255', 0, 0),
(122, 784794, '2026-02-11 07:09:46', '223.237.142.255', 0, 0),
(123, 147405, '2026-02-11 07:12:16', '223.237.142.255', 0, 0),
(124, 784794, '2026-02-11 09:05:43', '223.237.128.255', 0, 0),
(125, 257861, '2026-02-11 09:23:34', '152.59.154.45', 0, 0),
(126, 784794, '2026-02-11 10:54:50', '223.237.134.208', 0, 0),
(127, 147405, '2026-02-11 10:56:02', '223.237.134.208', 0, 0),
(128, 257861, '2026-02-11 10:56:19', '223.237.134.208', 0, 0),
(129, 784794, '2026-02-11 10:58:19', '223.237.134.208', 0, 0),
(130, 735913, '2026-02-11 11:29:52', '157.49.176.51', 0, 0),
(131, 784794, '2026-02-11 12:57:01', '223.237.142.208', 0, 0),
(132, 147405, '2026-02-11 16:44:10', '223.237.138.169', 0, 0),
(133, 147405, '2026-02-11 17:45:53', '223.235.101.48', 0, 0),
(134, 217260, '2026-02-11 18:21:25', '106.192.132.189', 0, 0),
(135, 616965, '2026-02-11 18:52:55', '106.192.132.189', 0, 0),
(136, 147405, '2026-02-11 21:25:56', '223.237.136.113', 0, 0),
(137, 784794, '2026-02-11 21:38:33', '223.237.136.113', 0, 0),
(138, 217260, '2026-02-11 22:31:21', '106.220.163.140', 0, 0),
(139, 147405, '2026-02-12 00:12:27', '223.237.128.57', 0, 0),
(140, 784794, '2026-02-12 00:14:35', '223.237.128.57', 0, 0),
(141, 147405, '2026-02-12 00:17:47', '223.237.128.57', 0, 0),
(142, 147405, '2026-02-12 00:23:03', '223.237.128.57', 0, 0),
(143, 784794, '2026-02-12 00:24:41', '223.237.128.57', 0, 0),
(144, 784794, '2026-02-12 01:11:49', '223.237.128.57', 0, 0),
(145, 433479, '2026-02-12 07:08:39', '106.192.111.0', 0, 0),
(146, 784794, '2026-02-12 07:36:49', '223.237.139.57', 0, 0),
(147, 147405, '2026-02-12 09:13:29', '223.237.128.20', 0, 0),
(148, 784794, '2026-02-12 10:02:58', '223.237.129.20', 0, 0),
(149, 784794, '2026-02-12 10:58:58', '223.237.128.20', 0, 0),
(150, 217260, '2026-02-12 11:04:12', '42.108.238.63', 0, 0),
(151, 433479, '2026-02-12 11:26:37', '106.192.189.253', 0, 0),
(152, 217260, '2026-02-12 11:45:59', '42.108.238.63', 0, 0),
(153, 735913, '2026-02-12 12:22:20', '157.49.48.99', 0, 0),
(154, 784794, '2026-02-12 12:47:35', '223.237.135.20', 0, 0),
(155, 217260, '2026-02-12 15:11:07', '42.108.236.86', 0, 0),
(156, 784794, '2026-02-12 17:02:43', '223.235.98.154', 0, 0),
(157, 784794, '2026-02-12 17:40:38', '223.237.142.124', 0, 0),
(158, 784794, '2026-02-12 17:40:38', '223.237.142.124', 0, 0),
(159, 784794, '2026-02-12 17:40:45', '223.237.142.124', 0, 0),
(160, 217260, '2026-02-12 18:05:37', '42.108.238.145', 0, 0),
(161, 147405, '2026-02-12 18:25:33', '223.237.135.124', 0, 0),
(162, 784794, '2026-02-12 18:27:28', '223.237.135.124', 0, 0),
(163, 735913, '2026-02-12 19:24:53', '157.49.68.133', 0, 0),
(164, 217260, '2026-02-12 20:20:53', '42.108.237.51', 0, 0),
(165, 784794, '2026-02-13 03:07:34', '223.237.132.80', 0, 0),
(166, 784794, '2026-02-13 06:18:02', '223.237.134.80', 0, 0),
(167, 433479, '2026-02-13 07:49:17', '106.219.247.56', 0, 0),
(168, 784794, '2026-02-13 08:31:47', '223.237.131.59', 0, 0),
(169, 784794, '2026-02-13 08:31:48', '223.237.131.59', 0, 0),
(170, 784794, '2026-02-13 08:45:51', '223.237.136.59', 0, 0),
(171, 147405, '2026-02-13 09:01:09', '223.237.138.59', 0, 0),
(172, 784794, '2026-02-13 10:06:02', '223.237.128.59', 0, 0),
(173, 784794, '2026-02-13 10:47:49', '223.237.140.59', 0, 0),
(174, 217260, '2026-02-13 10:47:52', '42.108.237.147', 0, 0),
(175, 744285, '2026-02-13 12:11:55', '152.58.180.62', 0, 0),
(176, 784794, '2026-02-13 12:24:00', '223.237.142.59', 0, 0),
(177, 217260, '2026-02-13 12:51:46', '42.108.237.107', 0, 0),
(178, 217260, '2026-02-13 13:09:58', '42.108.238.0', 0, 0),
(179, 784794, '2026-02-13 13:47:52', '171.51.163.58', 0, 0),
(180, 784794, '2026-02-13 16:03:52', '171.51.166.188', 0, 0),
(181, 784794, '2026-02-13 16:43:45', '171.51.172.169', 0, 0),
(182, 217260, '2026-02-13 17:04:42', '42.108.237.137', 0, 0),
(183, 784794, '2026-02-13 17:07:42', '171.51.168.41', 0, 0),
(184, 784794, '2026-02-13 18:10:58', '171.51.167.222', 0, 0),
(185, 217260, '2026-02-13 18:21:32', '42.108.238.15', 0, 0),
(186, 784794, '2026-02-13 18:59:09', '223.176.23.251', 0, 0),
(187, 217260, '2026-02-13 19:45:25', '42.108.237.248', 0, 0),
(188, 735913, '2026-02-13 20:00:11', '157.49.56.103', 0, 0),
(189, 784794, '2026-02-14 06:45:38', '171.51.167.28', 0, 0),
(190, 217260, '2026-02-14 07:29:41', '42.108.239.57', 0, 0),
(191, 583836, '2026-02-14 07:35:48', '42.108.239.57', 0, 0),
(192, 433479, '2026-02-14 08:42:52', '106.192.188.240', 0, 0),
(193, 784794, '2026-02-14 10:42:25', '106.216.117.2', 0, 0),
(194, 784794, '2026-02-14 10:42:25', '106.216.117.2', 0, 0),
(195, 262638, '2026-02-14 21:33:10', '49.15.127.180', 0, 0),
(196, 262638, '2026-02-14 21:34:35', '49.15.127.180', 0, 0),
(197, 784794, '2026-02-14 21:37:31', '106.216.123.124', 0, 0),
(198, 147405, '2026-02-14 21:37:50', '106.216.123.124', 0, 0),
(199, 257861, '2026-02-14 21:41:51', '106.216.123.124', 0, 0),
(200, 433479, '2026-02-14 21:45:05', '223.228.150.218', 0, 0),
(201, 242249, '2026-02-14 22:32:32', '47.15.103.72', 0, 0),
(202, 100, '2026-02-15 05:49:05', '49.47.153.194', 0, 0),
(203, 509367, '2026-02-15 05:51:28', '49.47.153.194', 0, 0),
(204, 100, '2026-02-15 05:53:58', '49.47.153.194', 0, 0),
(205, 100, '2026-02-15 05:55:19', '49.47.153.194', 0, 0),
(206, 509367, '2026-02-15 05:56:26', '49.47.153.194', 0, 0),
(207, 100, '2026-02-15 05:56:32', '49.47.153.194', 0, 0),
(208, 509367, '2026-02-15 05:59:26', '49.47.153.194', 0, 0),
(209, 100, '2026-02-15 06:00:04', '49.47.153.194', 0, 0),
(210, 100, '2026-02-15 06:01:25', '49.47.153.194', 0, 0),
(211, 100, '2026-02-15 06:02:30', '49.47.153.194', 0, 0),
(212, 100, '2026-02-15 06:03:28', '49.47.153.194', 0, 0),
(213, 160665, '2026-02-15 06:10:27', '49.47.153.194', 0, 0),
(214, 100, '2026-02-15 06:10:35', '49.47.153.194', 0, 0),
(215, 160665, '2026-02-15 06:14:48', '49.47.153.194', 0, 0),
(216, 509367, '2026-02-15 06:16:28', '49.47.153.194', 0, 0),
(217, 100, '2026-02-15 06:18:16', '49.47.153.194', 0, 0),
(218, 100, '2026-02-15 06:18:57', '49.47.153.194', 0, 0),
(219, 100, '2026-02-15 06:20:30', '49.47.153.194', 0, 0),
(220, 100, '2026-02-15 06:22:07', '49.47.153.194', 0, 0),
(221, 100, '2026-02-15 06:23:05', '49.47.153.194', 0, 0),
(222, 217260, '2026-02-15 09:37:07', '27.97.92.196', 0, 0),
(223, 257861, '2026-02-15 09:57:43', '152.56.134.122', 0, 0),
(224, 433479, '2026-02-15 17:49:01', '117.96.144.89', 0, 0),
(225, 433479, '2026-02-15 17:49:02', '117.96.144.89', 0, 0),
(226, 100, '2026-02-16 01:06:17', '49.47.153.68', 0, 0),
(227, 509367, '2026-02-16 01:06:47', '49.47.153.68', 0, 0),
(228, 160665, '2026-02-16 01:07:50', '49.47.153.68', 0, 0),
(229, 100, '2026-02-16 01:08:21', '49.47.153.68', 0, 0),
(230, 784794, '2026-02-16 07:42:08', '106.216.120.247', 0, 0),
(231, 160665, '2026-02-16 09:26:15', '49.47.153.68', 0, 0),
(232, 509367, '2026-02-16 09:26:32', '49.47.153.68', 0, 0),
(233, 784794, '2026-02-16 10:07:01', '106.216.126.247', 0, 0),
(234, 147405, '2026-02-16 10:12:40', '106.216.126.247', 0, 0),
(235, 784794, '2026-02-16 10:14:32', '106.216.126.247', 0, 0),
(236, 217260, '2026-02-16 11:57:07', '27.97.81.90', 0, 0),
(237, 784794, '2026-02-16 13:44:47', '106.216.112.12', 0, 0),
(238, 735913, '2026-02-16 13:45:55', '157.49.80.239', 0, 0),
(239, 217260, '2026-02-16 15:28:02', '1.187.154.228', 0, 0),
(240, 784794, '2026-02-16 16:20:10', '106.216.119.10', 0, 0),
(241, 257861, '2026-02-16 19:22:59', '152.59.155.126', 0, 0),
(242, 257861, '2026-02-16 19:23:00', '152.59.155.126', 0, 0),
(243, 735913, '2026-02-16 20:51:13', '157.49.64.242', 0, 0),
(244, 548886, '2026-02-16 21:34:14', '49.36.27.92', 0, 0),
(245, 784794, '2026-02-16 22:05:09', '106.216.125.44', 0, 0),
(246, 147405, '2026-02-16 22:05:41', '106.216.125.44', 0, 0),
(247, 217260, '2026-02-16 22:06:07', '1.187.150.37', 0, 0),
(248, 784794, '2026-02-16 22:10:32', '106.216.125.44', 0, 0),
(249, 243807, '2026-02-16 22:11:01', '1.187.150.140', 0, 0),
(250, 784794, '2026-02-17 00:43:25', '106.216.125.44', 0, 0),
(251, 784794, '2026-02-17 07:22:12', '106.216.124.66', 0, 0),
(252, 147405, '2026-02-17 07:22:47', '106.216.124.66', 0, 0),
(253, 784794, '2026-02-17 07:29:23', '106.216.124.66', 0, 0),
(254, 217260, '2026-02-17 09:05:26', '27.97.93.170', 0, 0),
(255, 262638, '2026-02-17 10:59:34', '49.15.127.10', 0, 0),
(256, 509367, '2026-02-17 11:11:07', '49.47.153.169', 0, 0),
(257, 160665, '2026-02-17 11:14:43', '49.47.153.169', 0, 0),
(258, 100, '2026-02-17 11:14:55', '49.47.153.169', 0, 0),
(259, 160665, '2026-02-17 11:15:59', '49.47.153.169', 0, 0),
(260, 100, '2026-02-17 11:16:07', '49.47.153.169', 0, 0),
(261, 509367, '2026-02-17 11:16:45', '49.47.153.169', 0, 0),
(262, 100, '2026-02-17 11:16:54', '49.47.153.169', 0, 0),
(263, 509367, '2026-02-17 11:18:29', '49.47.153.169', 0, 0),
(264, 735913, '2026-02-17 13:47:02', '157.49.77.130', 0, 0),
(265, 784794, '2026-02-17 15:41:51', '106.221.37.208', 0, 0),
(266, 147405, '2026-02-17 15:45:01', '106.221.37.208', 0, 0),
(267, 548886, '2026-02-17 16:11:14', '152.56.253.255', 0, 0),
(268, 217260, '2026-02-17 16:45:54', '27.97.80.235', 0, 0),
(269, 784794, '2026-02-17 20:00:25', '106.221.37.208', 0, 0),
(270, 784794, '2026-02-18 06:58:10', '106.221.37.214', 0, 0),
(271, 147405, '2026-02-18 07:02:28', '106.221.37.214', 0, 0),
(272, 257861, '2026-02-18 07:03:25', '106.221.37.214', 0, 0),
(273, 217260, '2026-02-18 07:12:54', '27.97.94.202', 0, 0),
(274, 784794, '2026-02-18 07:51:52', '106.221.37.214', 0, 0),
(275, 242249, '2026-02-18 08:35:06', '47.15.103.72', 0, 0),
(276, 217260, '2026-02-18 08:51:53', '27.97.80.124', 0, 0),
(277, 339495, '2026-02-18 09:18:12', '27.97.87.112', 0, 0),
(278, 142993, '2026-02-18 09:30:43', '27.97.87.78', 0, 0),
(279, 623564, '2026-02-18 09:42:09', '27.97.87.115', 0, 0),
(280, 257861, '2026-02-18 10:47:59', '152.58.133.135', 0, 0),
(281, 257861, '2026-02-18 10:48:00', '152.58.133.135', 0, 0),
(282, 217260, '2026-02-18 12:28:13', '27.97.88.144', 0, 0),
(283, 784794, '2026-02-18 13:30:29', '106.221.37.198', 0, 0),
(284, 433479, '2026-02-18 15:02:12', '106.192.181.240', 0, 0),
(285, 217260, '2026-02-18 15:13:04', '27.97.92.138', 0, 0),
(286, 784794, '2026-02-18 16:16:13', '106.221.37.205', 0, 0),
(287, 698202, '2026-02-18 18:15:56', '27.97.95.47', 0, 0),
(288, 784794, '2026-02-18 19:33:21', '106.221.37.205', 0, 0),
(289, 784794, '2026-02-18 20:49:27', '106.221.37.205', 0, 0),
(290, 784794, '2026-02-18 21:41:28', '106.221.37.205', 0, 0),
(291, 784794, '2026-02-18 22:52:33', '106.221.37.205', 0, 0),
(292, 784794, '2026-02-19 05:44:57', '106.221.37.205', 0, 0),
(293, 217260, '2026-02-19 07:46:25', '27.97.81.161', 0, 0),
(294, 784794, '2026-02-19 08:49:39', '106.221.37.205', 0, 0),
(295, 147405, '2026-02-19 08:50:25', '106.221.37.205', 0, 0),
(296, 784794, '2026-02-19 08:51:09', '106.221.37.205', 0, 0),
(297, 339495, '2026-02-19 08:57:49', '27.97.81.161', 0, 0),
(298, 509367, '2026-02-19 09:01:49', '49.47.153.42', 0, 0),
(299, 100, '2026-02-19 09:02:57', '49.47.153.42', 0, 0),
(300, 100, '2026-02-19 09:04:06', '49.47.153.42', 0, 0),
(301, 217260, '2026-02-19 12:08:32', '27.97.92.78', 0, 0),
(302, 784794, '2026-02-19 14:25:31', '106.221.37.205', 0, 0),
(303, 262638, '2026-02-19 18:04:27', '157.38.248.169', 0, 0),
(304, 548886, '2026-02-19 18:46:24', '152.59.46.19', 0, 0),
(305, 262638, '2026-02-19 20:48:47', '49.15.126.223', 0, 0),
(306, 217260, '2026-02-19 21:56:10', '27.97.94.230', 0, 0),
(307, 566768, '2026-02-19 22:09:50', '49.37.101.161', 0, 0),
(308, 566768, '2026-02-19 22:57:46', '49.37.101.161', 0, 0),
(309, 784794, '2026-02-20 06:26:24', '106.221.37.205', 0, 0),
(310, 147405, '2026-02-20 06:26:57', '106.221.37.205', 0, 0),
(311, 339495, '2026-02-20 08:16:45', '27.97.92.228', 0, 0),
(312, 217260, '2026-02-20 08:19:00', '27.97.92.228', 0, 0),
(313, 404878, '2026-02-20 08:21:06', '27.97.92.228', 0, 0),
(314, 705490, '2026-02-20 08:35:02', '27.97.92.228', 0, 0),
(315, 770106, '2026-02-20 08:44:22', '27.97.94.181', 0, 0),
(316, 475003, '2026-02-20 08:49:40', '27.97.94.181', 0, 0),
(317, 404878, '2026-02-20 09:01:12', '27.97.94.181', 0, 0),
(318, 784794, '2026-02-20 09:24:59', '106.221.37.205', 0, 0),
(319, 147405, '2026-02-20 09:26:00', '106.221.37.205', 0, 0),
(320, 784794, '2026-02-20 09:30:09', '106.221.37.205', 0, 0),
(321, 735913, '2026-02-20 11:59:44', '157.49.51.133', 0, 0),
(322, 566768, '2026-02-20 12:20:12', '152.58.168.139', 0, 0),
(323, 217260, '2026-02-20 14:05:14', '1.187.145.194', 0, 0),
(324, 262638, '2026-02-20 14:09:45', '49.15.119.37', 0, 0),
(325, 339495, '2026-02-20 14:12:56', '1.187.145.194', 0, 0),
(326, 566768, '2026-02-20 14:48:15', '49.37.103.77', 0, 0),
(327, 566768, '2026-02-20 15:07:35', '49.37.103.77', 0, 0),
(328, 217260, '2026-02-20 18:42:26', '27.97.89.236', 0, 0),
(329, 566768, '2026-02-20 20:57:33', '49.37.103.243', 0, 0),
(330, 744285, '2026-02-20 22:16:46', '152.58.176.255', 0, 0),
(331, 784794, '2026-02-20 22:54:44', '106.221.37.212', 0, 0),
(332, 147405, '2026-02-20 22:55:24', '106.221.37.212', 0, 0),
(333, 784794, '2026-02-20 22:56:42', '106.221.37.212', 0, 0),
(334, 784794, '2026-02-21 06:38:07', '106.221.37.212', 0, 0),
(335, 404878, '2026-02-21 08:10:14', '27.97.84.107', 0, 0),
(336, 735913, '2026-02-21 09:32:26', '157.49.182.106', 0, 0),
(337, 566768, '2026-02-21 09:56:18', '152.58.168.171', 0, 0),
(338, 735913, '2026-02-21 10:50:53', '157.49.182.11', 0, 0),
(339, 262638, '2026-02-21 13:22:21', '49.15.118.163', 0, 0),
(340, 433479, '2026-02-22 11:28:33', '223.176.61.152', 0, 0),
(341, 784794, '2026-02-22 11:32:58', '117.96.23.97', 0, 0),
(342, 735913, '2026-02-22 11:56:12', '157.49.80.178', 0, 0),
(343, 217260, '2026-02-22 13:43:51', '27.97.87.236', 0, 0),
(344, 262638, '2026-02-22 14:35:47', '49.15.126.46', 0, 0),
(345, 744331, '2026-02-22 15:18:50', '27.97.86.208', 0, 0),
(346, 784794, '2026-02-22 16:27:21', '117.96.18.97', 0, 0),
(347, 257861, '2026-02-22 17:49:27', '152.56.135.16', 0, 0),
(348, 566768, '2026-02-22 18:43:13', '49.37.102.123', 0, 0),
(349, 251790, '2026-02-22 18:49:46', '49.37.102.123', 0, 0),
(350, 566768, '2026-02-22 18:52:14', '49.37.102.123', 0, 0),
(351, 251790, '2026-02-22 18:54:36', '49.37.102.123', 0, 0),
(352, 251790, '2026-02-22 20:55:50', '49.37.101.228', 0, 0),
(353, 784794, '2026-02-23 06:16:50', '117.96.146.80', 0, 0),
(354, 147405, '2026-02-23 06:20:13', '117.96.146.80', 0, 0),
(355, 404878, '2026-02-23 07:50:16', '106.193.229.206', 0, 0),
(356, 324037, '2026-02-23 08:02:23', '106.193.229.206', 0, 0),
(357, 147405, '2026-02-23 09:59:30', '117.96.19.80', 0, 0),
(358, 784794, '2026-02-23 10:04:23', '117.96.19.80', 0, 0),
(359, 147405, '2026-02-23 10:05:15', '117.96.19.80', 0, 0),
(360, 147405, '2026-02-23 10:08:03', '117.96.19.80', 0, 0),
(361, 784794, '2026-02-23 10:08:22', '117.96.19.80', 0, 0),
(362, 540691, '2026-02-23 10:12:22', '117.96.19.80', 0, 0),
(363, 147405, '2026-02-23 10:15:07', '117.96.19.80', 0, 0),
(364, 784794, '2026-02-23 10:19:07', '117.96.145.171', 0, 0),
(365, 147405, '2026-02-23 10:19:40', '117.96.145.171', 0, 0),
(366, 784794, '2026-02-23 10:21:49', '117.96.145.171', 0, 0),
(367, 147405, '2026-02-23 10:22:06', '117.96.145.171', 0, 0),
(368, 784794, '2026-02-23 10:24:30', '117.96.145.171', 0, 0),
(369, 557635, '2026-02-23 11:25:23', '59.184.166.47', 0, 0),
(370, 217260, '2026-02-23 12:20:12', '106.216.241.231', 0, 0),
(371, 520874, '2026-02-23 14:06:29', '157.50.195.25', 0, 0),
(372, 735913, '2026-02-23 17:11:01', '157.49.187.121', 0, 0),
(373, 540691, '2026-02-23 21:54:17', '117.96.16.214', 0, 0),
(374, 784794, '2026-02-23 21:54:36', '117.96.16.214', 0, 0),
(375, 251790, '2026-02-23 22:15:05', '49.37.102.141', 0, 0),
(376, 251790, '2026-02-23 22:15:08', '49.37.102.141', 0, 0),
(377, 566768, '2026-02-23 22:25:46', '49.37.102.141', 0, 0),
(378, 566768, '2026-02-23 22:31:40', '49.37.102.141', 0, 0),
(379, 251790, '2026-02-23 22:40:55', '49.37.102.141', 0, 0),
(380, 784794, '2026-02-24 05:05:43', '117.96.146.79', 0, 0),
(381, 566768, '2026-02-24 09:46:32', '152.58.168.5', 0, 0),
(382, 784794, '2026-02-24 10:58:52', '117.96.144.79', 0, 0),
(383, 262638, '2026-02-24 11:37:01', '49.15.119.110', 0, 0),
(384, 735913, '2026-02-24 12:32:48', '157.49.187.29', 0, 0),
(385, 217260, '2026-02-24 12:43:17', '27.97.84.245', 0, 0),
(386, 257861, '2026-02-24 12:45:47', '152.59.146.225', 0, 0),
(387, 238601, '2026-02-24 12:47:42', '103.157.37.106', 0, 0),
(388, 238601, '2026-02-24 12:48:11', '103.157.37.106', 0, 0),
(389, 238601, '2026-02-24 12:50:32', '103.157.37.106', 0, 0),
(390, 251790, '2026-02-24 14:55:23', '49.37.102.237', 0, 0),
(391, 433479, '2026-02-24 15:23:12', '106.192.111.112', 0, 0),
(392, 238601, '2026-02-24 16:03:21', '152.58.63.76', 0, 0),
(393, 147405, '2026-02-24 16:13:05', '117.96.149.86', 0, 0),
(394, 251790, '2026-02-24 16:28:08', '49.37.102.237', 0, 0),
(395, 251790, '2026-02-24 16:46:32', '49.37.102.237', 0, 0),
(396, 566768, '2026-02-24 16:52:31', '49.37.102.237', 0, 0),
(397, 566768, '2026-02-24 18:22:31', '49.37.102.237', 0, 0),
(398, 566768, '2026-02-24 18:45:33', '49.37.102.237', 0, 0),
(399, 566768, '2026-02-24 19:39:08', '49.37.102.237', 0, 0),
(400, 251790, '2026-02-24 19:40:08', '49.37.102.237', 0, 0),
(401, 784794, '2026-02-24 20:06:36', '117.96.22.86', 0, 0),
(402, 566768, '2026-02-24 20:42:23', '49.37.102.237', 0, 0),
(403, 566768, '2026-02-24 21:24:57', '49.37.102.237', 0, 0),
(404, 238601, '2026-02-24 21:25:28', '152.58.62.13', 0, 0),
(405, 251790, '2026-02-24 21:25:51', '49.37.102.237', 0, 0),
(406, 784794, '2026-02-25 07:43:33', '117.96.17.123', 0, 0),
(407, 147405, '2026-02-25 07:45:05', '117.96.17.123', 0, 0),
(408, 251790, '2026-02-25 08:15:08', '27.60.34.2', 0, 0),
(409, 566768, '2026-02-25 08:16:20', '27.60.34.31', 0, 0),
(410, 238601, '2026-02-25 08:17:01', '152.58.60.131', 0, 0),
(411, 238601, '2026-02-25 11:17:21', '152.59.37.43', 0, 0),
(412, 735913, '2026-02-25 11:35:37', '157.49.80.211', 0, 0),
(413, 735913, '2026-02-25 16:09:22', '157.49.80.50', 0, 0),
(414, 251790, '2026-02-25 16:56:56', '171.48.93.16', 0, 0),
(415, 643662, '2026-02-25 17:08:32', '27.97.95.21', 0, 0),
(416, 394067, '2026-02-25 17:23:23', '27.97.88.130', 0, 0),
(417, 298848, '2026-02-25 17:35:25', '27.97.94.61', 0, 0),
(418, 366214, '2026-02-25 17:38:22', '27.97.94.61', 0, 0),
(419, 628335, '2026-02-25 17:50:02', '27.97.95.249', 0, 0),
(420, 435406, '2026-02-25 17:52:54', '27.97.95.249', 0, 0),
(421, 548886, '2026-02-25 18:59:46', '49.36.27.22', 0, 0),
(422, 251790, '2026-02-25 19:18:47', '49.37.102.243', 0, 0),
(423, 251790, '2026-02-25 21:48:03', '49.37.102.243', 0, 0),
(424, 262638, '2026-02-25 22:57:44', '49.15.127.53', 0, 0),
(425, 262638, '2026-02-25 22:57:47', '49.15.127.53', 0, 0),
(426, 784794, '2026-02-26 07:28:41', '117.96.151.219', 0, 0),
(427, 147405, '2026-02-26 07:30:23', '117.96.151.219', 0, 0),
(428, 217260, '2026-02-26 07:52:39', '27.97.86.177', 0, 0),
(429, 404878, '2026-02-26 07:56:33', '27.97.86.177', 0, 0),
(430, 238601, '2026-02-26 08:14:35', '152.58.37.37', 0, 0),
(431, 657732, '2026-02-26 08:58:46', '27.97.81.127', 0, 0),
(432, 251790, '2026-02-26 09:41:50', '106.202.28.222', 0, 0),
(433, 566768, '2026-02-26 09:44:37', '106.202.28.222', 0, 0),
(434, 737674, '2026-02-26 11:16:15', '27.97.85.146', 0, 0),
(435, 238601, '2026-02-26 12:00:14', '152.58.36.173', 0, 0),
(436, 686678, '2026-02-26 12:07:25', '152.58.36.68', 0, 0),
(437, 686678, '2026-02-26 12:11:08', '106.202.127.0', 0, 0),
(438, 238601, '2026-02-26 14:10:42', '152.59.19.29', 0, 0),
(439, 238601, '2026-02-26 14:57:03', '152.59.19.54', 0, 0),
(440, 420021, '2026-02-26 15:05:16', '152.59.19.30', 0, 0),
(441, 217260, '2026-02-26 16:02:51', '27.97.88.6', 0, 0),
(442, 784794, '2026-02-26 16:15:49', '117.96.23.184', 0, 0),
(443, 737674, '2026-02-26 16:29:23', '27.97.88.6', 0, 0),
(444, 117197, '2026-02-26 16:32:53', '27.97.87.81', 0, 0),
(445, 784794, '2026-02-26 19:16:21', '117.96.149.184', 0, 0),
(446, 257861, '2026-02-26 20:13:19', '152.56.134.163', 0, 0),
(447, 238601, '2026-02-26 21:58:34', '152.59.34.15', 0, 0),
(448, 686678, '2026-02-26 22:00:40', '152.59.34.15', 0, 0),
(449, 217260, '2026-02-26 22:09:11', '42.108.238.249', 0, 0),
(450, 257861, '2026-02-27 02:44:03', '152.58.191.133', 0, 0),
(451, 420021, '2026-02-27 05:30:04', '152.59.56.51', 0, 0),
(452, 238601, '2026-02-27 05:30:16', '152.59.56.51', 0, 0),
(453, 238601, '2026-02-27 06:09:48', '152.59.56.174', 0, 0),
(454, 784794, '2026-02-27 07:41:30', '117.96.146.112', 0, 0),
(455, 147405, '2026-02-27 07:42:22', '117.96.146.112', 0, 0),
(456, 540691, '2026-02-27 07:43:01', '117.96.146.112', 0, 0),
(457, 257861, '2026-02-27 07:43:19', '117.96.146.112', 0, 0),
(458, 784794, '2026-02-27 09:31:02', '117.96.150.168', 0, 0),
(459, 262638, '2026-02-27 10:48:27', '49.15.119.111', 0, 0),
(460, 566768, '2026-02-27 12:38:59', '152.59.151.234', 0, 0),
(461, 251790, '2026-02-27 12:41:51', '152.59.151.234', 0, 0),
(462, 784794, '2026-02-27 12:57:54', '117.96.22.168', 0, 0),
(463, 257861, '2026-02-27 13:01:14', '117.96.22.168', 0, 0),
(464, 217260, '2026-02-27 16:10:39', '27.97.94.124', 0, 0),
(465, 100, '2026-02-27 18:31:18', '49.47.153.212', 0, 0),
(466, 509367, '2026-02-27 18:32:31', '49.47.153.212', 0, 0),
(467, 557635, '2026-02-27 18:46:46', '1.187.162.6', 0, 0),
(468, 250607, '2026-02-27 19:18:52', '27.97.94.208', 0, 0),
(469, 150670, '2026-02-27 19:41:17', '27.97.95.18', 0, 0),
(470, 238601, '2026-02-27 20:59:08', '152.59.58.53', 0, 0),
(471, 566768, '2026-02-27 21:05:41', '49.37.103.20', 0, 0),
(472, 540691, '2026-02-27 21:17:01', '117.96.147.77', 0, 0),
(473, 557635, '2026-02-27 21:35:25', '1.187.162.6', 0, 0),
(474, 557635, '2026-02-27 21:37:49', '1.187.162.6', 0, 0),
(475, 557635, '2026-02-27 21:39:52', '1.187.162.6', 0, 0),
(476, 557635, '2026-02-27 22:02:15', '49.37.103.20', 0, 0),
(477, 566768, '2026-02-27 22:07:31', '49.37.103.20', 0, 0),
(478, 251790, '2026-02-27 22:10:02', '49.37.103.20', 0, 0),
(479, 557635, '2026-02-27 22:18:38', '1.187.171.146', 0, 0),
(480, 251790, '2026-02-27 22:24:19', '49.37.103.20', 0, 0),
(481, 566768, '2026-02-27 22:26:45', '49.37.103.20', 0, 0),
(482, 557635, '2026-02-27 22:29:04', '49.37.103.20', 0, 0),
(483, 217260, '2026-02-28 07:16:28', '27.97.95.239', 0, 0),
(484, 540691, '2026-02-28 07:48:48', '117.96.18.163', 0, 0),
(485, 238601, '2026-02-28 08:01:13', '152.59.58.86', 0, 0),
(486, 566768, '2026-02-28 10:57:38', '152.58.145.13', 0, 0),
(487, 557635, '2026-02-28 11:00:17', '152.58.145.13', 0, 0),
(488, 251790, '2026-02-28 11:01:18', '152.58.145.13', 0, 0),
(489, 686678, '2026-02-28 11:13:59', '27.60.20.127', 0, 0),
(490, 262638, '2026-02-28 14:18:33', '49.15.119.176', 0, 0),
(491, 217260, '2026-02-28 15:55:33', '27.97.88.206', 0, 0),
(492, 557635, '2026-02-28 19:51:21', '117.212.239.150', 0, 0),
(493, 686678, '2026-03-01 06:56:35', '106.202.121.210', 0, 0),
(494, 238601, '2026-03-01 09:13:24', '152.58.34.88', 0, 0),
(495, 238601, '2026-03-01 09:24:29', '152.58.35.228', 0, 0),
(496, 548886, '2026-03-01 10:01:38', '152.59.29.230', 0, 0),
(497, 557635, '2026-03-01 19:20:50', '117.198.63.146', 0, 0),
(498, 238601, '2026-03-01 21:14:48', '152.58.63.88', 0, 0),
(499, 238601, '2026-03-02 07:41:16', '152.58.63.171', 0, 0),
(500, 217260, '2026-03-02 08:15:21', '27.97.92.170', 0, 0),
(501, 324037, '2026-03-02 08:37:00', '27.97.92.170', 0, 0),
(502, 250607, '2026-03-02 08:38:27', '27.97.92.170', 0, 0),
(503, 262638, '2026-03-02 09:11:53', '1.39.219.11', 0, 0),
(504, 238601, '2026-03-02 10:30:08', '152.59.19.28', 0, 0),
(505, 217260, '2026-03-02 11:00:16', '27.97.87.54', 0, 0),
(506, 557635, '2026-03-02 12:08:20', '152.58.145.35', 0, 0),
(507, 566768, '2026-03-02 12:09:06', '152.58.145.35', 0, 0),
(508, 251790, '2026-03-02 12:09:44', '152.58.145.35', 0, 0),
(509, 557635, '2026-03-02 19:06:23', '59.184.162.44', 0, 0),
(510, 557635, '2026-03-02 20:18:03', '49.37.101.226', 0, 0),
(511, 238601, '2026-03-03 08:06:47', '152.59.34.32', 0, 0),
(512, 217260, '2026-03-03 08:08:52', '27.97.85.28', 0, 0),
(513, 557635, '2026-03-03 09:30:02', '49.37.102.38', 0, 0),
(514, 262638, '2026-03-03 09:30:19', '157.38.148.208', 0, 0),
(515, 251790, '2026-03-03 09:30:31', '49.37.102.38', 0, 0),
(516, 566768, '2026-03-03 09:32:03', '49.37.102.38', 0, 0),
(517, 251790, '2026-03-03 09:44:20', '49.37.102.38', 0, 0),
(518, 238601, '2026-03-03 10:04:03', '152.58.61.116', 0, 0),
(519, 686678, '2026-03-03 10:05:52', '152.58.61.116', 0, 0),
(520, 238601, '2026-03-03 11:17:17', '152.58.60.4', 0, 0),
(521, 238601, '2026-03-03 11:18:06', '152.58.60.4', 0, 0),
(522, 566768, '2026-03-03 14:05:52', '49.37.102.132', 0, 0),
(523, 557635, '2026-03-03 14:06:21', '49.37.102.132', 0, 0),
(524, 557635, '2026-03-03 14:06:26', '59.184.172.62', 0, 0),
(525, 217260, '2026-03-03 14:28:13', '1.187.152.118', 0, 0),
(526, 456057, '2026-03-03 14:44:07', '1.187.150.83', 0, 0),
(527, 769850, '2026-03-03 14:44:43', '1.187.150.83', 0, 0),
(528, 396381, '2026-03-03 14:45:25', '1.187.150.83', 0, 0),
(529, 217260, '2026-03-03 14:46:03', '1.187.150.83', 0, 0),
(530, 195686, '2026-03-03 14:46:59', '1.187.150.83', 0, 0),
(531, 455114, '2026-03-03 16:18:36', '1.187.157.147', 0, 0),
(532, 217260, '2026-03-03 19:40:45', '1.187.145.13', 0, 0),
(533, 251790, '2026-03-03 19:50:39', '49.37.100.21', 0, 0),
(534, 238601, '2026-03-03 20:47:04', '152.59.35.26', 0, 0),
(535, 686678, '2026-03-03 22:21:02', '106.202.122.196', 0, 0),
(536, 217260, '2026-03-04 07:18:48', '1.187.154.40', 0, 0),
(537, 238601, '2026-03-04 08:10:16', '152.58.34.106', 0, 0),
(538, 262638, '2026-03-04 08:43:30', '1.39.158.91', 0, 0),
(539, 372594, '2026-03-04 09:05:16', '1.39.154.22', 0, 0),
(540, 262638, '2026-03-04 10:22:52', '1.39.163.188', 0, 0),
(541, 372594, '2026-03-04 10:24:27', '1.39.163.188', 0, 0),
(542, 262638, '2026-03-04 10:24:48', '1.39.163.188', 0, 0),
(543, 686678, '2026-03-04 11:09:42', '27.59.93.238', 0, 0),
(544, 217260, '2026-03-04 13:41:57', '1.187.158.45', 0, 0),
(545, 706571, '2026-03-04 13:48:59', '1.187.158.45', 0, 0),
(546, 238601, '2026-03-04 17:02:45', '27.61.232.106', 0, 0),
(547, 378428, '2026-03-04 19:10:41', '27.97.84.163', 0, 0),
(548, 251790, '2026-03-04 20:47:35', '49.37.101.214', 0, 0),
(549, 566768, '2026-03-04 20:48:53', '49.37.101.214', 0, 0),
(550, 217260, '2026-03-04 23:11:52', '27.97.92.84', 0, 0),
(551, 473529, '2026-03-04 23:14:26', '27.97.92.84', 0, 0),
(552, 435268, '2026-03-04 23:15:30', '27.97.92.84', 0, 0),
(553, 217260, '2026-03-04 23:33:20', '27.97.92.116', 0, 0),
(554, 686678, '2026-03-05 06:46:49', '106.194.61.151', 0, 0),
(555, 217260, '2026-03-05 13:50:48', '1.187.155.42', 0, 0),
(556, 251790, '2026-03-05 13:54:41', '152.58.168.185', 0, 0),
(557, 557635, '2026-03-05 14:00:52', '117.212.224.11', 0, 0),
(558, 557635, '2026-03-05 14:02:32', '117.212.224.11', 0, 0),
(559, 238601, '2026-03-05 14:29:41', '152.59.34.218', 0, 0),
(560, 566768, '2026-03-05 14:41:12', '49.37.101.42', 0, 0),
(561, 251790, '2026-03-05 14:44:23', '49.37.101.42', 0, 0),
(562, 557635, '2026-03-05 14:46:08', '49.37.101.42', 0, 0),
(563, 251790, '2026-03-05 14:46:28', '49.37.101.42', 0, 0),
(564, 566768, '2026-03-05 15:55:56', '49.37.101.42', 0, 0),
(565, 251790, '2026-03-05 15:57:13', '49.37.101.42', 0, 0),
(566, 520874, '2026-03-05 23:52:21', '152.57.7.45', 0, 0),
(567, 566768, '2026-03-06 10:14:55', '152.59.150.156', 0, 0),
(568, 251790, '2026-03-06 10:15:55', '152.59.150.156', 0, 0),
(569, 217260, '2026-03-06 11:08:51', '1.187.155.138', 0, 0),
(570, 489496, '2026-03-06 14:29:14', '1.187.154.165', 0, 0),
(571, 238601, '2026-03-06 14:30:12', '103.157.37.106', 0, 0),
(572, 686678, '2026-03-06 14:31:08', '103.157.37.106', 0, 0),
(573, 217260, '2026-03-06 14:41:51', '1.187.154.165', 0, 0),
(574, 251790, '2026-03-06 16:00:50', '49.37.102.2', 0, 0),
(575, 217260, '2026-03-06 17:23:31', '1.187.151.3', 0, 0),
(576, 251790, '2026-03-06 18:19:19', '152.59.151.87', 0, 0),
(577, 566768, '2026-03-06 18:19:57', '152.59.151.87', 0, 0),
(578, 251790, '2026-03-07 06:38:27', '49.37.100.139', 0, 0),
(579, 737674, '2026-03-07 08:10:01', '1.187.158.159', 0, 0),
(580, 217260, '2026-03-07 08:22:11', '1.187.158.159', 0, 0),
(581, 238601, '2026-03-07 08:48:08', '152.59.33.70', 0, 0),
(582, 566768, '2026-03-07 09:42:13', '152.59.151.217', 0, 0),
(583, 251790, '2026-03-07 09:43:01', '152.59.151.217', 0, 0),
(584, 217260, '2026-03-07 11:32:02', '1.187.159.47', 0, 0),
(585, 346504, '2026-03-07 12:41:07', '1.187.159.71', 0, 0),
(586, 238601, '2026-03-07 13:27:49', '152.59.33.61', 0, 0),
(587, 686678, '2026-03-07 14:08:21', '106.216.109.146', 0, 0),
(588, 686678, '2026-03-08 10:08:35', '106.221.79.84', 0, 0),
(589, 217260, '2026-03-08 11:30:26', '1.187.147.138', 0, 0),
(590, 557635, '2026-03-08 11:33:57', '117.198.56.15', 0, 0),
(591, 251790, '2026-03-08 12:13:53', '49.37.100.246', 0, 0),
(592, 251790, '2026-03-08 15:30:19', '49.37.101.174', 0, 0),
(593, 557635, '2026-03-08 16:31:22', '117.198.56.15', 0, 0),
(594, 557635, '2026-03-08 16:42:30', '117.198.56.15', 0, 0),
(595, 251790, '2026-03-08 19:55:30', '49.37.100.185', 0, 0),
(596, 238601, '2026-03-09 07:56:01', '152.58.60.76', 0, 0),
(597, 686678, '2026-03-09 09:30:02', '27.59.84.49', 0, 0),
(598, 217260, '2026-03-09 09:41:41', '27.57.248.89', 0, 0),
(599, 251790, '2026-03-09 10:54:01', '152.59.151.232', 0, 0),
(600, 566768, '2026-03-09 10:55:32', '152.59.151.232', 0, 0),
(601, 566768, '2026-03-09 18:40:42', '49.37.102.237', 0, 0),
(602, 557635, '2026-03-09 19:37:12', '59.184.172.64', 0, 0),
(603, 566768, '2026-03-09 20:59:50', '49.37.102.237', 0, 0),
(604, 566768, '2026-03-10 07:54:50', '49.37.102.237', 0, 0),
(605, 251790, '2026-03-10 07:56:13', '49.37.102.237', 0, 0),
(606, 557635, '2026-03-10 07:57:16', '49.37.102.237', 0, 0),
(607, 238601, '2026-03-10 08:04:03', '152.59.15.21', 0, 0),
(608, 420021, '2026-03-10 08:06:11', '152.59.15.21', 0, 0),
(609, 251790, '2026-03-10 10:25:36', '152.59.151.237', 0, 0),
(610, 420021, '2026-03-10 10:39:57', '152.59.15.149', 0, 0),
(611, 238601, '2026-03-10 10:40:14', '152.59.15.149', 0, 0),
(612, 217260, '2026-03-10 11:30:44', '106.211.120.80', 0, 0),
(613, 238601, '2026-03-10 14:06:55', '152.59.37.183', 0, 0),
(614, 238601, '2026-03-10 14:08:07', '152.59.37.183', 0, 0),
(615, 744285, '2026-03-10 15:37:14', '152.58.181.8', 0, 0),
(616, 238601, '2026-03-10 17:08:11', '152.58.36.239', 0, 0),
(617, 238601, '2026-03-10 17:09:21', '152.58.36.239', 0, 0),
(618, 557635, '2026-03-10 21:25:31', '117.198.57.171', 0, 0),
(619, 217260, '2026-03-10 22:53:26', '27.97.92.11', 0, 0),
(620, 251790, '2026-03-10 22:57:02', '49.37.101.245', 0, 0),
(621, 379774, '2026-03-10 23:05:54', '27.97.92.11', 0, 0),
(622, 217260, '2026-03-10 23:18:17', '1.187.150.68', 0, 0),
(623, 238601, '2026-03-11 08:09:43', '152.59.15.239', 0, 0),
(624, 251790, '2026-03-11 08:46:20', '49.37.101.245', 0, 0),
(625, 217260, '2026-03-11 10:24:43', '1.187.154.219', 0, 0),
(626, 648774, '2026-03-11 13:37:02', '1.187.155.252', 0, 0),
(627, 372594, '2026-03-11 13:43:27', '49.15.119.156', 0, 0),
(628, 262638, '2026-03-11 13:43:44', '49.15.119.156', 0, 0),
(629, 217260, '2026-03-11 13:50:53', '1.187.155.252', 0, 0),
(630, 238601, '2026-03-11 20:18:15', '152.59.18.129', 0, 0),
(631, 686678, '2026-03-11 21:53:55', '27.60.23.184', 0, 0),
(632, 217260, '2026-03-12 07:45:47', '1.187.147.54', 0, 0),
(633, 238601, '2026-03-12 09:00:22', '152.58.63.238', 0, 0),
(634, 217260, '2026-03-12 11:02:24', '1.187.150.47', 0, 0),
(635, 420021, '2026-03-12 15:35:46', '152.59.37.190', 0, 0),
(636, 557635, '2026-03-12 19:22:12', '117.212.227.126', 0, 0),
(637, 744285, '2026-03-13 09:21:25', '152.58.176.42', 0, 0),
(638, 251790, '2026-03-13 09:42:48', '152.59.151.84', 0, 0),
(639, 557635, '2026-03-13 09:43:21', '152.59.151.84', 0, 0),
(640, 566768, '2026-03-13 09:43:45', '152.59.151.84', 0, 0),
(641, 217260, '2026-03-13 10:41:48', '1.187.151.212', 0, 0),
(642, 393907, '2026-03-13 14:23:23', '103.201.139.18', 0, 0),
(643, 393907, '2026-03-13 17:42:15', '103.201.139.18', 0, 0),
(644, 540691, '2026-03-13 22:26:03', '106.219.245.68', 0, 0),
(645, 540691, '2026-03-13 22:26:41', '106.219.245.68', 0, 0),
(646, 251790, '2026-03-14 08:20:50', '49.37.103.55', 0, 0),
(647, 566768, '2026-03-14 08:22:00', '49.37.103.55', 0, 0),
(648, 557635, '2026-03-14 08:23:28', '49.37.103.55', 0, 0),
(649, 393907, '2026-03-14 08:35:46', '152.58.16.47', 0, 0),
(650, 217260, '2026-03-14 14:03:24', '27.97.88.218', 0, 0),
(651, 393907, '2026-03-14 18:18:30', '103.201.139.18', 0, 0),
(652, 217260, '2026-03-14 20:10:45', '1.187.158.81', 0, 0),
(653, 420021, '2026-03-14 20:45:42', '152.58.60.231', 0, 0),
(654, 251790, '2026-03-15 10:43:48', '152.59.150.230', 0, 0),
(655, 217260, '2026-03-15 14:34:42', '1.187.157.67', 0, 0),
(656, 217260, '2026-03-15 14:55:41', '1.187.151.43', 0, 0),
(657, 251790, '2026-03-15 20:03:17', '152.59.150.170', 0, 0),
(658, 557635, '2026-03-15 20:22:18', '1.187.162.85', 0, 0),
(659, 251790, '2026-03-16 08:12:11', '49.37.103.251', 0, 0),
(660, 566768, '2026-03-16 08:12:34', '49.37.103.251', 0, 0),
(661, 557635, '2026-03-16 08:12:56', '49.37.103.251', 0, 0),
(662, 251790, '2026-03-16 08:13:28', '49.37.103.251', 0, 0),
(663, 217260, '2026-03-16 09:08:17', '1.187.149.183', 0, 0),
(664, 420021, '2026-03-16 10:11:51', '152.58.61.196', 0, 0),
(665, 262638, '2026-03-16 12:34:25', '49.15.118.50', 0, 0),
(666, 393907, '2026-03-16 16:17:53', '152.59.61.143', 0, 0),
(667, 217260, '2026-03-16 19:02:30', '1.187.150.216', 0, 0),
(668, 251790, '2026-03-16 22:54:18', '49.37.101.171', 0, 0),
(669, 238601, '2026-03-17 00:12:59', '152.59.15.128', 0, 0),
(670, 238601, '2026-03-17 07:36:12', '152.59.16.82', 0, 0),
(671, 238601, '2026-03-17 07:37:37', '152.59.16.82', 0, 0),
(672, 251790, '2026-03-17 07:44:45', '49.37.101.249', 0, 0),
(673, 557635, '2026-03-17 07:45:46', '49.37.101.249', 0, 0),
(674, 566768, '2026-03-17 07:46:15', '49.37.101.249', 0, 0),
(675, 217260, '2026-03-17 08:18:31', '1.187.150.169', 0, 0),
(676, 358894, '2026-03-17 08:36:41', '157.48.176.115', 0, 0),
(677, 566768, '2026-03-17 10:42:10', '27.56.84.226', 0, 0),
(678, 393907, '2026-03-17 11:07:38', '47.11.47.189', 0, 0),
(679, 217260, '2026-03-17 11:14:42', '1.187.145.210', 0, 0),
(680, 217260, '2026-03-17 14:57:10', '1.187.150.129', 0, 0),
(681, 238601, '2026-03-17 16:00:41', '152.58.34.224', 0, 0),
(682, 416830, '2026-03-17 16:40:27', '42.104.220.103', 0, 0),
(683, 238601, '2026-03-17 19:17:40', '152.58.60.236', 0, 0),
(684, 217260, '2026-03-18 07:57:33', '27.97.83.160', 0, 0),
(685, 618989, '2026-03-18 08:00:58', '27.97.95.132', 0, 0),
(686, 217260, '2026-03-18 10:10:43', '27.97.84.241', 0, 0),
(687, 251790, '2026-03-18 11:13:26', '49.37.101.46', 0, 0),
(688, 566768, '2026-03-18 11:14:24', '49.37.101.46', 0, 0),
(689, 496048, '2026-03-18 13:50:20', '27.97.93.0', 0, 0),
(690, 262638, '2026-03-18 14:22:37', '49.15.126.153', 0, 0),
(691, 416830, '2026-03-18 16:23:10', '42.104.228.12', 0, 0),
(692, 217260, '2026-03-18 16:43:47', '27.97.94.10', 0, 0),
(693, 557635, '2026-03-18 17:56:14', '1.187.169.34', 0, 0),
(694, 566768, '2026-03-18 21:16:44', '49.37.103.55', 0, 0),
(695, 251790, '2026-03-18 21:17:33', '49.37.103.55', 0, 0),
(696, 496048, '2026-03-19 07:42:03', '27.97.94.117', 0, 0),
(697, 416830, '2026-03-19 08:12:36', '42.104.228.30', 0, 0),
(698, 217260, '2026-03-19 08:45:56', '27.97.85.159', 0, 0),
(699, 566768, '2026-03-19 09:35:54', '223.237.145.2', 0, 0),
(700, 238601, '2026-03-19 13:38:05', '103.157.37.106', 0, 0),
(701, 251790, '2026-03-19 17:11:08', '223.237.157.2', 0, 0),
(702, 217260, '2026-03-19 20:22:46', '27.97.92.238', 0, 0),
(703, 570511, '2026-03-19 20:39:47', '152.58.62.48', 0, 0),
(704, 570511, '2026-03-19 20:40:08', '152.59.37.178', 0, 0),
(705, 251790, '2026-03-19 22:41:55', '49.37.101.159', 0, 0),
(706, 217260, '2026-03-20 07:01:54', '27.97.93.214', 0, 0),
(707, 570511, '2026-03-20 09:34:26', '152.58.62.222', 0, 0),
(708, 251790, '2026-03-20 09:39:25', '223.237.156.94', 0, 0),
(709, 566768, '2026-03-20 09:41:09', '223.237.156.94', 0, 0),
(710, 217260, '2026-03-20 09:45:42', '27.97.85.172', 0, 0),
(711, 238601, '2026-03-20 16:38:05', '103.157.37.106', 0, 0),
(712, 238601, '2026-03-21 09:55:38', '152.58.62.187', 0, 0),
(713, 570511, '2026-03-21 15:52:45', '152.59.36.97', 0, 0),
(714, 566768, '2026-03-21 19:50:42', '49.37.102.109', 0, 0),
(715, 557635, '2026-03-21 19:51:31', '49.37.102.109', 0, 0),
(716, 566768, '2026-03-21 19:52:11', '49.37.102.109', 0, 0),
(717, 557635, '2026-03-21 23:08:08', '117.198.49.17', 0, 0),
(718, 566768, '2026-03-22 07:07:52', '49.37.102.109', 0, 0),
(719, 744285, '2026-03-22 09:33:17', '152.58.182.177', 0, 0),
(720, 570511, '2026-03-22 10:28:19', '152.59.40.95', 0, 0),
(721, 744285, '2026-03-23 06:47:34', '152.56.157.26', 0, 0),
(722, 217260, '2026-03-23 09:24:14', '27.97.86.158', 0, 0),
(723, 744285, '2026-03-23 10:28:52', '152.58.176.238', 0, 0),
(724, 744285, '2026-03-23 17:21:42', '152.58.178.127', 0, 0),
(725, 217260, '2026-03-23 22:08:41', '1.187.150.217', 0, 0),
(726, 393907, '2026-03-23 22:35:28', '152.59.10.160', 0, 0),
(727, 566768, '2026-03-24 09:30:38', '49.37.102.16', 0, 0),
(728, 557635, '2026-03-24 09:31:05', '49.37.102.16', 0, 0),
(729, 557635, '2026-03-24 09:31:40', '49.37.102.16', 0, 0),
(730, 566768, '2026-03-24 09:32:12', '49.37.102.16', 0, 0),
(731, 393907, '2026-03-24 11:47:58', '103.201.139.191', 0, 0),
(732, 557635, '2026-03-24 13:00:20', '27.97.102.168', 0, 0),
(733, 713474, '2026-03-24 14:53:03', '1.187.151.243', 0, 0),
(734, 410714, '2026-03-24 15:14:42', '1.187.150.135', 0, 0),
(735, 366214, '2026-03-24 15:44:47', '1.187.155.233', 0, 0),
(736, 238601, '2026-03-24 16:26:31', '152.58.34.201', 0, 0),
(737, 217260, '2026-03-25 06:53:48', '27.97.81.47', 0, 0),
(738, 767236, '2026-03-25 06:58:09', '27.97.81.47', 0, 0),
(739, 393907, '2026-03-25 07:02:12', '47.11.11.24', 0, 0),
(740, 217260, '2026-03-25 08:23:49', '27.97.81.47', 0, 0),
(741, 477477, '2026-03-25 08:26:25', '27.97.86.124', 0, 0),
(742, 217260, '2026-03-25 08:47:34', '27.97.86.197', 0, 0),
(743, 366214, '2026-03-25 08:53:04', '27.97.81.47', 0, 0),
(744, 394067, '2026-03-25 08:56:00', '27.97.81.47', 0, 0),
(745, 410714, '2026-03-25 08:57:53', '27.97.81.47', 0, 0),
(746, 713474, '2026-03-25 08:59:12', '27.97.81.47', 0, 0),
(747, 217260, '2026-03-25 10:44:37', '27.97.87.194', 0, 0),
(748, 238601, '2026-03-25 13:10:10', '103.157.37.106', 0, 0),
(749, 485868, '2026-03-25 14:59:09', '27.97.85.248', 0, 0),
(750, 138545, '2026-03-25 15:03:17', '27.97.94.19', 0, 0),
(751, 477477, '2026-03-25 15:29:36', '27.97.85.248', 0, 0),
(752, 393907, '2026-03-25 16:03:34', '152.59.61.30', 0, 0),
(753, 566768, '2026-03-25 16:42:22', '49.37.102.54', 0, 0),
(754, 557635, '2026-03-25 16:43:18', '49.37.102.54', 0, 0),
(755, 566768, '2026-03-25 16:43:51', '49.37.102.54', 0, 0),
(756, 540691, '2026-03-25 18:47:55', '117.99.231.37', 0, 0),
(757, 394067, '2026-03-25 18:53:55', '27.97.90.111', 0, 0),
(758, 366214, '2026-03-26 07:05:56', '1.187.144.113', 0, 0),
(759, 394067, '2026-03-26 07:07:33', '1.187.144.113', 0, 0),
(760, 410714, '2026-03-26 07:08:43', '1.187.144.113', 0, 0),
(761, 713474, '2026-03-26 07:09:51', '1.187.144.113', 0, 0),
(762, 217260, '2026-03-26 07:10:51', '1.187.144.113', 0, 0),
(763, 250607, '2026-03-26 07:13:30', '1.187.144.113', 0, 0),
(764, 643662, '2026-03-26 07:14:42', '1.187.144.113', 0, 0),
(765, 393907, '2026-03-26 08:10:53', '152.59.63.126', 0, 0),
(766, 217260, '2026-03-26 10:45:31', '1.187.146.98', 0, 0),
(767, 217260, '2026-03-26 14:10:22', '1.187.148.241', 0, 0),
(768, 712227, '2026-03-26 14:22:49', '1.187.148.241', 0, 0),
(769, 393907, '2026-03-26 16:20:54', '152.59.57.11', 0, 0),
(770, 217260, '2026-03-26 18:02:31', '1.187.145.144', 0, 0),
(771, 570511, '2026-03-26 18:48:21', '152.59.37.37', 0, 0),
(772, 217260, '2026-03-26 19:51:22', '1.187.150.154', 0, 0),
(773, 416830, '2026-03-26 21:00:34', '42.104.219.31', 0, 0),
(774, 643662, '2026-03-27 06:30:45', '1.187.148.55', 0, 0),
(775, 250607, '2026-03-27 06:31:26', '1.187.148.55', 0, 0),
(776, 217260, '2026-03-27 06:31:45', '1.187.148.55', 0, 0),
(777, 250607, '2026-03-27 07:55:42', '1.187.151.253', 0, 0),
(778, 643662, '2026-03-27 07:56:37', '1.187.151.253', 0, 0),
(779, 217260, '2026-03-27 08:16:39', '1.187.151.253', 0, 0),
(780, 217260, '2026-03-27 08:45:32', '1.187.147.248', 0, 0),
(781, 570511, '2026-03-27 15:08:24', '152.59.41.87', 0, 0),
(782, 570511, '2026-03-27 17:03:05', '152.59.37.58', 0, 0),
(783, 566768, '2026-03-27 18:58:06', '49.37.102.18', 0, 0),
(784, 557635, '2026-03-27 19:02:49', '49.37.102.18', 0, 0),
(785, 265405, '2026-03-27 22:52:28', '27.97.95.27', 0, 0),
(786, 217260, '2026-03-28 07:42:52', '1.187.144.77', 0, 0),
(787, 673638, '2026-03-28 07:43:48', '1.187.144.77', 0, 0),
(788, 217260, '2026-03-28 08:00:42', '1.187.158.249', 0, 0),
(789, 393907, '2026-03-28 08:37:28', '152.59.11.199', 0, 0),
(790, 570511, '2026-03-28 10:02:48', '152.58.35.167', 0, 0),
(791, 217260, '2026-03-28 11:09:40', '1.187.159.120', 0, 0),
(792, 393907, '2026-03-28 12:39:19', '152.59.59.151', 0, 0),
(793, 238601, '2026-03-28 15:59:41', '103.157.37.106', 0, 0),
(794, 217260, '2026-03-28 18:51:41', '27.97.82.196', 0, 0),
(795, 217260, '2026-03-28 18:51:51', '27.97.82.196', 0, 0),
(796, 217260, '2026-03-28 18:57:19', '27.97.82.31', 0, 0),
(797, 504288, '2026-03-28 18:58:57', '27.97.82.31', 0, 0),
(798, 217260, '2026-03-28 21:56:39', '27.97.83.209', 0, 0),
(799, 570511, '2026-03-29 08:18:55', '152.59.37.251', 0, 0),
(800, 416830, '2026-03-29 08:23:01', '42.104.220.33', 0, 0),
(801, 217260, '2026-03-29 11:22:35', '27.97.91.89', 0, 0),
(802, 566768, '2026-03-29 16:03:43', '49.37.103.36', 0, 0),
(803, 217260, '2026-03-29 16:59:37', '27.97.87.152', 0, 0),
(804, 510484, '2026-03-29 17:00:40', '27.97.87.152', 0, 0),
(805, 594313, '2026-03-29 18:49:37', '27.97.84.181', 0, 0),
(806, 217260, '2026-03-29 19:23:51', '27.97.90.152', 0, 0),
(807, 557635, '2026-03-29 20:30:14', '117.198.59.99', 0, 0),
(808, 570511, '2026-03-29 21:29:29', '1.38.164.229', 0, 0),
(809, 217974, '2026-03-29 21:34:20', '1.38.164.229', 0, 0),
(810, 570511, '2026-03-29 22:02:28', '1.38.164.229', 0, 0),
(811, 217974, '2026-03-29 22:26:31', '1.38.164.229', 0, 0),
(812, 570511, '2026-03-29 23:09:34', '152.59.15.109', 0, 0),
(813, 570511, '2026-03-29 23:09:35', '152.59.15.109', 0, 0),
(814, 217260, '2026-03-30 06:34:18', '27.97.94.92', 0, 0),
(815, 139519, '2026-03-30 07:33:55', '27.97.86.191', 0, 0),
(816, 217260, '2026-03-30 08:07:28', '27.97.86.20', 0, 0),
(817, 570511, '2026-03-30 08:46:20', '152.59.18.13', 0, 0),
(818, 217974, '2026-03-30 10:14:18', '49.34.100.66', 0, 0),
(819, 217260, '2026-03-30 11:02:13', '27.97.87.242', 0, 0),
(820, 393907, '2026-03-30 17:49:30', '152.59.60.124', 0, 0),
(821, 570511, '2026-03-30 18:54:44', '152.59.37.153', 0, 0),
(822, 557635, '2026-03-30 20:54:57', '117.212.230.187', 0, 0),
(823, 217260, '2026-03-31 07:06:26', '1.187.150.204', 0, 0),
(824, 570511, '2026-03-31 08:38:47', '152.58.62.233', 0, 0),
(825, 570511, '2026-03-31 10:34:05', '152.58.62.110', 0, 0),
(826, 393907, '2026-03-31 11:05:40', '103.201.139.188', 0, 0),
(827, 566768, '2026-03-31 12:35:50', '152.59.152.225', 0, 0),
(828, 566768, '2026-03-31 12:38:30', '152.59.152.225', 0, 0),
(829, 619673, '2026-03-31 13:21:36', '1.187.147.79', 0, 0),
(830, 217260, '2026-03-31 16:04:34', '1.187.154.175', 0, 0),
(831, 217974, '2026-03-31 16:18:08', '42.108.130.45', 0, 0),
(832, 570511, '2026-04-01 01:02:20', '152.58.61.50', 0, 0),
(833, 570511, '2026-04-01 07:20:11', '152.59.34.106', 0, 0),
(834, 217260, '2026-04-01 09:32:18', '1.187.158.147', 0, 0),
(835, 217260, '2026-04-01 11:55:27', '42.104.223.67', 0, 0),
(836, 416830, '2026-04-01 12:08:49', '42.104.228.84', 0, 0),
(837, 217260, '2026-04-01 15:04:56', '1.187.155.47', 0, 0),
(838, 217260, '2026-04-01 16:30:44', '1.187.155.47', 0, 0),
(839, 217974, '2026-04-01 17:43:15', '42.104.161.213', 0, 0),
(840, 566768, '2026-04-01 18:34:00', '49.37.100.5', 0, 0),
(841, 217974, '2026-04-01 21:47:55', '42.108.128.106', 0, 0),
(842, 393907, '2026-04-02 08:07:26', '152.58.15.63', 0, 0),
(843, 217974, '2026-04-02 10:48:11', '1.38.164.182', 0, 0),
(844, 570511, '2026-04-02 11:23:59', '152.59.36.145', 0, 0),
(845, 422853, '2026-04-02 12:03:30', '152.59.34.193', 0, 0),
(846, 238601, '2026-04-02 12:36:52', '152.58.34.81', 0, 0),
(847, 570511, '2026-04-02 13:05:04', '152.59.36.233', 0, 0),
(848, 422853, '2026-04-02 13:05:46', '152.59.34.225', 0, 0),
(849, 422853, '2026-04-02 15:53:06', '152.59.34.214', 0, 0);
INSERT INTO `user_login_detail` (`recid`, `uid`, `datetime`, `ip`, `type`, `status`) VALUES
(850, 114365, '2026-04-02 15:54:32', '1.187.150.47', 0, 0),
(851, 177723, '2026-04-02 15:55:15', '1.187.150.47', 0, 0),
(852, 746115, '2026-04-02 15:56:05', '1.187.150.47', 0, 0),
(853, 491106, '2026-04-02 15:56:35', '1.187.150.47', 0, 0),
(854, 553150, '2026-04-02 15:57:12', '1.187.150.47', 0, 0),
(855, 114365, '2026-04-02 15:57:49', '1.187.150.47', 0, 0),
(856, 394067, '2026-04-02 16:01:27', '1.187.150.47', 0, 0),
(857, 366214, '2026-04-02 16:12:12', '1.187.146.17', 0, 0),
(858, 570358, '2026-04-02 16:17:26', '1.187.146.17', 0, 0),
(859, 416830, '2026-04-03 05:21:18', '42.104.221.8', 0, 0),
(860, 217974, '2026-04-03 07:09:24', '42.104.162.200', 0, 0),
(861, 422853, '2026-04-03 07:36:10', '152.59.15.133', 0, 0),
(862, 570511, '2026-04-03 08:36:44', '152.58.34.15', 0, 0),
(863, 570511, '2026-04-03 11:27:47', '152.58.35.226', 0, 0),
(864, 561984, '2026-04-03 12:20:28', '103.86.17.233', 0, 0),
(865, 561984, '2026-04-03 13:02:46', '103.86.17.233', 0, 0),
(866, 561984, '2026-04-03 13:31:46', '103.86.17.233', 0, 0),
(867, 561984, '2026-04-03 14:18:39', '152.58.36.193', 0, 0),
(868, 570511, '2026-04-03 14:19:15', '152.58.36.201', 0, 0),
(869, 561984, '2026-04-03 15:47:35', '152.58.36.164', 0, 0),
(870, 422853, '2026-04-03 16:33:59', '152.59.15.131', 0, 0),
(871, 422853, '2026-04-03 17:06:40', '152.59.15.41', 0, 0),
(872, 217260, '2026-04-03 17:09:28', '27.97.87.47', 0, 0),
(873, 422853, '2026-04-03 19:29:28', '152.59.15.63', 0, 0),
(874, 570511, '2026-04-03 20:28:19', '152.58.60.35', 0, 0),
(875, 217260, '2026-04-03 20:29:08', '27.97.87.237', 0, 0),
(876, 475305, '2026-04-03 20:46:22', '59.184.104.226', 0, 0),
(877, 475305, '2026-04-03 20:46:54', '59.184.104.226', 0, 0),
(878, 566768, '2026-04-03 21:15:34', '49.37.100.236', 0, 0),
(879, 566768, '2026-04-03 21:22:05', '49.37.100.236', 0, 0),
(880, 422853, '2026-04-03 22:04:38', '152.59.15.41', 0, 0),
(881, 217260, '2026-04-03 22:27:00', '27.97.89.254', 0, 0),
(882, 683005, '2026-04-03 22:46:14', '152.58.60.38', 0, 0),
(883, 570511, '2026-04-03 22:46:46', '152.58.60.69', 0, 0),
(884, 683005, '2026-04-03 23:21:49', '152.58.61.49', 0, 0),
(885, 570511, '2026-04-03 23:24:46', '152.58.61.49', 0, 0),
(886, 317468, '2026-04-03 23:33:49', '27.97.89.254', 0, 0),
(887, 712506, '2026-04-03 23:53:25', '27.97.87.26', 0, 0),
(888, 475305, '2026-04-04 00:31:55', '59.184.104.226', 0, 0),
(889, 475305, '2026-04-04 00:35:07', '59.184.104.226', 0, 0),
(890, 683005, '2026-04-04 00:50:32', '152.58.36.152', 0, 0),
(891, 683005, '2026-04-04 00:57:26', '152.58.36.152', 0, 0),
(892, 422853, '2026-04-04 07:30:46', '152.59.15.200', 0, 0),
(893, 217974, '2026-04-04 08:36:59', '42.108.128.166', 0, 0),
(894, 317468, '2026-04-04 08:45:23', '27.97.81.218', 0, 0),
(895, 405830, '2026-04-04 08:45:27', '49.36.74.117', 0, 0),
(896, 422853, '2026-04-04 08:46:20', '152.59.15.190', 0, 0),
(897, 217260, '2026-04-04 10:03:17', '1.187.151.79', 0, 0),
(898, 504288, '2026-04-04 10:04:11', '1.187.151.79', 0, 0),
(899, 217974, '2026-04-04 11:24:32', '1.38.164.106', 0, 0),
(900, 475305, '2026-04-04 11:31:59', '49.34.249.190', 0, 0),
(901, 475305, '2026-04-04 11:32:27', '49.34.249.190', 0, 0),
(902, 570511, '2026-04-04 12:39:05', '152.58.62.1', 0, 0),
(903, 405830, '2026-04-04 12:54:43', '49.36.74.117', 0, 0),
(904, 405830, '2026-04-04 12:55:50', '49.36.74.117', 0, 0),
(905, 405830, '2026-04-04 13:09:50', '49.36.74.117', 0, 0),
(906, 405830, '2026-04-04 13:44:07', '49.36.74.117', 0, 0),
(907, 570511, '2026-04-04 13:49:10', '152.58.63.34', 0, 0),
(908, 393907, '2026-04-04 14:21:06', '152.59.59.92', 0, 0),
(909, 416830, '2026-04-04 19:02:13', '27.97.174.144', 0, 0),
(910, 570511, '2026-04-04 19:03:35', '152.58.60.232', 0, 0),
(911, 599157, '2026-04-04 19:05:29', '42.104.223.31', 0, 0),
(912, 491106, '2026-04-04 20:30:26', '42.108.236.221', 0, 0),
(913, 405830, '2026-04-04 23:08:46', '49.36.74.117', 0, 0),
(914, 570511, '2026-04-05 08:45:45', '152.59.33.55', 0, 0),
(915, 422853, '2026-04-05 09:34:23', '103.85.89.60', 0, 0),
(916, 475305, '2026-04-05 10:07:33', '49.34.112.30', 0, 0),
(917, 557635, '2026-04-05 10:14:51', '1.187.172.139', 0, 0),
(918, 570511, '2026-04-05 11:07:07', '152.59.35.59', 0, 0),
(919, 405830, '2026-04-05 12:30:23', '152.59.34.102', 0, 0),
(920, 570511, '2026-04-05 15:46:07', '152.59.37.0', 0, 0),
(921, 422853, '2026-04-05 15:57:58', '103.85.89.60', 0, 0),
(922, 422853, '2026-04-05 21:01:20', '152.58.63.124', 0, 0),
(923, 570511, '2026-04-05 23:02:03', '152.59.33.109', 0, 0),
(924, 416830, '2026-04-06 01:23:43', '42.104.227.13', 0, 0),
(925, 570511, '2026-04-06 08:26:41', '152.58.34.33', 0, 0),
(926, 422853, '2026-04-06 09:02:39', '152.59.34.236', 0, 0),
(927, 570511, '2026-04-06 10:26:58', '152.58.37.54', 0, 0),
(928, 570511, '2026-04-06 11:28:43', '152.59.36.74', 0, 0),
(929, 570511, '2026-04-06 13:04:01', '152.59.36.147', 0, 0),
(930, 570511, '2026-04-06 13:36:10', '152.59.36.221', 0, 0),
(931, 570511, '2026-04-06 14:07:56', '152.59.36.52', 0, 0),
(932, 570511, '2026-04-06 15:10:35', '152.59.16.119', 0, 0),
(933, 570511, '2026-04-06 16:30:56', '152.59.16.117', 0, 0),
(934, 217974, '2026-04-06 16:39:57', '42.104.161.237', 0, 0),
(935, 416830, '2026-04-06 16:55:07', '42.104.219.79', 0, 0),
(936, 422853, '2026-04-06 17:39:33', '152.59.34.3', 0, 0),
(937, 570511, '2026-04-06 17:49:38', '152.59.16.242', 0, 0),
(938, 393907, '2026-04-06 17:52:55', '152.59.57.148', 0, 0),
(939, 570511, '2026-04-06 18:58:22', '152.59.16.115', 0, 0),
(940, 570511, '2026-04-06 19:13:01', '152.59.16.237', 0, 0),
(941, 570511, '2026-04-06 20:45:57', '152.58.36.120', 0, 0),
(942, 570511, '2026-04-06 21:05:17', '152.58.36.120', 0, 0),
(943, 570511, '2026-04-06 21:53:24', '152.58.36.109', 0, 0),
(944, 683005, '2026-04-06 22:47:29', '152.59.32.104', 0, 0),
(945, 683005, '2026-04-06 22:50:20', '152.59.32.104', 0, 0),
(946, 570511, '2026-04-06 23:12:15', '152.58.35.220', 0, 0),
(947, 416830, '2026-04-07 01:49:39', '42.104.216.14', 0, 0),
(948, 405830, '2026-04-07 06:23:01', '49.36.75.153', 0, 0),
(949, 570358, '2026-04-07 06:35:12', '42.104.223.13', 0, 0),
(950, 561505, '2026-04-07 06:38:45', '42.104.223.13', 0, 0),
(951, 360565, '2026-04-07 06:47:05', '42.104.223.13', 0, 0),
(952, 340484, '2026-04-07 06:50:57', '42.104.223.13', 0, 0),
(953, 277670, '2026-04-07 06:58:12', '42.104.223.13', 0, 0),
(954, 570511, '2026-04-07 08:17:10', '152.59.35.30', 0, 0),
(955, 570511, '2026-04-07 09:09:17', '152.59.40.148', 0, 0),
(956, 422853, '2026-04-07 09:14:51', '152.59.34.148', 0, 0),
(957, 570511, '2026-04-07 11:26:07', '152.59.40.161', 0, 0),
(958, 393907, '2026-04-07 12:26:35', '103.201.139.184', 0, 0),
(959, 442475, '2026-04-07 12:37:04', '223.184.230.245', 0, 0),
(960, 422853, '2026-04-07 12:51:31', '152.59.37.108', 0, 0),
(961, 405830, '2026-04-07 13:52:06', '42.108.133.169', 0, 0),
(962, 217974, '2026-04-07 15:41:59', '42.108.142.103', 0, 0),
(963, 683005, '2026-04-07 16:42:47', '152.58.37.164', 0, 0),
(964, 422853, '2026-04-07 17:53:32', '152.59.35.135', 0, 0),
(965, 217260, '2026-04-07 18:37:59', '42.108.238.245', 0, 0),
(966, 145703, '2026-04-07 18:50:29', '42.108.238.245', 0, 0),
(967, 683005, '2026-04-07 18:53:42', '152.58.37.65', 0, 0),
(968, 570511, '2026-04-07 21:35:32', '152.59.18.21', 0, 0),
(969, 570511, '2026-04-08 01:29:36', '152.58.62.239', 0, 0),
(970, 217260, '2026-04-08 04:44:10', '42.108.237.204', 0, 0),
(971, 217260, '2026-04-08 06:16:11', '42.104.220.44', 0, 0),
(972, 393907, '2026-04-08 07:59:05', '152.58.15.50', 0, 0),
(973, 217974, '2026-04-08 08:17:40', '42.108.197.83', 0, 0),
(974, 422853, '2026-04-08 08:56:03', '152.59.36.226', 0, 0),
(975, 416830, '2026-04-08 09:09:57', '42.104.219.32', 0, 0),
(976, 570511, '2026-04-08 11:15:32', '152.58.61.137', 0, 0),
(977, 217260, '2026-04-08 12:26:10', '42.104.216.25', 0, 0),
(978, 442475, '2026-04-08 12:57:06', '103.165.72.42', 0, 0),
(979, 570511, '2026-04-08 12:57:46', '152.58.61.175', 0, 0),
(980, 442475, '2026-04-08 15:08:23', '103.165.72.42', 0, 0),
(981, 475305, '2026-04-08 15:28:17', '117.222.143.9', 0, 0),
(982, 570511, '2026-04-08 16:46:44', '152.58.60.19', 0, 0),
(983, 683005, '2026-04-08 18:00:54', '152.59.35.6', 0, 0),
(984, 570511, '2026-04-08 19:59:39', '152.59.14.25', 0, 0),
(985, 570511, '2026-04-08 20:14:37', '152.59.14.25', 0, 0),
(986, 217260, '2026-04-08 20:24:08', '27.97.87.71', 0, 0),
(987, 491106, '2026-04-08 20:25:07', '27.97.87.71', 0, 0),
(988, 114365, '2026-04-08 20:26:10', '27.97.87.71', 0, 0),
(989, 599157, '2026-04-08 20:26:43', '27.97.87.71', 0, 0),
(990, 317468, '2026-04-08 20:27:25', '27.97.87.71', 0, 0),
(991, 500464, '2026-04-08 20:28:45', '27.97.88.255', 0, 0),
(992, 570358, '2026-04-08 20:29:24', '27.97.88.255', 0, 0),
(993, 583864, '2026-04-08 20:30:12', '27.97.88.255', 0, 0),
(994, 162047, '2026-04-08 20:32:12', '27.97.88.255', 0, 0),
(995, 482137, '2026-04-08 20:50:34', '47.15.74.78', 0, 0),
(996, 410714, '2026-04-08 22:11:59', '27.97.86.158', 0, 0),
(997, 713474, '2026-04-08 22:13:36', '27.97.86.158', 0, 0),
(998, 394067, '2026-04-08 22:14:35', '27.97.86.158', 0, 0),
(999, 366214, '2026-04-08 22:16:36', '27.97.88.255', 0, 0),
(1000, 250607, '2026-04-08 22:17:33', '27.97.88.255', 0, 0),
(1001, 643662, '2026-04-08 22:18:45', '27.97.88.255', 0, 0),
(1002, 394067, '2026-04-08 22:19:53', '27.97.88.255', 0, 0),
(1003, 570511, '2026-04-08 22:20:38', '152.59.16.229', 0, 0),
(1004, 422853, '2026-04-08 22:26:34', '152.59.36.217', 0, 0),
(1005, 570358, '2026-04-08 22:28:17', '27.97.88.255', 0, 0),
(1006, 500464, '2026-04-08 22:29:01', '27.97.88.255', 0, 0),
(1007, 570358, '2026-04-08 22:30:15', '27.97.88.255', 0, 0),
(1008, 394067, '2026-04-08 22:31:10', '27.97.88.255', 0, 0),
(1009, 500464, '2026-04-08 22:32:36', '27.97.88.255', 0, 0),
(1010, 340983, '2026-04-08 22:33:35', '27.97.88.255', 0, 0),
(1011, 482137, '2026-04-08 22:53:46', '152.59.16.229', 0, 0),
(1012, 588537, '2026-04-08 22:54:22', '1.39.16.141', 0, 0),
(1013, 570511, '2026-04-08 22:55:29', '152.59.16.229', 0, 0),
(1014, 588537, '2026-04-08 23:07:01', '152.59.16.229', 0, 0),
(1015, 570511, '2026-04-08 23:13:58', '152.59.16.229', 0, 0),
(1016, 570511, '2026-04-08 23:40:15', '152.59.16.148', 0, 0),
(1017, 265008, '2026-04-09 00:04:27', '49.15.102.173', 0, 0),
(1018, 570511, '2026-04-09 00:37:33', '152.59.16.40', 0, 0),
(1019, 570511, '2026-04-09 01:40:56', '152.59.35.104', 0, 0),
(1020, 570511, '2026-04-09 02:06:42', '152.59.35.233', 0, 0),
(1021, 588537, '2026-04-09 05:35:46', '1.39.17.132', 0, 0),
(1022, 394067, '2026-04-09 06:06:22', '27.97.80.248', 0, 0),
(1023, 643662, '2026-04-09 06:07:35', '27.97.80.248', 0, 0),
(1024, 250607, '2026-04-09 06:08:16', '27.97.80.248', 0, 0),
(1025, 217260, '2026-04-09 06:08:39', '27.97.80.248', 0, 0),
(1026, 482137, '2026-04-09 06:13:06', '47.15.108.32', 0, 0),
(1027, 250607, '2026-04-09 06:27:20', '27.97.94.127', 0, 0),
(1028, 643662, '2026-04-09 06:27:59', '27.97.94.127', 0, 0),
(1029, 366214, '2026-04-09 06:28:51', '27.97.94.127', 0, 0),
(1030, 539160, '2026-04-09 06:29:41', '27.97.94.127', 0, 0),
(1031, 405830, '2026-04-09 06:35:33', '49.36.75.153', 0, 0),
(1032, 217260, '2026-04-09 07:33:19', '27.97.94.47', 0, 0),
(1033, 588537, '2026-04-09 08:46:52', '1.39.17.132', 0, 0),
(1034, 442475, '2026-04-09 08:47:10', '103.240.235.194', 0, 0),
(1035, 422853, '2026-04-09 09:24:21', '152.59.36.74', 0, 0),
(1036, 570511, '2026-04-09 09:43:34', '152.58.36.234', 0, 0),
(1037, 482137, '2026-04-09 09:46:52', '47.15.101.197', 0, 0),
(1038, 570511, '2026-04-09 10:10:39', '152.58.36.234', 0, 0),
(1039, 570511, '2026-04-09 10:51:25', '152.58.36.151', 0, 0),
(1040, 217260, '2026-04-09 11:07:26', '1.187.153.29', 0, 0),
(1041, 217974, '2026-04-09 11:13:29', '42.108.196.90', 0, 0),
(1042, 588537, '2026-04-09 11:48:09', '1.39.16.140', 0, 0),
(1043, 482137, '2026-04-09 11:51:41', '157.48.103.91', 0, 0),
(1044, 570511, '2026-04-09 13:06:30', '152.58.63.82', 0, 0),
(1045, 557635, '2026-04-09 14:25:02', '1.187.171.131', 0, 0),
(1046, 422853, '2026-04-09 14:59:40', '152.59.36.187', 0, 0),
(1047, 570511, '2026-04-09 15:25:42', '152.58.62.17', 0, 0),
(1048, 475305, '2026-04-09 16:44:39', '117.222.143.183', 0, 0),
(1049, 570511, '2026-04-09 17:44:50', '152.58.63.249', 0, 0),
(1050, 570511, '2026-04-09 18:13:33', '152.58.63.249', 0, 0),
(1051, 217260, '2026-04-09 19:37:19', '27.97.87.213', 0, 0),
(1052, 671950, '2026-04-09 19:38:41', '152.58.60.49', 0, 0),
(1053, 442475, '2026-04-09 19:39:21', '150.242.86.69', 0, 0),
(1054, 570511, '2026-04-09 19:39:54', '152.58.60.49', 0, 0),
(1055, 671950, '2026-04-09 19:51:12', '1.39.16.142', 0, 0),
(1056, 570511, '2026-04-09 20:29:27', '152.58.60.156', 0, 0),
(1057, 570511, '2026-04-09 21:46:35', '152.58.60.17', 0, 0),
(1058, 482137, '2026-04-09 21:46:48', '157.48.111.38', 0, 0),
(1059, 570511, '2026-04-09 21:50:27', '152.58.60.17', 0, 0),
(1060, 570511, '2026-04-09 22:04:03', '152.58.34.120', 0, 0),
(1061, 744285, '2026-04-09 22:57:12', '47.30.225.244', 0, 0),
(1062, 570511, '2026-04-09 23:07:54', '152.58.34.156', 0, 0),
(1063, 570511, '2026-04-09 23:42:45', '152.58.35.222', 0, 0),
(1064, 394067, '2026-04-10 05:15:12', '27.97.95.107', 0, 0),
(1065, 643662, '2026-04-10 05:16:21', '27.97.95.107', 0, 0),
(1066, 683005, '2026-04-10 05:57:34', '103.86.17.135', 0, 0),
(1067, 217260, '2026-04-10 06:15:08', '27.97.94.145', 0, 0),
(1068, 671950, '2026-04-10 07:39:01', '1.39.18.132', 0, 0),
(1069, 570511, '2026-04-10 08:04:32', '152.58.60.56', 0, 0),
(1070, 570511, '2026-04-10 08:25:23', '152.58.60.56', 0, 0),
(1071, 422853, '2026-04-10 09:36:55', '152.59.36.21', 0, 0),
(1072, 482137, '2026-04-10 10:11:04', '47.15.68.48', 0, 0),
(1073, 217974, '2026-04-10 11:00:43', '42.108.196.197', 0, 0),
(1074, 482137, '2026-04-10 11:41:15', '47.15.98.27', 0, 0),
(1075, 570511, '2026-04-10 12:20:27', '152.58.60.31', 0, 0),
(1076, 570511, '2026-04-10 13:01:55', '152.58.60.119', 0, 0),
(1077, 683005, '2026-04-10 14:13:35', '152.59.37.132', 0, 0),
(1078, 198503, '2026-04-10 14:29:02', '152.59.37.27', 0, 0),
(1079, 683005, '2026-04-10 14:29:36', '152.59.37.27', 0, 0),
(1080, 198503, '2026-04-10 14:29:54', '152.59.37.27', 0, 0),
(1081, 198503, '2026-04-10 15:05:14', '152.59.35.196', 0, 0),
(1082, 198503, '2026-04-10 15:26:57', '152.59.35.106', 0, 0),
(1083, 482137, '2026-04-10 16:49:35', '47.15.84.243', 0, 0),
(1084, 198503, '2026-04-10 17:44:20', '152.59.35.203', 0, 0),
(1085, 683005, '2026-04-10 18:13:13', '152.59.35.55', 0, 0),
(1086, 570511, '2026-04-10 18:19:10', '152.59.38.7', 0, 0),
(1087, 198503, '2026-04-10 18:28:17', '152.59.35.184', 0, 0),
(1088, 442475, '2026-04-10 19:15:59', '106.196.52.125', 0, 0),
(1089, 217260, '2026-04-10 19:59:08', '27.97.80.95', 0, 0),
(1090, 557635, '2026-04-10 21:13:28', '49.37.100.14', 0, 0),
(1091, 557635, '2026-04-10 21:14:33', '49.37.100.14', 0, 0),
(1092, 482137, '2026-04-10 21:48:03', '47.15.64.210', 0, 0),
(1093, 557635, '2026-04-10 22:13:22', '27.97.103.90', 0, 0),
(1094, 405830, '2026-04-11 05:43:13', '49.36.75.153', 0, 0),
(1095, 482137, '2026-04-11 06:40:51', '47.15.80.194', 0, 0),
(1096, 671950, '2026-04-11 07:03:14', '1.39.16.135', 0, 0),
(1097, 422853, '2026-04-11 07:06:29', '152.59.36.121', 0, 0),
(1098, 570511, '2026-04-11 07:22:04', '152.58.60.248', 0, 0),
(1099, 405830, '2026-04-11 08:18:06', '42.108.133.213', 0, 0),
(1100, 217974, '2026-04-11 10:18:31', '42.108.129.9', 0, 0),
(1101, 217260, '2026-04-11 10:27:30', '42.108.238.235', 0, 0),
(1102, 217260, '2026-04-11 10:29:34', '42.108.238.235', 0, 0),
(1103, 570511, '2026-04-11 11:48:11', '152.58.61.131', 0, 0),
(1104, 393907, '2026-04-11 11:56:57', '103.201.139.190', 0, 0),
(1105, 570511, '2026-04-11 13:49:11', '152.58.61.220', 0, 0),
(1106, 405830, '2026-04-11 17:06:54', '42.108.137.56', 0, 0),
(1107, 570511, '2026-04-11 18:57:02', '152.59.36.57', 0, 0),
(1108, 482137, '2026-04-11 19:06:28', '47.15.64.35', 0, 0),
(1109, 442475, '2026-04-11 19:21:38', '223.228.72.80', 0, 0),
(1110, 570511, '2026-04-11 19:58:34', '152.59.36.254', 0, 0),
(1111, 570511, '2026-04-11 21:25:50', '152.59.36.223', 0, 0),
(1112, 570511, '2026-04-11 22:26:21', '152.59.36.78', 0, 0),
(1113, 482137, '2026-04-11 23:00:49', '47.15.107.211', 0, 0),
(1114, 671950, '2026-04-11 23:33:12', '1.39.16.130', 0, 0),
(1115, 570511, '2026-04-12 05:17:14', '152.58.60.16', 0, 0),
(1116, 482137, '2026-04-12 05:48:08', '47.15.65.167', 0, 0),
(1117, 405830, '2026-04-12 06:08:48', '49.36.75.153', 0, 0),
(1118, 570511, '2026-04-12 06:30:49', '59.95.39.81', 0, 0),
(1119, 198503, '2026-04-12 08:46:36', '152.58.36.178', 0, 0),
(1120, 442475, '2026-04-12 10:31:40', '223.228.72.80', 0, 0),
(1121, 482137, '2026-04-12 10:54:01', '47.15.78.45', 0, 0),
(1122, 570511, '2026-04-12 13:00:50', '152.59.3.207', 0, 0),
(1123, 198503, '2026-04-12 15:36:03', '152.59.33.200', 0, 0),
(1124, 393907, '2026-04-12 19:29:15', '103.201.139.190', 0, 0),
(1125, 442475, '2026-04-12 20:07:30', '150.129.237.127', 0, 0),
(1126, 570511, '2026-04-12 21:05:00', '152.59.17.89', 0, 0),
(1127, 509367, '2026-04-13 00:23:09', '152.59.167.109', 0, 0),
(1128, 509367, '2026-04-13 00:23:44', '152.59.167.109', 0, 0),
(1129, 100, '2026-04-13 00:25:24', '152.59.167.109', 0, 0),
(1130, 100, '2026-04-13 00:26:01', '152.59.167.109', 0, 0),
(1131, 217260, '2026-04-13 05:07:19', '42.108.237.230', 0, 0),
(1132, 217260, '2026-04-13 05:12:52', '42.108.237.230', 0, 0),
(1133, 570511, '2026-04-13 05:46:21', '152.59.17.95', 0, 0),
(1134, 482137, '2026-04-13 06:00:03', '47.15.73.68', 0, 0),
(1135, 570511, '2026-04-13 07:46:10', '152.59.17.88', 0, 0),
(1136, 217260, '2026-04-13 08:12:31', '42.104.227.9', 0, 0),
(1137, 442475, '2026-04-13 08:15:45', '223.228.75.187', 0, 0),
(1138, 405830, '2026-04-13 09:32:06', '42.108.192.55', 0, 0),
(1139, 422853, '2026-04-13 12:16:09', '152.59.36.136', 0, 0),
(1140, 570511, '2026-04-13 12:32:49', '152.59.15.155', 0, 0),
(1141, 217260, '2026-04-13 13:23:12', '42.104.227.34', 0, 0),
(1142, 671950, '2026-04-13 14:42:24', '1.39.21.133', 0, 0),
(1143, 422853, '2026-04-13 14:54:17', '152.59.36.81', 0, 0),
(1144, 570511, '2026-04-13 16:04:27', '47.11.118.206', 0, 0),
(1145, 198503, '2026-04-13 16:42:20', '152.59.32.159', 0, 0),
(1146, 683005, '2026-04-13 16:43:48', '152.59.32.159', 0, 0),
(1147, 570511, '2026-04-13 17:25:49', '152.59.0.162', 0, 0),
(1148, 217974, '2026-04-13 18:03:55', '42.108.196.64', 0, 0),
(1149, 422853, '2026-04-13 18:51:46', '152.59.36.17', 0, 0),
(1150, 570511, '2026-04-13 20:22:58', '152.59.1.242', 0, 0),
(1151, 482137, '2026-04-14 06:55:42', '47.15.92.88', 0, 0),
(1152, 570511, '2026-04-14 07:02:28', '152.59.17.114', 0, 0),
(1153, 405830, '2026-04-14 07:45:57', '42.108.192.18', 0, 0),
(1154, 475305, '2026-04-14 09:59:32', '117.222.138.187', 0, 0),
(1155, 570511, '2026-04-14 10:33:40', '152.59.17.105', 0, 0),
(1156, 217260, '2026-04-14 11:51:13', '42.108.237.144', 0, 0),
(1157, 422853, '2026-04-14 12:25:54', '152.59.36.45', 0, 0),
(1158, 198503, '2026-04-14 12:48:59', '152.58.36.137', 0, 0),
(1159, 570511, '2026-04-14 13:31:33', '152.58.35.88', 0, 0),
(1160, 570511, '2026-04-14 14:15:43', '152.58.34.26', 0, 0),
(1161, 422853, '2026-04-14 15:49:42', '152.59.36.91', 0, 0),
(1162, 405830, '2026-04-14 17:10:55', '152.59.33.82', 0, 0),
(1163, 570511, '2026-04-14 17:13:41', '152.59.33.82', 0, 0),
(1164, 570511, '2026-04-14 18:53:22', '152.59.14.247', 0, 0),
(1165, 217260, '2026-04-14 19:18:07', '42.108.239.158', 0, 0),
(1166, 422853, '2026-04-14 20:03:28', '152.59.36.243', 0, 0),
(1167, 405830, '2026-04-14 20:18:48', '152.58.62.172', 0, 0),
(1168, 422853, '2026-04-14 20:49:57', '152.59.34.66', 0, 0),
(1169, 570511, '2026-04-14 20:52:24', '152.58.60.248', 0, 0),
(1170, 683005, '2026-04-14 21:07:49', '152.58.36.22', 0, 0),
(1171, 198503, '2026-04-14 21:16:25', '152.58.36.82', 0, 0),
(1172, 198503, '2026-04-14 22:06:32', '152.58.36.16', 0, 0),
(1173, 442475, '2026-04-14 23:22:52', '49.42.64.15', 0, 0),
(1174, 482137, '2026-04-15 06:33:49', '47.15.80.90', 0, 0),
(1175, 195686, '2026-04-15 06:58:32', '42.104.223.26', 0, 0),
(1176, 570511, '2026-04-15 07:17:11', '152.59.39.154', 0, 0),
(1177, 405830, '2026-04-15 07:17:52', '152.59.39.154', 0, 0),
(1178, 422853, '2026-04-15 08:40:42', '152.59.36.146', 0, 0),
(1179, 217260, '2026-04-15 09:39:02', '42.104.223.11', 0, 0),
(1180, 217974, '2026-04-15 10:59:00', '42.108.196.55', 0, 0),
(1181, 405830, '2026-04-15 11:18:14', '152.59.38.83', 0, 0),
(1182, 570511, '2026-04-15 11:18:27', '152.59.38.83', 0, 0),
(1183, 195686, '2026-04-15 12:43:35', '42.104.223.82', 0, 0),
(1184, 217260, '2026-04-15 13:15:31', '42.104.223.82', 0, 0),
(1185, 570511, '2026-04-15 16:13:54', '152.59.39.74', 0, 0),
(1186, 405830, '2026-04-15 16:14:50', '152.59.39.74', 0, 0),
(1187, 482137, '2026-04-15 16:44:02', '47.15.84.177', 0, 0),
(1188, 198503, '2026-04-15 17:25:49', '152.59.37.183', 0, 0),
(1189, 475305, '2026-04-15 18:43:11', '117.222.137.208', 0, 0),
(1190, 405830, '2026-04-15 20:16:39', '152.59.37.68', 0, 0),
(1191, 570511, '2026-04-15 20:16:56', '152.59.37.68', 0, 0),
(1192, 422853, '2026-04-15 21:40:49', '152.59.36.152', 0, 0),
(1193, 570511, '2026-04-16 00:06:12', '152.58.34.167', 0, 0),
(1194, 405830, '2026-04-16 00:12:10', '152.58.34.167', 0, 0),
(1195, 570511, '2026-04-16 00:16:40', '152.58.34.167', 0, 0),
(1196, 482137, '2026-04-16 07:22:26', '117.237.6.5', 0, 0),
(1197, 570511, '2026-04-16 07:46:41', '152.58.36.101', 0, 0),
(1198, 405830, '2026-04-16 08:15:36', '152.58.36.101', 0, 0),
(1199, 405830, '2026-04-16 11:51:42', '152.58.36.243', 0, 0),
(1200, 570511, '2026-04-16 11:51:48', '152.58.36.243', 0, 0),
(1201, 100, '2026-07-17 11:28:15', '::1', 0, 0),
(1202, 100, '2026-08-27 14:22:08', '::1', 0, 0),
(1203, 534191, '2026-08-29 09:20:03', '::1', 0, 0),
(1204, 100, '2026-08-29 10:41:41', '::1', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `walfare_fund`
--

CREATE TABLE `walfare_fund` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_block`
--

CREATE TABLE `withdrawal_block` (
  `recid` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `fee` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `net_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `amount_coin` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `remark` text NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `approved_datetime` datetime DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `withdrawal_address` varchar(255) NOT NULL DEFAULT '',
  `withdrawal_id` varchar(500) NOT NULL DEFAULT '',
  `withdrawal_status` varchar(255) NOT NULL DEFAULT '',
  `error` varchar(255) NOT NULL DEFAULT '',
  `type2` varchar(20) NOT NULL DEFAULT '',
  `widthdrawal_type` enum('USDT','INR') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `withdrawal_block`
--

INSERT INTO `withdrawal_block` (`recid`, `uid`, `amount`, `fee`, `net_amount`, `amount_coin`, `remark`, `datetime`, `approved_datetime`, `type`, `status`, `withdrawal_address`, `withdrawal_id`, `withdrawal_status`, `error`, `type2`, `widthdrawal_type`) VALUES
(1, 100, 1000.00000000, 20.00000000, 980.00000000, 980.00000000, '', '2026-08-29 09:13:23', NULL, 'USDT', 0, 'wdq', '', '', '', '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`recid`),
  ADD UNIQUE KEY `login_id` (`login_id`);

--
-- Indexes for table `admin_login_detail`
--
ALTER TABLE `admin_login_detail`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `cms`
--
ALTER TABLE `cms`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `cms_categories`
--
ALTER TABLE `cms_categories`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `cms_menu`
--
ALTER TABLE `cms_menu`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `deposit_block`
--
ALTER TABLE `deposit_block`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `fund_deduct`
--
ALTER TABLE `fund_deduct`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `fund_transfer`
--
ALTER TABLE `fund_transfer`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `hot_news`
--
ALTER TABLE `hot_news`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_binary`
--
ALTER TABLE `income_binary`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_direct`
--
ALTER TABLE `income_direct`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_growth`
--
ALTER TABLE `income_growth`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_level`
--
ALTER TABLE `income_level`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_reward`
--
ALTER TABLE `income_reward`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `income_royalty`
--
ALTER TABLE `income_royalty`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `investments_plan`
--
ALTER TABLE `investments_plan`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `recharge`
--
ALTER TABLE `recharge`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `smart_contract_batches`
--
ALTER TABLE `smart_contract_batches`
  ADD PRIMARY KEY (`batch_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`recid`),
  ADD UNIQUE KEY `user_id` (`uid`,`login_id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `userre`
--
ALTER TABLE `userre`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `user_login_detail`
--
ALTER TABLE `user_login_detail`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `walfare_fund`
--
ALTER TABLE `walfare_fund`
  ADD PRIMARY KEY (`recid`);

--
-- Indexes for table `withdrawal_block`
--
ALTER TABLE `withdrawal_block`
  ADD PRIMARY KEY (`recid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_detail`
--
ALTER TABLE `admin_login_detail`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms`
--
ALTER TABLE `cms`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_categories`
--
ALTER TABLE `cms_categories`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_menu`
--
ALTER TABLE `cms_menu`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `deposit_block`
--
ALTER TABLE `deposit_block`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fund_deduct`
--
ALTER TABLE `fund_deduct`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fund_transfer`
--
ALTER TABLE `fund_transfer`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hot_news`
--
ALTER TABLE `hot_news`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `income_binary`
--
ALTER TABLE `income_binary`
  MODIFY `recid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_direct`
--
ALTER TABLE `income_direct`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_growth`
--
ALTER TABLE `income_growth`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `income_level`
--
ALTER TABLE `income_level`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_reward`
--
ALTER TABLE `income_reward`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_royalty`
--
ALTER TABLE `income_royalty`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `investments_plan`
--
ALTER TABLE `investments_plan`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `recharge`
--
ALTER TABLE `recharge`
  MODIFY `recid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smart_contract_batches`
--
ALTER TABLE `smart_contract_batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userre`
--
ALTER TABLE `userre`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login_detail`
--
ALTER TABLE `user_login_detail`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1205;

--
-- AUTO_INCREMENT for table `walfare_fund`
--
ALTER TABLE `walfare_fund`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_block`
--
ALTER TABLE `withdrawal_block`
  MODIFY `recid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
