SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `ln_diccionario` (
  `palabra` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ln_diccionario` (`palabra`) VALUES
('barato'),
('busco'),
('casa'),
('Centro'),
('chalet'),
('Chorrillos'),
('cuadrados'),
('deseo'),
('dormitorios'),
('garaje'),
('Magdalena'),
('metros'),
('Miraflores'),
('Nervión'),
('norte'),
('piso'),
('quiero');

CREATE TABLE `ln_patrones` (
  `id` int(11) NOT NULL,
  `patron` varchar(255) DEFAULT NULL,
  `consultasql` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ln_patrones` (`id`, `patron`, `consultasql`) VALUES
(1, 'busco tipo', "SELECT * FROM viviendas WHERE tipo = '%1'"),
(2, 'busco zona', "SELECT * FROM viviendas WHERE zona = '%1'"),
(3, 'busco tipo zona', "SELECT * FROM viviendas WHERE tipo = '%1' AND zona = '%2'"),
(4, 'busco tipo numero dormitorios zona', "SELECT * FROM viviendas WHERE tipo = '%1' AND ndormitorios = %2 AND zona = '%3'"),
(5, 'busco tipo zona1 o zona2', "SELECT * FROM viviendas WHERE tipo = '%1' AND (zona = '%2' OR zona = '%3')"),
(6, 'busco tipo mas numero dormitorios zona', "SELECT * FROM viviendas WHERE tipo = '%1' AND ndormitorios > %2 AND zona = '%3'"),
(7, 'busco tipo mas metros metros cuadrados', "SELECT * FROM viviendas WHERE tipo = '%1' AND metros_cuadrados > %2"),
(8, 'busco tipo barato', "SELECT * FROM viviendas WHERE tipo = '%1' AND precio < 100000"),
(9, 'busco tipo zona garaje', "SELECT * FROM viviendas WHERE tipo = '%1' AND zona = '%2' AND extras = 'garaje'");

CREATE TABLE `viviendas` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `zona` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ndormitorios` int(11) DEFAULT NULL,
  `metros_cuadrados` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `extras` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `viviendas` (`id`, `tipo`, `zona`, `ndormitorios`, `metros_cuadrados`, `precio`, `extras`,`foto`) VALUES
(1, 'chalet', 'Miraflores', 6, 180, 350000.00, 'garaje', 'foto_1.jpg'),
(2, 'casa', 'Nervión', 4, 140, 220000.00, '', 'foto_2.jpg'),
(3, 'piso', 'Centro', 3, 75, 95000.00, '', 'foto_3.jpg'),
(4, 'chalet', 'Miraflores', 5, 250, 400000.00, 'garaje', 'foto_4.jpg'),
(5, 'piso', 'Nervión', 2, 60, 70000.00, 'garaje', 'foto_5.jpg'),
(6, 'casa', 'Magdalena', 4, 170, 260000.00, 'garaje', 'foto_6.jpg'),
(7, 'piso', 'Magdalena', 3, 110, 150000.00, '', 'foto_7.jpg'),
(8, 'chalet', 'Nervión', 5, 280, 370000.00, 'garaje', 'foto_8.jpg'),
(9, 'casa', 'Nervión', 3, 130, 180000.00, '', 'foto_9.jpg'),
(10, 'piso', 'Centro', 4, 90, 125000.00, '', 'foto_10.jpg'),
(11, 'chalet', 'Magdalena', 4, 200, 320000.00, 'garaje', 'foto_11.jpg'),
(12, 'piso', 'Chorrillos', 1, 45, 50000.00, '', 'foto_12.jpg'),
(13, 'casa', 'Centro', 5, 190, 300000.00, 'garaje', 'foto_13.jpg'),
(14, 'chalet', 'Chorrillos', 6, 270, 410000.00, 'garaje', 'foto_14.jpg'),
(15, 'piso', 'Chorrillos', 2, 65, 85000.00, '', 'foto_15.jpg');

ALTER TABLE `ln_diccionario`
  ADD PRIMARY KEY (`palabra`);

ALTER TABLE `ln_patrones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `viviendas`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ln_patrones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `viviendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;
