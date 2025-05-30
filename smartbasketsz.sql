-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 30, 2025 at 12:29 PM
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
-- Database: `smartbasketsz`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `profilePicture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `firstName`, `lastName`, `address`, `country`, `email`, `phoneNumber`, `passwordHash`, `profilePicture`) VALUES
(1, 'Harishh', 'Haqim', '17, Menara U2', 'Malaysia', 'harishhaqim@gmail.com', '+60112283929', '$2y$10$wpxWqfJ9wl8SNvVxcDt1h.DHNgorjzPPd4sT6n6sCx2N4oyy30DRy', '1_ProfilePicture_1747725422.png'),
(2, 'Najmi', 'Ryan', '8, Petaling Jaya', 'Malaysia', 'najmiryan@yahoo.com', '+601929293829', '$2y$10$6ePWPz7rupWax5SlgBgme.R6ii9QoaT2ReTC4FAE8aHltZPdnMXmi', '2_ProfilePicture_1747534035.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `ID` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `orderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `totalPrice` decimal(10,2) NOT NULL,
  `paymentMethod` enum('Credit/Debit Card','E-Wallet','QR Pay') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`ID`, `customerId`, `orderDate`, `totalPrice`, `paymentMethod`) VALUES
(3, 1, '2025-05-18 09:18:34', 137.00, 'E-Wallet'),
(5, 1, '2025-05-18 18:46:10', 0.00, 'Credit/Debit Card'),
(7, 1, '2025-05-19 00:44:57', 72.00, 'Credit/Debit Card'),
(8, 7, '2025-05-19 02:09:15', 566.00, 'E-Wallet'),
(9, 8, '2025-05-20 01:12:39', 213.00, 'E-Wallet'),
(10, 8, '2025-05-20 01:13:44', 4.00, 'QR Pay'),
(11, 8, '2025-05-21 01:51:48', 76.00, 'Credit/Debit Card'),
(12, 8, '2025-05-21 02:42:24', 263.00, 'E-Wallet');

-- --------------------------------------------------------

--
-- Table structure for table `contactform`
--

CREATE TABLE `contactform` (
  `ID` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `customerName` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactform`
--

INSERT INTO `contactform` (`ID`, `customerId`, `customerName`, `subject`, `message`, `image`) VALUES
(4, 1, 'Kuvaraji Ravichandran', 'Best Shop Everrrr!', '5 Star rating will be given to you!!!', '4_contactForm_1747625619.jpg'),
(6, 7, 'Kuvalesan Kumar Loid', 'Pricing Adjustment', 'Please lower the price of brocolli', '6_contactForm_1747642246_682ae786cb132.jpg'),
(7, 1, 'Harishhh Haqim', 'Best Shop!', '10000000 stars', ''),
(8, 1, 'Harishhh Haqim', 'Best Shop!', '10000000 stars', ''),
(11, 8, 'Harish Ryan Hakim', 'Best Shop!', '888888888888888888 STARS!!!!!!!!!!!!', '');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `ID` int(11) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `profilePicture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`ID`, `firstName`, `lastName`, `address`, `country`, `email`, `phoneNumber`, `passwordHash`, `profilePicture`) VALUES
(1, 'Kuvaraji', 'Ravichandran', 'Seksyen 13, Shah Alam', 'Malaysia', 'kuvarajiravichandran@gmail.com', '+601928188122', '$2y$10$5KCTrdv4LKUOo.kdG09HK./JUIOc1kU3Wj.gf/xICrAIxit0I56cW', '1_ProfilePicture_1747625206.jpg'),
(2, 'John', 'Daniel', '7, Street of Beachers Hope', 'Singapore', 'johndaniel@gmail.com', '+93829392992', '$2y$10$0zF54Mc.kUzQ5iB8989HQerlQFAcE9rUnE.HlL9/hDphGnvKjJjR2', 'JohnDanielProfilePicture.png'),
(7, 'Kuvalesan', 'Kumar', 'Seksyen 13, Shah Alam', 'Malaysia', 'kuvalesankumar@gmail.com', '+60192818812', '$2y$10$3O6HhBx4jX9tyRXQnz2KMehc3oqgpesRzwBNjHFtSIouweD1o9Ki6', 'profile_7.png'),
(8, 'Ahmad', 'Aiman', 'G3, Arte, Section 13', 'Malaysia', 'ahmadaiman@gmail.com', '+60192920372983', '$2y$10$8YIciSvv3dZKeQpxwi3/2.w5NBboRyL.2RmMA0F85cCE4TWpT2ex2', '8_ProfilePicture_1747801970.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `basePrice` decimal(10,2) NOT NULL,
  `salePrice` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`ID`, `name`, `description`, `category`, `basePrice`, `salePrice`, `image`) VALUES
(1, 'Bread', 'A staple food prepared from dough and water', 'Carbohydrate', 2.00, 4.00, '1_item_1747537236.jpeg'),
(4, 'Carrot Fresh', 'An orange root vegetable', 'Vegetable', 9.00, 13.00, '4_item_1747537432.jpg'),
(5, 'Brocoli', 'A cruciferous vegetable', 'Vegetable', 5.00, 10.00, '5_item_1747537717.jpg'),
(7, 'Apple', 'A round, edible fruit produced by an apple tree', 'Fruit', 7.00, 14.00, '7_item_1747537770.jpg'),
(9, 'Orange', 'A citrus fruit', 'Fruit', 5.00, 9.00, '9_item_1747537881.png'),
(12, 'Milk', 'A sweet drink', 'Beverage', 12.00, 20.00, '12_item_1747538486.jpg'),
(13, 'Orange Juice', 'A juice made of oranges', 'Beverage', 6.00, 10.00, '13_item_1747622843.jpg'),
(14, 'Watermelon Fresh', 'A fresh red watermelon', 'Fruit', 8.00, 15.00, '14_item_1747725512.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
  `ID` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `cartId` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`ID`, `customerId`, `itemId`, `quantity`, `cartId`) VALUES
(4, 1, 1, 8, 3),
(5, 1, 4, 15, 3),
(15, 1, 1, 18, 7),
(16, 7, 1, 14, 8),
(18, 8, 4, 1, 9),
(19, 8, 4, 18, 9),
(20, 8, 12, 4, 9),
(21, 8, 1, 1, 10),
(22, 8, 7, 4, 11),
(23, 8, 1, 5, 11),
(24, 8, 1, 8, 12),
(25, 8, 4, 7, 12),
(26, 8, 12, 7, 12);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `customerID` (`customerId`);

--
-- Indexes for table `contactform`
--
ALTER TABLE `contactform`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `customerID` (`customerId`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `customerID` (`customerId`),
  ADD KEY `itemID` (`itemId`),
  ADD KEY `fk_cart` (`cartId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contactform`
--
ALTER TABLE `contactform`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orderitem`
--
ALTER TABLE `orderitem`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `contactform`
--
ALTER TABLE `contactform`
  ADD CONSTRAINT `contactform_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `fk_cart` FOREIGN KEY (`cartId`) REFERENCES `cart` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`itemID`) REFERENCES `item` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
