<?php
require 'includes/config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM khach_hang WHERE email = ?'); $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['mat_khau'] ?? '', $u['mat_khau'])) {
        $_SESSION['kh'] = ['id' => $u['id'], 'ho_ten' => $u['ho_ten'], 'email' => $u['email']];
        flash('Đăng nhập thành công.'); redirect('index.php');
    }
    $err = 'Email hoặc mật khẩu không đúng.';
}
$title = 'Đăng nhập';
require 'includes/header.php';
?>
<div class="box" style="max-width:450px;margin:auto"><h2 style="margin-bottom:15px">Đăng nhập</h2>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
    <div class="form-row"><label>Mật khẩu</label><input type="password" name="mat_khau" required></div>
    <button class="btn">Đăng nhập</button> <a href="dang-ky.php">Chưa có tài khoản?</a>
</form></div>
<?php require 'includes/footer.php'; ?>
