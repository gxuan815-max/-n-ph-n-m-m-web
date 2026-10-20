<?php
$title = 'Quản lý liên hệ';
require '_head.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['xoa'])) { $pdo->prepare('DELETE FROM lien_he WHERE id = ?')->execute([(int)$_POST['xoa']]); flash('Đã xóa liên hệ.'); }
    elseif (isset($_POST['xuly'])) { $pdo->prepare('UPDATE lien_he SET da_xu_ly = 1 WHERE id = ?')->execute([(int)$_POST['xuly']]); flash('Đã đánh dấu đã xử lý.'); }
    redirect('lien-he.php');
}
$list = $pdo->query('SELECT * FROM lien_he ORDER BY da_xu_ly, id DESC')->fetchAll();
?>
<h2 style="margin-bottom:15px">Liên hệ từ khách hàng</h2>
<?php if (!$list): ?><div class="box">Chưa có liên hệ nào.</div><?php endif; ?>
<?php foreach ($list as $l): ?>
<div class="box"><b><?= e($l['tieu_de'] ?: '(Không tiêu đề)') ?></b> <span class="st"><?= $l['da_xu_ly'] ? 'Đã xử lý' : 'Chưa xử lý' ?></span>
    <p><?= e($l['ho_ten']) ?> &lt;<?= e($l['email']) ?>&gt; — <?= e($l['ngay_gui']) ?></p><p style="margin:8px 0"><?= nl2br(e($l['noi_dung'])) ?></p>
    <?php if (!$l['da_xu_ly']): ?><form method="post" style="display:inline"><input type="hidden" name="xuly" value="<?= $l['id'] ?>"><button class="btn sm green">✔ Đã xử lý</button></form><?php endif; ?>
    <form method="post" style="display:inline" onsubmit="return confirm('Xóa?')"><input type="hidden" name="xoa" value="<?= $l['id'] ?>"><button class="btn sm red">Xóa</button></form></div>
<?php endforeach; ?>
<?php require '_foot.php'; ?>
