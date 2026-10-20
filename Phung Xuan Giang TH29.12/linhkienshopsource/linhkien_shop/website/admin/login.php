<?php
require_once __DIR__ . '/../includes/config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM quan_tri WHERE ten_dang_nhap = ?'); $st->execute([trim($_POST['u'] ?? '')]);
    $a = $st->fetch();
    if ($a && password_verify($_POST['p'] ?? '', $a['mat_khau'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = ['id' => $a['id'], 'ho_ten' => $a['ho_ten']];
        redirect('index.php');
    }
    $err = 'Sai tên đăng nhập hoặc mật khẩu.';
}
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đăng nhập quản trị</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<main class="container"><div class="box" style="max-width:400px;margin:80px auto">
<h2 style="margin-bottom:15px">🔧 Đăng nhập quản trị</h2>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Tên đăng nhập</label><input name="u" required autofocus></div>
    <div class="form-row"><label>Mật khẩu</label><input type="password" name="p" required></div>
    <button class="btn">Đăng nhập</button> <a href="../index.php">← Về website</a>
</form></div></main></body></html>
