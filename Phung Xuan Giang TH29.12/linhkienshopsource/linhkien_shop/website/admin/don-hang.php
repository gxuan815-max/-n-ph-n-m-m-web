<?php
$title = 'Quản lý đơn hàng';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['xoa'])) {                                                   // XÓA
        $pdo->prepare('DELETE FROM don_hang WHERE id = ?')->execute([(int)$_POST['xoa']]);
        flash('Đã xóa đơn hàng.');
    } elseif (isset($_POST['id'], $_POST['trang_thai']) && isset($TRANG_THAI[$_POST['trang_thai']])) {   // SỬA trạng thái
        $pdo->prepare('UPDATE don_hang SET trang_thai = ? WHERE id = ?')->execute([$_POST['trang_thai'], (int)$_POST['id']]);
        flash('Đã cập nhật trạng thái đơn #' . (int)$_POST['id']);
    }
    redirect('don-hang.php');
}
$tt = $_GET['tt'] ?? '';
$sql = 'SELECT * FROM don_hang' . (isset($TRANG_THAI[$tt]) ? ' WHERE trang_thai = ' . $pdo->quote($tt) : '') . ' ORDER BY id DESC';
$orders = $pdo->query($sql)->fetchAll();
$ct = $pdo->prepare('SELECT c.*, sp.ten_sp FROM chi_tiet_don_hang c JOIN san_pham sp ON sp.id = c.san_pham_id WHERE c.don_hang_id = ?');
?>
<h2 style="margin-bottom:12px">Quản lý đơn hàng</h2>
<p style="margin-bottom:15px">Lọc: <a href="don-hang.php">Tất cả</a>
<?php foreach ($TRANG_THAI as $k => $v): ?> | <a href="?tt=<?= $k ?>"><?= e($v) ?></a><?php endforeach; ?></p>
<?php if (!$orders): ?><div class="box">Chưa có đơn hàng.</div><?php endif; ?>
<?php foreach ($orders as $o): $ct->execute([$o['id']]); ?>
<div class="box">
    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px"><b>Đơn #<?= $o['id'] ?> — <?= e($o['ngay_dat']) ?></b><span class="st"><?= e($TRANG_THAI[$o['trang_thai']]) ?></span></div>
    <p>👤 <?= e($o['ho_ten_nhan']) ?> | 📞 <?= e($o['so_dien_thoai']) ?> | ✉️ <?= e($o['email']) ?></p>
    <p>📍 <?= e($o['dia_chi_giao']) ?><?= $o['ghi_chu'] ? ' — Ghi chú: ' . e($o['ghi_chu']) : '' ?></p>
    <?php foreach ($ct->fetchAll() as $c): ?><p>• <?= e($c['ten_sp']) ?> × <?= $c['so_luong'] ?> (<?= vnd($c['don_gia']) ?>)</p><?php endforeach; ?>
    <p class="price">Tổng: <?= vnd($o['tong_tien']) ?></p>
    <form method="post" style="display:flex;gap:8px;max-width:420px">
        <input type="hidden" name="id" value="<?= $o['id'] ?>">
        <select name="trang_thai"><?php foreach ($TRANG_THAI as $k => $v): ?><option value="<?= $k ?>" <?= $o['trang_thai'] == $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
        <button class="btn sm">Cập nhật</button>
    </form>
    <form method="post" style="margin-top:8px" onsubmit="return confirm('Xóa đơn hàng này?')"><input type="hidden" name="xoa" value="<?= $o['id'] ?>"><button class="btn red sm">Xóa đơn</button></form>
</div>
<?php endforeach; ?>
<?php require '_foot.php'; ?>
