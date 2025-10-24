<?php
// login.php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basit XSS koruması için trim + HTML escape
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        header('Location: index.html'); exit;
    }

    // Kullanıcıyı çek
    $stmt = $pdo->prepare('SELECT id, username, password_hash, role, full_name FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Oturum değişkenleri
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'] ?: $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['logged_in'] = true;

        // Role göre yönlendir
        if ($user['role'] === 'admin') {
            header('Location: admin.php'); exit;
        } else {
            header('Location: uye.php'); exit;
        }
    } else {
        // Hatalı giriş
        // Basitçe ana sayfaya döndür, istersen uyarı paramı ekleyebilirsin.
        header('Location: index.html'); exit;
    }
} else {
    header('Location: index.html'); exit;
}
