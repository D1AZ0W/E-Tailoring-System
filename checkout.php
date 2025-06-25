<?php
session_start();
// This script handles the final order placement.

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to proceed with checkout.';
    $_SESSION['message_type'] = 'error';
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = 'Your cart is empty. Add some items before checkout.';
    $_SESSION['message_type'] = 'error';
    header('Location: products.php');
    exit;
}

include 'config/database.php';

$message = '';
$message_type = '';

// Calculate cart total
$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['total_price'] * $item['quantity'];
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();
        
        $order_number = 'TC' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        
        // Insert order into 'orders' table
        $stmt = $db->prepare("INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, total_amount, special_instructions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $order_number,
            $_POST['customer_name'],
            $_POST['customer_email'],
            $_POST['customer_phone'],
            $total,
            $_POST['special_instructions'] ?? ''
        ]);
        
        $order_id = $db->lastInsertId();
            
        // Insert each cart item into 'order_items' table
        $item_stmt = $db->prepare("INSERT INTO order_items (order_id, product_name, fabric, color, size, quantity, unit_price, total_price, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($cart as $item) {
            $item_stmt->execute([
                $order_id,
                $item['product_name'],
                $item['fabric'],
                $item['color'],
                $item['size'],
                $item['quantity'],
                $item['total_price'],
                $item['total_price'] * $item['quantity'],
                $item['special_instructions']
            ]);
        }
            
        $db->commit();
            
        // Clear cart
        $_SESSION['cart'] = [];
            
        $_SESSION['message'] = 'Order placed successfully! Your order number is: ' . $order_number;
        $_SESSION['message_type'] = 'success';
            
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        $db->rollback();
        // For a real project, you would log the error: error_log($e->getMessage());
        $message = 'Error placing order. Please try again.';
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
    <title>Checkout - TailorCraft</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
       </header>

    <main>
        <div class="container" style="margin-top: 100px;">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <h1>Checkout</h1>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
                <div style="background: #f8f9fa; padding: 2rem; border-radius: 10px;">
                    <h2>Order Summary</h2>
                    
                    <?php foreach ($cart as $item): ?>
                        <div style="border-bottom: 1px solid #eee; padding: 1rem 0; display: flex; justify-content: space-between;">
                            <div>
                                <h4><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</h4>
                                <p style="color: #666; font-size: 0.9rem; margin:0;">
                                    <?php echo ucfirst(htmlspecialchars($item['fabric'])); ?> • <?php echo ucfirst(htmlspecialchars($item['color'])); ?> • Size: <?php echo htmlspecialchars($item['size']); ?>
                                </p>
                            </div>
                            <strong>NPR <?php echo number_format($item['total_price'] * $item['quantity']); ?></strong>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="padding: 1rem 0; font-size: 1.2rem; font-weight: bold; text-align: right;">
                        Total: NPR <?php echo number_format($total); ?>
                    </div>
                </div>
                
                <div class="form-container" style="margin: 0; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h2>Customer Information</h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" required 
                                   value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_email">Email *</label>
                            <input type="email" id="customer_email" name="customer_email" required 
                                   value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_phone">Phone Number</label>
                            <input type="tel" id="customer_phone" name="customer_phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions (Optional)</label>
                            <textarea id="special_instructions" name="special_instructions" rows="3" placeholder="Any special delivery instructions or notes..."></textarea>
                        </div>
                        
                        <div style="background: #e3f2fd; padding: 1rem; border-radius: 5px; margin: 1rem 0; border-left: 4px solid #2196f3;">
                            <h4>Payment Information</h4>
                            <p style="color: #666; margin: 0.5rem 0;">Payment will be collected upon delivery or pickup.</p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        </footer>

    <script src="script.js"></script>
</body>
</html>