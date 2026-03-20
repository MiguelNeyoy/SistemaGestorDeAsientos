-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-03-2026 a las 20:36:12
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
  `numero` char(1) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
