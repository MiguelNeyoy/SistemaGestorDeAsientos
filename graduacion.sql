-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-03-2026 a las 22:50:36
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `graduacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `admin_id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `usuario` varchar(20) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `numCuenta` varchar(7) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `carrera` varchar(100) NOT NULL,
  `turno` char(1) NOT NULL,
  `cantInvitado` int(11) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`numCuenta`, `nombre`, `apellido`, `carrera`, `turno`, `cantInvitado`, `email`) VALUES
('0154847', 'JOSE ARMANDO', 'JIMENEZ FUENTES', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1187668', 'LUIS GERARDO', 'GUTIERREZ VARGAS ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1187733', 'ALBERTO', 'TRIPP NAVA', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1193594', 'RICARDO', 'LUIS ESTRADA', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('1286378', 'SERGIO EDUARDO', 'TORRES BRACAMONTES', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1581151', 'JOSE ARMANDO', 'CASILLAS MACIAS', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1584009', 'MARIA DE JESUS', 'KELLY AGUIRRE ', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('1588229', 'BRANDON', 'GUTIERREZ ROCHIN ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1588397', ' JOAQUIN', 'GUEVARA SANCHEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1588653', 'JONATHAN', 'FRAGOSO RIVERA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1593466', 'MARCOS DANIEL', 'CAMACHO CAMACHO', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1593483', 'ANDRES', 'LOMELI IBAÑEZ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1632569', 'GABRIELA', 'VAZQUEZ OLIVO ', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('1680092', 'JENYFER RUBY', 'VARGAS MORAN ', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('1684253', 'MIGUEL ANGEL', 'CAZARES GARCIA', 'Licenciatura en Informática', 'M', 0, '0'),
('1693192', 'JOSE ANGEL', 'VELAZQUEZ DOMINGUEZ ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1694278', 'JESUS ALFONSO AGNI', 'GONZALEZ CAMBEROS', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('1783157', 'ALEJANDRINA', 'RAMOS CASTAÑEDA', 'Licenciatura en Informática', 'M', 0, '0'),
('1785709', 'SARAI', 'ROJAS SOLIS ', 'Licenciatura en informatica', 'V', 0, '13'),
('1786611', 'LUIS JAFET', 'ALCARAZ AGUILAR', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1787514', 'JESUS ANTONIO', 'BUSTAMANTE TIZNADO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1787570', 'LUIS DAVID', 'RUVALCABA AGUILAR', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1787810', 'MODESTO ALONSO', 'ALCARAZ DURAN', 'Licenciatura en informatica', 'V', 0, '3'),
('1788080', 'JOSE RODRIGO', 'AGUIRRE LIZARRAGA', 'Licenciatura en Informática', 'M', 0, '0'),
('1793731', 'MARIO ENRIQUE', 'ALVAREZ MORALES', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1885742', 'CHRISTIAN ROSSELL', 'CRUZ GARCIA', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1887418', 'JORGE ALBERTO', 'SARMIENTO RODRIGUEZ ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1887555', 'JORGE EDUARDO', 'MARTINEZ GALINDO ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1887588', 'VICTOR EDUARDO', 'VIZCARRA PALOMARES ', 'Licenciatura en Informática', 'M', 0, '0'),
('1887766', 'GONZALO JAVIER', 'LLAMAS RIOS', 'Licenciatura en Informática', 'M', 0, '0'),
('1887819', 'JOSE ALFREDO', 'VIZCARRA TIRADO ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1888839', ' ZAREK DE JESUS', 'RODRIGUEZ GONZALEZ ', 'Licenciatura en Informática', 'M', 0, '0'),
('1892442', 'CRISTINA', 'PEREZ BASTIDAS', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1980006', ' GEORGINA YARASED', 'LOZA SEGURA', 'Licenciatura en Informática', 'M', 0, '0'),
('1983229', ' LUIS ARMANDO', 'GARZON TIRADO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1984296', 'DIEGO', 'GARCIA MANZANO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1984785', 'ADAL SAID', 'MORALES CHAVEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1984919', 'FRANCISCO', 'RIVERA FIGUEROA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1985707', ' DANIEL ALFREDO', 'RUIZ SEVILLA', 'Licenciatura en Informática', 'M', 0, '0'),
('1985731', 'BRYAN ANTONIO', 'REYES LOPEZ ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1986109', 'RAMIRO ALEJANDRO', 'TISNADO SOMOZA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1986507', ' JUAN JOSE', 'GARCIA GUZMAN', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1986529', ' RICARDO', 'PADILLA MARTINEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1986830', 'LUIS YAEL', 'ARAMBURO CONTRERAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1987317', 'ANTONIO', 'GUTIERREZ BELTRAN', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1987322', 'PEDRO GILBERTO', 'MUÑOZ TIZNADO', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1987359', 'JOSE ANGEL', 'MORALES GONZALEZ', 'Licenciatura en Informática', 'M', 0, '0'),
('1987447', 'JULIO CESAR', 'SUAREZ MANJARREZ ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1987449', ' EMMANUEL ANTONIO', 'AGUILAR OSUNA', 'Licenciatura en Informática', 'M', 0, '0'),
('1987469', ' ANGEL SANTIAGO', 'SALAZAR SEGURA', 'Licenciatura en Informática', 'M', 0, '0'),
('1987755', 'CARLOS EDUARDO', 'DE LA TOBA NORIEGA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1987759', ' FRANCISCO MIGUEL', 'SOBERANES FRANCO', 'Licenciatura en Informática', 'M', 0, '0'),
('1987762', 'ROLANDO', 'OSUNA GAXIOLA', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1987779', 'ALEXIS VALENTIN', 'LOPEZ PALOMARES', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1987808', 'MARLENE ALEJANDRA', 'COVARRUBIAS BASTIDAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1987818', 'LUIS FERNANDO', 'MEDINA LIZARRAGA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1987843', ' AARON', 'RUELAS VELAZQUEZ ', 'Licenciatura en Informática', 'M', 0, '0'),
('1987882', ' GUSTAVO ANGEL', 'DIAZ LUCAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1988019', 'JESUS RODOLFO', 'CARVAJAL LIZARRAGA', 'Licenciatura en informatica', 'V', 0, '6'),
('1988095', 'GABRIEL ALBERTO', 'CONTRERAS BRITO', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1988101', 'DANIEL', 'LEON NERIZ ', 'Licenciatura en informatica', 'V', 0, '8'),
('1988234', ' CHRISTIAN YAIR', 'CRUZ ALANIZ', 'Licenciatura en Informática', 'M', 0, '0'),
('1988256', 'JESUS BENJAMIN', 'SANDOVAL MORALES ', 'Licenciatura en Informática', 'M', 0, '0'),
('1988295', 'ANGELICA', 'RIVERA ESCALERA', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('1988364', 'KEVIN BRYAN', 'CAÑEDO RAYGOZA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1989114', ' EDGAR JESUS', 'TELLEZ TORRES ', 'Licenciatura en Informática', 'M', 0, '0'),
('1992502', ' GRISEL', 'SILVA SANCHEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('1993572', 'CARLOS ALEXIS', 'MACHADO TAPIA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2090095', 'JESUS GUADALUPE', 'RODRIGUEZ OSUNA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2093599', 'MIGUEL ANGEL', 'AGUILAR LOAIZA', 'Licenciatura en informatica', 'V', 0, '1'),
('2093600', 'ALEJANDRO', 'CARRILLO COLADO', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2102276', 'ANGEL JAIR', 'HINOGIANTE LOPEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2119855', ' CESAR ALEJANDRO', 'QUEVEDO MENDOZA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2120832', 'LUIS ADOLFO', 'SANCHEZ OSUNA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2121218', 'HUMBERTO', 'AVENDAÑO COVARRUBIAS', 'Licenciatura en Informática', 'M', 0, '0'),
('2121221', 'JORDAN ALFONSO', 'COVARRUBIAS OJEDA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2121222', ' DANIEL HERNAN', 'CORTEZ SARABIA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2121224', ' ALDO DAVID', 'ENCISO MILLAN', 'Licenciatura en Informática', 'M', 0, '0'),
('2121227', 'PABLO ALEJANDRO', 'BATANI SILVA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2121240', ' JESUS MOISES', 'SALAZAR RUBIO', 'Licenciatura en Informática', 'M', 0, '0'),
('2121241', 'AMPELIO', 'BASTIDAS VIZCARRA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2121252', ' JESUS DANIEL', 'JIMENEZ GUERRERO', 'Licenciatura en Informática', 'M', 0, '0'),
('2121268', ' ABEL ABRAHAM', 'CRUZ JIMENEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2121270', 'MARTIN ARMANDO', 'TOSTADO GARAY', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2121312', 'VICTOR GABRIEL', 'BOJORQUEZ BASTIDAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2218702', 'ALINE DINORA CHRISTA', 'ORTEGA MORALES', 'Licenciatura en informatica', 'V', 0, '10'),
('2219292', 'AMAURY ALFONSO', 'QUINTERO ALVAREZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219682', ' ALEXIS JAVIER', 'DEVORA MOLINA', 'Licenciatura en Informática', 'M', 0, '0'),
('2219684', ' ARTURO', 'MORALES OSUNA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219688', 'MARIA DE LOS ANGELES', 'RENDON RENDON ', 'Licenciatura en informatica', 'V', 0, '12'),
('2219689', ' ROGELIO GAEL', 'RIOS NAVARRETE ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219691', 'LUIS MARIO', 'VIZCARRA PERAZA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219693', ' DANIA LIZETH', 'ZATARAIN PROA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219694', 'BRYAN ORLANDO', 'GALLARDO VALADEZ ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219695', 'FRANCISCO DANIEL', 'CARO MORALES ', 'Licenciatura en informatica', 'V', 0, '4'),
('2219696', 'JACINTO LEONARDO', 'CARO MORALES ', 'Licenciatura en informatica', 'V', 0, '5'),
('2219700', 'JOSUE JOAQUIN', 'AGUILERA ZATARAIN', 'Licenciatura en informatica', 'V', 0, '2'),
('2219703', ' RAMON EDUARDO', 'LIZARRAGA VILLASEÑOR', 'Licenciatura en Informática', 'M', 0, '0'),
('2219704', ' EMILIANO', 'LOPEZ CAMACHO ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219705', 'MITZI NARUMY', 'PEREZ GONZALEZ ', 'Licenciatura en informatica', 'V', 0, '11'),
('2219715', ' LUIS ORLANDO', 'FLORES CANIZALES ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219717', ' CARLOS EDUARDO', 'OSUNA OSUNA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219720', 'MICHELLE', 'BALLESTEROS AGUIRRE', 'Licenciatura en Informática', 'M', 0, '0'),
('2219721', 'JUAN MANUEL', 'GUEVARA GURROLA', 'Licenciatura en Informática', 'M', 0, '0'),
('2219722', 'EDGAR ADRIAN', 'RUEZGAS PINEDA ', 'Licenciatura en informatica', 'V', 0, '14'),
('2219725', ' JOSE MANUEL', 'RUBIO MARTINEZ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219726', 'DIEGO ALBERTO', 'TIRADO MURRAY', 'Licenciatura en Informática', 'M', 0, '0'),
('2219729', 'JUAN DANIEL', 'VAZQUEZ GARCIA ', 'Licenciatura en informatica', 'V', 0, '15'),
('2219732', 'ANGEL MICHAEL', 'ESCOBEDO RODRIGUEZ ', 'Licenciatura en informatica', 'V', 0, '7'),
('2219733', 'JESUS ALEJANDRO', 'IBARRA GARCIA', 'Licenciatura en Informática', 'M', 0, '0'),
('2219734', 'MIGUEL ANGEL', 'NEYOY CENICEROS', 'Licenciatura en informatica', 'V', 0, '9'),
('2219743', 'JONATHAN LEONEL', 'OLIVAS ARAMBURO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219745', 'JUAN CARLOS', 'OSUNA CAÑEDO ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219746', 'CARLOS HUGO', 'CEREZO ARREOLA ', 'Licenciatura en Informática', 'M', 0, '0'),
('2219749', 'FRANCISCO ALAIN', 'BRACAMONTES ESPINOZA', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('2219752', 'EDUARDO', 'VAZQUEZ DIAZ', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('2219757', 'LUIS GUILLERMO', 'MORALES PAEZ', 'Ingeniería en Sistemas de Información (Modalidad Virtual)', 'M', 0, '0'),
('2219760', 'JAEL SAID', 'CRISTERNA PERAZA ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219761', 'LESLIE DANIELA', 'VALENZUELA ZATARAIN', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219763', 'ALEJANDRO DANIEL', 'PEREZ AVILA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219764', 'JANIA YAMILETH', 'MURGUIA SERRANO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219769', ' WENDI AIME', 'ROMAN ONTIVEROS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219770', 'CARLOS IVAN', 'QUINTANA CANIZALEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219771', 'CARLOS DANIEL', 'IBAÑEZ SANCHEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219772', 'JOSE IGNACIO', 'CORDOBA LOPEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219773', 'SEBASTIAN', 'ZATARAIN CAMPOS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219774', 'DAVID', 'CRUZ GONZALEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219776', 'FRANCISCO ALFONSO', 'SARMIENTO RENDON', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219777', ' DULCE GIOVANNA', 'SOTO BALDENEGRO', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219780', 'JESUS MANUEL', 'BANCALARI OSUNA', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219782', 'KAREN GUADALUPE', 'TAPIA TORRES', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219784', 'DANIEL HIRAM', 'OSUNA AGUIRRE', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219787', 'SAUL ISAC', 'DELGADO CRUZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219789', 'JESSIE ALEJANDRO', 'MUÑOZ ARCE ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219794', ' PEDRO FAVIO', 'RONDAN GARCIA', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219800', 'FRANCISCO EMILIANO', 'CASTRO SANCHEZ ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219801', ' GILBERTO', 'LERMA RODRIGUEZ', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219804', 'JUAN FRANCISCO', 'FELIX GONZALEZ ', ' Licenciatura en Ingeniería en Sistemas de Información', 'V', 0, '0'),
('2219809', 'ESMERALDA JOSSELIN', 'CASTAÑEDA ROJAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0'),
('2219823', 'ERIC', 'JAUREGUI RUELAS', 'Licenciatura en Ingeniería en Sistemas de Información', 'M', 0, '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asiento`
--

CREATE TABLE `asiento` (
  `idAsiento` int(11) NOT NULL,
  `numCuenta` varchar(7) DEFAULT NULL,
  `letra` char(1) DEFAULT NULL,
  `numero` char(2) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asiento`
--

INSERT INTO `asiento` (`idAsiento`, `numCuenta`, `letra`, `numero`, `estado`) VALUES
(1, '1987449', 'B', '8', 0),
(2, '1788080', 'B', '9', 0),
(3, '2121218', 'B', '10', 0),
(4, '2219720', 'B', '11', 0),
(5, '2121241', 'B', '12', 0),
(6, '2121227', 'B', '13', 0),
(7, '1684253', 'B', '14', 0),
(8, '2219746', 'B', '15', 0),
(9, '2121222', 'B', '16', 0),
(10, '2121221', 'B', '17', 0),
(11, '1988234', 'B', '18', 0),
(12, '2219682', 'B', '19', 0),
(13, '2121224', 'B', '20', 0),
(14, '2219715', 'B', '21', 0),
(15, '2219694', 'B', '22', 0),
(16, '2219721', 'B', '23', 0),
(17, '2219733', 'C', '8', 0),
(18, '2121252', 'C', '9', 0),
(19, '2219703', 'C', '10', 0),
(20, '1887766', 'C', '11', 0),
(21, '2219704', 'C', '12', 0),
(22, '1980006', 'C', '13', 0),
(23, '1987359', 'C', '14', 0),
(24, '2219684', 'C', '15', 0),
(25, '2219717', 'C', '16', 0),
(26, '2119855', 'C', '17', 0),
(27, '1783157', 'C', '18', 0),
(28, '2219689', 'C', '19', 0),
(29, '1888839', 'C', '20', 0),
(30, '2219725', 'C', '21', 0),
(31, '1987843', 'C', '22', 0),
(32, '1985707', 'C', '23', 0),
(33, '2121240', 'D', '8', 0),
(34, '1987469', 'D', '9', 0),
(35, '1988256', 'D', '10', 0),
(36, '1987759', 'D', '11', 0),
(37, '1989114', 'D', '12', 0),
(38, '2219726', 'D', '13', 0),
(39, '1887588', 'D', '14', 0),
(40, '2219691', 'D', '15', 0),
(41, '2219693', 'D', '16', 0),
(42, '1793731', 'D', '17', 0),
(43, '1986830', 'D', '18', 0),
(44, '2121312', 'D', '19', 0),
(45, '1787514', 'D', '20', 0),
(46, '1988364', 'D', '21', 0),
(47, '2219809', 'D', '22', 0),
(48, '2219772', 'D', '23', 0),
(49, '1987808', 'E', '8', 0),
(50, '2219760', 'E', '9', 0),
(51, '2219774', 'E', '10', 0),
(52, '2121268', 'E', '11', 0),
(53, '1987755', 'E', '12', 0),
(54, '2219787', 'E', '13', 0),
(55, '1987882', 'E', '14', 0),
(56, '1588653', 'E', '15', 0),
(57, '1986507', 'E', '16', 0),
(58, '1984296', 'E', '17', 0),
(59, '1983229', 'E', '18', 0),
(60, '1588397', 'E', '19', 0),
(61, '2102276', 'E', '20', 0),
(62, '2219771', 'E', '21', 0),
(63, '2219823', 'E', '22', 0),
(64, '2219801', 'E', '23', 0),
(65, '1987779', 'F', '8', 0),
(66, '1993572', 'F', '9', 0),
(67, '1987818', 'F', '10', 0),
(68, '1984785', 'F', '11', 0),
(69, '2219764', 'F', '12', 0),
(70, '2219743', 'F', '13', 0),
(71, '1986529', 'F', '14', 0),
(72, '2219763', 'F', '15', 0),
(73, '2219770', 'F', '16', 0),
(74, '2219292', 'F', '17', 0),
(75, '1985731', 'F', '18', 0),
(76, '1984919', 'F', '19', 0),
(77, '2090095', 'F', '20', 0),
(78, '2219769', 'F', '21', 0),
(79, '2219794', 'F', '22', 0),
(80, '1787570', 'F', '23', 0),
(81, '2120832', 'G', '8', 0),
(82, '2219776', 'G', '9', 0),
(83, '1992502', 'G', '10', 0),
(84, '2219777', 'G', '11', 0),
(85, '1986109', 'G', '12', 0),
(86, '2121270', 'G', '13', 0),
(87, '2219761', 'G', '14', 0),
(88, '1887819', 'G', '15', 0),
(89, '2219773', 'G', '16', 0),
(90, '2219749', 'G', '17', 0),
(91, '1694278', 'G', '18', 0),
(92, '1584009', 'G', '19', 0),
(93, '1193594', 'G', '20', 0),
(94, '2219757', 'G', '21', 0),
(95, '1680092', 'G', '22', 0),
(96, '2219752', 'G', '23', 0),
(97, '1632569', 'H', '8', 0),
(98, '2093599', 'H', '9', 0),
(99, '2219700', 'H', '10', 0),
(100, '1787810', 'H', '11', 0),
(101, '2219695', 'H', '12', 0),
(102, '2219696', 'H', '13', 0),
(103, '1988019', 'H', '14', 0),
(104, '2219732', 'H', '15', 0),
(105, '1988101', 'H', '16', 0),
(106, '2219734', 'H', '17', 0),
(107, '2218702', 'H', '18', 0),
(108, '2219705', 'H', '19', 0),
(109, '2219688', 'H', '20', 0),
(110, '1785709', 'H', '21', 0),
(111, '2219722', 'H', '22', 0),
(112, '2219729', 'H', '23', 0),
(113, '1786611', 'I', '8', 0),
(114, '2219780', 'I', '9', 0),
(115, '1593466', 'I', '10', 0),
(116, '2093600', 'I', '11', 0),
(117, '1581151', 'I', '12', 0),
(118, '2219800', 'I', '13', 0),
(119, '1988095', 'I', '14', 0),
(120, '1885742', 'I', '15', 0),
(121, '2219804', 'I', '16', 0),
(122, '1987317', 'I', '17', 0),
(123, '1588229', 'I', '18', 0),
(124, '1187668', 'I', '19', 0),
(125, '0154847', 'I', '20', 0),
(126, '1593483', 'I', '21', 0),
(127, '1887555', 'I', '22', 0),
(128, '2219789', 'I', '23', 0),
(129, '1987322', 'J', '8', 0),
(130, '2219784', 'J', '9', 0),
(131, '2219745', 'J', '10', 0),
(132, '1987762', 'J', '11', 0),
(133, '1892442', 'J', '12', 0),
(134, '1988295', 'J', '13', 0),
(135, '1887418', 'J', '14', 0),
(136, '1987447', 'J', '15', 0),
(137, '2219782', 'J', '16', 0),
(138, '1286378', 'J', '17', 0),
(139, '1187733', 'J', '18', 0),
(140, '1693192', 'J', '19', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `numCuenta` varchar(7) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`numCuenta`);

--
-- Indices de la tabla `asiento`
--
ALTER TABLE `asiento`
  ADD PRIMARY KEY (`idAsiento`),
  ADD KEY `numCuenta` (`numCuenta`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD KEY `numCuenta` (`numCuenta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asiento`
--
ALTER TABLE `asiento`
  ADD CONSTRAINT `asiento_ibfk_1` FOREIGN KEY (`numCuenta`) REFERENCES `alumno` (`numCuenta`);

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`numCuenta`) REFERENCES `alumno` (`numCuenta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
