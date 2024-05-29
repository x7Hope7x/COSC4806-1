<?php
session_start();

$correct_username = 'admin';
$correct_password = 'password';

if ($_POST['username'] === $correct_username && $_POST['password'] === $correct_password) {
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $correct_username;
    $_SESSION['login_attempts'] = 0; // Reset login attempts on successful login
    header('Location: index.php');
    exit();
} else {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['login_attempts'] += 1;
    header('Location: login.php');
    exit();
}
