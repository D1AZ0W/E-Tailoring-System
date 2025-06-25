<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_email'] !== 'anshshrestha15@gmail.com') {
    $_SESSION['message'] = 'Access denied. Admin privileges required.';
    $_SESSION['message_type'] = 'error';
    header('Location: login.php');
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['status'];
        
        $valid_statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        if (in_array($new_status, $valid_statuses)) {
            try {
                $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $result = $stmt->execute([$new_status, $order_id]);
                
                if ($result) {
                    $message = 'Order status updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating order status.';
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            $message = 'Invalid status selected.';
            $message_type = 'error';
        }
    }
}

try {
    $stmt = $db->prepare("
        SELECT o.*, 
               u.first_name, u.last_name, u.email as user_email,
               COUNT(oi.id) as item_count,
               GROUP_CONCAT(oi.product_name SEPARATOR ', ') as products
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Error loading orders: ' . $e->getMessage();
    $message_type = 'error';
    $orders = [];
}


foreach ($orders as &$order) {
    if (!isset($order['status']) || empty($order['status'])) {
        $order['status'] = 'pending';
    }
}
unset($order);


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
    <title>Admin - Manage Orders - E-Tailor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">E-Tailor Admin</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="admin_orders.php" class="active">Manage Orders</a></li>
                <li><a href="orders.php">My Orders</a></li>
            </ul>
            <div class="nav-auth">
                <span class="user-welcome">Admin Panel</span>
                <a href="logout.php" class="btn-auth">Logout</a>
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

            <div style="background: #e3f2fd; padding: 1rem; border-radius: 5px; margin: 100px 0 2rem; border-left: 4px solid #2196f3;">
                <h3>🔧 Admin Panel - Order Management</h3>
                <p>Manage all customer orders and update their status.</p>
            </div>

            <h1>All Orders</h1>

            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 4rem 0; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                    <h3>No orders found</h3>
                    <p style="color: #666; margin: 1rem 0;">No customer orders have been placed yet.</p>
                </div>
            <?php else: ?>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <?php
                    $stats = ['pending' => 0, 'confirmed' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];
                    $total_revenue = 0;
                    foreach ($orders as $order) {
                        $order_status = $order['status'] ?? 'pending';
                        $stats[$order_status] = ($stats[$order_status] ?? 0) + 1;
                        if ($order_status !== 'cancelled') {
                            $total_revenue += $order['total_amount'];
                        }
                    }
                    ?>
                    <div style="text-align: center; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;"><?php echo count($orders); ?></div>
                        <div style="font-size: 0.9rem; color: #666;">Total Orders</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #ffc107;"><?php echo $stats['pending']; ?></div>
                        <div style="font-size: 0.9rem; color: #666;">Pending</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #007bff;"><?php echo $stats['in_progress']; ?></div>
                        <div style="font-size: 0.9rem; color: #666;">In Progress</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="font-size: 1.5rem; font-weight: bold; color: #28a745;"><?php echo $stats['completed']; ?></div>
                        <div style="font-size: 0.9rem; color: #666;">Completed</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <div style="font-size: 1.2rem; font-weight: bold; color: #2c3e50;">NPR <?php echo number_format($total_revenue); ?></div>
                        <div style="font-size: 0.9rem; color: #666;">Total Revenue</div>
                    </div>
                </div>


                <div style="background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #ddd;">Order #</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #ddd;">Customer</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #ddd;">Products</th>
                                <th style="padding: 1rem; text-align: center; border-bottom: 1px solid #ddd;">Status</th>
                                <th style="padding: 1rem; text-align: right; border-bottom: 1px solid #ddd;">Amount</th>
                                <th style="padding: 1rem; text-align: center; border-bottom: 1px solid #ddd;">Date</th>
                                <th style="padding: 1rem; text-align: center; border-bottom: 1px solid #ddd;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php 
                                $order_status = $order['status'] ?? 'pending';
                                $status = $status_config[$order_status] ?? $status_config['pending']; 
                                ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">
                                        <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                        </div>
                                        <div style="font-size: 0.9rem; color: #666;">
                                            <?php echo htmlspecialchars($order['customer_email']); ?>
                                        </div>
                                        <?php if ($order['customer_phone']): ?>
                                            <div style="font-size: 0.9rem; color: #666;">
                                                <?php echo htmlspecialchars($order['customer_phone']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($order['products']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #666;">
                                            <?php echo $order['item_count']; ?> item<?php echo $order['item_count'] != 1 ? 's' : ''; ?>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="background: <?php echo $status['color']; ?>; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">
                                                <?php foreach ($status_config as $status_key => $status_info): ?>
                                                    <option value="<?php echo $status_key; ?>" 
                                                            <?php echo $order_status === $status_key ? 'selected' : ''; ?>
                                                            style="background: white; color: black;">
                                                        <?php echo $status_info['icon']; ?> <?php echo $status_info['label']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td style="padding: 1rem; text-align: right;">
                                        <strong>NPR <?php echo number_format($order['total_amount']); ?></strong>
                                    </td>
                                    <td style="padding: 1rem; text-align: center; font-size: 0.9rem;">
                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>E-Tailor Admin</h3>
                    <p>Order management system.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 E-Tailor. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>

    <style>
    select {
        cursor: pointer;
    }
    
    select option {
        padding: 0.5rem;
    }
    
    @media (max-width: 768px) {
        table {
            font-size: 0.8rem;
        }
        
        th, td {
            padding: 0.5rem !important;
        }
        
        .container > div:nth-child(3) {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    </style>
</body>
</html>
