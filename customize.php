<?php
session_start();

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get product information
$product_name = isset($_GET['product']) ? $_GET['product'] : 'shirt';
$product_price = isset($_GET['price']) ? intval($_GET['price']) : 5000;

$product_info = [
    'shirt' => ['name' => 'Custom Shirt', 'image' => 'https://via.placeholder.com/600x800/4a5568/ffffff?text=Custom+Shirt'],
    'pant' => ['name' => 'Custom Pant', 'image' => 'https://via.placeholder.com/600x800/2d3748/ffffff?text=Custom+Pant'],
    'coat' => ['name' => 'Custom Coat', 'image' => 'https://via.placeholder.com/600x800/1a202c/ffffff?text=Custom+Coat'],
    'daura-suruwal' => ['name' => 'Daura Suruwal', 'image' => 'https://via.placeholder.com/600x800/744210/ffffff?text=Daura+Suruwal'],
    'business-shirt' => ['name' => 'Business Shirt', 'image' => 'https://via.placeholder.com/600x800/4a5568/ffffff?text=Business+Shirt'],
    'wedding-suit' => ['name' => 'Wedding Suit', 'image' => 'https://via.placeholder.com/600x800/1a202c/ffffff?text=Wedding+Suit']
];

$current_product = $product_info[$product_name] ?? $product_info['shirt'];

// Default fabric and color options
$fabrics = [
    ['id' => 1, 'name' => 'Cotton', 'price_modifier' => 0],
    ['id' => 2, 'name' => 'Linen', 'price_modifier' => 500],
    ['id' => 3, 'name' => 'Wool', 'price_modifier' => 2000],
    ['id' => 4, 'name' => 'Silk', 'price_modifier' => 3000]
];

$colors = [
    ['id' => 1, 'name' => 'White', 'hex_code' => '#FFFFFF'],
    ['id' => 2, 'name' => 'Navy Blue', 'hex_code' => '#1e3a8a'],
    ['id' => 3, 'name' => 'Charcoal', 'hex_code' => '#374151'],
    ['id' => 4, 'name' => 'Light Grey', 'hex_code' => '#9ca3af'],
    ['id' => 5, 'name' => 'Black', 'hex_code' => '#000000']
];

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize - TailorCraft</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">TailorCraft</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php" class="active">Designs</a></li>
                <li><a href="measurements.php">Measurements</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-auth">
                <?php if ($is_logged_in): ?>
                    <span class="user-welcome">Welcome, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                    <a href="logout.php" class="btn-auth">Logout</a>
                    <a href="cart.php" class="cart-link">Cart (<?php echo $cart_count; ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="btn-auth">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                    <a href="cart.php" class="cart-link">Cart (<?php echo $cart_count; ?>)</a>
                <?php endif; ?>
            </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?>" style="margin-top: 100px;">
                    <?php echo $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <div style="margin: 100px 0 20px;">
                <a href="products.php" style="color: #666; text-decoration: none;">&larr; Back to Products</a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
                <!-- Left: Image Preview -->
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <img id="product-preview-image" src="<?php echo $current_product['image']; ?>" alt="Product Preview" style="width: 100%; border-radius: 10px;">
                </div>
                
                <!-- Right: Customization Options -->
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #2c3e50; margin-bottom: 1rem;"><?php echo htmlspecialchars($current_product['name']); ?></h2>
                    <p style="font-size: 1.5rem; color: #2c3e50; margin-bottom: 2rem;" id="product-price">NPR <?php echo number_format($product_price); ?></p>
                    
                    <form id="customize-form" method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($current_product['name']); ?>">
                        <input type="hidden" name="base_price" value="<?php echo $product_price; ?>">
                        
                        <!-- Fabric Selection -->
                        <div style="margin-bottom: 2rem;">
                            <h3 style="font-size: 1.2rem; font-weight: 600; color: #2c3e50; margin-bottom: 1rem;">1. Select Fabric</h3>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                <?php foreach ($fabrics as $index => $fabric): ?>
                                    <label style="cursor: pointer;">
                                        <input type="radio" name="fabric" value="<?php echo strtolower($fabric['name']); ?>" 
                                               data-price-mod="<?php echo $fabric['price_modifier']; ?>" 
                                               style="display: none;" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                        <div class="swatch fabric-swatch <?php echo $index === 0 ? 'selected' : ''; ?>" 
                                             style="padding: 1rem; border: 2px solid #ddd; border-radius: 5px; text-align: center; min-width: 80px;">
                                            <div style="font-size: 0.9rem; font-weight: 500;"><?php echo $fabric['name']; ?></div>
                                            <?php if ($fabric['price_modifier'] > 0): ?>
                                                <div style="font-size: 0.8rem; color: #28a745;">+NPR <?php echo $fabric['price_modifier']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Color Selection -->
                        <div style="margin-bottom: 2rem;">
                            <h3 style="font-size: 1.2rem; font-weight: 600; color: #2c3e50; margin-bottom: 1rem;">2. Select Color</h3>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                <?php foreach ($colors as $index => $color): ?>
                                    <label style="cursor: pointer;">
                                        <input type="radio" name="color" value="<?php echo strtolower($color['name']); ?>" 
                                               style="display: none;" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                        <div class="swatch color-swatch <?php echo $index === 0 ? 'selected' : ''; ?>" 
                                             style="width: 50px; height: 50px; border-radius: 5px; background-color: <?php echo $color['hex_code']; ?>; border: 2px solid #ddd;">
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Size Selection -->
                        <div class="form-group">
                            <label for="size">3. Select Size</label>
                            <select name="size" id="size" required>
                                <option value="">Choose Size</option>
                                <option value="XS">Extra Small (XS)</option>
                                <option value="S">Small (S)</option>
                                <option value="M">Medium (M)</option>
                                <option value="L">Large (L)</option>
                                <option value="XL">Extra Large (XL)</option>
                                <option value="XXL">Double Extra Large (XXL)</option>
                                <option value="Custom">Custom Size</option>
                            </select>
                        </div>

                        <!-- Special Instructions -->
                        <div class="form-group">
                            <label for="special_instructions">4. Special Instructions (Optional)</label>
                            <textarea name="special_instructions" id="special_instructions" rows="3" placeholder="Any special requirements or modifications..."></textarea>
                        </div>

                        <!-- Add to Cart Button -->
                        <?php if ($user_logged_in): ?>
                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Add to Cart</button>
                        <?php else: ?>
                            <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px; margin-bottom: 1rem;">
                                <p style="margin-bottom: 1rem; color: #666;">Please log in to add items to your cart</p>
                                <div style="display: flex; gap: 1rem; justify-content: center;">
                                    <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-primary">Login</a>
                                    <a href="register.php" class="btn btn-secondary">Sign Up</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>

                    <!-- Measurement Reminder -->
                    <div style="margin-top: 2rem; padding: 1rem; background: #e3f2fd; border-radius: 5px; border-left: 4px solid #2196f3;">
                        <p style="font-weight: 500; color: #1565c0;">📏 Need custom measurements?</p>
                        <p style="font-size: 0.9rem; color: #1976d2; margin-top: 0.5rem;">
                            <a href="measurements.php" style="color: #1976d2; text-decoration: underline;">Click here to provide your measurements</a> for the perfect fit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TailorCraft</h3>
                    <p>Bespoke tailoring for the modern individual.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Designs</a></li>
                        <li><a href="measurements.php">Measurements</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TailorCraft. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePrice = <?php echo $product_price; ?>;
        const priceElement = document.getElementById('product-price');
        
        function updatePrice() {
            const selectedFabric = document.querySelector('input[name="fabric"]:checked');
            const fabricMod = selectedFabric ? parseInt(selectedFabric.dataset.priceMod) : 0;
            const totalPrice = basePrice + fabricMod;
            priceElement.textContent = 'NPR ' + totalPrice.toLocaleString();
        }
        
        // Handle fabric selection
        document.querySelectorAll('input[name="fabric"]').forEach(option => {
            option.addEventListener('change', function() {
                document.querySelectorAll('.fabric-swatch').forEach(swatch => {
                    swatch.classList.remove('selected');
                });
                this.parentElement.querySelector('.swatch').classList.add('selected');
                updatePrice();
            });
        });
        
        // Handle color selection
        document.querySelectorAll('input[name="color"]').forEach(option => {
            option.addEventListener('change', function() {
                document.querySelectorAll('.color-swatch').forEach(swatch => {
                    swatch.classList.remove('selected');
                });
                this.parentElement.querySelector('.swatch').classList.add('selected');
            });
        });
        
        updatePrice();
    });
    </script>

    <style>
    @media (max-width: 768px) {
        .container > div:nth-child(2) {
            grid-template-columns: 1fr !important;
            gap: 2rem !important;
        }
    }
    </style>
</body>
</html>
