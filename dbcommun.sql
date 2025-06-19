-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 18 juin 2025 à 21:10
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbcommun`
--

-- --------------------------------------------------------

--
-- Structure de la table `adminisateur`
--

DROP TABLE IF EXISTS `adminisateur`;
CREATE TABLE IF NOT EXISTS `adminisateur` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `photo` text,
  `id_FAQ` int DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  KEY `id_FAQ` (`id_FAQ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE IF NOT EXISTS `faq` (
  `id_FAQ` int NOT NULL,
  PRIMARY KEY (`id_FAQ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `liens` varchar(180) NOT NULL,
  `numero_photo` int DEFAULT NULL,
  `description` text,
  `nom_galerie` varchar(255) DEFAULT NULL,
  `numero_annonce` int DEFAULT NULL,
  PRIMARY KEY (`liens`),
  KEY `numero_annonce` (`numero_annonce`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `id_FAQ` int DEFAULT NULL,
  `Photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Frontend/default-avatar.png',
  PRIMARY KEY (`id_utilisateur`),
  KEY `id_FAQ` (`id_FAQ`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `prenom`, `nom`, `mail`, `mot_de_passe`, `id_FAQ`, `Photo`) VALUES
(2, 'Louis', 'Humbert', 'louis.humbert@eleve.isep.fr', '$2y$10$EXX9a7MzAaGa/MPSDheEV.UJf.YuMimnWkN1aGdzzntQJtogFtN5m', NULL, 'uploads/photos/6853287289f38_IMG_4312.PNG'),
(3, 'Edouard', 'Humbert', 'louis.humbert@eleve.isep.fr', '$2y$10$UXyzujXrEU.sDoOYNawmR.j0G1QqVWoL5cYHgq81kKGJBzT.aUdfy', NULL, 'uploads/photos/68532a787cc53_WhatsApp Image 2025-05-08 à 23.19.10_1f426dd4.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
