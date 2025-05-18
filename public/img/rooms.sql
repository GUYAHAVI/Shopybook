-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 08:17 PM
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
-- Database: `discover`
--

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_type` varchar(255) NOT NULL,
  `available_units` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `property_description` text NOT NULL,
  `services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`services`)),
  `proximity_attractions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`proximity_attractions`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`images`)),
  `main_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `property_name`, `property_type`, `available_units`, `price`, `property_description`, `services`, `proximity_attractions`, `images`, `main_image`, `created_at`, `updated_at`) VALUES
(1, 'Enashipai', 'Villas', 10, 10.00, 'property description for enashipai', '[\"Free Wifi\",\"Parking\",\"Swimming Pool\",\"Restaurant\",\"Spa\",\"Gym\",\"Airport Shuttle\",\"Bar\",\"Pet Friendly\",\"Family Rooms\",\"Room Service\",\"Laundry\",\"Concierge\",\"Business Center\",\"Free Breakfast\"]', '[\"Hell\'s Gate National Park\",\"Mount Longonot\",\"Crater Lake\"]', '[\"property_images\\/cuz8x6GVHqOyj8Rlx30uu4FLmMQfxSTDt5RbhT0S.jpg\",\"property_images\\/raauCSgnsJyQgW6RaqJHLGBj1dplIBYGTDVrwjZO.jpg\",\"property_images\\/tDajhwQduIcZzIiYP1YzQxHP3GmMWkB742cknlIu.jpg\",\"property_images\\/RP7e8xj2pircuTnAyoC8pxpKGdHrbTFhJxv1Rype.jpg\",\"property_images\\/qaHREUHrSEYqDQ1mXwSCi2c4TtyqguTg8fetKKtn.jpg\"]', 'property_images/cuz8x6GVHqOyj8Rlx30uu4FLmMQfxSTDt5RbhT0S.jpg', '2025-03-27 04:25:50', '2025-03-27 04:25:50'),
(2, 'Panorama', 'Villas', 12, 8.00, 'property description for panorama', '[\"Free Wifi\",\"Gym\",\"Airport Shuttle\",\"Pet Friendly\",\"Family Rooms\",\"Room Service\",\"Concierge\",\"Business Center\",\"Free Breakfast\"]', '[\"Hell\'s Gate National Park\",\"Lake Naivasha\",\"Crescent Island\"]', '[\"property_images\\/GIHL5uoWtxmQXfG0U9cZCF299XTKAjWwTYLTH6Go.jpg\",\"property_images\\/JX5bibJ2J7UrhqwfBPCpYqggPTu21VP4fPamm3di.jpg\",\"property_images\\/6P0f4AYS0j2cEp90ldyuRQdmsjPtT5wanNdXUG9V.jpg\",\"property_images\\/w7W6MiylJBcxOYSR6c1KwmhjMucIDzMuFU09scqn.jpg\",\"property_images\\/cpPdXm8lQIOULSwLjqBDik4kRftNOB2kVPuigN4w.jpg\",\"property_images\\/pSU9jQ7lq4D1LuiLLtD8iCz8w7cxrT7888WCnbaV.jpg\",\"property_images\\/YUrujFyUO9P1AfFyUqWqolyqRadIRNtqdnOpMmMd.jpg\",\"property_images\\/0QvQwpwVTZuJZctp25RXCry14PFXBs6F58VS1K9F.jpg\",\"property_images\\/N2PvsBL3k7YCS9czYbExgtTgaYPPBhbT0NjQrSZD.jpg\"]', 'property_images/GIHL5uoWtxmQXfG0U9cZCF299XTKAjWwTYLTH6Go.jpg', '2025-03-27 04:57:21', '2025-03-27 04:57:21'),
(3, 'naiposh', 'Hotels', 12, 7.00, 'property description for naiposh', '[\"Parking\",\"Swimming Pool\",\"Restaurant\",\"Spa\",\"Gym\",\"Airport Shuttle\",\"Bar\",\"Pet Friendly\",\"Family Rooms\",\"Room Service\",\"Laundry\",\"Concierge\",\"Business Center\",\"Free Breakfast\"]', '[\"Hell\'s Gate National Park\",\"Lake Naivasha\",\"Crescent Island\"]', '[\"property_images\\/hOYjG5CeVQHrDaEMtbSPVYtMRgccpjmHaDNPg7rA.jpg\",\"property_images\\/77Q8pOymQc5syB8ctIOiNu38NoLRerJ6raTWzvkG.jpg\",\"property_images\\/hhBWUil9rnXBLPcfDQ7UaEKF266OX8f6BaZVRbNA.jpg\",\"property_images\\/KnTfyP3zvzpgeWEtQ6Sumkv8BKPVaUsd7FvoWmD8.jpg\",\"property_images\\/BATEgmxLNboYhwMah5UgfAON1lBuu4z3mKZUT9Rz.jpg\",\"property_images\\/zdHfy1NTtwuP68v73jja111FnBk85HrfCSmgw5NK.jpg\",\"property_images\\/f2adPC7uxcAriILMzcGJ6n8hGsr59fJE4UEed0uL.jpg\",\"property_images\\/DUrLrQYB64QJk1b9y3g5Wh9ZIsbyjmHWjgXwd1Zd.jpg\",\"property_images\\/agV9HaG1RUj2SRD5PiF4j1fhrj0K7ry7KuoTkobg.jpg\",\"property_images\\/uUlFzBeLi26tcIci75hNsX71tyrPLWRnRe24QoZe.jpg\",\"property_images\\/7WBS6w5xUVSIof4co7rPAoQ5v7MldweWEd6O1JWU.jpg\",\"property_images\\/HDI6xzxJniJgOYngCm8qkOoctaaY5JE2nM9ubTjO.jpg\",\"property_images\\/6Mf5VuOsjTJRLAYuIJknwRtbySDKwUt7cZMTikTa.jpg\"]', 'property_images/hOYjG5CeVQHrDaEMtbSPVYtMRgccpjmHaDNPg7rA.jpg', '2025-03-27 08:20:28', '2025-03-27 08:20:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
