<?php
session_start();
include 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        
        $result = $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'] ?? '',
            $_POST['subject'],
            $_POST['message']
        ]);
        
        if ($result) {
            $message = 'Thank you for your message! We will get back to you within 24 hours.';
            $message_type = 'success';
        } else {
            $message = 'Error sending message. Please try again.';
            $message_type = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Database error. Please try again later.';
        $message_type = 'error';
    }
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TailorGhar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">TailorGhar</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Designs</a></li>
                <li><a href="measurements.php">Measurements</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
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
        </nav>
    </header>

    <main>
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" style="margin-top: 100px;"><?php echo $message; ?></div>
            <?php endif; ?>

            <h1>Get In Touch</h1>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h2>Contact Information</h2>
                    
                    <div style="margin: 2rem 0;">
                        <h4>📍 Address</h4>
                        <p>Bagbazar, Kathmandu<br>Nepal </p>
                    </div>
                    
                    <div style="margin: 2rem 0;">
                        <h4>📞 Phone</h4>
                        <p>+977-1-4567890<br>+977-9841234567</p>
                    </div>
                    
                    <div style="margin: 2rem 0;">
                        <h4>✉️ Email</h4>
                        <p>contact_tailorghar@gmail.com<br>info_tailorghar@gmail.com</p>
                    </div>
                    
                    <div style="margin: 2rem 0;">
                        <h4>🕒 Business Hours</h4>
                        <p>Sunday - Friday: 10:00 AM - 7:00 PM<br>Saturday: 12:00 PM - 4:00 PM<br></p>
                    </div>
                </div>
                
                <div class="form-container" style="margin: 0;">
                    <h2>Send us a Message</h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo $is_logged_in ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Custom Order">Custom Order</option>
                                <option value="Measurement Help">Measurement Help</option>
                                <option value="Order Status">Order Status</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Feedback">Feedback</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="5" required placeholder="Please describe your inquiry in detail..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TailorGhar</h3>
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
                <p>&copy; 2024 TailorGhar. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>

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
