-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql103.infinityfree.com
-- Tiempo de generación: 18-06-2026 a las 23:40:47
-- Versión del servidor: 11.4.12-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_42154355_motomania`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'Emilio Izaguire', 'emilio@gmail.com', '88791946', 'Bo, Cabañas 13 y 14 calle 13 ave', '2026-06-11 06:17:10'),
(2, 'Dorina', 'dori@gmail.com', '12330214', 'brhrybt', '2026-06-12 18:25:40'),
(4, 'arroz con pollo', 'polloac@gmail.com', '12330214', 'brhrybt', '2026-06-13 02:06:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'texto',
  `grupo` varchar(50) DEFAULT 'general',
  `rol_permiso` varchar(100) DEFAULT 'superadmin',
  `actualizado` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `tipo`, `grupo`, `rol_permiso`, `actualizado`) VALUES
(1, 'empresa_nombre', 'MotoGestión Web', 'Nombre de la empresa', 'texto', 'empresa', 'admin', '2026-06-16 22:50:31'),
(2, 'empresa_rtn', '0000-0000-00000', 'RTN de la empresa', 'texto', 'empresa', 'admin', '2026-06-16 22:50:31'),
(3, 'empresa_telefono', '+504 0000-0000', 'Teléfono de la empresa', 'texto', 'empresa', 'admin', '2026-06-16 22:50:31'),
(4, 'empresa_direccion', 'Tegucigalpa, Honduras', 'Dirección de la empresa', 'texto', 'empresa', 'admin', '2026-06-16 22:50:31'),
(5, 'empresa_email', 'info@motogestion.com', 'Email de la empresa', 'texto', 'empresa', 'admin', '2026-06-16 22:50:31'),
(6, 'tasa_impuesto', '15.00', 'Tasa de impuesto (ISV) en %', 'numero', 'impuestos', 'admin', '2026-06-16 22:50:31'),
(7, 'aplicar_impuesto', '1', 'Aplicar impuesto en ventas (1=Si, 0=No)', 'booleano', 'impuestos', 'admin', '2026-06-16 22:50:31'),
(8, 'factura_prefijo', 'INV-', 'Prefijo de factura', 'texto', 'factura', 'admin', '2026-06-16 22:50:31'),
(9, 'factura_inicial', '1001', 'Número de factura inicial', 'numero', 'factura', 'admin', '2026-06-16 22:50:31'),
(10, 'factura_pie', '¡Gracias por su compra!', 'Pie de página de factura', 'texto', 'factura', 'admin', '2026-06-16 22:50:31'),
(11, 'notificaciones_stock', '5', 'Alertar cuando stock <= X unidades', 'numero', 'notificaciones', 'admin', '2026-06-16 22:50:31'),
(12, 'notificaciones_cobros', '1', 'Alertar de cobros pendientes (1=Si, 0=No)', 'booleano', 'notificaciones', 'admin', '2026-06-16 22:50:31'),
(13, 'moneda_predeterminada', 'Lempiras', 'Moneda predeterminada', 'texto', 'finanzas', 'finance', '2026-06-16 22:50:31'),
(14, 'formato_decimales', '2', 'Número de decimales', 'numero', 'finanzas', 'finance', '2026-06-16 22:50:31'),
(15, 'umbral_stock_bajo', '5', 'Umbral de stock bajo para inventario', 'numero', 'inventario', 'inventory', '2026-06-16 22:50:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory_movements`
--

CREATE TABLE `inventory_movements` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type` enum('IN','OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `inventory_movements`
--

INSERT INTO `inventory_movements` (`id`, `product_id`, `type`, `quantity`, `reference`, `created_at`, `user_id`) VALUES
(1, 7, 'IN', 150, 'CREACION_PRODUCTO', '2026-06-15 21:34:53', 4),
(2, 1, 'IN', 2, 'EDICION_PRODUCTO', '2026-06-15 22:52:38', 1),
(3, 1, 'IN', 1, 'EDICION_PRODUCTO', '2026-06-16 01:45:51', 1),
(4, 3, 'IN', 1000, 'EDICION_PRODUCTO', '2026-06-18 01:16:53', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preferencias_usuario`
--

CREATE TABLE `preferencias_usuario` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `preferencias_usuario`
--

INSERT INTO `preferencias_usuario` (`id`, `user_id`, `clave`, `valor`) VALUES
(1, 4, 'tema', 'claro'),
(2, 4, 'fuente', 'normal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) DEFAULT 5,
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `image`, `description`, `price`, `cost`, `stock`, `min_stock`, `supplier_id`, `created_at`) VALUES
(1, '001', 'Tubo Galvanizado', 'uploads/products/6a305c76d7845.webp', NULL, '2000.00', '650.00', 51, 50, 1, '2026-06-11 06:18:26'),
(2, '002', 'Lamina de Zinc', 'uploads/products/6a305c3ba5862.webp', NULL, '278.00', '95.00', 48, 100, 1, '2026-06-12 17:24:54'),
(3, '003', 'Bloque de Cemento 8\"', 'uploads/products/6a2c82ae664ef.jpg', NULL, '24.00', '7.00', 1000, 250, 2, '2026-06-12 17:53:15'),
(4, '004', 'Metro', 'uploads/products/6a2c78030711b.jpg', NULL, '100.00', '25.00', 197, 20, 2, '2026-06-12 18:24:30'),
(5, '005', 'Clavos Galvanizados', 'uploads/products/6a2c79b539f0d.jpg', NULL, '5.00', '2.50', 22777, 500, 2, '2026-06-12 21:20:44'),
(7, '006', 'Pinturas', 'uploads/products/6a306ffe42c62.webp', NULL, '675.00', '325.00', 147, 25, 2, '2026-06-15 21:34:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 15.00,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `total_profit` decimal(10,2) DEFAULT 0.00,
  `user_id` int(11) NOT NULL,
  `status` enum('pendiente','pagado','cancelado') DEFAULT 'pendiente',
  `payment_method` varchar(50) DEFAULT NULL,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `payment_date` datetime DEFAULT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `cashier_name` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `invoice_number`, `client_id`, `sale_date`, `total`, `subtotal`, `tax`, `tax_rate`, `total_cost`, `total_profit`, `user_id`, `status`, `payment_method`, `change_amount`, `payment_date`, `cashier_id`, `cashier_name`) VALUES
(1, 'INV-1781158738-819', 1, '2026-06-10 23:18:57', '4000.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pagado', 'Efectivo', '0.00', '2026-06-16 20:42:21', 5, 'Roberto Cajero'),
(2, 'INV-1781158800-836', 1, '2026-06-10 23:20:00', '1920000.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pagado', 'Efectivo', '0.00', '2026-06-17 10:29:51', 5, 'Roberto Cajero'),
(3, 'INV-1781288325-651', 1, '2026-06-12 11:18:45', '834000.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pagado', 'Efectivo', '0.00', '2026-06-17 11:01:35', 5, 'Roberto Cajero'),
(4, 'INV-20260612-750', 1, '2026-06-12 11:22:06', '76000.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pagado', 'Efectivo', '0.00', '2026-06-17 13:53:17', 1, 'Administrador'),
(5, 'INV-20260612-190', 1, '2026-06-12 11:25:07', '723800.00', '0.00', '0.00', '15.00', '0.00', '0.00', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(6, 'INV-20260612-404', 2, '2026-06-12 11:27:48', '500.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(7, 'INV-20260612-139', 2, '2026-06-12 14:30:04', '1850.00', '0.00', '0.00', '15.00', '0.00', '0.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(8, 'INV-20260612-548', 1, '2026-06-12 14:56:37', '500.00', '0.00', '0.00', '15.00', '250.00', '250.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(9, 'INV-20260612-303', NULL, '2026-06-12 15:18:14', '10100.00', '0.00', '0.00', '15.00', '5000.00', '5100.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(10, 'INV-20260612-230', NULL, '2026-06-12 15:19:13', '1300.00', '0.00', '0.00', '15.00', '50.00', '1250.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(11, 'INV-20260612-950', NULL, '2026-06-12 15:26:53', '2650.00', '0.00', '0.00', '15.00', '125.00', '2525.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(12, 'INV-20260612-719', NULL, '2026-06-12 15:29:08', '1096.00', '0.00', '0.00', '15.00', '500.00', '596.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(13, 'INV-20260612-107', NULL, '2026-06-12 15:36:14', '3600.00', '0.00', '0.00', '15.00', '550.00', '3050.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(14, 'INV-20260612-680', NULL, '2026-06-12 15:41:04', '558.00', '0.00', '0.00', '15.00', '155.00', '403.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(15, 'INV-20260612-549', 2, '2026-06-12 15:44:05', '940.00', '0.00', '0.00', '15.00', '300.00', '640.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(16, 'INV-20260612-785', 1, '2026-06-12 15:47:40', '1074.00', '0.00', '0.00', '15.00', '275.00', '799.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(17, 'INV-20260612-529', 4, '2026-06-12 19:06:52', '600.00', '0.00', '0.00', '15.00', '0.00', '600.00', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(18, 'INV-20260612-787', 2, '2026-06-12 19:07:13', '125.00', '0.00', '0.00', '15.00', '62.50', '62.50', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(19, 'INV-20260615-982', NULL, '2026-06-15 14:37:41', '1450.00', '0.00', '0.00', '15.00', '675.00', '775.00', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(20, 'INV-20260615-503', 4, '2026-06-15 15:51:31', '6000.00', '0.00', '0.00', '15.00', '0.00', '6000.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(21, 'INV-20260615-327', NULL, '2026-06-15 18:48:27', '18400.00', '0.00', '0.00', '15.00', '7000.00', '11400.00', 1, 'pagado', 'Tarjeta Crédito', '0.00', '2026-06-16 18:37:18', 5, 'Roberto Cajero'),
(22, 'INV-20260616-420', NULL, '2026-06-16 10:36:09', '675.00', '0.00', '0.00', '15.00', '325.00', '350.00', 2, 'pagado', 'Efectivo', '0.00', '2026-06-16 18:20:33', 5, 'Roberto Cajero'),
(23, 'INV-20260616-219', NULL, '2026-06-16 18:02:49', '1000.00', '0.00', '0.00', '15.00', '500.00', '500.00', 1, 'pagado', 'Efectivo', '0.00', '2026-06-16 18:07:27', 1, 'Administrador'),
(24, 'INV-20260616-908', 4, '2026-06-16 20:39:06', '100.00', '0.00', '0.00', '15.00', '50.00', '50.00', 1, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(25, 'INV-20260616-493', 2, '2026-06-16 20:40:38', '70.00', '0.00', '0.00', '15.00', '35.00', '35.00', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL),
(26, 'INV-20260616-580', 1, '2026-06-16 20:56:24', '556.00', '0.00', '0.00', '15.00', '190.00', '366.00', 8, 'pagado', 'Efectivo', '0.00', '2026-06-17 11:08:53', 5, 'Roberto Cajero'),
(27, 'INV-20260617-882', 2, '2026-06-16 22:17:59', '55.20', '48.00', '7.20', '15.00', '14.00', '34.00', 1, 'pagado', 'Efectivo', '277.80', '2026-06-16 22:18:27', 1, 'Administrador'),
(28, 'INV-20260617-783', NULL, '2026-06-17 18:11:15', '9660.00', '8400.00', '1260.00', '15.00', '2450.00', '5950.00', 1, 'pagado', 'Efectivo', '340.00', '2026-06-17 18:12:42', 1, 'Administrador'),
(29, 'INV-20260617-312', 4, '2026-06-17 18:24:15', '549.70', '478.00', '71.70', '15.00', '145.00', '333.00', 2, 'pendiente', NULL, '0.00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `subtotal_cost` decimal(10,2) DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `unit_cost`, `subtotal`, `subtotal_cost`) VALUES
(1, 1, 1, 2, '2000.00', '0.00', '4000.00', '0.00'),
(2, 2, 1, 960, '2000.00', '0.00', '1920000.00', '0.00'),
(3, 3, 2, 3000, '278.00', '0.00', '834000.00', '0.00'),
(4, 4, 1, 38, '2000.00', '0.00', '76000.00', '0.00'),
(5, 5, 2, 2500, '278.00', '0.00', '695000.00', '0.00'),
(6, 5, 3, 1200, '24.00', '0.00', '28800.00', '0.00'),
(7, 6, 4, 5, '100.00', '0.00', '500.00', '0.00'),
(8, 7, 5, 10, '5.00', '0.00', '50.00', '0.00'),
(9, 7, 4, 18, '100.00', '0.00', '1800.00', '0.00'),
(10, 8, 5, 100, '5.00', '2.50', '500.00', '250.00'),
(11, 9, 4, 1, '100.00', '0.00', '100.00', '0.00'),
(12, 9, 4, 1, '100.00', '0.00', '100.00', '0.00'),
(13, 10, 3, 50, '24.00', '0.00', '1200.00', '0.00'),
(14, 10, 3, 50, '24.00', '0.00', '1200.00', '0.00'),
(15, 11, 3, 100, '24.00', '0.00', '2400.00', '0.00'),
(16, 11, 3, 100, '24.00', '0.00', '2400.00', '0.00'),
(17, 12, 5, 200, '5.00', '2.50', '1000.00', '500.00'),
(18, 12, 5, 200, '5.00', '2.50', '1000.00', '500.00'),
(19, 13, 3, 100, '24.00', '0.00', '2400.00', '0.00'),
(20, 13, 5, 200, '5.00', '2.50', '1000.00', '500.00'),
(21, 13, 5, 200, '5.00', '2.50', '1000.00', '500.00'),
(22, 14, 3, 2, '24.00', '0.00', '48.00', '0.00'),
(23, 14, 5, 22, '5.00', '2.50', '110.00', '55.00'),
(24, 14, 5, 22, '5.00', '2.50', '110.00', '55.00'),
(25, 15, 5, 100, '5.00', '2.50', '500.00', '250.00'),
(26, 15, 3, 10, '24.00', '0.00', '240.00', '0.00'),
(27, 15, 3, 10, '24.00', '0.00', '240.00', '0.00'),
(28, 16, 3, 1, '24.00', '0.00', '24.00', '0.00'),
(29, 16, 5, 10, '5.00', '2.50', '50.00', '25.00'),
(30, 16, 4, 10, '100.00', '25.00', '1000.00', '250.00'),
(31, 17, 3, 25, '24.00', '0.00', '600.00', '0.00'),
(32, 18, 5, 25, '5.00', '2.50', '125.00', '62.50'),
(33, 19, 7, 2, '675.00', '325.00', '1350.00', '650.00'),
(34, 19, 4, 1, '100.00', '25.00', '100.00', '25.00'),
(35, 20, 1, 3, '2000.00', '0.00', '6000.00', '0.00'),
(36, 21, 2, 50, '278.00', '95.00', '13900.00', '4750.00'),
(37, 21, 5, 900, '5.00', '2.50', '4500.00', '2250.00'),
(38, 22, 7, 1, '675.00', '325.00', '675.00', '325.00'),
(39, 23, 5, 200, '5.00', '2.50', '1000.00', '500.00'),
(40, 24, 5, 20, '5.00', '2.50', '100.00', '50.00'),
(41, 25, 5, 14, '5.00', '2.50', '70.00', '35.00'),
(42, 26, 2, 2, '278.00', '95.00', '556.00', '190.00'),
(43, 27, 3, 2, '24.00', '7.00', '48.00', '14.00'),
(44, 28, 3, 350, '24.00', '7.00', '8400.00', '2450.00'),
(45, 29, 2, 1, '278.00', '95.00', '278.00', '95.00'),
(46, 29, 4, 2, '100.00', '25.00', '200.00', '50.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details_backup`
--

CREATE TABLE `sale_details_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `subtotal_cost` decimal(10,2) DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`, `phone`, `created_at`) VALUES
(1, 'Diunsa', 'Carlos', '8879-1946', '2026-06-11 06:16:25'),
(2, 'La Mundial', 'Susana Perez', '8888888', '2026-06-12 17:52:33'),
(3, 'Alibaba', 'Fernando Suarez', '+504 9999-9999', '2026-06-15 22:50:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 7, 'admincarlos', 'LOGOUT', 'users', 7, 'Usuario admincarlos cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:20:19'),
(2, 7, 'admincarlos', 'LOGIN_EXITOSO', 'users', 7, 'Usuario admincarlos inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:20:28'),
(3, 7, 'admincarlos', 'LOGOUT', 'users', 7, 'Usuario admincarlos cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:31:46'),
(4, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:31:52'),
(5, 1, 'admin', 'CREATE', 'suppliers', 2, 'Proveedor creado: La Mundial - Contacto: Susana Perez - Teléfono: 8888888', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:52:33'),
(6, 1, 'admin', 'CREATE', 'products', 3, 'Producto creado: Bloque de Cemento 8\" - Código: 003 - Precio: L 24 - Stock: 2000', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:53:15'),
(7, 1, 'admin', 'UPDATE', 'users', 7, 'Usuario editado: admincarlos - Nuevo rol: seller', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:56:32'),
(8, 1, 'admin', 'TEST', 'test', 1, 'Este es un log de prueba', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 17:57:46'),
(9, 1, 'admin', 'CLEAN_LOGS', 'system_logs', NULL, 'Se eliminaron 0 registros antiguos (más de 30 días)', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:06:39'),
(10, 1, 'admin', 'CLEAN_LOGS', 'system_logs', NULL, 'Se eliminaron 0 registros antiguos (más de 1 días)', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:07:11'),
(11, 1, 'admin', 'CREATE', 'sales', 3, 'Venta registrada - Factura: INV-1781288325-651 - Total: L 834000 - Items: 1 - Cliente ID: 1', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:18:45'),
(12, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 18:20:31'),
(13, 1, 'admin', 'CREATE', 'sales', 4, 'Venta registrada - Factura: INV-20260612-750 - Total: L 76000 - Items: 1 - Cliente ID: 1', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:22:06'),
(14, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:23:53'),
(15, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:24:01'),
(16, 2, 'vendedor', 'UPDATE', 'products', 2, 'Producto actualizado: Lamina de Zinc - Cambios: [Código: 002, Nombre: Lamina de Zinc, Precio: 278.00, Stock: -2500] → [Código: 002, Nombre: Lamina de Zinc, Precio: 278.00, Stock: 2500]', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:24:19'),
(17, 1, 'admin', 'CREATE', 'products', 4, 'Producto creado: Metro - Código: 004 - Precio: L 100 - Stock: 25', '216.234.223.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 18:24:30'),
(18, 2, 'vendedor', 'CREATE', 'sales', 5, 'Venta registrada - Factura: INV-20260612-190 - Total: L 723800 - Items: 2 - Cliente ID: 1', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:25:07'),
(19, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:25:35'),
(20, 1, 'admin', 'CREATE', 'clients', 2, 'Cliente creado: Dorina - Email: dori@gmail.com - Teléfono: 12330214', '216.234.223.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 18:25:40'),
(21, 1, 'admin', 'CREATE', 'sales', 6, 'Venta registrada - Factura: INV-20260612-404 - Total: L 500 - Items: 1 - Cliente ID: 2', '216.234.223.124', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-12 18:27:48'),
(22, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:29:37'),
(23, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 18:30:37'),
(24, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:29:43'),
(25, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:33:04'),
(26, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: ventas', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:33:10'),
(27, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:33:18'),
(28, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:34:00'),
(29, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:34:08'),
(30, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:34:23'),
(31, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:37:51'),
(32, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:47:24'),
(33, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 20:47:34'),
(34, 1, 'admin', 'UPDATE', 'products', 4, 'Producto actualizado: Metro - Stock: 20', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:20:02'),
(35, 1, 'admin', 'CREATE', 'products', 5, 'Producto creado: Clavos Galvanizados - Código: 005 - Precio: L 5 - Stock: 25000', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:20:44'),
(36, 1, 'admin', 'UPDATE', 'products', 5, 'Producto actualizado: Clavos Galvanizados - Stock: 25000', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:27:02'),
(37, 1, 'admin', 'UPDATE', 'products', 5, 'Producto actualizado: Clavos Galvanizados - Stock: 25000', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:27:16'),
(38, 1, 'admin', 'CREATE', 'sales', 7, 'Venta registrada - Factura: INV-20260612-139 - Total: L 1850 - Items: 2 - Cliente ID: 2', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:30:04'),
(39, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:30:27'),
(40, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:30:33'),
(41, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:34:09'),
(42, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:34:44'),
(43, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:38:10'),
(44, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:38:18'),
(45, 1, 'admin', 'UPDATE', 'products', 5, 'Producto actualizado: Clavos Galvanizados - Costo: L 2.50 - Precio: L 5.00 - Ganancia: L 2.5', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:52:37'),
(46, 1, 'admin', 'CREATE', 'sales', 8, 'Venta registrada - Factura: INV-20260612-548 - Total: L 500 - Ganancia: L 250 - Items: 1', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:56:37'),
(47, 1, 'admin', 'UPDATE', 'products', 3, 'Producto actualizado: Bloque de Cemento 8\" - Costo: L 0.00 - Precio: L 24.00 - Ganancia: L 24', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:05:34'),
(48, 1, 'admin', 'CREATE', 'clients', 3, 'Cliente creado: Cliente Generico - Email: abc@hotmail.com - Teléfono: 0000-0000', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:08:46'),
(49, 1, 'admin', 'DELETE', 'clients', 3, 'Cliente eliminado: Cliente Generico', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:17:47'),
(50, 1, 'admin', 'CREATE', 'sales', 9, 'Venta registrada - Factura: INV-20260612-303 - Total: L 10100 - Ganancia: L 5100 - Items: 2', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:18:14'),
(51, 1, 'admin', 'CREATE', 'sales', 10, 'Venta registrada - Factura: INV-20260612-230 - Total: L 1300 - Ganancia: L 1250 - Items: 2', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:19:13'),
(52, 1, 'admin', 'CREATE', 'sales', 11, 'Venta registrada - Factura: INV-20260612-950 - Total: L 2650 - Ganancia: L 2525 - Items: 2', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:26:53'),
(53, 1, 'admin', 'CREATE', 'sales', 12, 'Venta registrada - Factura: INV-20260612-719 - Total: L 1096 - Ganancia: L 596 - Items: 2', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:29:08'),
(54, 1, 'admin', 'UPDATE', 'products', 4, 'Producto actualizado: Metro - Costo: L 25 - Precio: L 100.00 - Ganancia: L 75', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:35:46'),
(55, 1, 'admin', 'CREATE', 'sales', 13, 'Venta registrada - Factura: INV-20260612-107 - Total: L 3600 - Ganancia: L 3050 - Items: 3', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:36:14'),
(56, 1, 'admin', 'CREATE', 'sales', 14, 'Venta registrada - Factura: INV-20260612-680 - Total: L 558 - Ganancia: L 403 - Items: 3', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:41:04'),
(57, 1, 'admin', 'CREATE', 'sales', 15, 'Venta registrada - Factura: INV-20260612-549 - Total: L 940 - Ganancia: L 640 - Items: 3', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:44:05'),
(58, 1, 'admin', 'CREATE', 'sales', 16, 'Venta registrada - Factura: INV-20260612-785 - Total: L 1074 - Ganancia: L 799 - Items: 3', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:47:40'),
(59, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.233.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:48:03'),
(60, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '181.115.64.76', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-12 23:16:15'),
(61, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:00:02'),
(62, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:00:33'),
(63, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:00:40'),
(64, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:00:53'),
(65, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:01:03'),
(66, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:01:16'),
(67, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:02:17'),
(68, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:02:22'),
(69, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:02:45'),
(70, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Jorge Admin', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:02:50'),
(71, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Jorge Admin', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:02:55'),
(72, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:03:02'),
(73, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:03:35'),
(74, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:03:39'),
(75, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:05:01'),
(76, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:05:06'),
(77, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:05:23'),
(78, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:05:34'),
(79, 2, 'vendedor', 'CREATE', 'clients', 4, 'Cliente creado: arroz con pollo - Email: polloac@gmail.com - Teléfono: 12330214', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:06:29'),
(80, 2, 'vendedor', 'CREATE', 'sales', 17, 'Venta registrada - Factura: INV-20260612-529 - Total: L 600 - Ganancia: L 600 - Items: 1', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:06:52'),
(81, 2, 'vendedor', 'CREATE', 'sales', 18, 'Venta registrada - Factura: INV-20260612-787 - Total: L 125 - Ganancia: L 62.5 - Items: 1', '216.234.223.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-13 02:07:13'),
(82, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:07:16'),
(83, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:09:37'),
(84, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:09:44'),
(85, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', '2026-06-13 02:10:29'),
(86, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:42:30'),
(87, NULL, 'Sistema', 'LOGIN_FALLIDO', 'users', 1, 'Intento fallido para usuario: admin', '201.220.128.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 02:43:12'),
(88, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '201.220.128.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 02:43:26'),
(89, 1, 'admin', 'UPDATE', 'users', 7, 'Usuario editado: admincarlos - Nuevo rol: superadmin', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:44:57'),
(90, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:45:23'),
(91, NULL, 'Sistema', 'LOGIN_FALLIDO', 'users', 4, 'Intento fallido para usuario: inventario', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:45:38'),
(92, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:45:49'),
(93, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:47:08'),
(94, 7, 'admincarlos', 'LOGIN_EXITOSO', 'users', 7, 'Usuario admincarlos inició sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:47:24'),
(95, 7, 'admincarlos', 'LOGOUT', 'users', 7, 'Usuario admincarlos cerró sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-15 02:49:07'),
(96, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '201.220.128.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 02:57:51'),
(97, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 19:30:05'),
(98, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 19:53:12'),
(99, NULL, 'Sistema', 'LOGIN_FALLIDO', 'users', 2, 'Intento fallido para usuario: vendedor', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 19:53:23'),
(100, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 19:53:35'),
(101, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:09:16'),
(102, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:09:23'),
(103, 4, 'inventario', 'UPDATE', 'products', 2, 'Producto actualizado: Lamina de Zinc - Costo: L 0.00 - Precio: L 278.00 - Ganancia: L 278', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:10:35'),
(104, 4, 'inventario', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 0.00 - Precio: L 2000.00 - Ganancia: L 2000', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:11:34'),
(105, 4, 'inventario', 'UPDATE', 'products', 2, 'Producto actualizado: Lamina de Zinc - Costo: L 0.00 - Precio: L 278.00 - Ganancia: L 278', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:11:45'),
(106, 4, 'inventario', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 0.00 - Precio: L 2000.00 - Ganancia: L 2000', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:11:57'),
(107, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:12:13'),
(108, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:12:31'),
(109, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:16:11'),
(110, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: carlosadmin', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:16:19'),
(111, 7, 'admincarlos', 'LOGIN_EXITOSO', 'users', 7, 'Usuario admincarlos inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:16:28'),
(112, 7, 'admincarlos', 'LOGOUT', 'users', 7, 'Usuario admincarlos cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:16:41'),
(113, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:20:44'),
(114, 4, 'inventario', 'UPDATE', 'products', 2, 'Producto actualizado: Lamina de Zinc - Costo: L 0.00 - Precio: L 278.00 - Ganancia: L 278', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 20:28:33'),
(115, 4, 'inventario', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 0.00 - Precio: L 2000.00 - Ganancia: L 2000', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:07:21'),
(116, 4, 'inventario', 'UPDATE', 'products', 4, 'Producto actualizado: Metro - Costo: L 25.00 - Precio: L 100.00 - Ganancia: L 75', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:16:54'),
(117, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 21:32:44'),
(118, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 21:33:24'),
(119, 7, 'admincarlos', 'LOGIN_EXITOSO', 'users', 7, 'Usuario admincarlos inició sesión', '216.234.223.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 21:33:29'),
(120, 7, 'admincarlos', 'LOGOUT', 'users', 7, 'Usuario admincarlos cerró sesión', '216.234.223.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 21:33:52'),
(121, 6, 'jefe', 'LOGIN_EXITOSO', 'users', 6, 'Usuario jefe inició sesión', '216.234.223.146', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 21:33:56'),
(122, 4, 'inventario', 'DELETE', 'products', 6, 'Producto eliminado: Pintuas (Código: 006)', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:34:25'),
(123, 4, 'inventario', 'CREATE', 'products', 7, 'Producto creado: Pinturas - Código: 006 - Costo: L 325 - Precio: L 675 - Ganancia: L 350', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:34:53'),
(124, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:36:35'),
(125, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: ventas', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:37:08'),
(126, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:37:17'),
(127, 2, 'vendedor', 'CREATE', 'sales', 19, 'Venta registrada - Factura: INV-20260615-982 - Total: L 1450 - Ganancia: L 775 - Items: 2', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:37:41'),
(128, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:37:48'),
(129, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:37:55'),
(130, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:38:18'),
(131, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:38:25'),
(132, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-06-15 21:39:16'),
(133, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:37:30'),
(134, 1, 'admin', 'CREATE', 'suppliers', 3, 'Proveedor creado: Alibaba - Contacto: Fernando Suarez - Teléfono: +504 9999-9999', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:50:23'),
(135, 1, 'admin', 'CREATE', 'sales', 20, 'Venta registrada - Factura: INV-20260615-503 - Total: L 6000 - Ganancia: L 6000 - Items: 1', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:51:31'),
(136, 1, 'admin', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 0.00 - Precio: L 2000.00 - Ganancia: L 2000', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:52:38'),
(137, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:55:49'),
(138, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:55:55'),
(139, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:57:44'),
(140, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:57:52'),
(141, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:58:00'),
(142, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:58:09'),
(143, 4, 'inventario', 'UPDATE', 'products', 3, 'Producto actualizado: Bloque de Cemento 8\" - Costo: L 7 - Precio: L 24.00 - Ganancia: L 17', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:58:58'),
(144, 4, 'inventario', 'UPDATE', 'products', 2, 'Producto actualizado: Lamina de Zinc - Costo: L 95 - Precio: L 278.00 - Ganancia: L 183', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:59:14'),
(145, 4, 'inventario', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 650 - Precio: L 2000.00 - Ganancia: L 1350', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 22:59:39'),
(146, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 23:01:54'),
(147, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 23:02:00'),
(148, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 23:24:35'),
(149, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 23:40:08'),
(150, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-15 23:40:15'),
(151, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 00:05:34'),
(152, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '181.115.64.192', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:43:50'),
(153, 1, 'admin', 'UPDATE', 'products', 1, 'Producto actualizado: Tubo Galvanizado - Costo: L 650.00 - Precio: L 2000.00 - Ganancia: L 1350', '181.115.64.192', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:45:51'),
(154, 1, 'admin', 'CREATE', 'sales', 21, 'Venta registrada - Factura: INV-20260615-327 - Total: L 18400 - Ganancia: L 11400 - Items: 2', '181.115.64.192', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:48:27'),
(155, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '181.115.63.128', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:53:44'),
(156, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '181.115.64.192', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:54:01'),
(157, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '181.115.64.192', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-16 01:55:25'),
(158, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:25:33'),
(159, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:27:58'),
(160, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:28:06'),
(161, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:35:21'),
(162, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:35:30'),
(163, 2, 'vendedor', 'CREATE', 'sales', 22, 'Venta registrada - Factura: INV-20260616-420 - Total: L 675 - Ganancia: L 350 - Items: 1', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:36:09'),
(164, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '200.107.232.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 17:37:03'),
(165, 6, 'jefe', 'LOGOUT', 'users', 6, 'Usuario jefe cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:22:49'),
(166, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:22:55'),
(167, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:24:01'),
(168, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:24:03'),
(169, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:34:40'),
(170, 6, 'jefe', 'LOGIN_EXITOSO', 'users', 6, 'Usuario jefe inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:34:48'),
(171, 6, 'jefe', 'LOGOUT', 'users', 6, 'Usuario jefe cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:35:29'),
(172, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:35:35'),
(173, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:36:54'),
(174, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:36:57'),
(175, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:37:17'),
(176, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 19:37:23'),
(177, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 20:03:45'),
(178, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 20:03:52'),
(179, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 20:11:30'),
(180, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-16 20:11:31'),
(181, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:02:29'),
(182, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:08:56'),
(183, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:09:16'),
(184, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:15:10'),
(185, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:15:16'),
(186, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:15:54'),
(187, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:16:00'),
(188, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:21:02'),
(189, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:21:11'),
(190, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:21:32'),
(191, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:21:42'),
(192, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:28:45'),
(193, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:28:59'),
(194, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:31:03'),
(195, 6, 'jefe', 'LOGIN_EXITOSO', 'users', 6, 'Usuario jefe inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:31:13');
INSERT INTO `system_logs` (`id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(196, 6, 'jefe', 'LOGOUT', 'users', 6, 'Usuario jefe cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:31:41'),
(197, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:31:49'),
(198, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:37:29'),
(199, 6, 'jefe', 'LOGIN_EXITOSO', 'users', 6, 'Usuario jefe inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:37:35'),
(200, 6, 'jefe', 'LOGOUT', 'users', 6, 'Usuario jefe cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:38:10'),
(201, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:38:18'),
(202, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:38:34'),
(203, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:46:02'),
(204, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:46:21'),
(205, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:46:33'),
(206, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.172.42.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 01:46:54'),
(207, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.223', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-06-17 03:37:20'),
(208, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:38:07'),
(209, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:38:41'),
(210, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:38:47'),
(211, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:39:14'),
(212, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:39:19'),
(213, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-06-17 03:39:29'),
(214, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '216.234.223.223', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-06-17 03:40:01'),
(215, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '216.234.223.223', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-06-17 03:41:21'),
(216, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '216.234.223.223', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-06-17 03:41:46'),
(217, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:45:00'),
(218, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:45:07'),
(219, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:45:26'),
(220, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:45:34'),
(221, 1, 'admin', 'CREATE', 'users', 8, 'Usuario creado: Jaime - Rol: seller', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:55:44'),
(222, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:55:53'),
(223, 8, 'Jaime', 'LOGIN_EXITOSO', 'users', 8, 'Usuario Jaime inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:56:01'),
(224, 8, 'Jaime', 'LOGOUT', 'users', 8, 'Usuario Jaime cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:56:48'),
(225, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 03:57:04'),
(226, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 04:08:49'),
(227, 6, 'jefe', 'LOGIN_EXITOSO', 'users', 6, 'Usuario jefe inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 04:08:56'),
(228, 6, 'jefe', 'LOGOUT', 'users', 6, 'Usuario jefe cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 04:09:40'),
(229, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 04:09:47'),
(230, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 04:17:15'),
(231, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:13:10'),
(232, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:14:02'),
(233, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:16:52'),
(234, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:18:44'),
(235, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:18:51'),
(236, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:19:25'),
(237, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Admin', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:24:33'),
(238, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:25:00'),
(239, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:25:15'),
(240, NULL, 'Sistema', 'LOGIN_FALLIDO', 'users', 1, 'Intento fallido para usuario: admin', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:29:17'),
(241, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:29:23'),
(242, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:29:49'),
(243, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:54:49'),
(244, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:55:12'),
(245, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:55:19'),
(246, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:56:03'),
(247, 4, 'inventario', 'LOGIN_EXITOSO', 'users', 4, 'Usuario inventario inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:56:09'),
(248, 4, 'inventario', 'LOGOUT', 'users', 4, 'Usuario inventario cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:57:29'),
(249, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:57:36'),
(250, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:57:44'),
(251, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 05:57:51'),
(252, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 06:03:51'),
(253, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 06:03:58'),
(254, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 06:05:01'),
(255, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-17 14:06:13'),
(256, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '38.7.24.206', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-17 14:07:39'),
(257, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:15:13'),
(258, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:17:01'),
(259, 3, 'finanzas', 'LOGIN_EXITOSO', 'users', 3, 'Usuario finanzas inició sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:17:09'),
(260, 3, 'finanzas', 'LOGOUT', 'users', 3, 'Usuario finanzas cerró sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:22:20'),
(261, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:22:26'),
(262, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:53:14'),
(263, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '216.234.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 17:53:23'),
(264, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '216.234.223.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 20:41:26'),
(265, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '216.234.223.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-17 20:41:30'),
(266, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:08:23'),
(267, 1, 'admin', 'UPDATE', 'products', 3, 'Producto actualizado: Bloque de Cemento 8\" - Costo: L 7.00 - Precio: L 24.00 - Ganancia: L 17', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:16:53'),
(268, 1, 'admin', 'LOGOUT', 'users', 1, 'Usuario admin cerró sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:17:34'),
(269, 5, 'cajero', 'LOGIN_EXITOSO', 'users', 5, 'Usuario cajero inició sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:17:45'),
(270, 5, 'cajero', 'LOGOUT', 'users', 5, 'Usuario cajero cerró sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:22:21'),
(271, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Vendedor', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:22:43'),
(272, 2, 'vendedor', 'LOGIN_EXITOSO', 'users', 2, 'Usuario vendedor inició sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:23:04'),
(273, 2, 'vendedor', 'LOGOUT', 'users', 2, 'Usuario vendedor cerró sesión', '181.115.5.105', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 01:29:46'),
(274, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Admin', '190.4.8.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 18:47:21'),
(275, NULL, 'Sistema', 'LOGIN_FALLIDO', NULL, NULL, 'Intento fallido - Usuario no existe: Admin', '190.4.8.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 18:47:28'),
(276, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '190.4.8.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 18:47:42'),
(277, NULL, 'Sistema', 'LOGIN_FALLIDO', 'users', 1, 'Intento fallido para usuario: admin', '138.204.181.196', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 21:33:24'),
(278, 1, 'admin', 'LOGIN_EXITOSO', 'users', 1, 'Usuario admin inició sesión', '138.204.181.196', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1', '2026-06-18 21:33:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `role` enum('admin','seller','superadmin','finance','inventory','cashier') DEFAULT 'seller',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$xwkZqyodirbSo4gIMfu7QOzExDd/jzosEQR/WqLN9hvYsBxYSo7i6', 'Administrador', 'superadmin', '2026-06-11 05:55:12'),
(2, 'vendedor', '$2y$10$xwkZqyodirbSo4gIMfu7QOzExDd/jzosEQR/WqLN9hvYsBxYSo7i6', 'Juan Vendedor', 'seller', '2026-06-12 16:04:11'),
(3, 'finanzas', '$2y$10$xwkZqyodirbSo4gIMfu7QOzExDd/jzosEQR/WqLN9hvYsBxYSo7i6', 'Carlos Finanzas', 'finance', '2026-06-12 16:04:11'),
(4, 'inventario', '$2y$10$xwkZqyodirbSo4gIMfu7QOzExDd/jzosEQR/WqLN9hvYsBxYSo7i6', 'Ana Inventario', 'inventory', '2026-06-12 16:04:11'),
(5, 'cajero', '$2y$10$xwkZqyodirbSo4gIMfu7QOzExDd/jzosEQR/WqLN9hvYsBxYSo7i6', 'Roberto Cajero', 'cashier', '2026-06-12 16:04:11'),
(6, 'jefe', '$2y$10$mvmOw5GzyPJftx.QsqQdTesHpMcaeRmhXE5neFoWCw3WycA.TKQIK', 'Jorge Admin', 'admin', '2026-06-12 16:55:05'),
(7, 'admincarlos', '$2y$10$K/QEduHYJQY8oU93MBVAKedbMWQCUn2WW2vGg3FV3cWfLvYHehWh2', 'Carlos Cardenas Admin', 'superadmin', '2026-06-12 17:08:25'),
(8, 'Jaime', '$2y$10$UH/Ayy6zMZYbLOHVJvxxD.DM0wLrhXXPDYyoSVM9ypeEvv5j7ZPRe', 'Jaime Espinoza', 'seller', '2026-06-17 03:55:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `preferencias_usuario`
--
ALTER TABLE `preferencias_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_pref` (`user_id`,`clave`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_username` (`username`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `preferencias_usuario`
--
ALTER TABLE `preferencias_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
