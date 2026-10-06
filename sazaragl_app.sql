-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 07:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sazaragl_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_id` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `excerpt_id` text DEFAULT NULL,
  `body` longtext NOT NULL,
  `body_id` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'published',
  `published_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `title_id`, `slug`, `excerpt`, `excerpt_id`, `body`, `body_id`, `thumbnail`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Why Indonesian Palm Broom Is Gaining Global Demand', 'Mengapa Palm Broom Indonesia Semakin Diminati Pasar Global', 'why-indonesian-palm-broom-is-gaining-global-demand', 'Natural, durable, and sustainable: how palm broom became a rising export commodity.', 'Alami, tahan lama, dan berkelanjutan: bagaimana palm broom menjadi komoditas ekspor yang naik daun.', 'Indonesian palm broom is produced from natural palm fibers, making it a sustainable alternative to synthetic cleaning tools. Global buyers increasingly value its durability for household, commercial, agricultural, and outdoor applications.\n\nSazara Global Trade sources palm broom from trusted producers and coordinates quality, documentation, and shipment to ensure a reliable supply for international markets.', 'Palm broom Indonesia diproduksi dari serat palma alami, menjadikannya alternatif berkelanjutan bagi alat pembersih sintetis. Pembeli global semakin menghargai daya tahannya untuk aplikasi rumah tangga, komersial, pertanian, dan luar ruang.\n\nSazara Global Trade sourcing palm broom dari produsen terpercaya serta mengoordinasikan kualitas, dokumentasi, dan pengiriman guna menjamin pasokan yang andal bagi pasar internasional.', NULL, 'published', '2026-10-06', '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(2, 'A Practical Guide to Indonesian Coffee Origins and Grades', 'Panduan Praktis Origin dan Grade Kopi Indonesia', 'a-practical-guide-to-indonesian-coffee-origins-and-grades', 'Understanding origin, grade, and processing method before placing your first order.', 'Memahami origin, grade, dan metode proses sebelum menempatkan pesanan pertama Anda.', 'Indonesian coffee offers diverse profiles shaped by origin, altitude, and processing method. Buyers should define grade, moisture content, and cup quality expectations early in the negotiation.\n\nOur team assists buyers in matching specifications with the right origin, from Sumatra to Java and Sulawesi, ensuring transparency throughout the transaction.', 'Kopi Indonesia menawarkan profil rasa yang beragam, dibentuk oleh origin, ketinggian, dan metode proses. Pembeli perlu menetapkan grade, kadar air, dan harapan cup quality sejak awal negosiasi.\n\nTim kami mendampingi pembeli mencocokkan spesifikasi dengan origin yang tepat, dari Sumatra hingga Java dan Sulawesi, dengan transparansi sepanjang transaksi.', NULL, 'published', '2026-09-29', '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(3, 'From Source to Shipment: How Commodity Export Coordination Works', 'Dari Sumber ke Pengiriman: Bagaimana Koordinasi Ekspor Komoditas Bekerja', 'from-source-to-shipment-how-commodity-export-coordination-works', 'A look at our four-stage business flow: source, Sazara, export, global buyer.', 'Melihat alur bisnis empat tahap kami: source, Sazara, export, global buyer.', 'Successful commodity trade requires coordination across the supply chain: sourcing from producers and farmers, commercial negotiation, export documentation, quality coordination, and logistics.\n\nSazara\'s role is to ensure commercial requirements between suppliers and buyers are properly coordinated throughout the transaction, from first inquiry to final shipment.', 'Perdagangan komoditas yang sukses membutuhkan koordinasi di seluruh rantai pasok: sourcing dari produsen dan petani, negosiasi komersial, dokumentasi ekspor, koordinasi kualitas, hingga logistik.\n\nPeran Sazara adalah memastikan persyaratan komersial antara pemasok dan pembeli terkoordinasi dengan baik sepanjang transaksi, dari inquiry pertama hingga pengiriman akhir.', NULL, 'published', '2026-09-22', '2026-10-06 00:51:30', '2026-10-06 00:51:30');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `filename`, `original_name`, `mime_type`, `size`, `path`, `alt`, `created_at`, `updated_at`) VALUES
(1, 'areca-nut.jpg', 'areca-nut.jpg', 'image/jpeg', 98398, 'media/areca-nut.jpg', 'Areca Nut ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(2, 'cinnamon.jpg', 'cinnamon.jpg', 'image/jpeg', 117341, 'media/cinnamon.jpg', 'Cinnamon ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(3, 'clove.jpg', 'clove.jpg', 'image/jpeg', 117244, 'media/clove.jpg', 'Clove ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(4, 'coffee.jpg', 'coffee.jpg', 'image/jpeg', 106738, 'media/coffee.jpg', 'Coffee ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(5, 'crude-palm-oil-cpo.jpg', 'crude-palm-oil-cpo.jpg', 'image/jpeg', 114453, 'media/crude-palm-oil-cpo.jpg', 'Crude Palm Oil Cpo ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(6, 'other-commodities.jpg', 'other-commodities.jpg', 'image/jpeg', 105297, 'media/other-commodities.jpg', 'Other Commodities ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(7, 'palm-broom.jpg', 'palm-broom.jpg', 'image/jpeg', 100061, 'media/palm-broom.jpg', 'Palm Broom ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(8, 'vanilla.jpg', 'vanilla.jpg', 'image/jpeg', 137113, 'media/vanilla.jpg', 'Vanilla ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(9, 'a-practical-guide-to-indonesian-coffee-origins-and-grades.jpg', 'a-practical-guide-to-indonesian-coffee-origins-and-grades.jpg', 'image/jpeg', 106738, 'media/a-practical-guide-to-indonesian-coffee-origins-and-grades.jpg', 'A Practical Guide To Indonesian Coffee Origins And Grades ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(10, 'from-source-to-shipment-how-commodity-export-coordination-works.jpg', 'from-source-to-shipment-how-commodity-export-coordination-works.jpg', 'image/jpeg', 105297, 'media/from-source-to-shipment-how-commodity-export-coordination-works.jpg', 'From Source To Shipment How Commodity Export Coordination Works ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(11, 'why-indonesian-palm-broom-is-gaining-global-demand.jpg', 'why-indonesian-palm-broom-is-gaining-global-demand.jpg', 'image/jpeg', 100061, 'media/why-indonesian-palm-broom-is-gaining-global-demand.jpg', 'Why Indonesian Palm Broom Is Gaining Global Demand ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(12, 'afriansyah.jpg', 'afriansyah.jpg', 'image/jpeg', 56814, 'media/afriansyah.jpg', 'Afriansyah ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(13, 'salim.jpg', 'salim.jpg', 'image/jpeg', 51892, 'media/salim.jpg', 'Salim ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(14, 'zakki.jpg', 'zakki.jpg', 'image/jpeg', 48875, 'media/zakki.jpg', 'Zakki ', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(15, 'hero.jpg', 'hero.jpg', 'image/jpeg', 112992, 'media/hero.jpg', 'Sazara Hero', '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(16, 'logo.jpg', 'logo.jpg', 'image/jpeg', 85459, 'media/logo.jpg', 'Sazara Logo', '2026-10-06 00:51:31', '2026-10-06 00:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_products_table', 1),
(5, '2026_01_01_000002_create_articles_table', 1),
(6, '2026_01_02_000001_add_bilingual_columns', 1),
(7, '2026_10_01_000001_create_media_table', 1),
(8, '2026_10_01_000002_create_page_sections_table', 1),
(9, '2026_10_01_000003_add_role_to_users_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `page_sections`
--

CREATE TABLE `page_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page` varchar(60) NOT NULL,
  `section` varchar(80) NOT NULL,
  `field` varchar(80) NOT NULL,
  `value` longtext DEFAULT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_sections`
--

INSERT INTO `page_sections` (`id`, `page`, `section`, `field`, `value`, `media_id`, `created_at`, `updated_at`) VALUES
(1, 'home', 'hero', 'eyebrow', 'PT Sazara Global Trade · Medan, Indonesia', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(2, 'home', 'hero', 'title', 'Indonesian Commodities.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(3, 'home', 'hero', 'title2', 'Global Connections.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(4, 'home', 'hero', 'description', 'Built in Indonesia. Driven by Global Opportunity. We source, trade, and facilitate the supply of Indonesian commodities for domestic and international markets.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(5, 'home', 'hero', 'btn_text', 'View Products', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(6, 'home', 'hero', 'btn_url', '/products', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(7, 'home', 'flow', 'title', 'Our Business Flow', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(8, 'home', 'flow', 'subtitle', 'Sazara operates across the commodity supply chain, connecting source to market.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(9, 'home', 'why', 'title', 'Why Partner With Us?', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(10, 'home', 'cta', 'title', 'Let\'s Build Global Opportunities Together.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(11, 'home', 'cta', 'description', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(12, 'about', 'intro', 'heading', 'Connecting Indonesian Commodities with Global Markets', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(13, 'about', 'intro', 'paragraph1', 'Established on 5 August 2026, PT Sazara Global Trade is an Indonesian commodity trading and export company headquartered in Medan, North Sumatra, Indonesia. We focus on sourcing, trading, and facilitating the supply of Indonesian commodities for domestic and international markets.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(14, 'about', 'intro', 'paragraph2', 'Our commodity portfolio includes Palm Broom, Crude Palm Oil (CPO), Coffee, Cloves, Cinnamon, Vanilla, Areca Nut, and other commodities according to market demand and buyer specifications.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(15, 'about', 'intro', 'paragraph3', 'With a commitment to quality, reliability, and long-term partnership, Sazara Global Trade aims to become a trusted business partner connecting Indonesia\'s diverse commodity resources with opportunities in global markets.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(16, 'about', 'story', 'title', 'Our Story', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(17, 'about', 'story', 'paragraph1', 'Indonesia possesses abundant natural resources and a strong agricultural and plantation ecosystem.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(18, 'about', 'story', 'paragraph2', 'PT Sazara Global Trade was established to capture the opportunity by creating a professional bridge between Indonesian commodity suppliers and buyers in international markets.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(19, 'about', 'story', 'paragraph3', 'From sourcing and supplier coordination to commercial negotiation, documentation, and shipment coordination, we strive to provide a seamless trading experience for our business partners.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(20, 'about', 'story', 'ambition', 'Our ambition is simple:', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(21, 'about', 'story', 'tagline', 'Built in Indonesia. Driven by Global Opportunity.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(22, 'about', 'mission', 'vision_title', 'Our Vision', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(23, 'about', 'mission', 'vision', 'To become a trusted Indonesian commodity trading company connecting Indonesia\'s resources to global markets.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(24, 'about', 'mission', 'mission_title', 'Our Mission', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(25, 'about', 'mission', 'mission', 'To make Indonesian commodities more accessible to global buyers through reliable and professional trading partnerships.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(26, 'about', 'values', 'title', 'Our Core Values', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(27, 'about', 'cta', 'title', 'Let\'s Build Global Opportunities Together.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(28, 'about', 'cta', 'description', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(29, 'products', 'header', 'title', 'Our Commodities', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(30, 'products', 'header', 'subtitle', 'A diverse portfolio of Indonesian commodities, supplied according to market demand and buyer specifications.', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(31, 'articles', 'header', 'title', 'Articles & Insights', NULL, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(32, 'articles', 'header', 'subtitle', 'Market insights and practical guides on Indonesian commodity trade.', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(33, 'contact', 'header', 'title', 'Let\'s Build Global Opportunities Together.', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(34, 'contact', 'header', 'subtitle', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(35, 'contact', 'info', 'address', 'Medan, North Sumatra, Indonesia', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(36, 'contact', 'info', 'email', 'contact@sazaraglobal.com', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(37, 'contact', 'info', 'website', 'www.sazaraglobal.com', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(38, 'settings', 'general', 'whatsapp_number', '+62 812-6040-7208', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(39, 'settings', 'general', 'whatsapp_message', 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(40, 'settings', 'general', 'contact_person', 'Afriansyah Munar', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(41, 'settings', 'general', 'email', 'contact@sazaraglobal.com', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(42, 'settings', 'general', 'phone', '+62 812-6040-7208', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(43, 'settings', 'general', 'address', 'Medan, North Sumatra, Indonesia', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_id` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `description_id` text DEFAULT NULL,
  `specification` text DEFAULT NULL,
  `specification_id` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `wa_template` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `name_id`, `slug`, `description`, `description_id`, `specification`, `specification_id`, `image`, `wa_template`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Palm Broom', NULL, 'palm-broom', 'Natural palm-based cleaning products suitable for household, commercial, agricultural, and outdoor applications.', 'Produk pembersih berbasis serat palma alami yang cocok untuk aplikasi rumah tangga, komersial, pertanian, dan luar ruang.', NULL, NULL, NULL, NULL, 1, 1, 1, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(2, 'Crude Palm Oil (CPO)', NULL, 'crude-palm-oil-cpo', 'Indonesian crude palm oil supplied in line with international trade standards and buyer specifications.', 'Crude palm oil Indonesia yang dipasok sesuai standar perdagangan internasional dan spesifikasi pembeli.', NULL, NULL, NULL, NULL, 1, 1, 2, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(3, 'Coffee', NULL, 'coffee', 'Indonesian coffee sourced according to origin, grade, processing method, quality, and buyer specifications.', 'Kopi Indonesia yang bersumber sesuai origin, grade, metode proses, kualitas, dan spesifikasi pembeli.', NULL, NULL, NULL, NULL, 1, 1, 3, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(4, 'Clove', NULL, 'clove', 'Indonesian cloves serving food, spice, manufacturing, and other international applications.', 'Cengkeh Indonesia untuk aplikasi makanan, rempah, manufaktur, dan keperluan internasional lainnya.', NULL, NULL, NULL, NULL, 0, 1, 4, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(5, 'Cinnamon', NULL, 'cinnamon', 'Indonesian cinnamon for food, beverage, spice, ingredient, and other commercial applications.', 'Kayu manis Indonesia untuk aplikasi makanan, minuman, rempah, bahan baku, dan keperluan komersial lainnya.', NULL, NULL, NULL, NULL, 0, 1, 5, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(6, 'Vanilla', NULL, 'vanilla', 'Indonesian vanilla for food, beverage, flavouring, fragrance, and related industries.', 'Vanili Indonesia untuk industri makanan, minuman, perisa, fragrans, dan industri terkait.', NULL, NULL, NULL, NULL, 0, 1, 6, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(7, 'Areca Nut', NULL, 'areca-nut', 'Indonesian areca nut supplied according to international market requirements and buyer specifications.', 'Pinang Indonesia yang dipasok sesuai persyaratan pasar internasional dan spesifikasi pembeli.', NULL, NULL, NULL, NULL, 0, 1, 7, '2026-10-06 00:51:30', '2026-10-06 00:51:30'),
(8, 'Other Commodities', NULL, 'other-commodities', 'Our sourcing capabilities extend beyond our core portfolio. If you have a specific Indonesian commodity requirement, talk to us.', 'Kemampuan sourcing kami melampaui portofolio inti. Bila Anda memiliki kebutuhan komoditas Indonesia yang spesifik, hubungi kami.', NULL, NULL, NULL, NULL, 0, 1, 8, '2026-10-06 00:51:30', '2026-10-06 00:51:30');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'admin',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Sazara Global Admin', 'sazara@gmail.com', NULL, '$2y$12$rF9kgwSSYKcrm42StXe7WeiounLBc74lasshI2mPa5XTwmkQrtaIy', 'admin', NULL, '2026-10-06 00:51:31', '2026-10-06 00:51:31'),
(2, 'Sazara Admin', 'admin@sazaraglobal.com', NULL, '$2y$12$4I6JUKNOrIMwksJ6Y7WCHOu88IhsfkfLwNuWUpPdfDfqqktYJaVW2', 'admin', NULL, '2026-10-06 00:51:32', '2026-10-06 00:51:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_sections_page_section_field_unique` (`page`,`section`,`field`),
  ADD KEY `page_sections_media_id_foreign` (`media_id`),
  ADD KEY `page_sections_page_section_index` (`page`,`section`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `page_sections`
--
ALTER TABLE `page_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `page_sections`
--
ALTER TABLE `page_sections`
  ADD CONSTRAINT `page_sections_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
