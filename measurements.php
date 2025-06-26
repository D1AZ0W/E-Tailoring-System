<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to save your measurements.';
    $_SESSION['message_type'] = 'error';
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

include 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("INSERT INTO customer_measurements (user_id, name, email, phone, neck, chest, sleeve_length, shoulder_width, waist, hip, pant_length, inseam, height, weight, special_notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $result = $stmt->execute([
            $user_id,
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['neck'],
            $_POST['chest'],
            $_POST['sleeve'],
            $_POST['shoulder'],
            $_POST['waist'],
            $_POST['hip'],
            $_POST['length'],
            $_POST['inseam'],
            $_POST['height'],
            $_POST['weight'],
            $_POST['special_notes']
        ]);
        
        if ($result) {
            $message = 'Measurements saved successfully! We will use these for your custom orders.';
            $message_type = 'success';
        } else {
            $message = 'Error saving measurements. Please try again.';
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
    <title>Measurements - TailorGhar</title>
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
                <li><a href="measurements.php" class="active">Measurements</a></li>
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
        </nav>
    </header>

    <main>
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php echo $_SESSION['message']; unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <div style="background: #e8f5e8; padding: 1rem; border-radius: 5px; margin-top: 100px; border-left: 4px solid #28a745;">
                <h3>👋 Welcome, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h3>
                <p>Let's get your measurements saved for the perfect fit on all your custom orders.</p>
            </div>

            <h1>Your Measurements</h1>

            <div class="form-container" style="max-width: 800px;">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>

                <div style="background: #e3f2fd; padding: 1rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #2196f3;">
                    <h3>📏 Measurement Guide</h3>
                    <p>For the most accurate measurements, we recommend having someone help you. All measurements should be taken in centimeters.</p>
                    <button type="button" onclick="toggleGuide()" style="background: none; border: none; color: #2196f3; text-decoration: underline; cursor: pointer;">View Detailed Guide</button>
                </div>
                
                <div id="measurement-guide" style="display: none; background: #f5f5f5; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4>Upper Body Measurements:</h4>
                            <ul>
                                <li><strong>Neck:</strong> Measure around the base of your neck</li>
                                <li><strong>Chest:</strong> Measure around the fullest part of your chest</li>
                                <li><strong>Sleeve:</strong> From shoulder to wrist with arm slightly bent</li>
                                <li><strong>Shoulder:</strong> From shoulder point to shoulder point across the back</li>
                            </ul>
                        </div>
                        <div>
                            <h4>Lower Body Measurements:</h4>
                            <ul>
                                <li><strong>Waist:</strong> Measure around your natural waistline</li>
                                <li><strong>Hip:</strong> Measure around the fullest part of your hips</li>
                                <li><strong>Pant Length:</strong> From waist to ankle (outside leg)</li>
                                <li><strong>Inseam:</strong> From crotch to ankle (inside leg)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="">
                    <h3 style="border-bottom: 2px solid #2c3e50; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Personal Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="height">Height (cm)</label>
                            <input type="number" id="height" name="height">
                        </div>
                    </div>

                    <h3 style="border-bottom: 2px solid #2c3e50; padding-bottom: 0.5rem; margin: 2rem 0 1.5rem;">Upper Body Measurements (inches)</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="neck">Neck *</label>
                            <input type="number" id="neck" name="neck" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="chest">Chest *</label>
                            <input type="number" id="chest" name="chest" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sleeve">Sleeve Length *</label>
                            <input type="number" id="sleeve" name="sleeve" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="shoulder">Shoulder Width *</label>
                            <input type="number" id="shoulder" name="shoulder" step="0.1" required>
                        </div>
                    </div>

                    <h3 style="border-bottom: 2px solid #2c3e50; padding-bottom: 0.5rem; margin: 2rem 0 1.5rem;">Lower Body Measurements (inches)</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="waist">Waist *</label>
                            <input type="number" id="waist" name="waist" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="hip">Hip *</label>
                            <input type="number" id="hip" name="hip" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="length">Pant Length (Outseam) *</label>
                            <input type="number" id="length" name="length" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="inseam">Inseam *</label>
                            <input type="number" id="inseam" name="inseam" step="0.1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="special_notes">Special Notes or Requirements</label>
                        <textarea id="special_notes" name="special_notes" rows="3" placeholder="Any specific fit preferences, body considerations, or special requirements..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Save Measurements</button>
                </form>
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
    <script>
        function toggleGuide() {
            const guide = document.getElementById('measurement-guide');
            guide.style.display = guide.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
