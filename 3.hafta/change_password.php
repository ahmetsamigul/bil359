<?php
require_once 'check_auth.php';
require_login();
require_once 'db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new1    = $_POST['new_password'] ?? '';
    $new2    = $_POST['new_password2'] ?? '';

    if ($new1 !== $new2) {
        $msg = 'Yeni şifreler aynı değil!';
    } elseif (strlen($new1) < 6) {
        $msg = 'Yeni şifre en az 6 karakter olmalı.';
    } else {
        // Kullanıcıyı çek
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password_hash'])) {
            $msg = 'Mevcut şifre yanlış.';
        } else {
            $newHash = password_hash($new1, PASSWORD_DEFAULT);
            $up = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $up->execute([$newHash, $_SESSION['user_id']]);
            $msg = 'Şifre başarıyla güncellendi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Şifre Değiştir</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body style="display:block;background:linear-gradient(to top,#000,#b8860b);color:#fff;font-family:Poppins,sans-serif;">
  <div class="login-container" style="max-width:480px">
    <h2>Şifre Değiştir</h2>
    <?php if ($msg): ?>
      <p style="background:rgba(0,0,0,.4);padding:8px;border-radius:6px;"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
    <form method="post" class="login-form" autocomplete="off">
      <input type="password" name="current_password" placeholder="Mevcut Şifre" required />
      <input type="password" name="new_password" placeholder="Yeni Şifre" required />
      <input type="password" name="new_password2" placeholder="Yeni Şifre (Tekrar)" required />
      <button type="submit">Güncelle</button>
    </form>
    <div style="margin-top:10px;">
      <a href="<?php echo ($_SESSION['role']==='admin')?'admin.php':'uye.php'; ?>" style="color:gold;">Panele dön</a>
    </div>
  </div>
</body>
</html>
