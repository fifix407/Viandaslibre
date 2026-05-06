<?php
// includes/auth.php - Authentication functions

session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (function_exists('route_url')) {
            header('Location: ' . route_url('/admin/login'));
        } elseif (defined('BASE_URL')) {
            header('Location: ' . BASE_URL . '/admin/login');
        } else {
            header('Location: /admin/login');
        }
        exit;
    }
}

function login($username, $password) {
    // Simple authentication - in production, use proper hashing
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    if (function_exists('route_url')) {
        header('Location: ' . route_url('/'));
    } elseif (defined('BASE_URL')) {
        header('Location: ' . BASE_URL . '/index.php');
    } else {
        header('Location: /index.php');
    }
    exit;
}
?>