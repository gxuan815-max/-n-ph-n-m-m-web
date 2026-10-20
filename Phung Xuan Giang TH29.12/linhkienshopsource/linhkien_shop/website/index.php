<?php
$title = 'Trang chủ';
require 'includes/header.php';

$q = trim($_GET['q'] ?? '');
$dm = (int)($_GET['dm'] ?? 0);
$min = ($_GET['min'] ?? '') !== '' ? (int)$_GET['min'] * 1000000 : null; // nhập theo đơn vị triệu đồng
$max = ($_GET['max'] ?? '') !== '' ? (int)$_GET['max'] * 1000000 : null;
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 12;

$where = ['1=1']; $params = [];
if ($q !== '')   { $where[] = 'sp.ten_sp LIKE ?'; $params[] = "%$q%"; }
if ($dm > 0)     { $where[] = 'sp.danh_muc_id = ?'; $params[] = $dm; }
if ($min !== null) { $where[] = 'sp.gia >= ?'; $params[] = $min; }
if ($max !== null) { $where[] = 'sp.gia <= ?'; $params[] = $max; }
$w = implode(' AND ', $where);

$st = $pdo->prepare("SELECT COUNT(*) FROM san_pham sp WHERE $w");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$off = ($page - 1) * $per;

$st = $pdo->prepare("SELECT sp.*, h.ten_hang FROM san_pham sp JOIN nha_san_xuat h ON h.id = sp.hang_id
                     WHERE $w ORDER BY sp.id LIMIT $per OFFSET $off");
$st->execute($params);
$list = $st->fetchAll();
$cats = $pdo->query('SELECT * FROM danh_muc ORDER BY id')->fetchAll();

$qs = $_GET; unset($qs['page']);
?>
<h2 style="margin-bottom:15px">Danh sách sản phẩm (<?= $total ?>)</h2>
<form class="filter" method="get">
    <input type="text" name="q" placeholder="Tìm tên sản phẩm..." value="<?= e($q) ?>">
    <select name="dm"><option value="0">Tất cả danh mục</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $dm == $c['id'] ? 'selected' : '' ?>><?= e($c['ten_danh_muc']) ?></option><?php endforeach; ?>
    </select>
    <input type="number" name="min" min="0" placeholder="Giá từ (triệu)" value="<?= e($_GET['min'] ?? '') ?>">
    <input type="number" name="max" min="0" placeholder="Đến (triệu)" value="<?= e($_GET['max'] ?? '') ?>">
    <button class="btn">🔍 Lọc</button> <a class="btn gray" href="index.php">Xóa lọc</a>
</form>

<?php if (!$list): ?><div class="box">Không tìm thấy sản phẩm phù hợp.</div><?php endif; ?>
<div class="grid">
<?php foreach ($list as $p): ?>
    <div class="card">
        <div class="img"><?= e($p['bieu_tuong']) ?><small><?= e($p['ten_hang']) ?></small></div>
        <div class="body">
            <h3><?= e($p['ten_sp']) ?></h3>
            <div class="price"><?= vnd($p['gia']) ?></div>
            <a class="btn sm" href="san-pham.php?id=<?= $p['id'] ?>">Chi tiết</a>
            <a class="btn sm green" href="gio-hang.php?add=<?= $p['id'] ?>">+ Giỏ hàng</a>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="pager">
<?php for ($i = 1; $i <= $pages; $i++): ?>
    <?php if ($i == $page): ?><span class="cur"><?= $i ?></span>
    <?php else: ?><a href="?<?= e(http_build_query($qs + ['page' => $i])) ?>"><?= $i ?></a><?php endif; ?>
<?php endfor; ?>
</div>
<?php require 'includes/footer.php'; ?>
