<?php
session_start();
$user_name = isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : '';


session_destroy();


session_start();
$_SESSION['message'] = 'Goodbye' . ($user_name ? ', ' . htmlspecialchars($user_name) : '') . '! You have been logged out successfully.';
$_SESSION['message_type'] = 'success';

header('Location: index.php');
exit;
?>