<?php
// Include database connection
require_once 'db_connect.php';

// Initialize variables
$cart_items = [];
$cart_total = 0;
$cart_count = 0;

// Process remove item action
if (isset($_POST['remove_item']) && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    $remove_query = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $conn->prepare($remove_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit();
}

// Process update quantity action
if (isset($_POST['update_quantity']) && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    // Ensure quantity is at least 1
    if ($quantity < 1) $quantity = 1;

    // Get product price to recalculate total
    $price_query = "SELECT p.price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.cart_id = ?";
    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $price_result = $stmt->get_result();

    if ($price_row = $price_result->fetch_assoc()) {
        $item_price = $price_row['price'];
        $total_price = $item_price * $quantity;

        // Update cart item with new quantity and total_price
        $update_query = "UPDATE cart SET quantity = ?, total_price = ? WHERE cart_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("idi", $quantity, $total_price, $cart_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    $stmt->close();

    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit();
}

// Fetch cart items
$query = "SELECT c.cart_id, c.product_id, c.quantity, c.total_price, 
          p.name, p.price, p.image_url, cat.category_name 
          FROM cart c
          JOIN products p ON c.product_id = p.product_id
          JOIN categories cat ON p.category_id = cat.category_id
          ORDER BY c.cart_id DESC";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $cart_total += $row['total_price'];
        $cart_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELISCA | Shopping Cart</title>
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
        
        .cart-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            padding: 8px;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }
        
        .quantity-input:focus {
            outline: none;
            border-color: var(--beige);
        }
        
        .remove-btn {
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            color: #d63031;
            transform: scale(1.1);
        }
        
        .update-btn {
            transition: all 0.3s ease;
        }
        
        .update-btn:hover {
            transform: scale(1.05);
        }
        
        .checkout-btn {
            letter-spacing: 1.5px;
            font-weight: 500;
            padding: 14px 40px;
            border-radius: 2px;
            transition: all 0.4s ease;
        }
        
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
        
        .empty-cart-container {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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
                    <h1 class="text-4xl md:text-5xl font-light mb-4" data-aos="fade-up">Shopping Cart</h1>
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
                                        <span class="text-gray-400">Cart</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Cart Content -->
        <section class="py-12">
            <div class="container mx-auto px-4">
            
                <?php if (count($cart_items) > 0): ?>
                
                <div class="mb-8" data-aos="fade-up">
                    <p class="text-gray-600 text-lg"><?php echo $cart_count; ?> item<?php echo $cart_count > 1 ? 's' : ''; ?> in your cart</p>
                </div>
                
                <div class="flex flex-col lg:flex-row gap-10">
                    <!-- Cart Items -->
                    <div class="flex-grow">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item bg-white rounded-xl shadow-md overflow-hidden mb-6 p-6" data-aos="fade-up">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <!-- Product Image -->
                                    <div class="md:w-1/4 flex-shrink-0">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                            class="w-full h-48 object-cover rounded-md">
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="md:w-3/4 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start mb-2">
                                                <h3 class="text-xl font-medium"><?php echo htmlspecialchars($item['name']); ?></h3>
                                                <form method="post" class="ml-2" onsubmit="return confirm('Are you sure you want to remove this item?');">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                    <button type="submit" name="remove_item" class="remove-btn text-gray-500 hover:text-red-500">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <p class="text-gray-500 mb-4"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                            <p class="text-black font-medium mb-6">₹<?php echo number_format($item['price'], 0); ?></p>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                            <form method="post" class="flex items-center space-x-4">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                <label for="quantity-<?php echo $item['cart_id']; ?>" class="text-gray-700">Quantity:</label>
                                                <div class="flex items-center">
                                                    <input type="number" id="quantity-<?php echo $item['cart_id']; ?>" name="quantity" 
                                                        value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                                    <button type="submit" name="update_quantity" class="update-btn ml-3 bg-gray-200 hover:bg-beige text-black px-4 py-2 rounded-md transition-colors">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <div class="mt-4 sm:mt-0 text-right">
                                                <p class="text-gray-600">Subtotal:</p>
                                                <p class="text-xl font-medium">₹<?php echo number_format($item['price'], 0); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="lg:w-1/3 flex-shrink-0">
                        <div class="bg-white rounded-xl shadow-md p-6" data-aos="fade-up">
                            <h3 class="text-2xl font-light mb-6 pb-4 border-b border-gray-200">Order Summary</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between">
                                    <p class="text-gray-600">Subtotal</p>
                                    <p class="font-medium">₹<?php echo number_format($cart_total, 0); ?></p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-gray-600">Shipping</p>
                                    <p>Calculated at checkout</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-gray-600">Tax</p>
                                    <p>Calculated at checkout</p>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-6">
                                    <p class="text-xl">Total</p>
                                    <p class="text-2xl font-semibold">₹<?php echo number_format($cart_total, 0); ?></p>
                                </div>
                                
                                <a href="checkout.php" class="block text-center bg-black text-white checkout-btn hover:bg-gray-900 w-full">
                                    PROCEED TO CHECKOUT
                                </a>
                                
                                <div class="mt-6 text-center">
                                    <a href="categories.php" class="inline-flex items-center text-gray-600 hover:text-black transition-colors">
                                        <i class="fas fa-long-arrow-alt-left mr-2"></i>
                                        Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                
                <!-- Empty Cart -->
                <div class="empty-cart-container text-center py-16" data-aos="fade-up">
                    <div class="mb-6">
                        <i class="fas fa-shopping-bag text-6xl text-gray-300"></i>
                    </div>
                    <h2 class="text-3xl font-light mb-4">Your Velisca cart is waiting to be filled with elegance.</h2>
                    <p class="text-gray-600 mb-10 max-w-lg mx-auto">Discover our curated collections of timeless luxury pieces crafted with exceptional materials and artisanal expertise.</p>
                    <a href="categories.php" class="btn-primary py-3 px-8 text-sm uppercase tracking-wider inline-block">
                        Explore Collections
                    </a>
                </div>
                
                <?php endif; ?>
                
            </div>
        </section>
        
        <!-- Recommended Products -->
        <?php if (count($cart_items) > 0): ?>
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-light mb-10 text-center" data-aos="fade-up">You May Also Like</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up">
                    <!-- This would typically be populated from the database with product recommendations -->
                    <!-- For now, showing placeholder items -->
                    <?php
                    // Query for recommended products (simple example - can be made more sophisticated)
                    $rec_query = "SELECT p.*, c.category_name FROM products p 
                                JOIN categories c ON p.category_id = c.category_id 
                                ORDER BY RAND() LIMIT 4";
                    $rec_result = $conn->query($rec_query);
                    
                    if ($rec_result && $rec_result->num_rows > 0) {
                        while ($product = $rec_result->fetch_assoc()) {
                    ?>
                    <div class="product-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
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
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
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
            easing: 'ease-out'
        });
        
        // Back to top button functionality
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible');
                backToTopButton.classList.remove('opacity-100', 'visible');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Navigation shrink on scroll
        const mainNav = document.getElementById('main-nav');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                mainNav.classList.add('py-3');
                mainNav.classList.remove('py-6');
            } else {
                mainNav.classList.add('py-6');
                mainNav.classList.remove('py-3');
            }
        });
        
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.createElement('div');
        mobileMenu.classList.add('lg:hidden', 'fixed', 'inset-0', 'bg-black', 'bg-opacity-95', 'z-50', 'flex', 'items-center', 'justify-center', 'transform', 'translate-x-full', 'transition-transform', 'duration-300', 'ease-in-out');
        document.body.appendChild(mobileMenu);
        
        // Add mobile menu content
        mobileMenu.innerHTML = `
            <div class="relative w-full h-full flex flex-col items-center justify-center">
                <button class="absolute top-6 right-6 text-white focus:outline-none" id="close-menu-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="#EFE1CB">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <nav class="flex flex-col items-center space-y-8 text-xl text-white">
                    <a href="index.html" class="nav-link hover:text-beige" style="color: #EFE1CB;">HOME</a>
                    <a href="women.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">WOMEN</a>
                    <a href="men.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">MEN</a>
                    <a href="unisex.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">UNISEX</a>
                    <a href="lifestyle.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">LIFESTYLE</a>
                    <a href="gifts.php" class="nav-link hover:text-beige" style="color: #EFE1CB;">GIFTS</a>
                </nav>
                <div class="mt-12 flex space-x-6">
                    <a href="categories.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="fas fa-search fa-lg"></i></a>
                    <a href="wishlist.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="far fa-heart fa-lg"></i></a>
                    <a href="cart.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="fas fa-shopping-bag fa-lg"></i></a>
                    <a href="#" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="far fa-user fa-lg"></i></a>
                </div>
            </div>
        `;
        
        // Mobile menu toggle
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
            mobileMenu.classList.add('translate-x-0');
            document.body.classList.add('overflow-hidden');
        });
        
        const closeMenuButton = document.getElementById('close-menu-button');
        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-0');
            mobileMenu.classList.add('translate-x-full');
            document.body.classList.remove('overflow-hidden');
        });
        
        // Quantity input validation for shopping cart
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) {
                    this.value = 1;
                }
            });
        });
    </script>
</body>
</html>