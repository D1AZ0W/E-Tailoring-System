<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - TailorCraft</title>
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

            <h1>Choose Your Garment</h1>
            
            <?php if (!$is_logged_in): ?>
                <div style="background: #fff3cd; padding: 1rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; color: #856404;">
                        <strong>💡 Tip:</strong> <a href="register.php" style="color: #856404; text-decoration: underline;">Create an account</a> to save your measurements and track your orders!
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Category Filter -->
            <div class="category-filter">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="shirts">Shirts</button>
                <button class="filter-btn" data-category="pants">Pants</button>
                <button class="filter-btn" data-category="coats">Coats</button>
                <button class="filter-btn" data-category="traditional">Traditional</button>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <div class="product-card" data-category="shirts">
                    <img src="https://via.placeholder.com/300x400/4a5568/ffffff?text=Custom+Shirt" alt="Custom Shirt">
                    <div class="product-info">
                        <h3>Custom Shirt</h3>
                        <p>Design a crisp, custom-fit shirt for any occasion.</p>
                        <div class="price">NPR 5,000</div>
                        <a href="customize.php?product=shirt&price=5000" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card" data-category="pants">
                    <img src="https://via.placeholder.com/300x400/2d3748/ffffff?text=Custom+Pant" alt="Custom Pant">
                    <div class="product-info">
                        <h3>Custom Pant</h3>
                        <p>Perfectly tailored trousers for superior comfort and style.</p>
                        <div class="price">NPR 4,500</div>
                        <a href="customize.php?product=pant&price=4500" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card" data-category="coats">
                    <img src="https://via.placeholder.com/300x400/1a202c/ffffff?text=Custom+Coat" alt="Custom Coat">
                    <div class="product-info">
                        <h3>Custom Coat</h3>
                        <p>Refined coats and blazers for a distinguished, sharp look.</p>
                        <div class="price">NPR 15,000</div>
                        <a href="customize.php?product=coat&price=15000" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card" data-category="traditional">
                    <img src="https://via.placeholder.com/300x400/744210/ffffff?text=Daura+Suruwal" alt="Daura Suruwal">
                    <div class="product-info">
                        <h3>Daura Suruwal</h3>
                        <p>Impeccably tailored national dress, honoring tradition.</p>
                        <div class="price">NPR 12,000</div>
                        <a href="customize.php?product=daura-suruwal&price=12000" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card" data-category="shirts">
                    <img src="https://via.placeholder.com/300x400/4a5568/ffffff?text=Business+Shirt" alt="Business Shirt">
                    <div class="product-info">
                        <h3>Business Shirt</h3>
                        <p>Professional shirts for the modern workplace.</p>
                        <div class="price">NPR 6,000</div>
                        <a href="customize.php?product=business-shirt&price=6000" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card" data-category="coats">
                    <img src="https://via.placeholder.com/300x400/1a202c/ffffff?text=Wedding+Suit" alt="Wedding Suit">
                    <div class="product-info">
                        <h3>Wedding Suit</h3>
                        <p>Elegant suits for your special day.</p>
                        <div class="price">NPR 25,000</div>
                        <a href="customize.php?product=wedding-suit&price=25000" class="btn btn-primary">Customize</a>
                        <?php if (!$is_logged_in): ?>
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem; text-align: center;">*Login required to add to cart</p>
                        <?php endif; ?>
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
</body>
</html>
