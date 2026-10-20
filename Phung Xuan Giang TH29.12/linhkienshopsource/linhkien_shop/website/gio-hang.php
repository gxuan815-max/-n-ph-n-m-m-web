<?php
require 'includes/config.php';
$cart = &$_SESSION['cart'];
$cart = $cart ?? [];

if (isset($_GET['add'])) {
    $id = (int)$_GET['add']; $qty = max(1, (int)($_GET['qty'] ?? 1));
    $st = $pdo->prepare('SELECT so_luong_ton FROM san_pham WHERE id = ?'); $st->execute([$id]);
    $ton = $st->fetchColumn();
    if ($ton === false) { flash('Sản phẩm không tồn tại.', 'error'); }
    else { $cart[$id] = min(($cart[$id] ?? 0) + $qty, (int)$ton); flash('Đã thêm vào giỏ hàng.'); }
    redirect('gio-hang.php');
}
if (isset($_GET['del'])) { unset($cart[(int)$_GET['del']]); flash('Đã xóa sản phẩm khỏi giỏ.'); redirect('gio-hang.php'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {          // cập nhật số lượng
    foreach (($_POST['qty'] ?? []) as $id => $q) {
        $q = (int)$q; if ($q <= 0) unset($cart[(int)$id]); else $cart[(int)$id] = $q;
    }
    flash('Đã cập nhật giỏ hàng.'); redirect('gio-hang.php');
}

$items = []; $sum = 0;
if ($cart) {
    $in = implode(',', array_fill(0, count($cart), '?'));
    $st = $pdo->prepare("SELECT sp.*, h.ten_hang FROM san_pham sp JOIN nha_san_xuat h ON h.id = sp.hang_id WHERE sp.id IN ($in)");
    $st->execute(array_keys($cart));
    foreach ($st->fetchAll() as $r) { $r['sl'] = $cart[$r['id']]; $r['tt'] = $r['sl'] * $r['gia']; $sum += $r['tt']; $items[] = $r; }
}
$title = 'Giỏ hàng';
require 'includes/header.php';
?>
<h2 style="margin-bottom:15px">Giỏ hàng của bạn</h2>
<?php if (!$items): ?><div class="box">Giỏ hàng trống. <a href="index.php">Tiếp tục mua sắm</a></div>
<?php else: ?>
<form method="post">
<table><tr><th>Sản phẩm</th><th>Đơn giá</th><th style="width:100px">Số lượng</th><th>Thành tiền</th><th></th></tr>
<?php foreach ($items as $r): ?>
<tr><td><b><?= e($r['ten_hang']) ?></b><br><?= e($r['ten_sp']) ?></td><td><?= vnd($r['gia']) ?></td>
<td><input type="number" name="qty[<?= $r['id'] ?>]" value="<?= $r['sl'] ?>" min="0" max="<?= $r['so_luong_ton'] ?>"></td>
<td><b><?= vnd($r['tt']) ?></b></td><td><a class="btn red sm" href="?del=<?= $r['id'] ?>">Xóa</a></td></tr>
<?php endforeach; ?>
</table>
<div class="box" style="margin-top:18px;text-align:right">
    <p style="font-size:20px;margin-bottom:12px">Tổng cộng: <b class="price"><?= vnd($sum) ?></b></p>
    <button class="btn gray">Cập nhật giỏ</button>
    <a class="btn" href="index.php">Tiếp tục mua</a>
    <a class="btn green" href="dat-hang.php">Đặt hàng →</a>
</div>
</form>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
