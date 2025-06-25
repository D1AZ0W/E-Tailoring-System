<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to add items to your cart.';
    $_SESSION['message_type'] = 'error';
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header('Location: login.php?redirect=' . urlencode($redirect_url));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['product_name']) || empty($_POST['base_price']) || empty($_POST['fabric']) || empty($_POST['color']) || empty($_POST['size'])) {
        $_SESSION['message'] = 'Missing product information. Please try again.';
        $_SESSION['message_type'] = 'error';
        header('Location: products.php');
        exit;
    }

    $fabric_prices = ['cotton' => 0, 'linen' => 500, 'wool' => 2000, 'silk' => 3000];
    $fabric_modifier = $fabric_prices[$_POST['fabric']] ?? 0;
    $total_price = floatval($_POST['base_price']) + $fabric_modifier;

    $newItem = [
        'product_name' => $_POST['product_name'],
        'base_price' => floatval($_POST['base_price']),
        'fabric' => $_POST['fabric'],
        'color' => $_POST['color'],
        'size' => $_POST['size'],
        'special_instructions' => $_POST['special_instructions'] ?? '',
        'quantity' => 1,
        'total_price' => $total_price, 
        'added_at' => date('Y-m-d H:i:s')
    ];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $item_exists_at_index = -1;
    foreach ($_SESSION['cart'] as $index => $cart_item) {
        if (
            $cart_item['product_name'] === $newItem['product_name'] &&
            $cart_item['fabric'] === $newItem['fabric'] &&
            $cart_item['color'] === $newItem['color'] &&
            $cart_item['size'] === $newItem['size'] &&
            $cart_item['special_instructions'] === $newItem['special_instructions']
        ) {
            $item_exists_at_index = $index;
            break;
        }
    }

    if ($item_exists_at_index > -1) {
        $_SESSION['cart'][$item_exists_at_index]['quantity']++;
        $_SESSION['message'] = 'Quantity updated for ' . htmlspecialchars($newItem['product_name']) . ' in your cart!';
    } else {
        $_SESSION['cart'][] = $newItem;
        $_SESSION['message'] = htmlspecialchars($newItem['product_name']) . ' has been added to your cart!';
    }
    
    $_SESSION['message_type'] = 'success';
    
    header('Location: cart.php');
    exit;
} else {
    header('Location: products.php');
    exit;
}
?>