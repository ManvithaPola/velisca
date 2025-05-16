<?php
// Include database connection
require_once 'db_connect.php';

// Set default sorting and subcategory filter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$selectedSubcategory = isset($_GET['subcategory_name']) ? $_GET['subcategory_name'] : '';

// Start base SQL query
$sql = "SELECT p.*, c.category_name FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.category_id = 1"; // Men category

// Add subcategory filter if selected
if (!empty($selectedSubcategory)) {
    $sql .= " AND p.subcategory_name = ?";
}

// Add sorting logic
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    default:
        $sql .= " ORDER BY p.product_id ASC"; // Featured by default
        break;
}

// Prepare and bind statement
$stmt = $conn->prepare($sql);

if (!empty($selectedSubcategory)) {
    $stmt->bind_param("s", $selectedSubcategory);
}

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get product count
$product_count = count($products);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Women's Collection | Velisca</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <style>
        :root {
            --beige: #EFE1CB;
            --gray: #545454;
            --black: #000000;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
        }
        
        h1, h2, h3, h4, .serif {
            font-family: 'Playfair Display', serif;
        }
        
        .logo-text {
            font-family: 'Cinzel', serif;
            font-weight: 500;
            letter-spacing: 2px;
        }
        
        .btn-primary {
            background-color: var(--beige);
            color: var(--black);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--black);
            color: var(--beige);
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: var(--beige);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-img-container {
            position: relative;
            width: 100%;
            padding-top: 125%; /* 4:5 aspect ratio */
            overflow: hidden;
        }
        
        .product-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 400;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 1px;
            background-color: var(--beige);
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-white text-black relative">
    <!-- Navigation -->
    <nav class="bg-black text-white py-6 fixed w-full z-50 transition-all duration-300" id="main-nav">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="index.html" class="logo-text text-2xl" style="color: #EFE1CB;">
                <span class="text-3xl font-bold">V</span>ELISCA
            </a>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex space-x-8 text-sm tracking-wider">
                <a href="index.html" class="nav-link hover:text-beige" style="color: #EFE1CB;">HOME</a>
                <a href="women.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">WOMEN</a>
                <a href="men.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">MEN</a>
                <a href="unisex.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">UNISEX</a>
                <a href="lifestyle.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">LIFESTYLE</a>
                <a href="gifts.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">GIFTS</a>
            </div>
            
            <!-- Icons -->
            <div class="hidden lg:flex items-center space-x-6">
                <a href="categories.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="fas fa-search"></i></a>
                <a href="wishlist.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="far fa-heart"></i></a>
                <a href="cart.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="fas fa-shopping-bag"></i></a>
                <a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="far fa-user"></i></a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button class="lg:hidden focus:outline-none" id="mobile-menu-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#EFE1CB">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="pt-24">
        <!-- Breadcrumb -->
        <div class="container mx-auto px-4 py-4">
            <nav class="text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="index.html" class="inline-flex items-center text-gray-700 hover:text-black">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                            <span class="text-gray-400">Women's Collection</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Collection Header -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-light mb-4" data-aos="fade-up">Women's Collection</h1>
                <p class="text-gray-600" data-aos="fade-up">Discover our latest designs for the modern woman</p>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="py-8">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center">
                    <div>
                        <form action="" method="get" id="sort-form">
                        <select name="subcategory" class="border rounded px-3 py-2">
  <option value="">All Subcategories</option>
  <option value="Luxe Evening Gowns" <?= ($selectedSubcategory == 'Luxe Evening Gowns') ? 'selected' : '' ?>>Luxe Evening Gowns</option>
  <option value="Silk & Satin Dresses" <?= ($selectedSubcategory == 'Silk & Satin Dresses') ? 'selected' : '' ?>>Silk & Satin Dresses</option>
  <option value="Designer Tops & Blouses" <?= ($selectedSubcategory == 'Designer Tops & Blouses') ? 'selected' : '' ?>>Designer Tops & Blouses</option>
  <option value="Tailored Trousers & Skirts" <?= ($selectedSubcategory == 'Tailored Trousers & Skirts') ? 'selected' : '' ?>>Tailored Trousers & Skirts</option>
  <option value="Premium Leather Handbags" <?= ($selectedSubcategory == 'Premium Leather Handbags') ? 'selected' : '' ?>>Premium Leather Handbags</option>
</select>


<select name="sort" class="border rounded px-3 py-2">
  <option value="default" <?= ($sort == 'default') ? 'selected' : '' ?>>Featured</option>
  <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
  <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
</select>

<button type="submit" class="bg-black text-white px-4 py-2 rounded">Apply</button>

                        </form>
                    </div>
                    <div class="text-gray-600">
                        <?php echo $product_count; ?> Products
                    </div>
                </div>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300" data-aos="fade-up">
                                <div class="product-img-container">
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-img">
                                </div>
                                <div class="text-center px-4 pb-6">
                                    <h3 class="text-lg font-semibold mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <p class="text-black font-medium mb-3">₹<?php echo number_format($product['price'], 0); ?></p>
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                                       class="inline-block bg-black text-white px-5 py-2 text-sm rounded-full hover:bg-gray-800 transition duration-300">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-20 col-span-full" data-aos="fade-up">
                            <h3 class="text-2xl font-light mb-4">No products found</h3>
                            <p class="text-gray-600 mb-6">We couldn't find any products in this collection.</p>
                            <a href="women.php" class="btn-primary inline-block px-8 py-3 text-sm uppercase tracking-wider">
                                Reset Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        
    </main>
    
    <!-- Footer -->
    <footer class="bg-black text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Logo and About -->
                <div>
                    <a href="index.html" class="logo-text text-2xl block mb-6" style="color: #EFE1CB;">
                        <span class="text-3xl font-bold">V</span>ELISCA
                    </a>
                    <p class="text-gray-400 mb-6">Crafting timeless luxury pieces with exceptional materials and artisanal expertise since 2005.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="social-icon text-gray-400 hover:text-beige" style="color: #EFE1CB;"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon text-gray-400 hover:text-beige" style="color: #EFE1CB;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon text-gray-400 hover:text-beige" style="color: #EFE1CB;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon text-gray-400 hover:text-beige" style="color: #EFE1CB;"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                
                <!-- Shopping -->
                <div>
                    <h3 class="text-lg mb-6 font-medium">Shopping</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Women</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Men</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Lifestyle</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Gifts</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Collections</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Store Locator</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div>
                    <h3 class="text-lg mb-6 font-medium">Customer Service</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Contact Us</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Shipping & Returns</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Secure Payment</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Track Your Order</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- About -->
                <div>
                    <h3 class="text-lg mb-6 font-medium">About Velisca</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Our Story</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Craftsmanship</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Sustainability</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Careers</a></li>
                        <li><a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Press</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 text-sm mb-4 md:mb-0">
                    &copy; 2025 Velisca. All rights reserved.
                </div>
                <div class="flex space-x-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Privacy Policy</a>
                    <a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Terms of Service</a>
                    <a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;">Cookie Preferences</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to top button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-black text-white w-10 h-10 rounded-full flex items-center justify-center opacity-0 invisible transition-all duration-300 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- JavaScript -->
    <script>
        // Initialize AOS animation library
        AOS.init({
            duration: 1000,
            once: true,
            offset: 50
        });
        
        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        
        document.getElementById('back-to-top').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            if (window.pageYOffset > 100) {
                nav.classList.add('py-4');
                nav.classList.remove('py-6');
            } else {
                nav.classList.add('py-6');
                nav.classList.remove('py-4');
            }
        });
    </script>
</body>
</html>