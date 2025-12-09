<?php
if (!isset($_SESSION)) session_start();

function user()
{
    return $_SESSION["user"] ?? null;
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['peran'] === 'admin';
}

function isLoggedIn()
{
    return isset($_SESSION["user"]);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: " . APP_URL . "/auth/login.php");
        exit;
    }
}
