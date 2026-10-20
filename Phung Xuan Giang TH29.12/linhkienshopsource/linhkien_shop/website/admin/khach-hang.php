<?php
$title = 'Quản lý khách hàng';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['xoa'])) {
    $pdo->prepare('DELETE FROM khach_hang WHERE id = ?')->execute([(int)$_POST['xoa']]);   // đơn cũ giữ lại (khach_hang_id = NULL)
    flash('Đã xóa khách hàng.'); redirect('khach-hang.php');
}
$list = $pdo->query('SELECT k.*, (SELECT COUNT(*) FROM don_hang WHERE khach_hang_id = k.id) AS sodon FROM khach_hang k ORDER BY id DESC')->fetchAll();
?>
<h2 style="margin-bottom:15px">Quản lý khách hàng</h2>
<table><tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Ngày đăng ký</th><th>Số đơn</th><th>Thao tác</th></tr>
<?php foreach ($list as $k): ?>
<tr><td><?= $k['id'] ?></td><td><?= e($k['ho_ten']) ?></td><td><?= e($k['email']) ?></td><td><?= e($k['ngay_dang_ky']) ?></td><td><?= $k['sodon'] ?></td>
<td><form method="post" onsubmit="return confirm('Xóa khách hàng này?')"><input type="hidden" name="xoa" value="<?= $k['id'] ?>"><button class="btn red sm">Xóa</button></form></td></tr>
<?php endforeach; if (!$list): ?><tr><td colspan="6">Chưa có khách hàng đăng ký.</td></tr><?php endif; ?></table>
<?php require '_foot.php'; ?>
