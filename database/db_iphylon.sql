-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 22, 2026 at 09:55 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_iphylon`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jo_spk`
--

CREATE TABLE `tbl_jo_spk` (
  `id_jo_spk` int(11) NOT NULL,
  `no_dokumen` varchar(100) DEFAULT NULL,
  `revisi` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `mesin` varchar(50) DEFAULT NULL,
  `injector` varchar(50) DEFAULT NULL,
  `line_produksi` varchar(50) DEFAULT NULL,
  `tanggal_spk` date DEFAULT NULL,
  `nama_spk` varchar(255) DEFAULT NULL,
  `no_jo` varchar(100) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `file_excel` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_jo_spk`
--

INSERT INTO `tbl_jo_spk` (`id_jo_spk`, `no_dokumen`, `revisi`, `item`, `mesin`, `injector`, `line_produksi`, `tanggal_spk`, `nama_spk`, `no_jo`, `tanggal_upload`, `uploaded_by`, `file_excel`, `created_at`) VALUES
(26, 'FORM/SHP-01/02', 1, 'NIKE COURT LITE 4 HC', '1', '2', '1', '2026-05-24', 'SPK LOW CARBON MATERIAL (LC)', 'JO-20260522091500', '2026-05-22 09:15:00', 'Ciko', '1779416100_template_spk_planning (14).xlsx', '2026-05-22 02:15:00'),
(29, 'FORM/SHP-01/02', 1, 'NIKE COURT LITE 4 HC', '1', '2', '1', '2026-05-24', 'SPK LOW CARBON MATERIAL (LC)', 'JO-20260522133831', '2026-05-22 13:38:31', 'Ciko', '1779431911_template_spk_planning (14).xlsx', '2026-05-22 06:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_master_barcode`
--

CREATE TABLE `tbl_master_barcode` (
  `id_barcode` int(11) NOT NULL,
  `id_size_qty` int(11) DEFAULT NULL,
  `qr_code` varchar(50) NOT NULL,
  `bucket` varchar(11) NOT NULL,
  `no_jo` varchar(100) DEFAULT NULL,
  `po` varchar(50) NOT NULL,
  `po_item` varchar(10) NOT NULL,
  `style` varchar(10) NOT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `colour` varchar(100) DEFAULT NULL,
  `item` varchar(100) NOT NULL,
  `line` varchar(100) DEFAULT NULL,
  `size` varchar(10) NOT NULL,
  `qty` varchar(10) NOT NULL,
  `status_scan` varchar(50) DEFAULT 'NO',
  `status_print` varchar(50) DEFAULT 'NO',
  `last_update` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `scan_time` datetime DEFAULT NULL,
  `scan_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_master_barcode`
--

INSERT INTO `tbl_master_barcode` (`id_barcode`, `id_size_qty`, `qr_code`, `bucket`, `no_jo`, `po`, `po_item`, `style`, `gender`, `colour`, `item`, `line`, `size`, `qty`, `status_scan`, `status_print`, `last_update`, `updated_by`, `created_at`, `scan_time`, `scan_by`) VALUES
(1247483, 115, '6a1002ec37e92-05e8784c', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247484, 115, '6a1002ec37f64-02e279bc', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247485, 115, '6a1002ec37fff-90e70c2e', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247486, 115, '6a1002ec380d1-df5c41bc', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247487, 115, '6a1002ec38148-e6d41758', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247488, 116, '6a1002ec381b4-b27c221a', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247489, 116, '6a1002ec38252-f91ba538', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247490, 116, '6a1002ec382c7-2fae7d21', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247491, 116, '6a1002ec38343-29f665fe', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247492, 116, '6a1002ec383df-c9d9f116', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '1T', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247493, 117, '6a1002ec3845e-c3a946b4', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247494, 117, '6a1002ec384cf-1deb1a8f', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247495, 117, '6a1002ec38559-759f95c6', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247496, 117, '6a1002ec385b9-363f903f', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247497, 117, '6a1002ec38629-732907f9', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247498, 118, '6a1002ec3868d-0db3f9f6', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247499, 118, '6a1002ec386fc-aad36a88', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247500, 118, '6a1002ec3879a-f3cdc422', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247501, 118, '6a1002ec387f6-d8c17af5', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247502, 118, '6a1002ec38852-86339823', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '2T', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247503, 119, '6a1002ec388b1-a6b574a8', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247504, 119, '6a1002ec3890a-17328455', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247505, 119, '6a1002ec38979-cbbefb09', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247506, 119, '6a1002ec389eb-4ca83b8b', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247507, 119, '6a1002ec38a6c-9bb6dbb5', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247508, 120, '6a1002ec38ad8-3eeb1341', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247509, 120, '6a1002ec38b3c-966353a7', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247510, 120, '6a1002ec38bac-aa46863d', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247511, 120, '6a1002ec38c1d-3eab581a', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247512, 120, '6a1002ec38c7c-c7cd626e', '260518', 'JO-20260522133831', '6204461204', '200', 'FD6574-111', 'MAN', 'WHITE ', 'NIKE COURT LITE 4 HC', '1', '3T', '2', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247513, 121, '6a1002ec38d2e-3a1f6ed4', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '1', '10', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247514, 122, '6a1002ec38dd6-d87138d8', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247515, 122, '6a1002ec38e3c-c81b5b5f', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '1T', '8', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247516, 123, '6a1002ec38ef5-000205c6', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '2', '10', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247517, 124, '6a1002ec38f91-eec75fa3', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247518, 124, '6a1002ec3904f-c55978a6', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '2T', '8', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247519, 125, '6a1002ec3912c-0517f875', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '3', '10', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247520, 126, '6a1002ec391f9-a42164bb', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '3T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247521, 126, '6a1002ec3929b-be34d6b2', '260525', 'JO-20260522133831', '6204461204', '300', 'FD6574-001', 'WOMAN', 'BLACK', 'NIKE COURT LITE 4 HC', '1', '3T', '8', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247522, 127, '6a1002ec3939a-0ef58bfe', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '1', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247523, 127, '6a1002ec39473-b0dea45d', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '1', '8', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247524, 128, '6a1002ec39522-1c7e4c20', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '1T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247525, 128, '6a1002ec395c6-e332e965', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '1T', '8', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247526, 129, '6a1002ec39648-63968128', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2', '10', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247527, 130, '6a1002ec396c7-358c559c', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247528, 130, '6a1002ec3972d-85d21788', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247529, 130, '6a1002ec39799-be96968d', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247530, 130, '6a1002ec39813-c96e0e11', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247531, 130, '6a1002ec3989c-4d6609a1', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247532, 130, '6a1002ec39905-1d44beb3', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247533, 130, '6a1002ec3995e-9a223719', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247534, 130, '6a1002ec399d3-1948122f', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '12', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL),
(1247535, 130, '6a1002ec39a27-b2995a5b', '260525', 'JO-20260522133831', '6204461204', '400', 'FD6574-002', 'GS', 'RED', 'NIKE COURT LITE 4 HC', '1', '2T', '4', 'NO', 'NO', '2026-05-22 14:17:00', 'Ciko', '2026-05-22 07:17:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_master_time`
--

CREATE TABLE `tbl_master_time` (
  `id_time` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `hour` varchar(100) NOT NULL,
  `shift` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_spk_detail`
--

CREATE TABLE `tbl_spk_detail` (
  `id_detail` int(11) NOT NULL,
  `id_jo_spk` int(11) DEFAULT NULL,
  `style` varchar(100) DEFAULT NULL,
  `colour` varchar(50) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `bucket` varchar(20) DEFAULT NULL,
  `po` varchar(100) DEFAULT NULL,
  `po_item` int(11) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `total_order` int(11) DEFAULT NULL,
  `total_qty` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_spk_detail`
--

INSERT INTO `tbl_spk_detail` (`id_detail`, `id_jo_spk`, `style`, `colour`, `gender`, `bucket`, `po`, `po_item`, `country`, `total_order`, `total_qty`, `created_at`) VALUES
(48, 26, 'FD6574-111', 'WHITE ', 'MAN', '260518', '6204461204', 200, 'Belgium', 507, 300, '2026-05-22 02:15:00'),
(49, 26, 'FD6574-001', 'BLACK', 'WOMAN', '260525', '6204461204', 300, 'Denmark', 1000, 90, '2026-05-22 02:15:00'),
(50, 26, 'FD6574-002', 'RED', 'GS', '260525', '6204461204', 400, 'Japan', 2000, 150, '2026-05-22 02:15:00'),
(55, 29, 'FD6574-111', 'WHITE ', 'MAN', '260518', '6204461204', 200, 'Belgium', 507, 300, '2026-05-22 06:38:31'),
(56, 29, 'FD6574-001', 'BLACK', 'WOMAN', '260525', '6204461204', 300, 'Denmark', 1000, 90, '2026-05-22 06:38:31'),
(57, 29, 'FD6574-002', 'RED', 'GS', '260525', '6204461204', 400, 'Japan', 2000, 150, '2026-05-22 06:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_spk_size_qty`
--

CREATE TABLE `tbl_spk_size_qty` (
  `id_size_qty` int(11) NOT NULL,
  `id_detail` int(11) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_spk_size_qty`
--

INSERT INTO `tbl_spk_size_qty` (`id_size_qty`, `id_detail`, `size`, `qty`, `created_at`) VALUES
(85, 48, '1', 50, '2026-05-22 02:15:00'),
(86, 48, '1T', 50, '2026-05-22 02:15:00'),
(87, 48, '2', 50, '2026-05-22 02:15:00'),
(88, 48, '2T', 50, '2026-05-22 02:15:00'),
(89, 48, '3', 50, '2026-05-22 02:15:00'),
(90, 48, '3T', 50, '2026-05-22 02:15:00'),
(91, 49, '1', 10, '2026-05-22 02:15:00'),
(92, 49, '1T', 20, '2026-05-22 02:15:00'),
(93, 49, '2', 10, '2026-05-22 02:15:00'),
(94, 49, '2T', 20, '2026-05-22 02:15:00'),
(95, 49, '3', 10, '2026-05-22 02:15:00'),
(96, 49, '3T', 20, '2026-05-22 02:15:00'),
(97, 50, '1', 20, '2026-05-22 02:15:00'),
(98, 50, '1T', 20, '2026-05-22 02:15:00'),
(99, 50, '2', 10, '2026-05-22 02:15:00'),
(100, 50, '2T', 100, '2026-05-22 02:15:00'),
(115, 55, '1', 50, '2026-05-22 06:38:31'),
(116, 55, '1T', 50, '2026-05-22 06:38:31'),
(117, 55, '2', 50, '2026-05-22 06:38:31'),
(118, 55, '2T', 50, '2026-05-22 06:38:31'),
(119, 55, '3', 50, '2026-05-22 06:38:31'),
(120, 55, '3T', 50, '2026-05-22 06:38:31'),
(121, 56, '1', 10, '2026-05-22 06:38:31'),
(122, 56, '1T', 20, '2026-05-22 06:38:31'),
(123, 56, '2', 10, '2026-05-22 06:38:31'),
(124, 56, '2T', 20, '2026-05-22 06:38:31'),
(125, 56, '3', 10, '2026-05-22 06:38:31'),
(126, 56, '3T', 20, '2026-05-22 06:38:31'),
(127, 57, '1', 20, '2026-05-22 06:38:31'),
(128, 57, '1T', 20, '2026-05-22 06:38:31'),
(129, 57, '2', 10, '2026-05-22 06:38:31'),
(130, 57, '2T', 100, '2026-05-22 06:38:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction_scan`
--

CREATE TABLE `tbl_transaction_scan` (
  `id_transac` int(11) NOT NULL,
  `qr_code` varchar(100) NOT NULL,
  `date_transaction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type_scan` varchar(100) NOT NULL,
  `hour_scan` varchar(11) NOT NULL,
  `shift` varchar(11) NOT NULL,
  `nik` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `cost_center` varchar(100) NOT NULL,
  `date_scan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaction_scan`
--

INSERT INTO `tbl_transaction_scan` (`id_transac`, `qr_code`, `date_transaction`, `type_scan`, `hour_scan`, `shift`, `nik`, `username`, `cost_center`, `date_scan`) VALUES
(3250486, '6a1002ec37e92-05e8784c', '2026-05-22 07:48:47', 'IN_SM', '', '', '1410422', 'Ciko', 'Line 1', '2026-05-22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transprod`
--
-- Error reading structure for table db_iphylon.tbl_transprod: #1932 - Table 'db_iphylon.tbl_transprod' doesn't exist in engine
-- Error reading data for table db_iphylon.tbl_transprod: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `db_iphylon`.`tbl_transprod`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nik` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `authorize` varchar(100) NOT NULL,
  `scan_type` varchar(100) NOT NULL,
  `cost_center` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `username`, `nik`, `password`, `authorize`, `scan_type`, `cost_center`) VALUES
(1, 'Ciko', '1410422', '123456', 'Admin', 'IN_SM', 'Line 1'),
(122, 'User Out Packing', '789456', '789456', 'User', 'OUT_PACKING', 'Line 8'),
(123, 'User IN SM', '222222', '123456', 'User', 'IN_SM', 'Line 8'),
(124, 'User Out SM', '333333', '123456', 'User', 'OUT_SM', 'Line 8');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_jo_spk`
--
ALTER TABLE `tbl_jo_spk`
  ADD PRIMARY KEY (`id_jo_spk`);

--
-- Indexes for table `tbl_master_barcode`
--
ALTER TABLE `tbl_master_barcode`
  ADD PRIMARY KEY (`id_barcode`),
  ADD KEY `qr_code` (`qr_code`),
  ADD KEY `idx_master_qrcode` (`qr_code`),
  ADD KEY `idx_master_po_size` (`po`,`po_item`,`size`);

--
-- Indexes for table `tbl_master_time`
--
ALTER TABLE `tbl_master_time`
  ADD PRIMARY KEY (`id_time`);

--
-- Indexes for table `tbl_spk_detail`
--
ALTER TABLE `tbl_spk_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_jo_spk` (`id_jo_spk`);

--
-- Indexes for table `tbl_spk_size_qty`
--
ALTER TABLE `tbl_spk_size_qty`
  ADD PRIMARY KEY (`id_size_qty`),
  ADD KEY `id_detail` (`id_detail`);

--
-- Indexes for table `tbl_transaction_scan`
--
ALTER TABLE `tbl_transaction_scan`
  ADD PRIMARY KEY (`id_transac`),
  ADD KEY `type_scan` (`type_scan`),
  ADD KEY `idx_join` (`qr_code`,`type_scan`,`date_transaction`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `nik` (`nik`),
  ADD KEY `password` (`password`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_jo_spk`
--
ALTER TABLE `tbl_jo_spk`
  MODIFY `id_jo_spk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tbl_master_barcode`
--
ALTER TABLE `tbl_master_barcode`
  MODIFY `id_barcode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1247536;

--
-- AUTO_INCREMENT for table `tbl_master_time`
--
ALTER TABLE `tbl_master_time`
  MODIFY `id_time` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13189;

--
-- AUTO_INCREMENT for table `tbl_spk_detail`
--
ALTER TABLE `tbl_spk_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tbl_spk_size_qty`
--
ALTER TABLE `tbl_spk_size_qty`
  MODIFY `id_size_qty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `tbl_transaction_scan`
--
ALTER TABLE `tbl_transaction_scan`
  MODIFY `id_transac` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3250487;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_spk_detail`
--
ALTER TABLE `tbl_spk_detail`
  ADD CONSTRAINT `tbl_spk_detail_ibfk_1` FOREIGN KEY (`id_jo_spk`) REFERENCES `tbl_jo_spk` (`id_jo_spk`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_spk_size_qty`
--
ALTER TABLE `tbl_spk_size_qty`
  ADD CONSTRAINT `tbl_spk_size_qty_ibfk_1` FOREIGN KEY (`id_detail`) REFERENCES `tbl_spk_detail` (`id_detail`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
