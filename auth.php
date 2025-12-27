<?php
session_start();
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireRole($roles = []) {
    if (!isLoggedIn() || !in_array(currentUserRole(), $roles)) {
        header('Location: index.php');
        exit;
    }
}
