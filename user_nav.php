<?php
// Include this file in PHP pages to show proper navigation based on login status
function renderNavigation($current_page = '') {
    $is_logged_in = isset($_SESSION['user_id']);
    $user_name = $is_logged_in ? $_SESSION['user_name'] : '';
    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    
    echo '<header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">TailorCraft</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php"' . ($current_page == 'home' ? ' class="active"' : '') . '>Home</a></li>
                <li><a href="products.php"' . ($current_page == 'products' ? ' class="active"' : '') . '>Designs</a></li>
                <li><a href="measurements.php"' . ($current_page == 'measurements' ? ' class="active"' : '') . '>Measurements</a></li>
                <li><a href="gallery.php"' . ($current_page == 'gallery' ? ' class="active"' : '') . '>Gallery</a></li>
                <li><a href="contact.php"' . ($current_page == 'contact' ? ' class="active"' : '') . '>Contact</a></li>
            </ul>
            <div class="nav-auth">';
    
    if ($is_logged_in) {
        echo '<span style="color: #666; margin-right: 1rem;">Welcome, ' . htmlspecialchars(explode(' ', $user_name)[0]) . '</span>
              <a href="logout.php" class="btn-auth">Logout</a>
              <a href="cart.php" class="cart-link">Cart (' . $cart_count . ')</a>';
    } else {
        echo '<a href="login.php" class="btn-auth">Login</a>
              <a href="register.php" class="btn btn-primary">Sign Up</a>
              <a href="cart.php" class="cart-link">Cart (' . $cart_count . ')</a>';
    }
    
    echo '</div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>';
}
?>
