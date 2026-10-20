<?php
$title = 'Quản lý đánh giá';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['xoa'])) {
    $pdo->prepare('DELETE FROM danh_gia WHERE id = ?')->execute([(int)$_POST['xoa']]);
    flash('Đã xóa đánh giá.'); redirect('danh-gia.php');
}
$list = $pdo->query('SELECT g.*, sp.ten_sp FROM danh_gia g JOIN san_pham sp ON sp.id = g.san_pham_id ORDER BY g.id DESC')->fetchAll();
?>
<h2 style="margin-bottom:15px">Quản lý đánh giá</h2>
<table><tr><th>Sản phẩm</th><th>Người đánh giá</th><th>Sao</th><th>Bình luận</th><th>Ngày</th><th></th></tr>
<?php foreach ($list as $g): ?>
<tr><td><?= e($g['ten_sp']) ?></td><td><?= e($g['ten_nguoi_danh_gia']) ?></td><td class="stars"><?= str_repeat('⭐', $g['so_sao']) ?></td>
<td><?= e($g['binh_luan']) ?></td><td><?= e($g['ngay_danh_gia']) ?></td>
<td><form method="post" onsubmit="return confirm('Xóa đánh giá này?')"><input type="hidden" name="xoa" value="<?= $g['id'] ?>"><button class="btn red sm">Xóa</button></form></td></tr>
<?php endforeach; if (!$list): ?><tr><td colspan="6">Chưa có đánh giá.</td></tr><?php endif; ?></table>
<?php require '_foot.php'; ?>
