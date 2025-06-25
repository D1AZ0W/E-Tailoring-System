<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to view your orders.';
    $_SESSION['message_type'] = 'error';
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

try {
    $stmt = $db->prepare("
        SELECT o.*, 
               COUNT(oi.id) as item_count,
               GROUP_CONCAT(oi.product_name SEPARATOR ', ') as products
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Error loading orders. Please try again.';
    $message_type = 'error';
    $orders = [];
}

foreach ($orders as &$order) {
    if (!isset($order['status']) || empty($order['status'])) {
        $order['status'] = 'pending';
    }
}
unset($order); 

$order_details = null;
$order_items = [];
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $order_id = intval($_GET['view']);
    
    try {

        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        $order_details = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order_details) {

            $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY created_at");
            $stmt->execute([$order_id]);
            $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $message = 'Error loading order details.';
        $message_type = 'error';
    }
}

if ($order_details && (!isset($order_details['status']) || empty($order_details['status']))) {
    $order_details['status'] = 'pending';
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

$status_config = [
    'pending' => ['color' => '#ffc107', 'label' => 'Pending', 'icon' => '⏳'],
    'confirmed' => ['color' => '#17a2b8', 'label' => 'Confirmed', 'icon' => '✅'],
    'in_progress' => ['color' => '#007bff', 'label' => 'In Progress', 'icon' => '🔨'],
    'completed' => ['color' => '#28a745', 'label' => 'Completed', 'icon' => '🎉'],
    'cancelled' => ['color' => '#dc3545', 'label' => 'Cancelled', 'icon' => '❌']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - E-Tailor</title>
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
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
            </ul>
            <div class="nav-auth">
                <span class="user-welcome">Welcome, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                <a href="logout.php" class="btn-auth">Logout</a>
                <a href="cart.php" class="cart-link">Cart (<?php echo $cart_count; ?>)</a>
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
                <div class="alert alert-<?php echo $message_type; ?>" style="margin-top: 100px;"><?php echo $message; ?></div>
            <?php endif; ?>

            <div style="background: #e8f5e8; padding: 1rem; border-radius: 5px; margin: 100px 0 2rem; border-left: 4px solid #28a745;">
                <h3>👋 Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h3>
                <p>Here's your complete order history and current order status.</p>
            </div>

            <?php if ($order_details): ?>

                <div style="margin-bottom: 2rem;">
                    <a href="orders.php" style="color: #666; text-decoration: none;">&larr; Back to All Orders</a>
                </div>

                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #f8f9fa; padding-bottom: 1rem;">
                        <div>
                            <h2 style="margin: 0; color: #2c3e50;">Order #<?php echo htmlspecialchars($order_details['order_number']); ?></h2>
                            <p style="color: #666; margin: 0.5rem 0;">Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order_details['created_at'])); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <?php $status = $status_config[$order_details['status']]; ?>
                            <div style="background: <?php echo $status['color']; ?>; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; display: inline-block;">
                                <?php echo $status['icon']; ?> <?php echo $status['label']; ?>
                            </div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #2c3e50; margin-top: 0.5rem;">
                                NPR <?php echo number_format($order_details['total_amount']); ?>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                        <div>
                            <h4 style="color: #2c3e50; margin-bottom: 1rem;">Customer Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order_details['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['customer_email']); ?></p>
                            <?php if ($order_details['customer_phone']): ?>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['customer_phone']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 style="color: #2c3e50; margin-bottom: 1rem;">Order Status Timeline</h4>
                            <div style="position: relative;">
                                <?php
                                $statuses = ['pending', 'confirmed', 'in_progress', 'completed'];
                                $current_status_index = array_search($order_details['status'], $statuses);
                                if ($order_details['status'] === 'cancelled') {
                                    $current_status_index = -1;
                                }
                                ?>
                                <?php foreach ($statuses as $index => $status_key): ?>
                                    <?php 
                                    $is_active = $index <= $current_status_index;
                                    $is_cancelled = $order_details['status'] === 'cancelled';
                                    ?>
                                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem; opacity: <?php echo ($is_active && !$is_cancelled) ? '1' : '0.3'; ?>;">
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo ($is_active && !$is_cancelled) ? $status_config[$status_key]['color'] : '#ddd'; ?>; margin-right: 1rem;"></div>
                                        <span><?php echo $status_config[$status_key]['label']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($order_details['status'] === 'cancelled'): ?>
                                    <div style="display: flex; align-items: center; margin-bottom: 0.5rem; color: #dc3545;">
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background: #dc3545; margin-right: 1rem;"></div>
                                        <span><strong>Cancelled</strong></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($order_details['special_instructions']): ?>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                            <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Special Instructions</h4>
                            <p style="margin: 0;"><?php echo htmlspecialchars($order_details['special_instructions']); ?></p>
                        </div>
                    <?php endif; ?>

                    <h4 style="color: #2c3e50; margin-bottom: 1rem;">Order Items</h4>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #ddd;">Product</th>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #ddd;">Specifications</th>
                                    <th style="padding: 1rem; text-align: center; border-bottom: 1px solid #ddd;">Quantity</th>
                                    <th style="padding: 1rem; text-align: right; border-bottom: 1px solid #ddd;">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 1rem;">
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <div style="font-size: 0.9rem; color: #666;">
                                                <?php if ($item['fabric_name']): ?>
                                                    <div>Fabric: <?php echo ucfirst($item['fabric_name']); ?></div>
                                                <?php endif; ?>
                                                <?php if ($item['color_name']): ?>
                                                    <div>Color: <?php echo ucfirst($item['color_name']); ?></div>
                                                <?php endif; ?>
                                                <?php if ($item['size']): ?>
                                                    <div>Size: <?php echo $item['size']; ?></div>
                                                <?php endif; ?>
                                                <?php if ($item['special_instructions']): ?>
                                                    <div style="margin-top: 0.5rem; font-style: italic;">
                                                        Note: <?php echo htmlspecialchars($item['special_instructions']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <?php echo $item['quantity']; ?>
                                        </td>
                                        <td style="padding: 1rem; text-align: right;">
                                            <strong>NPR <?php echo number_format($item['total_price']); ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot style="background: #f8f9fa;">
                                <tr>
                                    <td colspan="3" style="padding: 1rem; text-align: right; font-weight: bold;">Total:</td>
                                    <td style="padding: 1rem; text-align: right; font-weight: bold; font-size: 1.2rem;">
                                        NPR <?php echo number_format($order_details['total_amount']); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div style="text-align: center; margin: 2rem 0;">
                    <?php if ($order_details['status'] === 'pending'): ?>
                        <p style="color: #666; margin-bottom: 1rem;">Need to make changes or cancel your order?</p>
                        <a href="contact.php" class="btn btn-secondary">Contact Support</a>
                    <?php elseif ($order_details['status'] === 'completed'): ?>
                        <p style="color: #28a745; margin-bottom: 1rem;">🎉 Your order is complete! We hope you love your custom garments.</p>
                        <a href="products.php" class="btn btn-primary">Order Again</a>
                    <?php else: ?>
                        <p style="color: #666; margin-bottom: 1rem;">Questions about your order?</p>
                        <a href="contact.php" class="btn btn-secondary">Contact Support</a>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <h1>My Orders</h1>

                <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 4rem 0; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                        <h3>No orders yet</h3>
                        <p style="color: #666; margin: 1rem 0;">Start shopping to place your first custom order.</p>
                        <a href="products.php" class="btn btn-primary">Browse Products</a>
                    </div>
                <?php else: ?>
                    <div style="display: grid; gap: 1.5rem;">
                        <?php foreach ($orders as $order): ?>
                            <?php 
                                $order_status = $order['status'] ?? 'pending';
                                $status = $status_config[$order_status] ?? $status_config['pending']; 
                            ?>
                            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-left: 4px solid <?php echo $status['color']; ?>;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="margin: 0; color: #2c3e50;">Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                        <p style="color: #666; margin: 0.25rem 0; font-size: 0.9rem;">
                                            <?php echo date('F j, Y', strtotime($order['created_at'])); ?> • 
                                            <?php echo $order['item_count']; ?> item<?php echo $order['item_count'] != 1 ? 's' : ''; ?>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="background: <?php echo $status['color']; ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.8rem; font-weight: bold; margin-bottom: 0.5rem;">
                                            <?php echo $status['icon']; ?> <?php echo $status['label']; ?>
                                        </div>
                                        <div style="font-weight: bold; color: #2c3e50;">
                                            NPR <?php echo number_format($order['total_amount']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="color: #666; margin-bottom: 1rem; font-size: 0.9rem;">
                                    <strong>Products:</strong> <?php echo htmlspecialchars($order['products']); ?>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="font-size: 0.9rem; color: #666;">
                                        Customer: <?php echo htmlspecialchars($order['customer_name']); ?>
                                    </div>
                                    <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 2rem;">
                        <h3 style="color: #2c3e50; margin-bottom: 1rem;">Order Summary</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                            <?php
                            $stats = ['pending' => 0, 'confirmed' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];
                            $total_spent = 0;
                            foreach ($orders as $order) {
                                $order_status = $order['status'] ?? 'pending';
                                $stats[$order_status] = ($stats[$order_status] ?? 0) + 1;
                                if ($order_status !== 'cancelled') {
                                    $total_spent += $order['total_amount'];
                                }
                            }
                            ?>
                            <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;"><?php echo count($orders); ?></div>
                                <div style="font-size: 0.9rem; color: #666;">Total Orders</div>
                            </div>
                            <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #28a745;"><?php echo $stats['completed']; ?></div>
                                <div style="font-size: 0.9rem; color: #666;">Completed</div>
                            </div>
                            <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #007bff;"><?php echo $stats['in_progress']; ?></div>
                                <div style="font-size: 0.9rem; color: #666;">In Progress</div>
                            </div>
                            <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">NPR <?php echo number_format($total_spent); ?></div>
                                <div style="font-size: 0.9rem; color: #666;">Total Spent</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
                        <li><a href="orders.php">Orders</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 E-Tailor. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
    <style>
    @media (max-width: 768px) {
        .container > div:nth-child(3) > div {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        
        table {
            font-size: 0.9rem;
        }
        
        th, td {
            padding: 0.5rem !important;
        }
    }
    </style>
</body>
</html>
