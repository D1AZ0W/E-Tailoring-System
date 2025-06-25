<?php
session_start();
// This script handles user logout. It's simple for a college project.

// Store user's first name for goodbye message
$user_name = isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : '';

// Destroy all session data
session_destroy();

// Start new session for message
session_start();
$_SESSION['message'] = 'Goodbye' . ($user_name ? ', ' . htmlspecialchars($user_name) : '') . '! You have been logged out successfully.';
$_SESSION['message_type'] = 'success';

header('Location: index.php');
exit;
?>