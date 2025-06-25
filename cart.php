<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
    header('Location: cart.php');
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
    <title>Shopping Cart - ETailor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">ETailor</a>
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
                <div class="container">
                <center>
                    <img src="https://media.istockphoto.com/id/1987775073/vector/shopping-cart-black-line-drawing-icon.jpg?s=612x612&w=0&k=20&c=zZP0Tl3NW6Q96YuaHs5UQCN7E3CGdfI30-JUcM8Z0F8=" style="width: 150px; height: 100px; margin-top: 50px;">
                    <h2>Your cart is empty</h2>
                    <a href="products.php" class="btn btn-primary">Browse Products</a><br>
                </center>
                </div>
            <?php else: ?>
                <div class="container">
                    <table class="cart-table" border=1>
                        <thead>
                            <tr>
                                <th>S.No</th>
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
                                        <?php echo $index + 1; ?>
                                    </td>
                                    <td>
                                        <div class="cart-product-info">
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
                    <br>
                    <div class="cart-summary">
                        <div class="total-price">Total💸: <br>NPR <?php echo number_format($total); ?></div><br>
                        <div>
                            <form method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                            </form>
                        </div>
                        <br>
                            <div class="cart-actions">
                                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                            </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <br><br>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ETailor</h3>
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
                <p>&copy; <?php echo date("Y"); ?> ETailor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>