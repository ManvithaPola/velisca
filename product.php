<?php
// Include database connection
require_once 'db_connect.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate product ID
if ($product_id <= 0) {
    header("Location: products.php");
    exit();
}

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();

// Process Add to Cart
if (isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity > 0) {
        // Check if product already exists in cart
        $check_sql = "SELECT cart_id, quantity FROM cart WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing cart item
            $cart_item = $check_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            $update_sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_id']);
            $update_stmt->execute();
        } else {
            // Add new cart item
            $insert_sql = "INSERT INTO cart (product_id, quantity) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ii", $product_id, $quantity);
            $insert_stmt->execute();
        }
        
        $cart_message = "Product added to cart successfully!";
    } else {
        $cart_error = "Please enter a valid quantity.";
    }
}

// Process Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    // Check if product already exists in wishlist
    $check_sql = "SELECT wishlist_id FROM wishlist WHERE product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows == 0) {
        // Add to wishlist
        $insert_sql = "INSERT INTO wishlist (product_id) VALUES (?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("i", $product_id);
        $insert_stmt->execute();
        
        $wishlist_message = "Product added to wishlist!";
    } else {
        $wishlist_message = "Product is already in your wishlist!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | Premium Fashion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font for luxury feel -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #000000;
            --color-accent-1: #EFE1CB;
            --color-accent-2: #545454;
            --color-white: #FFFFFF;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--color-white);
            color: var(--color-primary);
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: var(--color-white);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--color-accent-2);
        }
        
        .btn-secondary {
            background-color: var(--color-white);
            color: var(--color-primary);
            border: 1px solid var(--color-primary);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: var(--color-accent-1);
        }
        
        /* Image zoom effect */
        .zoom-container {
            overflow: hidden;
        }
        
        .zoom-image {
            transition: transform 0.5s ease;
        }
        
        .zoom-container:hover .zoom-image {
            transform: scale(1.2);
        }
        
        /* Toast notification styling */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background-color: var(--color-accent-1);
            color: var(--color-primary);
            border-left: 4px solid var(--color-primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .toast.show {
            opacity: 1;
        }
        
        .toast.error {
            background-color: #FFF1F1;
            border-left-color: #FF4747;
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-8 text-gray-500">
            <ol class="flex flex-wrap">
                <li><a href="index.html" class="hover:text-black">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="categories.php" class="hover:text-black">Collections</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="font-medium text-black"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <!-- Product Display -->
<!-- Product Display -->
<div class="flex flex-col lg:flex-row gap-6 lg:gap-10 xl:gap-12 max-w-7xl mx-auto px-4 py-10">
    <!-- Left: Product Image -->
    <div class="w-full lg:w-1/2">
        <div class="bg-[#F7F7F7] rounded-xl overflow-hidden shadow-md transition duration-300 hover:shadow-lg">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="w-full h-auto object-contain p-6 transition-transform duration-300 hover:scale-105" 
                 style="max-height: 65vh; min-height: 300px;">
        </div>
    </div>

    <!-- Right: Product Info -->
    <div class="w-full lg:w-1/2 space-y-6 text-[#333]">
        <!-- Title and Price -->
        <div>
            <h1 class="text-3xl font-bold mb-2 tracking-tight text-[#222]">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>
            <div class="flex items-center justify-between text-xl font-semibold">
                <span><?php echo number_format($product['price'], 2); ?> €</span>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="text-green-600 text-sm flex items-center">
                        <i class="fas fa-check-circle mr-1"></i> In Stock
                    </span>
                <?php else: ?>
                    <span class="text-red-500 text-sm flex items-center">
                        <i class="fas fa-times-circle mr-1"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description -->
        <div class="text-sm leading-relaxed text-[#555] border-t pt-4">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </div>

        <!-- Actions -->
        <form method="post" action="" class="space-y-4">
            <!-- Quantity -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-[#444] font-medium">Quantity:</span>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <button type="button" id="decrease-qty"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-minus text-sm"></i>
                    </button>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" 
                           max="<?php echo $product['stock_quantity']; ?>"
                           class="w-12 text-center text-sm focus:outline-none py-1">
                    <button type="button" id="increase-qty"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" name="add_to_cart"
                        class="bg-black text-white px-6 py-3 rounded-md font-semibold text-sm tracking-wide hover:bg-[#222] transition"
                        <?php echo ($product['stock_quantity'] <= 0) ? 'disabled class="opacity-50 cursor-not-allowed"' : ''; ?>>
                    <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                </button>

                <button type="submit" name="add_to_wishlist"
                        class="border border-gray-400 text-gray-800 px-6 py-3 rounded-md font-medium text-sm hover:bg-gray-100 transition">
                    <i class="fas fa-heart mr-2"></i> Add to Wishlist
                </button>
            </div>
        </form>

        <!-- Shipping Info -->
        <div class="text-xs text-gray-600 border-t pt-4 space-y-1">
            <div class="flex items-center gap-2">
                <i class="fas fa-truck text-sm"></i> <span>Free shipping on orders over 50€</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-undo-alt text-sm"></i> <span>30-day return policy</span>
            </div>
        </div>
    </div>
</div>

    </div>
    
    <!-- Toast Notifications -->
    <?php if (isset($cart_message)): ?>
        <div id="cart-toast" class="toast">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <?php echo $cart_message; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($wishlist_message)): ?>
        <div id="wishlist-toast" class="toast">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
                <?php echo $wishlist_message; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($cart_error)): ?>
        <div id="error-toast" class="toast error">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <?php echo $cart_error; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <script>
        // Quantity controls
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        const maxQuantity = <?php echo $product['stock_quantity']; ?>;
        
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        // Toast notifications
        const toasts = document.querySelectorAll('.toast');
        
        if (toasts.length > 0) {
            toasts.forEach(toast => {
                // Show toast
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                
                // Hide toast after 5 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    // Remove from DOM after fade out
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 5000);
            });
        }
    </script>
</body>
</html>