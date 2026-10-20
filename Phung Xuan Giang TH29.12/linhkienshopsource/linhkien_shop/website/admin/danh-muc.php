<?php
$title = 'Quản lý danh mục';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    if (isset($_POST['xoa'])) {                                                   // XÓA
        try { $pdo->prepare('DELETE FROM danh_muc WHERE id = ?')->execute([(int)$_POST['xoa']]); flash('Đã xóa danh mục.'); }
        catch (PDOException $ex) { flash('Không thể xóa: danh mục còn sản phẩm.', 'error'); }
    } elseif ($ten === '') { flash('Tên danh mục không được trống.', 'error'); }
    elseif (!empty($_POST['id'])) {                                               // SỬA
        $pdo->prepare('UPDATE danh_muc SET ten_danh_muc = ? WHERE id = ?')->execute([$ten, (int)$_POST['id']]); flash('Đã cập nhật danh mục.');
    } else {                                                                      // THÊM
        $pdo->prepare('INSERT INTO danh_muc (ten_danh_muc) VALUES (?)')->execute([$ten]); flash('Đã thêm danh mục.');
    }
    redirect('danh-muc.php');
}
$list = $pdo->query('SELECT d.*, (SELECT COUNT(*) FROM san_pham WHERE danh_muc_id = d.id) AS sl FROM danh_muc d ORDER BY id')->fetchAll();
?>
<h2 style="margin-bottom:15px">Quản lý danh mục</h2>
<form method="post" class="box" style="display:flex;gap:10px"><input name="ten" placeholder="Tên danh mục mới" required><button class="btn green">+ Thêm</button></form>
<table><tr><th>ID</th><th>Tên danh mục (sửa trực tiếp)</th><th>Số SP</th><th>Thao tác</th></tr>
<?php foreach ($list as $d): ?>
<tr><td><?= $d['id'] ?></td>
<td><form method="post" style="display:flex;gap:8px"><input type="hidden" name="id" value="<?= $d['id'] ?>"><input name="ten" value="<?= e($d['ten_danh_muc']) ?>"><button class="btn sm">Lưu</button></form></td>
<td><?= $d['sl'] ?></td>
<td><form method="post" onsubmit="return confirm('Xóa danh mục này?')"><input type="hidden" name="xoa" value="<?= $d['id'] ?>"><button class="btn red sm">Xóa</button></form></td></tr>
<?php endforeach; ?></table>
<?php require '_foot.php'; ?>
