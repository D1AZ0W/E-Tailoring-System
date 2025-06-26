<?php
session_start();
include 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);
    
    // Validation
    $errors = [];
    
    if (empty($first_name)) $errors[] = 'First name is required.';
    if (empty($last_name)) $errors[] = 'Last name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters long.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
    
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = 'An account with this email already exists.';
                $message_type = 'error';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, password, address, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                
                $result = $stmt->execute([
                    $first_name,
                    $last_name,
                    $email,
                    $phone,
                    $hashed_password,
                    $address
                ]);
                
                if ($result) {
                    // Get the new user ID
                    $user_id = $db->lastInsertId();
                    
                    // Auto-login the user
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                    $_SESSION['user_email'] = $email;
                    
                    // Set success message
                    $_SESSION['message'] = 'Welcome to TailorGhar, ' . $first_name . '! Your account has been created successfully.';
                    $_SESSION['message_type'] = 'success';
                    
                    // Redirect to measurements page for new users
                    header('Location: measurements.php');
                    exit;
                } else {
                    $message = 'Error creating account. Please try again.';
                    $message_type = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = 'Registration error. Please try again.';
            $message_type = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - TailorGhar</title>
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
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-auth">
                <a href="login.php" class="btn-auth">Login</a>
                <a href="register.php" class="btn btn-primary active">Sign Up</a>
                <a href="cart.php" class="cart-link">Cart (0)</a>
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
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="form-container" style="max-width: 700px;">
                <div style="text-center; margin-bottom: 2rem;">
                    <h2>Create Your Account</h2>
                    <p style="color: #666; margin-top: 0.5rem;">Join TailorGhar and start your custom tailoring journey</p>
                </div>
                
                <form method="POST" action="">
                    <h3 style="border-bottom: 2px solid #2c3e50; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">Personal Information</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                                   placeholder="John">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                                   placeholder="Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   placeholder="john@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                   placeholder="+977-9841234567">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="2" 
                                  placeholder="Your full address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <h3 style="border-bottom: 2px solid #2c3e50; padding-bottom: 0.5rem; margin: 2rem 0 1.5rem;">Account Security</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="At least 6 characters">
                            <p style="font-size: 0.8rem; color: #666; margin-top: 0.25rem;">Must be at least 6 characters long</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="Repeat your password">
                        </div>
                    </div>
                    
                    <div style="margin: 2rem 0;">
                        <label style="display: flex; align-items: flex-start; font-size: 0.9rem;">
                            <input id="terms" name="terms" type="checkbox" required style="margin-right: 0.5rem; margin-top: 0.25rem;">
                            I agree to the  <a href="#" style="color: #2c3e50; text-decoration: underline;">Terms of Service</a> 
                            and <a href="#" style="color: #2c3e50; text-decoration: underline;">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                        Create Account
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p style="color: #666;">
                        Already have an account? 
                        <a href="login.php" style="color: #2c3e50; font-weight: 500; text-decoration: underline;">Sign in here</a>
                    </p>
                </div>
            </div>

            <!-- Benefits Section -->
            <div style="max-width: 800px; margin: 3rem auto 0;">
                <h3 style="text-align: center; margin-bottom: 2rem;">Member Benefits</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                    <div style="text-align: center; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="background: #e8f5e8; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.2rem;">👤</div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Personal Profile</h4>
                        <p style="color: #666; font-size: 0.8rem;">Save your measurements and preferences</p>
                    </div>
                    
                    <div style="text-align: center; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="background: #e3f2fd; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.2rem;">📋</div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Order History</h4>
                        <p style="color: #666; font-size: 0.8rem;">Track all your orders and reorder easily</p>
                    </div>
                    
                    <div style="text-align: center; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="background: #f3e5f5; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.2rem;">🏷️</div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Special Offers</h4>
                        <p style="color: #666; font-size: 0.8rem;">Exclusive discounts and early access</p>
                    </div>
                    
                    <div style="text-align: center; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="background: #fff3e0; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.2rem;">🎯</div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Priority Support</h4>
                        <p style="color: #666; font-size: 0.8rem;">Faster response and dedicated assistance</p>
                    </div>
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
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // You can add visual feedback here
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
