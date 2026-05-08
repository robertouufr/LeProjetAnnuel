-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 08, 2026 at 05:48 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projetannuelb1`
--

-- --------------------------------------------------------

--
-- Table structure for table `budget_limites`
--

CREATE TABLE `budget_limites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `categorie_id` int NOT NULL,
  `montant_limite` decimal(10,2) NOT NULL,
  `mois` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budget_limites`
--

INSERT INTO `budget_limites` (`id`, `user_id`, `categorie_id`, `montant_limite`, `mois`) VALUES
(1, 1, 3, 200.00, 1),
(6, 1, 1, 300.00, 1),
(9, 1, 2, 25.00, 1),
(15, 1, 4, 800.00, 1),
(26, 1, 5, 350.00, 1),
(45, 1, 6, 400.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `icone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `icone`) VALUES
(1, 'Alimentation', '🍕'),
(2, 'Transport', '🚗'),
(3, 'Loisirs', '🎮'),
(4, 'Logement', '🏠'),
(5, 'Santé', '💊'),
(6, 'Shopping', '🛍️');

-- --------------------------------------------------------

--
-- Table structure for table `defis_epargne`
--

CREATE TABLE `defis_epargne` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `titre` varchar(150) NOT NULL,
  `montant_objectif` decimal(10,2) NOT NULL,
  `montant_economise` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `defis_epargne`
--

INSERT INTO `defis_epargne` (`id`, `user_id`, `titre`, `montant_objectif`, `montant_economise`, `date_fin`) VALUES
(9, 1, 'iphone 17 pro', 1600.00, 1300.00, '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `depenses`
--

CREATE TABLE `depenses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `categorie_id` int NOT NULL,
  `date_depense` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `depenses`
--

INSERT INTO `depenses` (`id`, `user_id`, `categorie_id`, `date_depense`, `description`, `montant`, `created_at`) VALUES
(1, 1, 2, '2026-04-14', 'carte tcl', 25.00, '2026-04-15 17:32:49'),
(2, 1, 1, '2026-04-13', 'courses', 100.00, '2026-04-15 17:59:06'),
(3, 1, 6, '2026-04-10', 'zara', 30.00, '2026-04-15 17:59:29'),
(4, 1, 5, '2026-04-05', 'finansteride', 200.00, '2026-04-15 18:11:13'),
(5, 1, 3, '2026-04-08', 'sortie', 200.00, '2026-04-15 19:39:53'),
(6, 1, 5, '2026-04-18', 'medicament', 120.00, '2026-04-16 15:10:13'),
(7, 1, 4, '2025-12-21', 'loyer', 400.00, '2026-04-21 01:26:39'),
(8, 1, 6, '2025-12-20', 'gloss', 300.00, '2026-04-21 09:56:20'),
(9, 1, 1, '2026-07-21', 'loyer', 21.00, '2026-04-27 02:22:35'),
(10, 1, 4, '2026-04-20', 'loyer', 318.00, '2026-04-29 19:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `infos`
--

CREATE TABLE `infos` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mdp` varchar(255) NOT NULL,
  `token_verification` varchar(255) DEFAULT NULL,
  `verifier_status` tinyint(1) DEFAULT '0' COMMENT '0=non vérifié, 1=vérifié',
  `token_psw` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `revenus_mensuels` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `infos`
--

INSERT INTO `infos` (`id`, `nom`, `email`, `phone`, `mdp`, `token_verification`, `verifier_status`, `token_psw`, `created_at`, `revenus_mensuels`) VALUES
(1, 'seyf', 'sifoda2107@gmail.com', '0608834516', '$2y$10$EP6yMA.Hp.mJIRGk4ixbP.EM/edfaNx1MXcYWsjlLC8./pb6TTEsC', '7e80fde455bcc59bef82b96a32ce017b', 1, '45a9c26533959130c848fad74e16b6bf', '2026-04-09 16:23:13', 4000.00),
(4, 'Abdo', 's.djilaliayad@gmail.com', '0609686859', '$2y$10$EjfoSsBTfM3ZlZLiQ1Khj.GeQKkh3fTHr9FT0sLqKeeSeDZH7XVDm', '46dba7faffeb2f648c47bd3499f0a85d', 1, NULL, '2026-05-08 17:28:45', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budget_limites`
--
ALTER TABLE `budget_limites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_cat_unique` (`user_id`,`categorie_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `defis_epargne`
--
ALTER TABLE `defis_epargne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `depenses`
--
ALTER TABLE `depenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `infos`
--
ALTER TABLE `infos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budget_limites`
--
ALTER TABLE `budget_limites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `defis_epargne`
--
ALTER TABLE `defis_epargne`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `depenses`
--
ALTER TABLE `depenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `infos`
--
ALTER TABLE `infos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget_limites`
--
ALTER TABLE `budget_limites`
  ADD CONSTRAINT `budget_limites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `infos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budget_limites_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `defis_epargne`
--
ALTER TABLE `defis_epargne`
  ADD CONSTRAINT `defis_epargne_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `infos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `depenses`
--
ALTER TABLE `depenses`
  ADD CONSTRAINT `depenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `infos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `depenses_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
