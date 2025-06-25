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
    <title>Gallery - E-Tailor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">E-Tailor</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Designs</a></li>
                <li><a href="measurements.php">Measurements</a></li>
                <li><a href="gallery.php" class="active">Gallery</a></li>
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

            <h1>Our Work Gallery</h1>
            
            <!-- Gallery Categories -->
            <div class="category-filter">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="shirts">Shirts</button>
                <button class="filter-btn" data-category="pants">Pants</button>
                <button class="filter-btn" data-category="coats">Coats</button>
                <button class="filter-btn" data-category="traditional">Traditional</button>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <div class="gallery-item" data-category="shirts">
                    <img src="img/buisness_shirt.png" alt="Custom Business Shirt" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Custom Business Shirt</h3>
                        <p>Premium cotton with mother-of-pearl buttons</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=shirt&price=5000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="shirts">
                    <img src="img/linenshirt.png" alt="Linen Shirt" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Casual Linen Shirt</h3>
                        <p>Breathable linen for summer comfort</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=shirt&price=5000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="pants">
                    <img src="img/formal_pants.png" alt="Formal Pants" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Formal Pants</h3>
                        <p>Wool blend with perfect crease</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=pant&price=4500" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="pants">
                    <img src="img/chinos.png" alt="Casual Chinos" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Casual Chinos</h3>
                        <p>Cotton twill for everyday wear</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=pant&price=4500" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="coats">
                    <img src="img/buisness_suit.png" alt="Business Suit" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Business Suit</h3>
                        <p>Two-piece wool suit with silk lining</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=coat&price=15000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="coats">
                    <img src="img/wedding_suit.png" alt="Wedding Blazer" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Wedding Blazer</h3>
                        <p>Elegant blazer with custom embroidery</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=wedding-suit&price=25000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="traditional" style="background-color: #eaeaea;">
                    <img src="img/daura1.png" alt="Traditional Daura Suruwal">
                    <div class="gallery-overlay">
                        <h3>Traditional Daura Suruwal</h3>
                        <p>Authentic Nepali formal wear</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=daura-suruwal&price=12000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="gallery-item" data-category="traditional">
                    <img src="img/wedding_daura.jpg" alt="Wedding Daura Suruwal" style="background-color: #eaeaea;">
                    <div class="gallery-overlay">
                        <h3>Wedding Daura Suruwal</h3>
                        <p>Silk with gold thread work</p>
                        <?php if ($is_logged_in): ?>
                            <a href="customize.php?product=daura-suruwal&price=12000" class="btn btn-secondary" style="margin-top: 1rem;">Order Similar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Customer Testimonials -->
            <section class="testimonials">
                <h2>What Our Customers Say</h2>
                <div class="testimonials-grid">
                    <div class="testimonial">
                        <div class="stars">★★★★★</div>
                        <p>"The attention to detail is incredible. My suit fits perfectly and the quality is outstanding."</p>
                        <cite>- Rajesh Sharma, Business Executive</cite>
                    </div>
                    <div class="testimonial">
                        <div class="stars">★★★★★</div>
                        <p>"Best tailoring service in Kathmandu. The traditional wear they made for my wedding was perfect."</p>
                        <cite>- Priya Thapa, Teacher</cite>
                    </div>
                    <div class="testimonial">
                        <div class="stars">★★★★★</div>
                        <p>"Professional service, timely delivery, and excellent craftsmanship. Highly recommended!"</p>
                        <cite>- Amit Gurung, Entrepreneur</cite>
                    </div>
                </div>
            </section>

            <?php if (!$is_logged_in): ?>
                <!-- Call to Action for Non-logged Users -->
                <section style="background: #f8f9fa; padding: 3rem 0; margin-top: 3rem; border-radius: 10px; text-align: center;">
                    <h3>Ready to Create Your Perfect Garment?</h3>
                    <p style="margin: 1rem 0; color: #666;">Join E-Tailor today and start your custom tailoring journey!</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="register.php" class="btn btn-primary">Sign Up Now</a>
                        <a href="products.php" class="btn btn-secondary">Browse Products</a>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>E-Tailor</h3>
                    <p>Tailored Clothing, Your Way.</p>
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
                <p>&copy; 2024 E-Tailor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
