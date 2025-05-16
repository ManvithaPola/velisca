<?php
require_once 'db_connect.php';

// Sample wishlist items for demonstration (remove in production)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['seeded'])) {
    // Check if wishlist is empty
    $check = $conn->query("SELECT COUNT(*) FROM wishlist");
    if ($check->fetch_row()[0] == 0) {
        // Seed sample data
        $conn->query("INSERT INTO wishlist (product_id) VALUES 
                     (1), (3), (5), (7), (9)");
        header("Location: wishlist.php?seeded=true");
        exit();
    }
}

// Handle item removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $wishlist_id = (int)$_POST['wishlist_id'];
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE wishlist_id = ?");
        $stmt->bind_param("i", $wishlist_id);
        $stmt->execute();
        
        // Return JSON response for AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
    }
    
    // Handle add to cart
    if (isset($_POST['add_to_cart'])) {
        $product_id = (int)$_POST['product_id'];
        // In a real implementation, you would add to cart here
        // For now we'll just return a success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Item added to cart']);
        exit();
    }
}

// Fetch wishlist items with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 8;
$offset = ($page - 1) * $per_page;

$sql = "SELECT SQL_CALC_FOUND_ROWS w.wishlist_id, p.* 
        FROM wishlist w
        JOIN products p ON w.product_id = p.product_id
        LIMIT $offset, $per_page";

$result = $conn->query($sql);
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);

// Get total count
$total_result = $conn->query("SELECT FOUND_ROWS()");
$total_items = $total_result->fetch_row()[0];
$total_pages = ceil($total_items / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist | Velisca</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --beige: #EFE1CB;
            --gray: #545454;
            --black: #000000;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--white);
            color: var(--black);
        }
        
        h1, h2, h3, h4 {
            font-family: 'Cinzel', serif;
        }
        
        .logo-text {
            font-family: 'Cinzel', serif;
            font-weight: 500;
            letter-spacing: 2px;
        }
        
        .wishlist-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        .btn-outline {
            border: 1px solid var(--beige);
            color: var(--black);
            transition: all 0.3s ease;
        }
        
        .btn-outline:hover {
            background-color: var(--beige);
            color: var(--black);
        }
        
        .btn-dark {
            background-color: var(--black);
            color: var(--white);
            transition: all 0.3s ease;
        }
        
        .btn-dark:hover {
            background-color: var(--gray);
        }
        
        .empty-wishlist {
            background-color: rgba(239, 225, 203, 0.1);
            border: 1px dashed var(--beige);
        }
        /* Animation for cart notification */
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(20px); opacity: 0; }
}

.cart-notification {
    animation: slideIn 0.3s forwards;
}

.cart-notification.hide {
    animation: slideOut 0.3s forwards;
}

/* Loading spinner for buttons */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading:after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
                <a href="wishlist.php" class="hover:text-beige transition-colors" style="color: #EFE1CB;"><i class="fas fa-heart"></i></a>
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
    <main class="pt-24 pb-16">
        <div class="container mx-auto px-4">
            <!-- Page Header -->
            <div class="py-12 text-center">
                <h1 class="text-4xl md:text-5xl font-light mb-4">Your Wishlist</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Curated pieces you love, saved for later</p>
            </div>
            
            <!-- Wishlist Items -->
            <div class="pb-16">
                <?php if (count($wishlist_items) > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($wishlist_items as $item): ?>
        <div class="wishlist-card bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
            <!-- Product Image with Quick View -->
            <div class="relative group overflow-hidden w-full h-80">
                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                     alt="<?= htmlspecialchars($item['name']) ?>" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                
                <!-- Quick View Button -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-30">
                    <a href="product.php?id=<?= $item['product_id'] ?>" 
                       class="bg-white text-black px-4 py-2 rounded-full text-sm font-medium">
                        Quick View
                    </a>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="p-4">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-lg font-semibold"><?= htmlspecialchars($item['name']) ?></h3>
                    <!-- <span class="text-gray-500 text-sm"><?= htmlspecialchars($item['category_name']) ?></span> -->
                </div>
                
                <div class="flex justify-between items-center mb-4">
                    <p class="text-black font-medium">₹<?= number_format($item['price'], 0) ?></p>
                    <div class="flex space-x-1 text-sm text-gray-400">
                        <i class="fas fa-star"></i>
                        <span>4.8</span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-2">
                    <form method="POST" action="wishlist.php" class="flex-1">
                        <input type="hidden" name="wishlist_id" value="<?= $item['wishlist_id'] ?>">
                        <button type="submit" name="remove_item" 
                                class="remove-from-wishlist w-full py-2 border border-gray-200 rounded-md text-sm flex items-center justify-center hover:bg-gray-50 transition">
                            <i class="fas fa-heart text-red-500 mr-2"></i>
                            Remove
                        </button>
                    </form>
                    
                    <form method="POST" action="wishlist.php" class="flex-1">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" name="add_to_cart" 
                                class="add-to-cart w-full py-2 bg-black text-white rounded-md text-sm flex items-center justify-center hover:bg-gray-800 transition"
                                data-product-id="<?= $item['product_id'] ?>">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
                <?php else: ?>
                    <!-- Empty Wishlist -->
                    <div class="empty-wishlist rounded-xl p-12 text-center border-2 border-dashed border-gray-100 bg-gray-50">
    <div class="text-7xl mb-6 text-gray-200">🖤</div>
    <h3 class="text-2xl font-light mb-3">Your Wishlist Awaits</h3>
    <p class="text-gray-500 mb-8 max-w-md mx-auto">
        Save your favorite Velisca pieces here to keep them close at hand. 
        When you're ready, they'll be waiting for you.
    </p>
    <div class="flex justify-center space-x-4">
        <a href="women.php" class="btn-outline px-8 py-3 text-sm uppercase tracking-wider">
            Women's Collection
        </a>
        <a href="men.php" class="btn-outline px-8 py-3 text-sm uppercase tracking-wider">
            Men's Collection
        </a>
    </div>
</div>
                <?php endif; ?>
            </div>
            <?php if ($total_pages > 1): ?>
    <div class="mt-12 flex justify-center">
        <nav class="flex items-center space-x-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" 
                   class="px-4 py-2 border border-gray-200 rounded-md hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-4 py-2 border rounded-md <?= $i == $page ? 'bg-black text-white border-black' : 'border-gray-200 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" 
                   class="px-4 py-2 border border-gray-200 rounded-md hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </nav>
    </div>
<?php endif; ?>
        </div>
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
    
    <!-- JavaScript -->
    <script>
    // Back to top button
    const backToTop = document.getElementById('back-to-top');
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.remove('opacity-0', 'invisible');
            backToTop.classList.add('opacity-100', 'visible');
        } else {
            backToTop.classList.add('opacity-0', 'invisible');
            backToTop.classList.remove('opacity-100', 'visible');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // Navbar scroll effect
    const nav = document.getElementById('main-nav');
    window.addEventListener('scroll', function() {
        nav.classList.toggle('py-4', window.scrollY > 100);
        nav.classList.toggle('py-6', window.scrollY <= 100);
    });
    
    // AJAX for wishlist actions
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const card = this.closest('.wishlist-card');
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fade out animation
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        card.remove();
                        // Check if wishlist is now empty
                        if (document.querySelectorAll('.wishlist-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            });
        });
    });
    
    // AJAX for add to cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            fetch('wishlist.php', {
                method: 'POST',
                body: new FormData(this.closest('form')),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed bottom-4 right-4 bg-black text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            ${data.message}
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.classList.remove('translate-y-2', 'opacity-0');
                        notification.classList.add('translate-y-0', 'opacity-100');
                    }, 10);
                    
                    setTimeout(() => {
                        notification.classList.remove('translate-y-0', 'opacity-100');
                        notification.classList.add('translate-y-2', 'opacity-0');
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                }
            });
        });
    });
    
    // Mobile menu toggle (if needed)
    document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
        // Implement mobile menu toggle logic here
    });
</script>
</body>
</html>