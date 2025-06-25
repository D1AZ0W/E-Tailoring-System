<?php
function requireLogin($redirect_url = null) {
    if (!isset($_SESSION['user_id'])) {
        if ($redirect_url) {
            $_SESSION['redirect_after_login'] = $redirect_url;
        }
        $_SESSION['message'] = 'Please log in to access this feature.';
        $_SESSION['message_type'] = 'error';
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
}
?>
