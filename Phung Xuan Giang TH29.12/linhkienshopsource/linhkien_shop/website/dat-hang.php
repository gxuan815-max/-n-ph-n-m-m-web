<?php
require 'includes/config.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Giỏ hàng trống.', 'error'); redirect('gio-hang.php'); }

$in = implode(',', array_fill(0, count($cart), '?'));
$st = $pdo->prepare("SELECT * FROM san_pham WHERE id IN ($in)");
$st->execute(array_keys($cart));
$sp = []; foreach ($st->fetchAll() as $r) $sp[$r['id']] = $r;
$sum = 0; foreach ($cart as $id => $q) { if (isset($sp[$id])) $sum += $sp[$id]['gia'] * $q; }

$kh = $_SESSION['kh'] ?? [];
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ho_ten'] ?? ''); $email = trim($_POST['email'] ?? '');
    $sdt = trim($_POST['sdt'] ?? ''); $dc = trim($_POST['dia_chi'] ?? ''); $gc = trim($_POST['ghi_chu'] ?? '');
    if ($ten === '' || $sdt === '' || $dc === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Vui lòng nhập đầy đủ thông tin hợp lệ.';
    } elseif (!preg_match('/^[0-9]{9,11}$/', $sdt)) {
        $err = 'Số điện thoại phải gồm 9-11 chữ số.';
    } else {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO don_hang (khach_hang_id, ho_ten_nhan, email, so_dien_thoai, dia_chi_giao, ghi_chu, tong_tien) VALUES (?,?,?,?,?,?,?)')
                ->execute([$kh['id'] ?? null, $ten, $email, $sdt, $dc, $gc, $sum]);
            $oid = $pdo->lastInsertId();
            $ins = $pdo->prepare('INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (?,?,?,?)');
            $upd = $pdo->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton - ? WHERE id = ? AND so_luong_ton >= ?');
            foreach ($cart as $id => $q) {
                if (!isset($sp[$id])) continue;
                $upd->execute([$q, $id, $q]);
                if ($upd->rowCount() === 0) throw new Exception('Sản phẩm "' . $sp[$id]['ten_sp'] . '" không đủ hàng.');
                $ins->execute([$oid, $id, $q, $sp[$id]['gia']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            flash("Đặt hàng thành công! Mã đơn hàng của bạn: #$oid");
            redirect('index.php');
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $err = $ex->getMessage();
        }
    }
}
$title = 'Đặt hàng';
require 'includes/header.php';
?>
<h2 style="margin-bottom:15px">Đặt hàng</h2>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<div class="two">
<form method="post" class="box">
    <div class="form-row"><label>Họ tên *</label><input name="ho_ten" required value="<?= e($_POST['ho_ten'] ?? $kh['ho_ten'] ?? '') ?>"></div>
    <div class="form-row"><label>Email *</label><input type="email" name="email" required value="<?= e($_POST['email'] ?? $kh['email'] ?? '') ?>"></div>
    <div class="form-row"><label>Số điện thoại *</label><input name="sdt" required value="<?= e($_POST['sdt'] ?? '') ?>"></div>
    <div class="form-row"><label>Địa chỉ giao hàng *</label><input name="dia_chi" required value="<?= e($_POST['dia_chi'] ?? '') ?>"></div>
    <div class="form-row"><label>Ghi chú</label><textarea name="ghi_chu" rows="3"><?= e($_POST['ghi_chu'] ?? '') ?></textarea></div>
    <button class="btn green">Xác nhận đặt hàng</button>
</form>
<div class="box"><h3 style="margin-bottom:12px">Tóm tắt đơn hàng</h3>
<?php foreach ($cart as $id => $q): if (!isset($sp[$id])) continue; ?>
    <p style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #eee"><span><?= e($sp[$id]['ten_sp']) ?> × <?= $q ?></span><b><?= vnd($sp[$id]['gia'] * $q) ?></b></p>
<?php endforeach; ?>
<p class="price" style="margin-top:14px;text-align:right">Tổng: <?= vnd($sum) ?></p></div>
</div>
<?php require 'includes/footer.php'; ?>
