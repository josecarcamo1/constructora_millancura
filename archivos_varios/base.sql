-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.27-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para constructora_millancura
CREATE DATABASE IF NOT EXISTS `constructora_millancura` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `constructora_millancura`;

-- Volcando estructura para tabla constructora_millancura.personas
CREATE TABLE IF NOT EXISTS `personas` (
  `per_id` int(11) NOT NULL AUTO_INCREMENT,
  `per_nombre_completo` varchar(255) DEFAULT NULL,
  `per_nombre` varchar(255) DEFAULT NULL,
  `per_segundo_nombre` varchar(255) DEFAULT NULL,
  `per_apellido` varchar(255) DEFAULT NULL,
  `per_segundo_apellido` varchar(255) DEFAULT NULL,
  `per_correo` varchar(255) DEFAULT NULL,
  `per_contrasena` longtext DEFAULT NULL,
  PRIMARY KEY (`per_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla constructora_millancura.personas: ~2 rows (aproximadamente)
INSERT INTO `personas` (`per_id`, `per_nombre_completo`, `per_nombre`, `per_segundo_nombre`, `per_apellido`, `per_segundo_apellido`, `per_correo`, `per_contrasena`) VALUES
	(1, NULL, NULL, NULL, NULL, NULL, 'matiasdeteran@gmail.com', '1234'),
	(4, NULL, 'Joseeeee', 'Miguel', 'Carcamo', 'Rodriguez', 'josecarcamorodriguez1234@gmail.com', '$2y$10$nCwL4hDh77tzzlslFbww3.BoxMoxq/9bWVx/AdPw2N.saA4FXeXOW');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
