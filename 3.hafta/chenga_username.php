<?php
require_once 'check_auth.php';
require_login();
require_once 'db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['new_username'] ?? '');
    if ($newUsername === '') {
        $msg = 'Kullanıcı adı boş olamaz.';
    } elseif (!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/', $newUsername)) {
        $msg = '3-30 karakter, sadece harf/rakam ve _.- karakterleri.';
    } else {
        // Zaten var mı?
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $check->execute([$newUsername]);
        if ($check->fetch()) {
            $msg = 'Bu kullanıcı adı zaten alınmış.';
        } else {
            $up = $pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
            $up->execute([$newUsername, $_SESSION['user_id']]);
            $_SESSION['username'] = $newUsername;
            $msg = 'Kullanıcı adın güncellendi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Kullanıcı Adı Değiştir</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body style="display:block;background:linear-gradient(to top,#000,#b8860b);color:#fff;font-family:Poppins,sans-serif;">
  <div class="login-container" style="max-width:480px">
    <h2>Kullanıcı Adı Değiştir</h2>
    <?php if ($msg): ?>
      <p style="background:rgba(0,0,0,.4);padding:8px;border-radius:6px;"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
    <form method="post" class="login-form" autocomplete="off">
      <input type="text" name="new_username" placeholder="Yeni Kullanıcı Adı" required />
      <button type="submit">Güncelle</button>
    </form>
    <div style="margin-top:10px;">
      <a href="<?php echo ($_SESSION['role']==='admin')?'admin.php':'uye.php'; ?>" style="color:gold;">Panele dön</a>
    </div>
  </div>
</body>
</html>
