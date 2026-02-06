-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 06 fév. 2026 à 08:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vite_gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `allergenes`
--

CREATE TABLE `allergenes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `user_id`, `commande_id`, `rating`, `comment`, `is_approved`, `created_at`) VALUES
(1, 1, 2, 4, 'délicieux', 1, '2026-02-05 17:20:01'),
(2, 1, 4, 5, 'Bon plat', 1, '2026-02-05 17:37:33');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `event_address` varchar(255) NOT NULL,
  `km_outside_bordeaux` decimal(6,2) DEFAULT 0.00,
  `people_count` int(11) NOT NULL,
  `price_food` decimal(10,2) NOT NULL,
  `price_delivery` decimal(10,2) NOT NULL,
  `price_total` decimal(10,2) NOT NULL,
  `status_current` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(50) GENERATED ALWAYS AS (`status_current`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `user_id`, `menu_id`, `event_date`, `event_time`, `event_address`, `km_outside_bordeaux`, `people_count`, `price_food`, `price_delivery`, `price_total`, `status_current`, `created_at`) VALUES
(1, 3, 1, '2026-02-25', '20:00:00', '15 avenue de France, Paris', 5.00, 30, 900.00, 1.50, 901.50, 'en préparation', '2026-02-05 12:43:18'),
(2, 1, 1, '2026-02-06', '15:36:00', '23 allée des champs', 0.00, 21, 630.00, 0.00, 630.00, 'terminée', '2026-02-05 13:36:55'),
(3, 2, 2, '2026-02-13', '19:11:00', '23 allée des champs', 0.00, 20, 560.00, 0.00, 560.00, 'acceptée', '2026-02-05 17:12:09'),
(4, 1, 4, '2026-02-18', '20:21:00', '15 allée des champs', 0.00, 30, 960.00, 0.00, 960.00, 'terminée', '2026-02-05 17:21:18'),
(5, 3, 4, '2026-02-01', '22:23:00', '45 avenue de l\'Europe', 0.00, 33, 1056.00, 0.00, 1056.00, 'terminée', '2026-02-05 17:23:45');

-- --------------------------------------------------------

--
-- Structure de la table `commande_historiques`
--

CREATE TABLE `commande_historiques` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `contact_mode` varchar(50) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_historiques`
--

INSERT INTO `commande_historiques` (`id`, `commande_id`, `status`, `changed_by`, `cancel_reason`, `contact_mode`, `changed_at`) VALUES
(1, 2, 'en préparation', NULL, NULL, NULL, '2026-02-05 13:39:04'),
(2, 1, 'annulée', 2, NULL, NULL, '2026-02-05 14:48:36'),
(3, 2, 'reçue', 2, NULL, NULL, '2026-02-05 14:57:36'),
(4, 1, 'en préparation', 2, NULL, NULL, '2026-02-05 15:07:11'),
(5, 2, 'acceptée', 2, NULL, NULL, '2026-02-05 16:46:06'),
(6, 2, 'terminée', 2, NULL, NULL, '2026-02-05 16:51:21'),
(7, 3, 'acceptée', 2, NULL, NULL, '2026-02-05 17:13:15'),
(8, 5, 'terminée', 2, NULL, NULL, '2026-02-05 17:26:44'),
(9, 4, 'terminée', 2, NULL, NULL, '2026-02-05 17:26:54'),
(10, 5, 'terminée', 2, NULL, NULL, '2026-02-05 17:39:59');

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

CREATE TABLE `horaires` (
  `id` int(11) NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `diet` varchar(100) DEFAULT NULL,
  `min_people` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id`, `title`, `theme`, `diet`, `min_people`, `base_price`, `stock`, `created_at`, `is_active`) VALUES
(1, 'Menu ECF Test', 'Gastronomique', 'Classique', 20, 20.00, 15, '2026-02-05 12:41:50', 1),
(2, 'Menu Italien', 'Cuisine italienne', 'Classique', 20, 28.00, 9, '2026-02-05 15:25:13', 1),
(3, 'Menu Vegan', 'Végétarien', 'Vegan', 15, 25.00, 8, '2026-02-05 15:25:13', 1),
(4, 'Menu Oriental', 'Cuisine orientale', 'Halal', 30, 32.00, 10, '2026-02-05 15:25:13', 1),
(5, 'Menu Street Food', 'Fast food', 'Classique', 25, 22.00, 20, '2026-02-05 15:25:13', 1),
(6, 'Menu Gastronomique', 'Gastronomie', 'Classique', 40, 55.00, 5, '2026-02-05 15:25:13', 1);

-- --------------------------------------------------------

--
-- Structure de la table `menu_images`
--

CREATE TABLE `menu_images` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_plats`
--

CREATE TABLE `menu_plats` (
  `menu_id` int(11) NOT NULL,
  `plat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plats`
--

CREATE TABLE `plats` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plat_allergenes`
--

CREATE TABLE `plat_allergenes` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(3, 'client'),
(2, 'employe');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password_hash`, `role_id`, `is_active`, `created_at`) VALUES
(1, 'Admin', 'ViteGourmand', 'admin@vitegourmand.test', '$2y$10$gQuIDbOXe6lSldKC5kGG7uyg9E/fD8MwOrOxlEvX.6W4IwBvQXYOO', 1, 1, '2026-02-05 12:01:15'),
(2, 'Employe', 'Test', 'employe@vitegourmand.test', '$2y$10$jZ/cpuTeRwHtkfTQGRVfqO7CjIamBzUtx8.o7FsvGlwIgPKxpKkaO', 2, 1, '2026-02-05 12:01:15'),
(3, 'Client', 'Test', 'client@vitegourmand.test', '$2y$10$xo57fCshuKhKJs0WVT6OO.6qaqv/QBRRkdbM0QktiopddGjIjUOlm', 3, 1, '2026-02-05 12:37:38');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `allergenes`
--
ALTER TABLE `allergenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_avis_commande` (`commande_id`),
  ADD KEY `fk_avis_user` (`user_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Index pour la table `commande_historiques`
--
ALTER TABLE `commande_historiques`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Index pour la table `horaires`
--
ALTER TABLE `horaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menu_images`
--
ALTER TABLE `menu_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_images_menu` (`menu_id`);

--
-- Index pour la table `menu_plats`
--
ALTER TABLE `menu_plats`
  ADD PRIMARY KEY (`menu_id`,`plat_id`),
  ADD KEY `plat_id` (`plat_id`);

--
-- Index pour la table `plats`
--
ALTER TABLE `plats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plat_allergenes`
--
ALTER TABLE `plat_allergenes`
  ADD PRIMARY KEY (`plat_id`,`allergene_id`),
  ADD KEY `fk_plat_allergenes_allergene` (`allergene_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `allergenes`
--
ALTER TABLE `allergenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commande_historiques`
--
ALTER TABLE `commande_historiques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `horaires`
--
ALTER TABLE `horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `menu_images`
--
ALTER TABLE `menu_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plats`
--
ALTER TABLE `plats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `fk_avis_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avis_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`);

--
-- Contraintes pour la table `commande_historiques`
--
ALTER TABLE `commande_historiques`
  ADD CONSTRAINT `commande_historiques_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`),
  ADD CONSTRAINT `commande_historiques_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `menu_images`
--
ALTER TABLE `menu_images`
  ADD CONSTRAINT `fk_menu_images_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu_plats`
--
ALTER TABLE `menu_plats`
  ADD CONSTRAINT `menu_plats_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_plats_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `plat_allergenes`
--
ALTER TABLE `plat_allergenes`
  ADD CONSTRAINT `fk_plat_allergenes_allergene` FOREIGN KEY (`allergene_id`) REFERENCES `allergenes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plat_allergenes_plat` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
