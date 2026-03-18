-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 28, 2025 at 10:44 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marketplace`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_electronics_listing` (IN `p_userid` INT, IN `p_headline` VARCHAR(200), IN `p_description` TEXT, IN `p_location_id` INT, IN `p_date_id` INT, IN `p_price_id` INT, IN `p_insurance` VARCHAR(50), IN `p_eage` INT, IN `p_ebrand` VARCHAR(50))   BEGIN
    DECLARE new_lid INT;

    INSERT INTO Listings (userid, headline, description, location_id, date_id, price_id)
    VALUES (p_userid, p_headline, p_description, p_location_id, p_date_id, p_price_id);

    SET new_lid = LAST_INSERT_ID();

    INSERT INTO Electronics (lid, category_id, insurance, eage, ebrand)
    VALUES (new_lid, 3, p_insurance, p_eage, p_ebrand);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_house_listing` (IN `p_userid` INT, IN `p_headline` VARCHAR(200), IN `p_description` TEXT, IN `p_location_id` INT, IN `p_date_id` INT, IN `p_price_id` INT, IN `p_m2` DECIMAL(10,2), IN `p_room_count` INT, IN `p_bage` INT)   BEGIN
    DECLARE new_lid INT;
    INSERT INTO Listings (userid, headline, description, location_id, date_id, price_id)
    VALUES (p_userid, p_headline, p_description, p_location_id, p_date_id, p_price_id);
    SET new_lid = LAST_INSERT_ID();
    INSERT INTO Houses (lid, category_id, m2, room_count, bage)
    VALUES (new_lid, 2, p_m2, p_room_count, p_bage);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_vehicle_listing` (IN `p_userid` INT, IN `p_headline` VARCHAR(200), IN `p_description` TEXT, IN `p_location_id` INT, IN `p_date_id` INT, IN `p_price_id` INT, IN `p_km` INT, IN `p_vbrand` VARCHAR(50), IN `p_vage` INT)   BEGIN
    DECLARE new_lid INT;
    
    -- First, find the maximum lid and increment it
    SELECT COALESCE(MAX(lid), 0) + 1 INTO new_lid FROM Listings;
    
    -- Insert into Listings table with the new lid
    INSERT INTO Listings (lid, userid, headline, description, location_id, date_id, price_id)
    VALUES (new_lid, p_userid, p_headline, p_description, p_location_id, p_date_id, p_price_id);
    
    -- Insert into Vehicles table
    INSERT INTO Vehicles (lid, category_id, km, vbrand, vage)
    VALUES (new_lid, 1, p_km, p_vbrand, p_vage);
    
    -- Return the new lid for reference
    SELECT new_lid AS new_listing_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generate_listing_report` (IN `p_category` VARCHAR(50))   BEGIN
    IF p_category = 'Vehicle' THEN
        SELECT L.lid, U.name, L.headline, V.km, V.vbrand, V.vage
        FROM Listings L
        JOIN Users U ON L.userid = U.userid
        JOIN Vehicles V ON L.lid = V.lid;
    ELSEIF p_category = 'House' THEN
        SELECT L.lid, U.name, L.headline, H.m2, H.room_count, H.bage
        FROM Listings L
        JOIN Users U ON L.userid = U.userid
        JOIN Houses H ON L.lid = H.lid;
    ELSEIF p_category = 'Electronics' THEN
        SELECT L.lid, U.name, L.headline, E.insurance, E.eage, E.ebrand
        FROM Listings L
        JOIN Users U ON L.userid = U.userid
        JOIN Electronics E ON L.lid = E.lid;
    ELSE
        SELECT 'Invalid category' AS Error;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL CHECK (`name` in ('Vehicle','House','Electronics'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`category_id`, `name`) VALUES
(1, 'Vehicle'),
(2, 'House'),
(3, 'Electronics');

-- --------------------------------------------------------

--
-- Table structure for table `Dates`
--

CREATE TABLE `Dates` (
  `date_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL CHECK (`month` between 1 and 12),
  `day` int(11) NOT NULL CHECK (`day` between 1 and 31)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Dates`
--

INSERT INTO `Dates` (`date_id`, `year`, `month`, `day`) VALUES
(1, 2025, 5, 21),
(4, 2025, 5, 20),
(5, 2025, 5, 21),
(6, 2025, 5, 21),
(7, 2025, 5, 21),
(8, 2025, 5, 27),
(9, 2025, 5, 27),
(10, 2025, 5, 27),
(11, 2025, 5, 27),
(12, 2025, 5, 27),
(13, 2025, 5, 27),
(14, 2025, 5, 27),
(15, 2025, 5, 27),
(16, 2025, 5, 27),
(17, 2025, 5, 27),
(18, 2025, 5, 27),
(19, 2025, 5, 27),
(20, 2025, 5, 27),
(21, 2025, 5, 27),
(22, 2025, 5, 27),
(23, 2025, 5, 27),
(24, 2025, 5, 27),
(25, 2025, 5, 27),
(26, 2025, 5, 27),
(27, 2025, 5, 27),
(28, 2025, 5, 27),
(29, 2025, 5, 27),
(30, 2025, 5, 27),
(31, 2025, 5, 27),
(32, 2025, 5, 27),
(33, 2025, 5, 27),
(34, 2025, 5, 27),
(35, 2025, 5, 27),
(36, 2025, 5, 27),
(37, 2025, 5, 27),
(38, 2025, 5, 27),
(39, 2025, 5, 27),
(40, 2025, 5, 27),
(41, 2025, 5, 27),
(42, 2025, 5, 27),
(43, 2025, 5, 27),
(44, 2025, 5, 27),
(45, 2025, 5, 27),
(46, 2025, 5, 27),
(47, 2025, 5, 27),
(48, 2025, 5, 27),
(49, 2025, 5, 27),
(50, 2025, 5, 27),
(51, 2025, 5, 28),
(52, 2025, 5, 28),
(53, 2025, 5, 28),
(54, 2025, 5, 28);

-- --------------------------------------------------------

--
-- Table structure for table `Electronics`
--

CREATE TABLE `Electronics` (
  `lid` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `insurance` varchar(50) DEFAULT NULL,
  `eage` int(11) DEFAULT NULL CHECK (`eage` >= 0),
  `ebrand` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Electronics`
--

INSERT INTO `Electronics` (`lid`, `category_id`, `insurance`, `eage`, `ebrand`) VALUES
(76, 3, 'Standard', 1, 'Samsung'),
(77, 3, 'Standard', 1, 'Samsung'),
(78, 3, 'Standard', 1, 'Samsung'),
(91, 3, 'Standard', 1, 'Samsung'),
(92, 3, 'Standard', 1, 'Samsung'),
(95, 3, 'Standard', 12, 'Apple'),
(104, 3, 'Standard', 1, 'Samsung'),
(105, 3, 'Standard', 1, 'Samsung'),
(106, 3, 'Standard', 1, 'Samsung'),
(107, 3, 'Standard', 1, 'Samsung'),
(108, 3, 'Standard', 1, 'Samsung'),
(109, 3, 'Standard', 1, 'Samsung'),
(110, 3, 'Standard', 1, 'Samsung'),
(111, 3, 'Standard', 1, 'Samsung'),
(112, 3, 'Standard', 1, 'Samsung'),
(113, 3, 'Standard', 1, 'Samsung'),
(114, 3, 'Standard', 1, 'Samsung'),
(115, 3, 'Standard', 1, 'Samsung'),
(116, 3, 'Standard', 1, 'Samsung'),
(117, 3, 'Standard', 1, 'Samsung'),
(120, 3, 'Standard', 1, 'Samsung'),
(121, 3, 'Standard', 1, 'Samsung'),
(122, 3, 'Standard', 1, 'Samsung'),
(124, 3, 'Standard', 2, 'Samsung'),
(126, 3, 'Standard', 2, 'Samsung'),
(128, 3, 'Standard', 2, 'Samsung'),
(140, 3, 'Standard', 2, 'Samsung');

--
-- Triggers `Electronics`
--
DELIMITER $$
CREATE TRIGGER `before_insert_electronics_specs` BEFORE INSERT ON `Electronics` FOR EACH ROW BEGIN
    IF NEW.insurance IS NULL OR NEW.eage IS NULL OR NEW.ebrand IS NULL OR NEW.ebrand = '' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Missing required specifications for electronics.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Houses`
--

CREATE TABLE `Houses` (
  `lid` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `m2` decimal(10,2) DEFAULT NULL CHECK (`m2` > 0),
  `room_count` int(11) DEFAULT NULL CHECK (`room_count` > 0),
  `bage` int(11) DEFAULT NULL CHECK (`bage` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Houses`
--

INSERT INTO `Houses` (`lid`, `category_id`, `m2`, `room_count`, `bage`) VALUES
(51, 2, 90.50, 3, 10),
(53, 2, 90.50, 3, 10),
(57, 2, 90.50, 3, 10),
(59, 2, 90.50, 3, 10),
(60, 2, 90.50, 3, 10),
(62, 2, 90.50, 3, 10),
(63, 2, 90.50, 3, 10),
(64, 2, 90.50, 3, 10),
(66, 2, 87.00, 2, 3),
(67, 2, 87.00, 2, 3),
(68, 2, 90.50, 3, 10),
(74, 2, 90.50, 3, 10),
(84, 2, 90.50, 3, 10),
(86, 2, 90.50, 3, 10),
(89, 2, 90.50, 3, 10),
(94, 2, 12.00, 2, 23),
(118, 2, 90.50, 3, 10),
(131, 2, 90.50, 3, 10),
(137, 1, 120.50, 4, 5);

--
-- Triggers `Houses`
--
DELIMITER $$
CREATE TRIGGER `before_insert_house_specs` BEFORE INSERT ON `Houses` FOR EACH ROW BEGIN
    IF NEW.m2 IS NULL OR NEW.room_count IS NULL OR NEW.bage IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Missing required specifications for house.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Listings`
--

CREATE TABLE `Listings` (
  `lid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `headline` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `date_id` int(11) NOT NULL,
  `price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Listings`
--

INSERT INTO `Listings` (`lid`, `userid`, `headline`, `description`, `location_id`, `date_id`, `price_id`) VALUES
(2, 2, 'Sıfır ayarında yeni kasa 320', 'Doktordan, bakımlı', 12, 4, 2),
(3, 1, 'jhdgjhad', 'kshdbfkhsabdc', 12, 5, 3),
(4, 4, 'jdnkajdn', 'kjadndkjasnd', 12, 6, 4),
(5, 4, 'ghfjgvj', 'jgfjgkg', 13, 7, 5),
(12, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(33, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(34, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(35, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(36, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(37, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(38, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(39, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(40, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(41, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(42, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(43, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(44, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(45, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(46, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(47, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(48, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(49, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(50, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(51, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(52, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(53, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(54, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(55, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(56, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(57, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(58, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(59, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(60, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(61, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(62, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(63, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(64, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(65, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(66, 4, 'pepe', 'pepe', 1, 8, 6),
(67, 4, 'pepe', 'pepe', 1, 9, 7),
(68, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(69, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(70, 4, 'Trigger Test', 'Testing trigger', 1, 1, 1),
(71, 4, 'Trigger Test', 'Testing trigger', 1, 1, 1),
(72, 4, 'hgfhj', 'hjgjhg', 12, 10, 8),
(73, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(74, 4, 'Trigger House Test', 'Testing house trigger', 1, 1, 1),
(75, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 11, 9),
(76, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 12, 10),
(77, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 13, 11),
(78, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 14, 12),
(79, 4, 'Trigger Test', 'Testing trigger', 1, 1, 1),
(80, 4, 'Trigger Test', 'Testing trigger', 1, 1, 1),
(81, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 15, 13),
(82, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 16, 14),
(83, 4, 'jshdgf', 'hsjdfb', 12, 17, 15),
(84, 4, 'House Trigger Test', 'Testing house trigger', 1, 1, 1),
(86, 4, 'House Trigger Test', 'Testing house trigger', 1, 1, 1),
(87, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 18, 16),
(88, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 19, 17),
(89, 4, 'House Trigger Test', 'Testing house trigger', 1, 1, 1),
(91, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 20, 18),
(92, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 21, 19),
(93, 4, 'hdajhgd', 'jshdbf', 12, 22, 20),
(94, 4, '123213', '21312312', 14, 23, 21),
(95, 4, '123213', '12323', 3, 24, 22),
(96, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 25, 23),
(97, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 26, 24),
(98, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 27, 25),
(99, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 28, 26),
(100, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 29, 27),
(101, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 30, 28),
(102, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 31, 29),
(104, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 32, 30),
(105, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 33, 31),
(106, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 34, 32),
(107, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 35, 33),
(108, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 36, 34),
(109, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 37, 35),
(110, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 38, 36),
(111, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 39, 37),
(112, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 40, 38),
(113, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 41, 39),
(114, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 42, 40),
(115, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 43, 41),
(116, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 44, 42),
(117, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 45, 43),
(118, 4, 'House Trigger Test', 'Testing house trigger', 1, 1, 1),
(119, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 46, 44),
(120, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 47, 45),
(121, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 48, 46),
(122, 4, 'TRIGGER TEST INVALID INSERT', 'test insert, brand: Samsung, address: İzmir, Konak', 13, 49, 47),
(124, 4, 'Trigger Electronics Test', 'Testing electronics trigger', 1, 1, 1),
(126, 4, 'Trigger Electronics Test', 'Testing electronics trigger', 1, 1, 1),
(128, 4, 'Trigger Electronics Test', 'Testing electronics trigger', 1, 1, 1),
(129, 4, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 50, 48),
(131, 4, 'House Trigger Test', 'Testing house trigger', 1, 1, 1),
(132, 6, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 51, 49),
(133, 6, 'YENI KASA 320İ', 'DOKTORDAN KAPALI GARAJDA TRAMERSİZ', 30, 52, 50),
(134, 6, 'TRIGGER TEST VALID INSERT', 'test insert, brand: BMW, address: İzmir, Konak', 13, 53, 51),
(137, 1, 'Test House Listing', 'Testing house trigger', 1, 1, 1),
(138, 6, 'DENEME', 'ACIKLAMA 2222', 8, 54, 52),
(140, 6, 'Trigger Electronics Test', 'Testing electronics trigger', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Locations`
--

CREATE TABLE `Locations` (
  `location_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Locations`
--

INSERT INTO `Locations` (`location_id`, `city`, `street`) VALUES
(1, 'İstanbul', 'Pendik'),
(2, 'İstanbul', 'Kadıköy'),
(3, 'İstanbul', 'Beşiktaş'),
(4, 'İstanbul', 'Üsküdar'),
(5, 'İstanbul', 'Şişli'),
(6, 'Ankara', 'Çankaya'),
(7, 'Ankara', 'Keçiören'),
(8, 'Ankara', 'Mamak'),
(9, 'Ankara', 'Sincan'),
(10, 'Ankara', 'Etimesgut'),
(11, 'İzmir', 'Karşıyaka'),
(12, 'İzmir', 'Bornova'),
(13, 'İzmir', 'Konak'),
(14, 'İzmir', 'Buca'),
(15, 'İzmir', 'Çiğli'),
(16, 'Adana', 'Seyhan'),
(17, 'Adana', 'Çukurova'),
(18, 'Adana', 'Yüreğir'),
(19, 'Adana', 'Sarıçam'),
(20, 'Adana', 'Ceyhan'),
(21, 'Bursa', 'Osmangazi'),
(22, 'Bursa', 'Nilüfer'),
(23, 'Bursa', 'Yıldırım'),
(24, 'Bursa', 'Mudanya'),
(25, 'Bursa', 'Gemlik'),
(26, 'Mersin', 'Akdeniz'),
(27, 'Mersin', 'Mezitli'),
(28, 'Mersin', 'Toroslar'),
(29, 'Mersin', 'Yenişehir'),
(30, 'Mersin', 'Tarsus');

-- --------------------------------------------------------

--
-- Table structure for table `Prices`
--

CREATE TABLE `Prices` (
  `price_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Prices`
--

INSERT INTO `Prices` (`price_id`, `currency`, `amount`) VALUES
(1, 'TRY', 654000.00),
(2, 'TRY', 20000000.00),
(3, 'TRY', 1200000.00),
(4, 'TRY', 21278.00),
(5, 'TRY', 54353.00),
(6, 'TRY', 12540000.00),
(7, 'TRY', 12540000.00),
(8, 'TRY', 124135.00),
(9, 'USD', 3000.00),
(10, 'USD', 20.00),
(11, 'USD', 3000.00),
(12, 'USD', 20.00),
(13, 'USD', 200.00),
(14, 'USD', 5000.00),
(15, 'TRY', 124314.00),
(16, 'USD', 5000.00),
(17, 'USD', 200.00),
(18, 'USD', 3000.00),
(19, 'USD', 20.00),
(20, 'USD', 123232.00),
(21, 'TRY', 123232.00),
(22, 'TRY', 123213.00),
(23, 'USD', 5000.00),
(24, 'USD', 200.00),
(25, 'USD', 5000.00),
(26, 'USD', 200.00),
(27, 'USD', 5000.00),
(28, 'USD', 200.00),
(29, 'USD', 5000.00),
(30, 'USD', 20.00),
(31, 'USD', 3000.00),
(32, 'USD', 20.00),
(33, 'USD', 20.00),
(34, 'USD', 20.00),
(35, 'USD', 3000.00),
(36, 'USD', 20.00),
(37, 'USD', 3000.00),
(38, 'USD', 20.00),
(39, 'USD', 3000.00),
(40, 'USD', 20.00),
(41, 'USD', 20.00),
(42, 'USD', 3000.00),
(43, 'USD', 20.00),
(44, 'USD', 5000.00),
(45, 'USD', 20.00),
(46, 'USD', 3000.00),
(47, 'USD', 20.00),
(48, 'USD', 5000.00),
(49, 'USD', 5000.00),
(50, 'TRY', 3500000.00),
(51, 'USD', 5000.00),
(52, 'TRY', 1500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userid`, `name`, `age`, `phone_number`, `password`) VALUES
(1, 'CemSarp', 21, '23323232', '$2y$10$otOSk0EOOLlpwSSffLIaMOBl0/g5mX5ihHBqXD2Y0iCh2SBBd0KpC'),
(2, 'öykü', 20, '4345345', '$2y$10$lrFP0ac2uuunjMx0dScUj.6u2ivPw1K7fDZKhv0GtnXEUIHs2mJ7G'),
(3, 'egederman', 21, '234234', '$2y$10$qeOODXyAnoujccpp3kASTefSWnCYAzo1oupnZhzulGti4RS5SlEHq'),
(4, 'petek', 22, '34324', '$2y$10$tiWNRQE4eG88wwQmNV6b.e1wJc.B72s/VxCxDiODRevdfN4QxCYI2'),
(5, 'petekadmin', 22, '213123', '$2y$10$s8eqf.be8BsCTGwSNogOUOvCWesr/6DsVZWu5pWv3UBeWDDcyVk/O'),
(6, 'cemo', 21, '5446578874', '$2y$10$5wSBZMmrvoKkXH9mTUNlXe2Qb02v82etSc/3FqdtZ6Hx2GMUAe1Y.');

--
-- Triggers `Users`
--
DELIMITER $$
CREATE TRIGGER `before_insert_user_specs` BEFORE INSERT ON `Users` FOR EACH ROW BEGIN
    IF NEW.phone_number IS NULL OR NEW.phone_number = '' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'User phone number must be provided.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Vehicles`
--

CREATE TABLE `Vehicles` (
  `lid` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `km` int(11) DEFAULT NULL CHECK (`km` >= 0),
  `vbrand` varchar(50) DEFAULT NULL,
  `vage` int(11) DEFAULT NULL CHECK (`vage` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Vehicles`
--

INSERT INTO `Vehicles` (`lid`, `category_id`, `km`, `vbrand`, `vage`) VALUES
(2, 1, 12000, 'BMW', 1),
(3, 1, 12000, 'Fiat', 2),
(70, 1, 1000, 'BrandX', 2),
(80, 1, 1000, 'BrandX', 2),
(81, 1, 1000, 'BMW', 1),
(82, 1, 1000, 'BMW', 1),
(83, 1, 1524, 'Fiat', 12),
(87, 1, 1000, 'BMW', 1),
(88, 1, 1000, 'BMW', 1),
(93, 1, 12323, 'Renault', 12),
(96, 1, 1000, 'BMW', 1),
(97, 1, 1000, 'BMW', 1),
(98, 1, 1000, 'BMW', 1),
(99, 1, 1000, 'BMW', 1),
(100, 1, 1000, 'BMW', 1),
(101, 1, 1000, 'BMW', 1),
(102, 1, 1000, 'BMW', 1),
(119, 1, 1000, 'BMW', 1),
(129, 1, 1000, 'BMW', 1),
(132, 1, 1000, 'BMW', 1),
(133, 1, 8000, 'BMW', 1),
(134, 1, 1000, 'BMW', 1),
(138, 1, 28000, 'Volkswagen', 3);

--
-- Triggers `Vehicles`
--
DELIMITER $$
CREATE TRIGGER `before_insert_vehicle_price_check` BEFORE INSERT ON `Vehicles` FOR EACH ROW BEGIN
    DECLARE price_amt DECIMAL(10,2);

    SELECT p.amount INTO price_amt
    FROM Prices p
    JOIN Listings l ON l.price_id = p.price_id
    WHERE l.lid = NEW.lid
    LIMIT 1;

    IF price_amt < 1000 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Price is suspiciously low for a vehicle. Must be at least 1000.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Wishlists`
--

CREATE TABLE `Wishlists` (
  `wishlist_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `wname` varchar(50) NOT NULL,
  `added_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `Dates`
--
ALTER TABLE `Dates`
  ADD PRIMARY KEY (`date_id`);

--
-- Indexes for table `Electronics`
--
ALTER TABLE `Electronics`
  ADD PRIMARY KEY (`lid`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `Houses`
--
ALTER TABLE `Houses`
  ADD PRIMARY KEY (`lid`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `Listings`
--
ALTER TABLE `Listings`
  ADD PRIMARY KEY (`lid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `date_id` (`date_id`),
  ADD KEY `price_id` (`price_id`);

--
-- Indexes for table `Locations`
--
ALTER TABLE `Locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `Prices`
--
ALTER TABLE `Prices`
  ADD PRIMARY KEY (`price_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `Vehicles`
--
ALTER TABLE `Vehicles`
  ADD PRIMARY KEY (`lid`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `Wishlists`
--
ALTER TABLE `Wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Listings`
--
ALTER TABLE `Listings`
  MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Electronics`
--
ALTER TABLE `Electronics`
  ADD CONSTRAINT `electronics_ibfk_1` FOREIGN KEY (`lid`) REFERENCES `Listings` (`lid`),
  ADD CONSTRAINT `electronics_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`);

--
-- Constraints for table `Houses`
--
ALTER TABLE `Houses`
  ADD CONSTRAINT `houses_ibfk_1` FOREIGN KEY (`lid`) REFERENCES `Listings` (`lid`),
  ADD CONSTRAINT `houses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`);

--
-- Constraints for table `Listings`
--
ALTER TABLE `Listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`),
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `Locations` (`location_id`),
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`date_id`) REFERENCES `Dates` (`date_id`),
  ADD CONSTRAINT `listings_ibfk_4` FOREIGN KEY (`price_id`) REFERENCES `Prices` (`price_id`);

--
-- Constraints for table `Vehicles`
--
ALTER TABLE `Vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`lid`) REFERENCES `Listings` (`lid`),
  ADD CONSTRAINT `vehicles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`);

--
-- Constraints for table `Wishlists`
--
ALTER TABLE `Wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
