<?php
require 'includes/config.php';
if (empty($_SESSION['kh'])) { flash('Vui lòng đăng nhập để xem đơn hàng.', 'error'); redirect('dang-nhap.php'); }
$st = $pdo->prepare('SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY id DESC');
$st->execute([$_SESSION['kh']['id']]);
$orders = $st->fetchAll();
$ct = $pdo->prepare('SELECT c.*, sp.ten_sp FROM chi_tiet_don_hang c JOIN san_pham sp ON sp.id = c.san_pham_id WHERE c.don_hang_id = ?');
$title = 'Đơn hàng của tôi';
require 'includes/header.php';
?>
<h2 style="margin-bottom:15px">Lịch sử đơn hàng</h2>
<?php if (!$orders): ?><div class="box">Bạn chưa có đơn hàng nào.</div><?php endif; ?>
<?php foreach ($orders as $o): $ct->execute([$o['id']]); ?>
<div class="box"><b>Đơn #<?= $o['id'] ?></b> • <?= e($o['ngay_dat']) ?> • <span class="st"><?= e($TRANG_THAI[$o['trang_thai']]) ?></span>
    <?php foreach ($ct->fetchAll() as $c): ?><p>• <?= e($c['ten_sp']) ?> × <?= $c['so_luong'] ?> — <?= vnd($c['don_gia'] * $c['so_luong']) ?></p><?php endforeach; ?>
    <p class="price" style="margin-top:8px">Tổng: <?= vnd($o['tong_tien']) ?></p></div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
