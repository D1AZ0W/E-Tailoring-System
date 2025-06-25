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
    <title>TailorCraft - Bespoke Tailoring</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">TailorCraft</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="products.php">Designs</a></li>
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
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?>" style="margin-top: 80px;">
                <?php echo $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Bespoke Tailoring, Redefined.</h1>
                <p>From classic suits to traditional Daura Surwals, we craft premium garments with precision and a perfect fit, guaranteed.</p>
                <?php if ($is_logged_in): ?>
                    <a href="products.php" class="btn btn-primary">Start Your Design</a>
                    <p style="margin-top: 1rem; opacity: 0.8;">Welcome back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>! Ready to create something amazing?</p>
                <?php else: ?>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="products.php" class="btn btn-primary">Browse Designs</a>
                        <a href="register.php" class="btn btn-secondary">Join TailorCraft</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <h2>Our Commitment</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🧵</div>
                        <h3>Premium Materials</h3>
                        <p>Only the finest selection of globally-sourced, high-quality fabrics.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">✂️</div>
                        <h3>Expert Craftsmanship</h3>
                        <p>Every garment is tailored with meticulous attention to detail and your vision.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📏</div>
                        <h3>Guaranteed Fit</h3>
                        <p>Follow our simple guide to provide your measurements for a perfect fit.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="products">
            <div class="container">
                <h2>Featured Designs</h2>
                <div class="products-grid">
                    <div class="product-card">
                        <img src="https://via.placeholder.com/300x400/4a5568/ffffff?text=Custom+Shirt" alt="Custom Shirt">
                        <div class="product-info">
                            <h3>Custom Shirts</h3>
                            <p>Starting from NPR 5,000</p>
                            <a href="customize.php?product=shirt&price=5000" class="btn btn-secondary">View Collection</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/300x400/2d3748/ffffff?text=Tailored+Pants" alt="Tailored Pants">
                        <div class="product-info">
                            <h3>Tailored Pants</h3>
                            <p>Starting from NPR 4,500</p>
                            <a href="customize.php?product=pant&price=4500" class="btn btn-secondary">View Collection</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/300x400/1a202c/ffffff?text=Elegant+Coats" alt="Elegant Coats">
                        <div class="product-info">
                            <h3>Elegant Coats</h3>
                            <p>Starting from NPR 15,000</p>
                            <a href="customize.php?product=coat&price=15000" class="btn btn-secondary">View Collection</a>
                        </div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/300x400/744210/ffffff?text=Daura+Suruwal" alt="Daura Suruwal">
                        <div class="product-info">
                            <h3>Daura Suruwal</h3>
                            <p>Starting from NPR 12,000</p>
                            <a href="customize.php?product=daura-suruwal&price=12000" class="btn btn-secondary">View Collection</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Member Benefits Section (for logged-in users) -->
        <?php if ($is_logged_in): ?>
        <section class="member-benefits" style="background: #e8f5e8; padding: 60px 0;">
            <div class="container">
                <h2>Your Member Benefits</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">👤</div>
                        <h3>Personal Profile</h3>
                        <p>Your measurements and preferences are saved for quick ordering.</p>
                        <a href="measurements.php" class="btn btn-secondary" style="margin-top: 1rem;">Manage Measurements</a>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📋</div>
                        <h3>Order History</h3>
                        <p>Track all your orders and easily reorder your favorites.</p>
                        <a href="orders.php" class="btn btn-secondary" style="margin-top: 1rem;">View Orders</a>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h3>Priority Support</h3>
                        <p>Get faster response times and dedicated customer service.</p>
                        <a href="contact.php" class="btn btn-secondary" style="margin-top: 1rem;">Contact Support</a>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Testimonials -->
        <section class="testimonials">
            <div class="container">
                <h2>What Our Customers Say</h2>
                <div class="testimonials-grid">
                    <div class="testimonial">
                        <p>"Exceptional quality and perfect fit. The attention to detail is remarkable."</p>
                        <cite>- Rajesh Sharma</cite>
                    </div>
                    <div class="testimonial">
                        <p>"Best tailoring service in Kathmandu. Highly recommended for custom suits."</p>
                        <cite>- Priya Thapa</cite>
                    </div>
                    <div class="testimonial">
                        <p>"Professional service and beautiful traditional wear. Very satisfied!"</p>
                        <cite>- Amit Gurung</cite>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TailorCraft</h3>
                    <p>Bespoke tailoring for the modern individual. Handcrafted with passion and precision.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Designs</a></li>
                        <li><a href="measurements.php">Measurements</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="products.php#shirts">Custom Shirts</a></li>
                        <li><a href="products.php#pants">Tailored Pants</a></li>
                        <li><a href="products.php#coats">Elegant Coats</a></li>
                        <li><a href="products.php#traditional">Daura Suruwal</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p>Kathmandu, Nepal<br>
                    contact@tailorcraft.com<br>
                    +977-1-4567890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TailorCraft. A Tribhuwan University Project.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
