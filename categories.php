<?php
// Include database connection
require_once 'db_connect.php';

// Default values for filters
$category_filter = isset($_GET['category']) ? $_GET['category'] : [];
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 100000;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'newest';

// Current page for pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 12;
$offset = ($current_page - 1) * $products_per_page;

// Prepare the query based on filters
$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE price BETWEEN ? AND ?";

$params = [$min_price, $max_price];
$types = "dd"; // Two decimals for min and max price

// Add category filter if selected
if (!empty($category_filter) && is_array($category_filter)) {
    $category_placeholders = str_repeat("?,", count($category_filter) - 1) . "?";
    $query .= " AND p.category_id IN ($category_placeholders)";
    foreach ($category_filter as $cat_id) {
        $params[] = $cat_id;
        $types .= "i"; // Integer for each category ID
    }
}

// Add sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id_asc'; // Default to newest first

$sql = "SELECT * FROM products";

switch ($sort_by) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'id_asc':
    default:
        $sql .= " ORDER BY product_id DESC"; // DESC for newest first (higher IDs are newer)
        break;
}

// Execute the query...
// Add limit and offset for pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $products_per_page;
$params[] = $offset;
$types .= "ii"; // Two integers for limit and offset

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind parameters dynamically
$stmt->bind_param($types, ...$params);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM products p WHERE price BETWEEN ? AND ?";
$count_params = [$min_price, $max_price];
$count_types = "dd";

if (!empty($category_filter) && is_array($category_filter)) {
    $category_placeholders = str_repeat("?,", count($category_filter) - 1) . "?";
    $count_query .= " AND p.category_id IN ($category_placeholders)";
    foreach ($category_filter as $cat_id) {
        $count_params[] = $cat_id;
        $count_types .= "i";
    }
}

$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param($count_types, ...$count_params);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_products = $count_row['total'];
$total_pages = ceil($total_products / $products_per_page);

// Get all categories for the filter sidebar
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Close the statements
$stmt->close();
$count_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELISCA | Collections</title>
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
        
        .btn-outline {
            border: 1px solid var(--beige);
            color: var(--gray);
            transition: all 0.3s ease;
        }
        
        .btn-outline:hover {
            border: 1px solid var(--beige);
            background-color: var(--beige);
            color: var(--black);
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
        
        .category-tile {
            overflow: hidden;
        }
        
        .category-tile img {
            transition: transform 0.6s ease;
        }
        
        .category-tile:hover img {
            transform: scale(1.05);
        }
        
        .social-icon {
            transition: color 0.3s ease, transform 0.3s ease;
        }
        
        .social-icon:hover {
            color: var(--beige);
            transform: translateY(-3px);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--white);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--beige);
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
        
        /* Price range slider */
        .price-range-container {
            margin: 30px 0;
        }
        
        .price-input-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .price-input-field {
            width: 45%;
            position: relative;
        }
        
        .price-input-field input {
            width: 100%;
            padding: 8px 8px 8px 25px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: left;
        }
        
        .price-input-field span {
            position: absolute;
            left: 8px;
            top: 8px;
        }
        
        .slider-container {
            position: relative;
            height: 5px;
            background-color: #e1e1e1;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .slider-progress {
            position: absolute;
            height: 100%;
            left: 0%;
            right: 0%;
            background: var(--beige);
            border-radius: 5px;
        }
        
        .range-input {
            position: absolute;
            width: 100%;
            height: 5px;
            top: -5px;
            background: none;
            pointer-events: none;
            -webkit-appearance: none;
        }
        
        .range-input::-webkit-slider-thumb {
            height: 17px;
            width: 17px;
            border-radius: 50%;
            background: var(--beige);
            pointer-events: auto;
            -webkit-appearance: none;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }
        
        /* Filter sidebar */
        .filter-sidebar {
            border-right: 1px solid rgba(239, 225, 203, 0.3);
        }
        
        .filter-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(239, 225, 203, 0.3);
        }
        
        .filter-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--black);
        }
        
        .category-filter label {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .category-filter label:hover {
            color: var(--gray);
        }
        
        .category-filter input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 1px solid var(--gray);
            margin-right: 10px;
            position: relative;
            cursor: pointer;
        }
        
        .category-filter input[type="checkbox"]:checked::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background-color: var(--gray);
        }
        
        /* Mobile menu toggle */
        .filter-mobile-toggle {
            display: none;
            padding: 1rem;
            background-color: var(--black);
            color: var(--beige);
            text-align: center;
            margin-bottom: 1rem;
            cursor: pointer;
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .filter-mobile-toggle {
                display: block;
            }
            
            .filter-sidebar {
                display: none;
            }
            
            .filter-sidebar.open {
                display: block;
            }
        }
        .product-card-container {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .product-image-container {
        position: relative;
        width: 100%;
        padding-top: 125%; /* 4:5 aspect ratio */
        overflow: hidden;
    }
    .product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
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
        <!-- Page Header -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-light mb-4" data-aos="fade-up">Our Collections</h1>
                    <div class="flex justify-center">
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
                                        <span class="text-gray-400">Shop</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Shop Content -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Mobile Filter Toggle -->
                    <button class="filter-mobile-toggle" id="filterToggle">
                        <i class="fas fa-filter mr-2"></i> FILTERS
                    </button>
                    
                    <!-- Filter Sidebar -->
                    <aside class="filter-sidebar w-full md:w-64 flex-shrink-0" id="filterSidebar">
                        <form action="categories.php" method="GET" id="filterForm">
                            <div class="filter-section category-filter">
                                <h3 class="filter-title">Categories</h3>
                                <?php foreach ($categories as $category): ?>
                                    <label>
                                        <input type="checkbox" name="category[]" value="<?php echo $category['category_id']; ?>"
                                        <?php echo (in_array($category['category_id'], (array)$category_filter)) ? 'checked' : ''; ?>>
                                        <span><?php echo htmlspecialchars($category['category_name']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="filter-section price-range-filter">
                                <h3 class="filter-title">Price Range</h3>
                                <div class="price-range-container">
                                    <div class="price-input-container">
                                        <div class="price-input-field">
                                            <span>₹</span>
                                            <input type="number" id="minPriceInput" value="<?php echo $min_price; ?>" min="0" max="100000">
                                        </div>
                                        <div class="price-input-field">
                                            <span>₹</span>
                                            <input type="number" id="maxPriceInput" value="<?php echo $max_price; ?>" min="0" max="100000">
                                        </div>
                                    </div>
                                    <div class="slider-container">
                                        <div class="slider-progress" id="sliderProgress"></div>
                                        <input type="range" class="range-input" id="minRange" min="0" max="100000" value="<?php echo $min_price; ?>" step="100">
                                        <input type="range" class="range-input" id="maxRange" min="0" max="100000" value="<?php echo $max_price; ?>" step="100">
                                    </div>
                                    <input type="hidden" name="min_price" id="minPrice" value="<?php echo $min_price; ?>">
                                    <input type="hidden" name="max_price" id="maxPrice" value="<?php echo $max_price; ?>">
                                </div>
                            </div>
                            
                            <div class="filter-section sort-filter">
    <h3 class="filter-title">Sort By</h3>
    <select name="sort_by" class="w-full p-2 border border-gray-300 bg-white focus:outline-none focus:border-beige">
        <option value="id_asc" <?php echo ($sort_by == 'id_asc') ? 'selected' : ''; ?>>Newest Arrivals</option>
        <option value="price_low" <?php echo ($sort_by == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
        <option value="price_high" <?php echo ($sort_by == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
    </select>
</div>
                            
                            <button type="submit" class="btn-primary w-full py-3 text-sm uppercase tracking-wider mt-6">
                                Apply Filters
                            </button>
                            
                            <a href="categories.php" class="btn-primary w-full py-3 text-sm uppercase tracking-wider mt-4 inline-block text-center">
                                Reset Filters
                            </a>
                        </form>
                    </aside>
                    
                    <!-- Product Grid -->
                    <div class="flex-1">
                        <div class="mb-8 flex justify-between items-center">
                            <p class="text-gray-600">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
                        </div>
                        
                        <?php if (count($products) > 0): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                              <?php foreach ($products as $product): ?>
        <div class="product-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300" data-aos="fade-up">
            <div class="relative overflow-hidden w-full h-80 mb-4">
                <img 
                    src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                >
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
</div>

                        <?php else: ?>
                            <div class="text-center py-20" data-aos="fade-up">
                                <h3 class="text-2xl font-light mb-4">No products found</h3>
                                <p class="text-gray-600 mb-6">We couldn't find any products matching your criteria.</p>
                                <a href="categories.php" class="btn-primary inline-block px-8 py-3 text-sm uppercase tracking-wider">
                                    Reset Filters
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="flex justify-center mt-16" data-aos="fade-up">
                                <nav class="flex items-center space-x-2">
                                    <?php if ($current_page > 1): ?>
                                        <a href="?page=<?php echo $current_page - 1; ?>&<?php echo http_build_query(array_filter([
                                            'category' => $category_filter,
                                            'min_price' => $min_price,
                                            'max_price' => $max_price,
                                            'sort_by' => $sort_by
                                        ])); ?>" class="px-3 py-1 border border-gray-300 text-gray-600 hover:bg-black hover:text-white hover:border-black transition-colors">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Show first page
                                    if ($current_page > 3): ?>
                                        <a href="?page=1&<?php echo http_build_query(array_filter([
                                            'category' => $category_filter,
                                            'min_price' => $min_price,
                                            'max_price' => $max_price,
                                            'sort_by' => $sort_by
                                        ])); ?>" class="px-3 py-1 border border-gray-300 text-gray-600 hover:bg-black hover:text-white hover:border-black transition-colors">
                                            1
                                        </a>
                                        <?php if ($current_page > 4): ?>
                                            <span class="px-3 py-1">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Show pages around current page
                                    for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter([
                                            'category' => $category_filter,
                                            'min_price' => $min_price,
                                            'max_price' => $max_price,
                                            'sort_by' => $sort_by
                                        ])); ?>" class="px-3 py-1 border <?php echo ($i == $current_page) ? 'bg-black text-white border-black' : 'border-gray-300 text-gray-600 hover:bg-black hover:text-white hover:border-black'; ?> transition-colors">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php 
                                    // Show last page
                                    if ($current_page < $total_pages - 2): ?>
                                        <?php if ($current_page < $total_pages - 3): ?>
                                            <span class="px-3 py-1">...</span>
                                        <?php endif; ?>
                                        <a href="?page=<?php echo $total_pages; ?>&<?php echo http_build_query(array_filter([
                                            'category' => $category_filter,
                                            'min_price' => $min_price,
                                            'max_price' => $max_price,
                                            'sort_by' => $sort_by
                                        ])); ?>" class="px-3 py-1 border border-gray-300 text-gray-600 hover:bg-black hover:text-white hover:border-black transition-colors">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="?page=<?php echo $current_page + 1; ?>&<?php echo http_build_query(array_filter([
                                            'category' => $category_filter,
                                            'min_price' => $min_price,
                                            'max_price' => $max_price,
                                            'sort_by' => $sort_by
                                        ])); ?>" class="px-3 py-1 border border-gray-300 text-gray-600 hover:bg-black hover:text-white hover:border-black transition-colors">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Newsletter Section -->
        <section class="py-24 bg-black text-white">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl md:text-4xl mb-6" data-aos="fade-up" style="color: #EFE1CB;">Join The Velisca World</h2>
                    <p class="text-gray-300 mb-10" data-aos="fade-up">Subscribe to receive exclusive offers, early access to new collections, and invitations to Velisca events.</p>
                    
                    <form class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4" data-aos="fade-up">
                        <input type="email" placeholder="Your email address" class="flex-grow bg-transparent py-3 px-4 border-b border-gray-600 focus:outline-none text-white placeholder-gray-400" />
                        <button type="submit" class="btn-primary py-3 px-8 text-sm uppercase tracking-wider">Subscribe</button>
                    </form>
                    
                    <p class="text-xs text-gray-400 mt-6" data-aos="fade-up">By subscribing, you agree to our Privacy Policy and consent to receive updates from Velisca.</p>
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
        
        // Mobile filter toggle
        document.getElementById('filterToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('filterSidebar');
            sidebar.classList.toggle('open');
            this.innerHTML = sidebar.classList.contains('open') 
                ? '<i class="fas fa-times mr-2"></i> CLOSE FILTERS' 
                : '<i class="fas fa-filter mr-2"></i> FILTERS';
        });
        
        // Price range slider functionality
        const minPriceInput = document.getElementById("minPriceInput");
        const maxPriceInput = document.getElementById("maxPriceInput");
        const minRange = document.getElementById("minRange");
        const maxRange = document.getElementById("maxRange");
        const sliderProgress = document.getElementById("sliderProgress");
        const minPriceHidden = document.getElementById("minPrice");
        const maxPriceHidden = document.getElementById("maxPrice");
        const priceGap = 1000;
        
        function updatePriceRange() {
            // Update the slider progress
            const minVal = parseInt(minRange.value);
            const maxVal = parseInt(maxRange.value);
            
            sliderProgress.style.left = (minVal / minRange.max) * 100 + "%";
            sliderProgress.style.right = 100 - (maxVal / maxRange.max) * 100 + "%";
            
            // Update the hidden fields for form submission
            minPriceHidden.value = minVal;
            maxPriceHidden.value = maxVal;
        }
        
        minRange.addEventListener("input", function() {
            let minVal = parseInt(minRange.value);
            let maxVal = parseInt(maxRange.value);
            
            if (maxVal - minVal < priceGap) {
                minRange.value = maxVal - priceGap;
                minVal = maxVal - priceGap;
            }
            
            minPriceInput.value = minVal;
            updatePriceRange();
        });
        
        maxRange.addEventListener("input", function() {
            let minVal = parseInt(minRange.value);
            let maxVal = parseInt(maxRange.value);
            
            if (maxVal - minVal < priceGap) {
                maxRange.value = minVal + priceGap;
                maxVal = minVal + priceGap;
            }
            
            maxPriceInput.value = maxVal;
            updatePriceRange();
        });
        
        minPriceInput.addEventListener("input", function() {
            let minVal = parseInt(minPriceInput.value) || 0;
            let maxVal = parseInt(maxPriceInput.value) || 100000;
            
            if (minVal < 0) minVal = 0;
            if (minVal > 100000) minVal = 100000;
            
            if (maxVal - minVal < priceGap) {
                minVal = maxVal - priceGap;
                minPriceInput.value = minVal;
            }
            
            minRange.value = minVal;
            updatePriceRange();
        });
        
        maxPriceInput.addEventListener("input", function() {
            let minVal = parseInt(minPriceInput.value) || 0;
            let maxVal = parseInt(maxPriceInput.value) || 100000;
            
            if (maxVal < 0) maxVal = 0;
            if (maxVal > 100000) maxVal = 100000;
            
            if (maxVal - minVal < priceGap) {
                maxVal = minVal + priceGap;
                maxPriceInput.value = maxVal;
            }
            
            maxRange.value = maxVal;
            updatePriceRange();
        });
        
        // Initialize the price range display
        updatePriceRange();
        
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