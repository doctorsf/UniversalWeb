-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 22 Décembre 2017 à 15:43
-- Version du serveur :  5.6.25
-- Version de PHP :  5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `films`
--

CREATE TABLE IF NOT EXISTS `films` (
  `titre` varchar(255) NOT NULL,
  `annee` int(4) unsigned NOT NULL,
  `realisateur` varchar(255) NOT NULL,
  `visuel` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `genre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `films`
--

INSERT INTO `films` (`titre`, `annee`, `realisateur`, `visuel`, `genre`) VALUES
('2001: l''odyssée de l''espace', 1968, 'Stanley Kubrick', 1, 'Science-fiction'),
('Amityville, la maison du diable', 1979, 'Stuart Rosenberg', 1, 'Horreur'),
('Chantons sous la pluie', 1952, 'Stanley Donen', 1, 'Comédie musicale'),
('Il était une fois dans l''Ouest', 1968, 'Sergio Leone', 1, 'Western'),
('L''Empire contre-attaque', 1980, 'Irvin Kershner', 1, 'Science-fiction'),
('La Guerre des étoiles', 1977, 'George Lucas', 1, 'Science-fiction'),
('La planète des singes', 1968, 'Franklin J. Schaffner', 1, 'Science-fiction'),
('La prisonnière du désert', 1956, 'John Ford', 1, 'Western'),
('Le bon, la brute et le truand', 1966, 'Sergio Leone', 1, 'Western'),
('Les parapluies de Cherbourg', 1964, 'Jacques Demy', 1, 'Comédie musicale'),
('Quo Vadis', 1951, 'Mervyn LeRoy', 1, 'Drame'),
('Autant en emporte le vent', 1939, 'Victor Fleming', 0, 'Drame'),
('Le jour le plus long', 1962, 'Ken Annakin', 0, 'Guerre'),
('La bête humaine', 1938, 'Jean Renoir', 0, 'Drame');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `films`
--
ALTER TABLE `films`
  ADD UNIQUE KEY `titre` (`titre`),
  ADD KEY `annee` (`annee`),
  ADD KEY `genre` (`genre`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
