<?php
session_start();

// This script displays the cart and handles updates.
// FIX: HTML is cleaned up to use CSS classes instead of inline styles.

// Handle cart updates (remove, update quantity, clear)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // User must be logged in to modify the cart
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = 'Please log in to manage your cart.';
        $_SESSION['message_type'] = 'error';
        header('Location: login.php?redirect=cart.php');
        exit;
    }

    switch ($_POST['action']) {
        case 'remove':
            $index = intval($_POST['index']);
            if (isset($_SESSION['cart'][$index])) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                $_SESSION['message'] = 'Item removed from cart.';
                $_SESSION['message_type'] = 'success';
            }
            break;
        case 'update_quantity':
            $index = intval($_POST['index']);
            $quantity = intval($_POST['quantity']);
            if (isset($_SESSION['cart'][$index]) && $quantity > 0 && $quantity <= 10) { // Limit max quantity
                $_SESSION['cart'][$index]['quantity'] = $quantity;
                $_SESSION['message'] = 'Cart updated successfully.';
                $_SESSION['message_type'] = 'success';
            }
            break;
        case 'clear':
            $_SESSION['cart'] = [];
            $_SESSION['message'] = 'Cart cleared.';
            $_SESSION['message_type'] = 'success';
            break;
    }
    header('Location: cart.php'); // Redirect to avoid form resubmission
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$cart_count = count($cart);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TailorCraft</title>
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
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'success'; ?>" style="margin-top: 100px;">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>

            <h1 style="margin-top: <?php echo isset($_SESSION['message']) ? '2rem' : '100px'; ?>;">Shopping Cart</h1>

            <?php if (empty($cart)): ?>
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>Start shopping to add items to your cart.</p>
                    <a href="products.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Details</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $index => $item): ?>
                                <?php 
                                $item_total = $item['total_price'] * $item['quantity'];
                                $total += $item_total;
                                ?>
                                <tr>
                                    <td>
                                        <div class="cart-product-info">
                                            <img src="https://via.placeholder.com/80x80" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="cart-product-image">
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td class="cart-item-details">
                                        <div>Fabric: <?php echo ucfirst(htmlspecialchars($item['fabric'])); ?></div>
                                        <div>Color: <?php echo ucfirst(htmlspecialchars($item['color'])); ?></div>
                                        <div>Size: <?php echo htmlspecialchars($item['size']); ?></div>
                                    </td>
                                    <td>
                                        <form method="POST" class="cart-quantity-form">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10" class="quantity-input">
                                            <button type="submit" class="btn-update">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <strong>NPR <?php echo number_format($item_total); ?></strong>
                                    </td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Remove this item from cart?')">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="cart-summary">
                        <div>
                            <form method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                            </form>
                        </div>
                        <div class="cart-totals">
                            <div class="total-price">Total: NPR <?php echo number_format($total); ?></div>
                            <div class="cart-actions">
                                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                <p>&copy; <?php echo date("Y"); ?> TailorCraft. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>