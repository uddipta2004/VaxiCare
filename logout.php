<?php
require_once 'config.php';

// Clear user session variables
unset($_SESSION['user_id'], $_SESSION['user_name']);

// If admin is also logged in, don't destroy their session
if (!isAdminLoggedIn()) {
    // Destroy session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    // Restart session only to carry flash message
    session_start();
}

$_SESSION['success'] = 'You have been logged out successfully.';
redirect('index.php');
?>
