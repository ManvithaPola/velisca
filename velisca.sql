-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 08:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `velisca`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `product_id`, `quantity`, `total_price`) VALUES
(2, 2, 1, 1399.99),
(3, 1, 1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Women’s Collection'),
(2, 'Men’s Collection'),
(3, 'Unisex Essentials'),
(4, 'Lifestyle & Home Collection'),
(5, 'Special Collections & Gift Sets');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `category_id`, `image_url`, `stock_quantity`, `subcategory_id`) VALUES
(1, 'Velvet Evening Gown', 'A luxurious velvet gown perfect for formal occasions.', 1299.99, 1, 'women/sb1/1.jpg', 50, 1),
(2, 'Satin Strapless Dress', 'Elegant satin strapless gown with a sweetheart neckline.', 1399.99, 1, 'women/sb1/2.jpg', 40, 1),
(3, 'Silk Floor-Length Gown', 'A timeless silk gown for a sophisticated look.', 1599.99, 1, 'women/sb1/3.jpg', 30, 1),
(4, 'Crystal-Embellished Maxi Dress', 'A glamorous maxi dress with crystal detailing.', 1999.99, 1, 'women/sb1/4.jpg', 25, 1),
(5, 'Lace Overlay Evening Gown', 'An elegant lace gown with a delicate overlay.', 1799.99, 1, 'women/sb1/5.jpg', 20, 1),
(6, 'Silk Midi Dress', 'A luxurious silk midi dress perfect for evening events.', 899.99, 1, 'women/sb2/1.jpg', 50, 2),
(7, 'Satin Slip Dress', 'A sleek satin slip dress for a minimalist chic look.', 749.99, 1, 'women/sb2/2.jpg', 45, 2),
(8, 'Silk A-Line Dress', 'A flowy A-line silk dress with a flattering silhouette.', 899.99, 1, 'women/sb2/3.jpg', 50, 2),
(9, 'Satin Wrap Dress', 'A satin wrap dress with a tie waist for a feminine touch.', 899.99, 1, 'women/sb2/4.jpg', 50, 2),
(10, 'Silk Pleated Dress', 'Elegant pleated silk dress for formal occasions.', 999.99, 1, 'women/sb2/5.jpg', 40, 2),
(11, 'Silk Button-Down Shirt', 'A luxurious silk button-down shirt for everyday elegance.', 699.99, 1, 'women/sb3/1.jpg', 60, 3),
(12, 'Lace Detail Blouse', 'Delicate lace detailing for a sophisticated blouse look.', 799.99, 1, 'women/sb3/2.jpg', 50, 3),
(13, 'Satin V-Neck Top', 'A satin V-neck top perfect for day-to-night wear.', 749.99, 1, 'women/sb3/3.jpg', 45, 3),
(14, 'Ruffled Silk Blouse', 'A flowy silk blouse with ruffled detailing.', 799.99, 1, 'women/sb3/4.jpg', 55, 3),
(15, 'Embellished Silk Top', 'A glamorous silk top with intricate embellishments.', 899.99, 1, 'women/sb3/5.jpg', 40, 3),
(16, 'High-Waisted Tailored Trousers', 'Sophisticated high-waisted trousers with a slim fit.', 799.99, 1, 'women/sb4/1.jpg\n', 60, 4),
(17, 'Pleated Satin Skirt', 'A luxurious pleated satin skirt for a chic silhouette.', 899.99, 1, 'women/sb4/2.jpg\n', 50, 4),
(18, 'Silk Palazzo Pants', 'Flowy silk palazzo pants with a comfortable fit.', 1099.99, 1, 'women/sb4/3.jpg\n', 45, 4),
(19, 'Wool Blend Trousers', 'Tailored wool blend trousers for a polished look.', 799.99, 1, 'women/sb4/4.jpg\n', 40, 4),
(20, 'Tailored Pencil Skirt', 'A classic tailored pencil skirt for formal events.', 799.99, 1, 'women/sb4/5.jpg\n', 50, 4),
(21, 'Classic Leather Tote Bag', 'A classic leather tote bag for everyday elegance.', 1299.99, 1, 'women/sb5/1.jpg\n', 50, 5),
(22, 'Structured Leather Satchel', 'A structured leather satchel with a chic design.', 1499.99, 1, 'women/sb5/2.jpg\n', 45, 5),
(23, 'Leather Crossbody Bag', 'A sleek leather crossbody bag for hands-free elegance.', 999.99, 1, 'women/sb5/3.jpg\n', 40, 5),
(24, 'Quilted Leather Clutch', 'An elegant quilted leather clutch for evening occasions.', 1299.99, 1, 'women/sb5/4.jpg\n', 30, 5),
(25, 'Leather Bucket Bag', 'A stylish leather bucket bag for everyday wear.', 1099.99, 1, 'women/sb5/5.jpg\n', 50, 5),
(26, 'Classic Black Custom Suit', 'A timeless black custom suit tailored to perfection.', 2999.99, 2, 'men/sb1/1.jpg', 40, 6),
(27, 'Navy Blue Tailored Suit', 'A navy blue suit designed for a sharp, elegant look.', 2799.99, 2, 'men/sb1/2.jpg', 35, 6),
(28, 'Gray Wool Custom Suit', 'A sophisticated gray wool suit with premium finish.', 2599.99, 2, 'men/sb1/3.jpg', 50, 6),
(29, 'Tuxedo Custom Fit', 'A luxury tuxedo for formal occasions with a perfect fit.', 3499.99, 2, 'men/sb1/4.jpg', 20, 6),
(30, 'Plaid Custom Suit', 'A stylish plaid suit that stands out with its unique design.', 2899.99, 2, 'men/sb1/5.jpg', 25, 6),
(31, 'White Silk Shirt', 'A luxurious white silk shirt for special occasions.', 999.99, 2, 'men/sb2/1.jpg', 50, 7),
(32, 'Linen Casual Shirt', 'A breathable linen shirt perfect for summer days.', 599.99, 2, 'men/sb2/2.jpg', 40, 7),
(33, 'Black Silk Button-Down', 'A classic black silk shirt for a sleek, polished look.', 1099.99, 2, 'men/sb2/3.jpg', 30, 7),
(34, 'Beige Linen Shirt', 'A stylish beige linen shirt for effortless casual wear.', 749.99, 2, 'men/sb2/4.jpg', 45, 7),
(35, 'Striped Silk Shirt', 'A chic striped silk shirt for refined tastes.', 899.99, 2, 'men/sb2/5.jpg', 25, 7),
(36, 'Black Italian Leather Jacket', 'A premium black leather jacket with a smooth finish.', 1599.99, 2, 'men/sb3/1.jpg', 30, 8),
(37, 'Brown Biker Leather Jacket', 'A classic brown leather biker jacket with a rugged look.', 1699.99, 2, 'men/sb3/2.jpg', 20, 8),
(38, 'Italian Black Bomber Jacket', 'A sleek black bomber jacket made from high-quality Italian leather.', 1799.99, 2, 'men/sb3/3.jpg', 25, 8),
(39, 'Suede Italian Jacket', 'A luxury suede leather jacket with a soft texture.', 1499.99, 2, 'men/sb3/4.jpg', 30, 8),
(40, 'Tan Leather Zip-Up', 'A stylish tan leather jacket with a modern design.', 1599.99, 2, 'men/sb3/5.jpg', 40, 8),
(41, 'Velisca Classic Gold Watch', 'A luxurious gold watch with a minimalist design.', 2499.99, 2, 'men/sb4/1.jpg', 25, 9),
(42, 'Silver Luxury Watch', 'A sleek silver watch that radiates sophistication.', 2199.99, 2, 'men/sb4/2.jpg', 30, 9),
(43, 'Italian Leather Strap Watch', 'A premium leather strap watch with a refined look.', 1899.99, 2, 'men/sb4/3.jpg', 40, 9),
(44, 'Titanium Sports Watch', 'A durable titanium watch built for the modern man.', 2699.99, 2, 'men/sb4/4.jpg', 20, 9),
(45, 'Luxury Chronograph Watch', 'A luxury chronograph watch with precise craftsmanship.', 3199.99, 2, 'men/sb4/5.jpg', 15, 9),
(46, 'Velisca Signature Cologne', 'A rich, sophisticated fragrance with warm, woody notes.', 1499.99, 2, 'men/sb5/1.jpg', 50, 10),
(47, 'Luxury Oud Perfume', 'An exotic fragrance with deep oud and musk undertones.', 1799.99, 2, 'men/sb5/2.jpg', 30, 10),
(48, 'Citrus Infused Fragrance', 'A refreshing citrus fragrance perfect for daily wear.', 1299.99, 2, 'men/sb5/3.jpg', 40, 10),
(49, 'Leather & Spice Perfume', 'A warm fragrance with hints of leather and spices.', 1599.99, 2, 'men/sb5/4.jpg', 25, 10),
(50, 'Fresh Aquatic Cologne', 'A clean and fresh aquatic fragrance for an energetic feel.', 1199.99, 2, 'men/sb5/5.jpg', 35, 10),
(126, 'Oversized Graphic Hoodie', 'Premium cotton hoodie with exclusive designer print.', 899.99, 3, 'uni/sb1/1.jpg', 50, 11),
(127, 'Minimalist Street Tee', 'Soft unisex t-shirt with clean lines and subtle branding.', 499.99, 3, 'uni/sb1/2.jpg', 60, 11),
(128, 'Velisca Street Joggers', 'High-end joggers with tailored fit and elastic cuffs.', 799.99, 3, 'uni/sb1/3.jpg', 40, 11),
(129, 'Layered Street Jacket', 'Urban-inspired jacket with modern silhouettes.', 1299.99, 3, 'uni/sb1/4.jpg', 35, 11),
(130, 'Designer Street Shirt', 'Statement streetwear shirt with artistic patches.', 649.99, 3, 'uni/sb1/5.jpg', 45, 11),
(131, 'Velisca Noir Essence', 'Bold unisex scent with smoky and citrus layers.', 1699.99, 3, 'uni/sb2/1.jpg', 40, 12),
(132, 'Amber Bloom Eau De Parfum', 'Warm floral fragrance perfect for all-day wear.', 1599.99, 3, 'uni/sb2/2.jpg', 30, 12),
(133, 'Oud Whisper', 'Intense oud blend with sensual undertones.', 1799.99, 3, 'uni/sb2/3.jpg', 35, 12),
(134, 'Crisp Citrus Drift', 'A vibrant burst of citrus and musk.', 1399.99, 3, 'uni/sb2/4.jpg', 50, 12),
(135, 'Velisca Signature Scent', 'Our iconic fragrance – balanced, bold, unforgettable.', 1899.99, 3, 'uni/sb2/5.jpg', 25, 12),
(136, 'Canvas Logo Tote', 'Stylish everyday tote with embossed Velisca logo.', 699.99, 3, 'uni/sb3/1.jpg', 60, 13),
(137, 'Leather Carryall Tote', 'Spacious luxury tote crafted from fine leather.', 1499.99, 3, 'uni/sb3/2.jpg', 30, 13),
(138, 'Velisca Studio Tote', 'Minimalist tote for work and travel.', 899.99, 3, 'uni/sb3/3.jpg', 40, 13),
(139, 'Eco Soft Tote', 'Sustainable tote made from eco-friendly fabric.', 599.99, 3, 'uni/sb3/4.jpg', 50, 13),
(140, 'Limited Edition Art Tote', 'Art-inspired collectible tote with signature print.', 1099.99, 3, 'uni/sb3/5.jpg', 20, 13),
(141, 'Logo Baseball Cap', 'Sleek cap featuring embroidered Velisca branding.', 399.99, 3, 'uni/sb4/1.jpg', 70, 14),
(142, 'Velisca Logo Belt', 'Premium leather belt with engraved logo buckle.', 799.99, 3, 'uni/sb4/2.jpg', 40, 14),
(143, 'Luxury Logo Scarf', 'Soft silk scarf with custom Velisca print.', 999.99, 3, 'uni/sb4/3.jpg', 30, 14),
(144, 'Velisca Key Charm', 'Elegant logo keychain with gold accents.', 299.99, 3, 'uni/sb4/4.jpg', 60, 14),
(145, 'Logo Statement Ring', 'Bold ring featuring Velisca’s signature emblem.', 899.99, 3, 'uni/sb4/5.jpg', 25, 14),
(146, 'Velisca Classic Sneakers', 'Minimalist sneakers with premium leather finish.', 1299.99, 3, 'uni/sb5/1.jpg', 50, 15),
(147, 'High-Top Luxe Kicks', 'Elevated street-style sneakers with cushioned sole.', 1399.99, 3, 'uni/sb5/2.jpg', 35, 15),
(148, 'Chunky Logo Trainers', 'Bold chunky trainers with embossed branding.', 1499.99, 3, 'uni/sb5/3.jpg', 30, 15),
(149, 'Monochrome Slip-Ons', 'Streamlined slip-ons for daily style and comfort.', 1199.99, 3, 'uni/sb5/4.jpg', 40, 15),
(150, 'Limited Edition Color Pop', 'Color-blocked sneakers made for fashion enthusiasts.', 1599.99, 3, 'uni/sb5/5.jpg', 20, 15),
(151, 'Velisca Cashmere Throw', 'Ultra-soft cashmere throw with elegant fringe detailing.', 1599.99, 4, 'home/sb1/1.jpg', 30, 16),
(152, 'Knitted Pattern Blanket', 'Cozy designer blanket with intricate woven patterns.', 899.99, 4, 'home/sb1/2.jpg', 40, 16),
(153, 'Monochrome Luxe Throw', 'Minimalist black & white throw for chic spaces.', 1099.99, 4, 'home/sb1/3.jpg', 35, 16),
(154, 'Velisca Signature Blanket', 'Exclusive branded throw with velvet finish.', 1299.99, 4, 'home/sb1/4.jpg', 25, 16),
(155, 'Wool Blend Designer Blanket', 'Warm wool blend in soft pastel tones.', 999.99, 4, 'home/sb1/5.jpg', 45, 16),
(156, 'Sculpted Ceramic Vase', 'Matte white sculpted vase made by artisans.', 699.99, 4, 'home/sb2/1.jpg', 50, 17),
(157, 'Velisca Glass Bloom Vase', 'Hand-blown vase with ripple textures.', 899.99, 4, 'home/sb2/2.jpg', 30, 17),
(158, 'Marble Finish Vase', 'Elegant marble-textured ceramic vase.', 1099.99, 4, 'home/sb2/3.jpg', 20, 17),
(159, 'Abstract Gold Vase', 'Modern gold-accented decorative vase.', 1299.99, 4, 'home/sb2/4.jpg', 25, 17),
(160, 'Pastel Pottery Vase', 'Earthy-toned handcrafted pottery vase.', 799.99, 4, 'home/sb2/5.jpg', 40, 17),
(161, 'Velisca Velvet Cushions - Set of 4', 'Luxurious velvet cushions in assorted shades.', 1499.99, 4, 'home/sb3/1.jpg', 35, 18),
(162, 'Minimalist Beige Cushion Set', 'Muted-tone cushions for modern interiors.', 999.99, 4, 'home/sb3/2.jpg', 40, 18),
(163, 'Textured Cushion Set', 'Soft cushions with geometric textures.', 1299.99, 4, 'home/sb3/3.jpg', 30, 18),
(164, 'Silk Cushion Pair', 'Elegant silk cushions with embroidery.', 1399.99, 4, 'home/sb3/4.jpg', 20, 18),
(165, 'Art-Inspired Cushion Set', 'Cushions with printed artwork motifs.', 1199.99, 4, 'home/sb3/5.jpg', 25, 18),
(166, 'Velisca Signature Scent Candle', 'Luxury soy candle with our signature blend.', 799.99, 4, 'home/sb4/1.jpg', 60, 19),
(167, 'Amber & Musk Soy Candle', 'Rich amber base with a musky finish.', 699.99, 4, 'home/sb4/2.jpg', 50, 19),
(168, 'Vanilla Oud Candle', 'Comforting vanilla and exotic oud fusion.', 849.99, 4, 'home/sb4/3.jpg', 35, 19),
(169, 'Floral Breeze Candle', 'Light floral fragrance with rose and jasmine.', 649.99, 4, 'home/sb4/4.jpg', 40, 19),
(170, 'Eucalyptus Mint Candle', 'Refreshing scent to calm your space.', 599.99, 4, 'home/sb4/5.jpg', 45, 19),
(171, 'Abstract Monochrome Print', 'Framed minimalist abstract in black and white.', 1299.99, 4, 'home/sb5/1.jpg', 20, 20),
(172, 'Velisca Fashion Sketch Set', 'Limited edition fashion-themed art prints.', 1499.99, 4, 'home/sb5/2.jpg', 15, 20),
(173, 'Architectural Lines Poster', 'Stylized line art inspired by modern architecture.', 1099.99, 4, 'home/sb5/3.jpg', 25, 20),
(174, 'Nature in Gold Foil', 'Botanical illustrations in gold foil accents.', 1399.99, 4, 'home/sb5/4.jpg', 10, 20),
(175, 'Milan Skyline Art', 'Luxurious cityscape print of Milan.', 1199.99, 4, 'home/sb5/5.jpg', 18, 20),
(176, 'Velisca Essentials for Him & Her', 'A dual luxury box with fragrance, silk scarves & grooming kits.', 2999.99, 5, 'gift/sb1/1.jpg', 20, 21),
(177, 'Date Night Duo Box', 'Coordinated accessories and scents for couples.', 2699.99, 5, 'gift/sb1/2.jpg', 25, 21),
(178, 'His & Hers Spa Kit', 'Relaxing bath and skincare essentials.', 2499.99, 5, 'gift/sb1/3.jpg', 30, 21),
(179, 'Matching Leather Accessories Set', 'Unisex wallet and cardholder combo.', 2899.99, 5, 'gift/sb1/4.jpg', 15, 21),
(180, 'Velisca Signature Couple Box', 'Velisca-curated essentials for style & comfort.', 3199.99, 5, 'gift/sb1/5.jpg', 18, 21),
(181, 'Golden Gown Glamour', 'Inspired by Cannes fashion week, radiant and regal.', 4499.99, 5, 'gift/sb2/1.jpg', 10, 22),
(182, 'Velisca Velvet Tux', 'Classic red carpet tux with modern tailoring.', 4899.99, 5, 'gift/sb2/2.jpg', 8, 22),
(183, 'Draped Silk Elegance Dress', 'Graceful red gown with open back and high slit.', 4299.99, 5, 'gift/sb2/3.jpg', 12, 22),
(184, 'Starlight Beaded Clutch', 'Hand-beaded luxury clutch to match red carpet looks.', 1599.99, 5, 'gift/sb2/4.jpg', 20, 22),
(185, 'Luxury Stiletto Heels', 'Glossy high heels perfect for spotlight events.', 2299.99, 5, 'gift/sb2/5.jpg', 14, 22),
(186, 'Bridal Couture Gown', 'Hand-embroidered gown for the Velisca bride.', 5599.99, 5, 'gift/sb3/1.jpg', 5, 23),
(187, 'Velisca Wedding Scent Box', 'Exclusive perfumes for bride and groom.', 2699.99, 5, 'gift/sb3/2.jpg', 10, 23),
(188, 'Wedding Accessories Set', 'Matching clutch, jewelry and tie pin set.', 1999.99, 5, 'gift/sb3/3.jpg', 12, 23),
(189, 'Velisca Couple Robes', 'Monogrammed plush robes for newlyweds.', 1799.99, 5, 'gift/sb3/4.jpg', 15, 23),
(190, 'Luxury Bridal Heels', 'Crystal-embellished white heels for brides.', 2999.99, 5, 'gift/sb3/5.jpg', 6, 23),
(191, 'Winter Glow Holiday Hamper', 'Includes soy candles, scarf, chocolate, and fragrance.', 2499.99, 5, 'gift/sb4/1.jpg', 30, 24),
(192, 'Velisca Festive Collection', 'Holiday-themed accessories and snacks.', 2199.99, 5, 'gift/sb4/2.jpg', 25, 24),
(193, 'Luxury Spa Holiday Box', 'Pamper set with essential oils and bath crystals.', 2599.99, 5, 'gift/sb4/3.jpg', 20, 24),
(194, 'New Year Countdown Kit', 'Includes diary, pen set, and limited-edition candles.', 2099.99, 5, 'gift/sb4/4.jpg', 28, 24),
(195, 'Holiday Warmth Set', 'Throw blanket, mugs, and cinnamon diffuser.', 2399.99, 5, 'gift/sb4/5.jpg', 32, 24),
(196, 'Velisca VIP Box – January', 'Winter luxury essentials curated by stylists.', 3499.99, 5, 'gift/sb5/1.jpg', 20, 25),
(197, 'Velisca VIP Box – February', 'Romantic accents, scents & silk accessories.', 3499.99, 5, 'gift/sb5/2.jpg', 20, 25),
(198, 'Velisca VIP Box – March', 'Spring hues, designer scarves, and gloss balm.', 3499.99, 5, 'gift/sb5/3.jpg', 20, 25),
(199, 'Velisca VIP Box – April', 'Natural skincare, earthy jewelry & style journal.', 3499.99, 5, 'gift/sb5/4.jpg', 20, 25),
(200, 'Velisca VIP Box – May', 'Light fragrance, summer wraps & limited-edition bag.', 3499.99, 5, 'gift/sb5/5.jpg', 20, 25);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`subcategory_id`, `subcategory_name`, `category_id`) VALUES
(1, 'Luxe Evening Gowns', 1),
(2, 'Silk & Satin Dresses', 1),
(3, 'Designer Tops & Blouses', 1),
(4, 'Tailored Trousers & Skirts', 1),
(5, 'Premium Leather Handbags', 1),
(6, 'Custom-Fit Suits', 2),
(7, 'Designer Shirts (Silk/Linen)', 2),
(8, 'Italian Leather Jackets', 2),
(9, 'Premium Watches', 2),
(10, 'Fragrance for Him', 2),
(11, 'Designer Streetwear', 3),
(12, 'Luxury Fragnances', 3),
(13, 'Branded Tote Bags', 3),
(14, 'Velisca Logo Accessories', 3),
(15, 'Trendy Sneakers', 3),
(16, 'Designer Throw Blankets', 4),
(17, 'Handcrafted Vases', 4),
(18, 'Premium cushion sets', 4),
(19, 'Scented Soy Candles', 4),
(20, 'Luxury Wall Art Prints', 4),
(21, 'His & Hers Gift Boxes', 5),
(22, 'Red Carpet-Inspired Styles', 5),
(23, 'Wedding Edit', 5),
(24, 'Holiday Luxury Hampers', 5),
(25, 'Velisca VIP Curated Box (Monthly)', 5);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `product_id`) VALUES
(6, 1),
(1, 6),
(2, 25),
(3, 31),
(4, 132),
(5, 158);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
