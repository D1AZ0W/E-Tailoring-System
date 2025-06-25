<?php
session_start();
// This script handles user login.

include 'config/database.php';

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $message = 'Please fill in all fields.';
        $message_type = 'error';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        try {
            // Check user credentials
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                session_regenerate_id(true); // Prevent session fixation
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect user back to the page they were on, or to index.php
                $redirect = $_GET['redirect'] ?? 'index.php';
                
                $_SESSION['message'] = 'Welcome back, ' . htmlspecialchars($user['first_name']) . '!';
                $_SESSION['message_type'] = 'success';
                
                header('Location: ' . $redirect);
                exit;
            } else {
                $message = 'Invalid email or password.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Login error. Please try again.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TailorCraft</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        </header>

    <main>
        <div class="container" style="padding-top: 100px;">
            <div class="form-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2>Welcome Back</h2>
                    <p style="color: #666;">Sign in to your TailorCraft account</p>
                </div>
                
                <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . htmlspecialchars(urlencode($_GET['redirect'])) : ''; ?>">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               placeholder="your@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Sign In
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <p style="color: #666;">
                        Don't have an account? 
                        <a href="register.php" style="color: #2c3e50; font-weight: 500;">Create one here</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        </footer>

    <script src="script.js"></script>
</body>
</html>