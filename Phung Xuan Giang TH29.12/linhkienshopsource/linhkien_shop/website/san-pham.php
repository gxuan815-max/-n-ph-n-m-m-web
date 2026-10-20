<?php
require 'includes/config.php';
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT sp.*, h.ten_hang, d.ten_danh_muc FROM san_pham sp
    JOIN nha_san_xuat h ON h.id = sp.hang_id JOIN danh_muc d ON d.id = sp.danh_muc_id WHERE sp.id = ?');
$st->execute([$id]);
$p = $st->fetch();
if (!$p) { flash('Không tìm thấy sản phẩm.', 'error'); redirect('index.php'); }

// Gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? ''); $sao = (int)($_POST['sao'] ?? 0); $bl = trim($_POST['binh_luan'] ?? '');
    if ($ten === '' || $bl === '' || $sao < 1 || $sao > 5) {
        flash('Vui lòng nhập đủ tên, số sao (1-5) và bình luận.', 'error');
    } else {
        $pdo->prepare('INSERT INTO danh_gia (san_pham_id, ten_nguoi_danh_gia, so_sao, binh_luan) VALUES (?,?,?,?)')
            ->execute([$id, $ten, $sao, $bl]);
        flash('Cảm ơn bạn đã đánh giá!');
    }
    redirect("san-pham.php?id=$id");
}

$rv = $pdo->prepare('SELECT * FROM danh_gia WHERE san_pham_id = ? ORDER BY id DESC');
$rv->execute([$id]);
$reviews = $rv->fetchAll();
$avg = $reviews ? array_sum(array_column($reviews, 'so_sao')) / count($reviews) : 0;

$title = $p['ten_sp'];
require 'includes/header.php';
?>
<a href="index.php">← Quay lại danh sách</a>
<div class="box two" style="margin-top:15px">
    <div class="card"><div class="img" style="height:280px;font-size:90px"><?= e($p['bieu_tuong']) ?><small><?= e($p['ten_hang']) ?></small></div></div>
    <div>
        <small><?= e($p['ten_danh_muc']) ?> • <?= e($p['ten_hang']) ?></small>
        <h1 style="margin:8px 0"><?= e($p['ten_sp']) ?></h1>
        <div class="stars">⭐ <?= $reviews ? number_format($avg, 1) : 'Chưa có' ?> (<?= count($reviews) ?> đánh giá)</div>
        <div class="price" style="font-size:30px;margin:15px 0"><?= vnd($p['gia']) ?></div>
        <p><?= e($p['mo_ta']) ?></p>
        <p style="margin:12px 0">Tình trạng: <?= $p['so_luong_ton'] > 0 ? "✅ Còn {$p['so_luong_ton']} sản phẩm" : '❌ Hết hàng' ?></p>
        <?php if ($p['so_luong_ton'] > 0): ?>
        <form method="get" action="gio-hang.php" style="display:flex;gap:10px;max-width:300px">
            <input type="hidden" name="add" value="<?= $p['id'] ?>">
            <input type="number" name="qty" value="1" min="1" max="<?= $p['so_luong_ton'] ?>">
            <button class="btn green">🛒 Thêm vào giỏ</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="two">
    <div class="box">
        <h3 style="margin-bottom:12px">Viết đánh giá</h3>
        <form method="post">
            <div class="form-row"><label>Tên của bạn</label><input name="ten" required></div>
            <div class="form-row"><label>Số sao</label>
                <select name="sao"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= str_repeat('⭐', $i) ?></option><?php endfor; ?></select></div>
            <div class="form-row"><label>Bình luận</label><textarea name="binh_luan" rows="3" required></textarea></div>
            <button class="btn">Gửi đánh giá</button>
        </form>
    </div>
    <div>
        <h3 style="margin-bottom:12px">Đánh giá của khách hàng</h3>
        <?php if (!$reviews): ?><div class="box">Chưa có đánh giá nào.</div><?php endif; ?>
        <?php foreach ($reviews as $r): ?>
            <div class="box" style="padding:14px"><b><?= e($r['ten_nguoi_danh_gia']) ?></b>
                <span class="stars"><?= str_repeat('⭐', $r['so_sao']) ?></span>
                <p><?= nl2br(e($r['binh_luan'])) ?></p><small><?= e($r['ngay_danh_gia']) ?></small></div>
        <?php endforeach; ?>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
