<?php
$title = 'Quản lý sản phẩm';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['xoa'])) {          // XÓA
    try {
        $pdo->prepare('DELETE FROM san_pham WHERE id = ?')->execute([(int)$_POST['xoa']]);
        flash('Đã xóa sản phẩm.');
    } catch (PDOException $ex) {
        flash('Không thể xóa: sản phẩm đã có trong đơn hàng.', 'error');
    }
    redirect('san-pham.php');
}
$q = trim($_GET['q'] ?? ''); $page = max(1, (int)($_GET['page'] ?? 1)); $per = 20;
$st = $pdo->prepare('SELECT COUNT(*) FROM san_pham WHERE ten_sp LIKE ?'); $st->execute(["%$q%"]);
$pages = max(1, (int)ceil($st->fetchColumn() / $per)); $page = min($page, $pages); $off = ($page - 1) * $per;
$st = $pdo->prepare("SELECT sp.*, d.ten_danh_muc, h.ten_hang FROM san_pham sp JOIN danh_muc d ON d.id = sp.danh_muc_id
    JOIN nha_san_xuat h ON h.id = sp.hang_id WHERE sp.ten_sp LIKE ? ORDER BY sp.id DESC LIMIT $per OFFSET $off");
$st->execute(["%$q%"]); $list = $st->fetchAll();
?>
<div style="display:flex;justify-content:space-between;margin-bottom:15px;gap:10px;flex-wrap:wrap">
    <h2>Quản lý sản phẩm</h2>
    <form style="display:flex;gap:8px"><input name="q" placeholder="Tìm sản phẩm..." value="<?= e($q) ?>"><button class="btn">Tìm</button></form>
    <a class="btn green" href="san-pham-form.php">+ Thêm sản phẩm</a>
</div>
<table><tr><th>ID</th><th>Tên sản phẩm</th><th>Danh mục</th><th>Hãng</th><th>Giá</th><th>Tồn</th><th style="width:150px">Thao tác</th></tr>
<?php foreach ($list as $p): ?>
<tr><td><?= $p['id'] ?></td><td><?= e($p['bieu_tuong'] . ' ' . $p['ten_sp']) ?></td><td><?= e($p['ten_danh_muc']) ?></td>
<td><?= e($p['ten_hang']) ?></td><td><?= vnd($p['gia']) ?></td><td><?= $p['so_luong_ton'] ?></td>
<td><a class="btn sm" href="san-pham-form.php?id=<?= $p['id'] ?>">Sửa</a>
<form method="post" style="display:inline" onsubmit="return confirm('Xóa sản phẩm này?')">
<input type="hidden" name="xoa" value="<?= $p['id'] ?>"><button class="btn red sm">Xóa</button></form></td></tr>
<?php endforeach; ?>
</table>
<div class="pager"><?php for ($i = 1; $i <= $pages; $i++): ?>
    <?= $i == $page ? "<span class='cur'>$i</span>" : '<a href="?' . e(http_build_query(['q' => $q, 'page' => $i])) . "\">$i</a>" ?>
<?php endfor; ?></div>
<?php require '_foot.php'; ?>
