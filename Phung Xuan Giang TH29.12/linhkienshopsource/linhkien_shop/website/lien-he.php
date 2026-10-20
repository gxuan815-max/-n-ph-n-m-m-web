<?php
require 'includes/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ho_ten'] ?? ''); $email = trim($_POST['email'] ?? '');
    $td = trim($_POST['tieu_de'] ?? ''); $nd = trim($_POST['noi_dung'] ?? '');
    if ($ten === '' || $nd === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Vui lòng nhập họ tên, email hợp lệ và nội dung.', 'error');
    } else {
        $pdo->prepare('INSERT INTO lien_he (ho_ten, email, tieu_de, noi_dung) VALUES (?,?,?,?)')->execute([$ten, $email, $td, $nd]);
        flash('Đã gửi liên hệ. Chúng tôi sẽ phản hồi sớm nhất!');
    }
    redirect('lien-he.php');
}
$title = 'Liên hệ';
require 'includes/header.php';
?>
<h2 style="margin-bottom:15px">Liên hệ với chúng tôi</h2>
<div class="two">
<div class="box"><h3>Thông tin cửa hàng</h3><br>
    <p>📍 Hà Nội, Việt Nam</p><p>📞 0123 456 789</p><p>✉️ support@techstore.vn</p><p>🕘 8:00 - 21:00 (T2 - CN)</p></div>
<form method="post" class="box">
    <div class="form-row"><label>Họ tên *</label><input name="ho_ten" required></div>
    <div class="form-row"><label>Email *</label><input type="email" name="email" required></div>
    <div class="form-row"><label>Tiêu đề</label><input name="tieu_de"></div>
    <div class="form-row"><label>Nội dung *</label><textarea name="noi_dung" rows="5" required></textarea></div>
    <button class="btn">Gửi liên hệ</button>
</form></div>
<?php require 'includes/footer.php'; ?>
