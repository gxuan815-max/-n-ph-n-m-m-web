<?php
require 'includes/config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ho_ten'] ?? ''); $email = trim($_POST['email'] ?? ''); $mk = $_POST['mat_khau'] ?? '';
    if ($ten === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($mk) < 6) {
        $err = 'Nhập đủ họ tên, email hợp lệ và mật khẩu từ 6 ký tự.';
    } else {
        $st = $pdo->prepare('SELECT id FROM khach_hang WHERE email = ?'); $st->execute([$email]);
        if ($st->fetch()) { $err = 'Email này đã được đăng ký.'; }
        else {
            $pdo->prepare('INSERT INTO khach_hang (ho_ten, email, mat_khau) VALUES (?,?,?)')
                ->execute([$ten, $email, password_hash($mk, PASSWORD_DEFAULT)]);
            flash('Đăng ký thành công, hãy đăng nhập.'); redirect('dang-nhap.php');
        }
    }
}
$title = 'Đăng ký';
require 'includes/header.php';
?>
<div class="box" style="max-width:450px;margin:auto"><h2 style="margin-bottom:15px">Đăng ký tài khoản</h2>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<form method="post">
    <div class="form-row"><label>Họ tên</label><input name="ho_ten" required value="<?= e($_POST['ho_ten'] ?? '') ?>"></div>
    <div class="form-row"><label>Email</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div>
    <div class="form-row"><label>Mật khẩu (≥ 6 ký tự)</label><input type="password" name="mat_khau" required></div>
    <button class="btn">Đăng ký</button>
</form></div>
<?php require 'includes/footer.php'; ?>
