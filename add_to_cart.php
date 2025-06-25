<?php
session_start();

// This script adds items to the cart.
// FIX: It now checks for existing items and updates quantity instead of adding duplicates.

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Please log in to add items to your cart.';
    $_SESSION['message_type'] = 'error';
    // Redirect to login, and send them back to the page they were on
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header('Location: login.php?redirect=' . urlencode($redirect_url));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation for required fields
    if (empty($_POST['product_name']) || empty($_POST['base_price']) || empty($_POST['fabric']) || empty($_POST['color']) || empty($_POST['size'])) {
        $_SESSION['message'] = 'Missing product information. Please try again.';
        $_SESSION['message_type'] = 'error';
        header('Location: products.php');
        exit;
    }

    // Calculate price based on fabric
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
        'total_price' => $total_price, // This is the unit price
        'added_at' => date('Y-m-d H:i:s')
    ];
    
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // **FIX:** Check if item already exists in cart. If so, update quantity.
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
        // Item exists, just increment the quantity
        $_SESSION['cart'][$item_exists_at_index]['quantity']++;
        $_SESSION['message'] = 'Quantity updated for ' . htmlspecialchars($newItem['product_name']) . ' in your cart!';
    } else {
        // Item is new, add it to the cart
        $_SESSION['cart'][] = $newItem;
        $_SESSION['message'] = htmlspecialchars($newItem['product_name']) . ' has been added to your cart!';
    }
    
    $_SESSION['message_type'] = 'success';
    
    header('Location: cart.php');
    exit;
} else {
    // If not a POST request, just redirect to products page
    header('Location: products.php');
    exit;
}
?>