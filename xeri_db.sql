-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 30 Ιαν 2026 στις 17:59:18
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `xeri_db`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `current_player` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `games`
--

INSERT INTO `games` (`id`, `player1_id`, `player2_id`, `current_player`, `status`) VALUES
(1, 1, NULL, 1, 'waiting'),
(2, 1, NULL, 1, 'waiting'),
(3, 1, NULL, 1, 'waiting'),
(4, 1, 2, 1, 'playing'),
(5, 3, 4, 4, 'playing'),
(6, 6, 7, 6, 'playing');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `game_state`
--

CREATE TABLE `game_state` (
  `game_id` int(11) NOT NULL,
  `deck` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`deck`)),
  `table_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`table_cards`)),
  `p1_hand` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`p1_hand`)),
  `p2_hand` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`p2_hand`)),
  `p1_collected` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`p1_collected`)),
  `p2_collected` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`p2_collected`)),
  `p1_xeri` int(11) DEFAULT 0,
  `p2_xeri` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `game_state`
--

INSERT INTO `game_state` (`game_id`, `deck`, `table_cards`, `p1_hand`, `p2_hand`, `p1_collected`, `p2_collected`, `p1_xeri`, `p2_xeri`) VALUES
(3, '[\"9S\",\"6C\",\"JH\",\"5C\",\"4H\",\"6S\",\"10H\",\"8H\",\"AS\",\"8D\",\"4D\",\"5H\",\"3C\",\"6D\",\"2D\",\"KS\",\"JC\",\"8C\",\"QH\",\"KC\",\"KD\",\"AC\",\"AD\",\"3D\",\"9H\",\"QD\",\"9D\",\"5S\",\"10S\",\"9C\",\"QC\",\"7C\",\"7H\",\"2S\",\"2H\",\"JS\",\"7S\",\"3H\",\"3S\",\"4C\",\"8S\",\"7D\"]', '[\"10D\",\"KH\",\"5D\",\"AH\"]', '[\"6H\",\"2C\",\"4S\",\"10C\",\"QS\",\"JD\"]', '[]', '[]', '[]', 0, 0),
(4, '[\"4D\",\"9S\",\"AS\",\"JS\",\"JC\",\"9D\",\"QH\",\"8C\",\"2C\",\"KD\",\"2H\",\"JD\",\"QD\",\"AC\",\"KC\",\"7H\",\"3C\",\"7D\",\"2D\",\"6H\",\"10C\",\"2S\",\"6S\",\"3D\",\"6C\",\"5C\",\"10D\",\"5D\",\"3S\",\"AD\",\"7C\",\"QS\",\"3H\",\"10H\",\"10S\",\"KS\"]', '[\"8D\",\"9C\",\"8S\",\"JH\",\"4S\",\"7S\",\"4H\"]', '[\"4C\",\"QC\",\"5H\"]', '[\"5S\",\"KH\",\"8H\",\"AH\",\"6D\",\"9H\"]', '[]', '[]', 0, 0),
(5, '[\"3H\",\"JC\",\"7D\",\"AC\",\"AH\",\"9D\",\"5C\",\"QH\",\"6H\",\"8C\",\"JS\",\"6D\",\"5S\",\"2D\",\"JD\",\"8D\",\"JH\",\"10S\",\"6C\",\"QD\",\"AD\",\"9S\",\"10H\",\"4C\",\"6S\",\"2C\",\"10C\",\"2H\",\"KC\",\"4H\",\"8H\",\"4D\",\"3C\",\"9H\",\"8S\",\"9C\"]', '[\"5H\",\"3S\",\"4S\",\"AS\",\"7C\"]', '[\"7H\",\"2S\",\"QC\",\"10D\",\"KD\"]', '[\"QS\",\"3D\",\"7S\",\"5D\",\"KS\",\"KH\"]', '[]', '[]', 0, 0),
(6, '[\"7S\",\"6C\",\"2D\",\"AH\",\"9C\",\"8S\",\"4S\",\"2H\",\"10C\",\"7D\",\"3H\",\"2S\",\"AC\",\"8H\",\"JD\",\"QC\",\"QD\",\"7H\",\"4D\",\"5C\",\"9S\",\"8C\",\"4H\",\"JH\",\"KS\",\"9H\",\"6S\",\"7C\",\"QH\",\"9D\",\"8D\",\"JC\",\"5S\",\"6H\",\"KD\",\"3C\"]', '[]', '[\"10D\",\"AD\",\"3S\",\"5D\",\"4C\"]', '[\"QS\",\"6D\",\"3D\",\"KH\",\"5H\"]', '[]', '[\"KC\",\"AS\",\"2C\",\"JS\",\"10S\",\"10H\"]', 0, 0);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `players`
--

INSERT INTO `players` (`id`, `username`, `token`) VALUES
(2, 'player2', 'be5972475ca027f6170fa5bb62bb764c'),
(3, 'player1', '9ade4364eda75b903a19d19cea9c1960'),
(4, 'nikos', '1594fdbeeed360584ad386e4db1a915a'),
(5, 'player1', 'ce66d585409ae688605fb90e24fa3d5e'),
(6, 'dimitris', 'edcf27e9c290f4f30bc0de9e17c62905'),
(7, 'GIANNIS', 'b27e7e272c4b0ab6b52d5d515a27e778'),
(8, 'player1', '3c7c7e1beef39d2867d3bbd4bbbcf162');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `game_state`
--
ALTER TABLE `game_state`
  ADD PRIMARY KEY (`game_id`);

--
-- Ευρετήρια για πίνακα `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT για πίνακα `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
