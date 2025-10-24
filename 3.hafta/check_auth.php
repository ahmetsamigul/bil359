<?php
// check_auth.php
session_start();

function require_login() {
    if (empty($_SESSION['logged_in'])) {
        header('Location: index.html');
        exit;
    }
}

function require_role($role) {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        // Yetkisiz erişimi, kullanıcının kendi paneline alabiliriz:
        if (($_SESSION['role'] ?? '') === 'admin') {
            header('Location: admin.php'); exit;
        } else {
            header('Location: uye.php'); exit;
        }
    }
}
