-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 03:04 AM
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
-- Database: `games_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `is_wishlist` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `user_id`, `title`, `platform`, `genre`, `release_year`, `rating`, `comment`, `is_wishlist`, `created_at`) VALUES
(2, 23, 'The Witcher 3: Wild Hunt', 'PC', 'RPG', 2015, 5, 'One of the best RPGs ever made.', 0, '2025-12-11 00:36:24'),
(3, 23, 'Red Dead Redemption 2', 'PlayStation', 'Adventure', 2018, 5, 'Incredible open world and storyline.', 0, '2025-12-11 00:37:00'),
(4, 23, 'Elden Ring', 'PC', 'Action', 2022, 5, 'Challenging but extremely rewarding.', 0, '2025-12-11 00:37:40'),
(5, 23, 'God of War', 'PlayStation', 'Action', 2018, 5, 'Emotional story with great combat.', 0, '2025-12-11 00:38:18'),
(6, 23, 'Cyberpunk 2077 (Updated)', 'PC', 'RPG', 2020, 4, 'Much better after patches.', 0, '2025-12-11 00:39:02'),
(7, 23, 'Pokémon Legends: Arceus', 'Nintendo', 'RPG', 2022, 4, 'Fresh open-world Pokémon experience.', 1, '2025-12-11 00:39:56'),
(8, 23, 'God of War Ragnarok', 'PlayStation', 'Action', 2022, 5, 'Stunning visuals and story.', 1, '2025-12-11 00:40:36'),
(9, 23, 'FIFA 23', 'PlayStation', 'Sports', 2022, 4, 'Best football mechanics so far.', 0, '2025-12-11 00:41:17'),
(10, 23, 'GTA V', 'PlayStation', 'Action', 2013, 5, 'Maasive open world', 0, '2025-12-11 00:41:48'),
(11, 23, 'Minecraft', 'PC', 'Survival', 2011, 5, '0', 0, '2025-12-11 00:42:29'),
(12, 23, 'Assassin’s Creed Valhalla', 'Xbox', 'Adventure', 2020, 4, 'Huge world with Viking combat', 0, '2025-12-11 00:45:00'),
(13, 23, 'Stardew Valley', 'Nintendo', 'Simulation', 2016, 5, 'Calm farming and relaxing gameplay.', 1, '2025-12-11 00:45:58'),
(14, 23, 'PUBG Mobile', 'Mobile', 'Shooter', 2018, 5, 'One of the most popular battle royale games worldwide.', 0, '2025-12-11 00:47:26'),
(15, 23, 'Call of Duty', 'Mobile', 'Shooter', 2019, 5, 'Great graphics and fast-paced multiplayer.', 0, '2025-12-11 00:48:05'),
(16, 23, 'Temple Run 2', 'Mobile', 'Platformer', 2013, 4, 'Classic endless runner.', 0, '2025-12-11 00:48:56'),
(17, 12, 'dfihu', 'Xbox', 'Puzzle', 2016, 5, '', 0, '2025-12-11 00:55:14'),
(18, 12, 'fv jnf', 'Nintendo', 'Survival', 2020, 1, '', 0, '2025-12-11 00:55:28'),
(20, 23, 'FIFA 2020', 'Mobile', 'Sports', 2020, 1, 'Extremely pay-to-win and poorly optimized.', 0, '2025-12-11 01:50:45'),
(21, 23, 'WWE 2K20', 'PlayStation', 'Fighting', 2019, 1, 'Extremely poor and lag', 0, '2025-12-11 01:52:01'),
(22, 23, 'Clash of Clans', 'Mobile', 'Survival', 2014, 3, '', 0, '2025-12-11 01:54:30'),
(23, 23, 'FIFA 2019', 'PlayStation', 'Sports', 2019, 2, '', 0, '2025-12-11 01:56:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `created_at`, `reset_token`, `reset_expires`) VALUES
(2, 'harshrouniyar', 'harshrouniyar@gmail.com', '$2y$10$gqEPVh8ZwJNkJWivJ3Ba/.TPTUJi35GlT0ARpdQIYfS4XAQljLNpK', '2025-12-02 07:25:33', NULL, NULL),
(12, 'surenn', 'surenn@gmail.com', '$2y$10$F1zzv1tx3D8VQaWl.8qjqu0vRpnnip4wFgMEkTODn749gUTQQsQOe', '2025-12-02 07:53:15', NULL, NULL),
(23, 'surendrathapa', NULL, '$2y$10$.dIP1TrxxsUf3DBpc2mr/uSJtlOEG0Tr6KNiBjZnNEOe74bZ02fgq', '2025-12-11 00:19:40', NULL, NULL),
(24, 'Rohan Shrestha', NULL, '$2y$10$NQue16PkmN6lgTAbn862X.rfOe2DZhZBhN02PBwXASttyhPj9rFCG', '2025-12-11 01:44:15', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
