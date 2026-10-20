<?php
$title = 'Tổng quan';
require '_head.php';
$n = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$doanhthu = (int)$pdo->query("SELECT COALESCE(SUM(tong_tien),0) FROM don_hang WHERE trang_thai <> 'da_huy'")->fetchColumn();
$top = $pdo->query('SELECT sp.ten_sp, SUM(c.so_luong) AS sl FROM chi_tiet_don_hang c JOIN san_pham sp ON sp.id = c.san_pham_id
                    JOIN don_hang d ON d.id = c.don_hang_id WHERE d.trang_thai <> "da_huy" GROUP BY sp.id ORDER BY sl DESC LIMIT 5')->fetchAll();
?>
<h2 style="margin-bottom:15px">Xin chào, <?= e($_SESSION['admin']['ho_ten']) ?></h2>
<div class="stat">
    <div><b><?= $n('SELECT COUNT(*) FROM san_pham') ?></b>Sản phẩm</div>
    <div><b><?= $n('SELECT COUNT(*) FROM don_hang') ?></b>Đơn hàng</div>
    <div><b><?= $n("SELECT COUNT(*) FROM don_hang WHERE trang_thai='cho_xac_nhan'") ?></b>Đơn chờ xác nhận</div>
    <div><b><?= $n('SELECT COUNT(*) FROM khach_hang') ?></b>Khách hàng</div>
    <div><b><?= vnd($doanhthu) ?></b>Doanh thu (trừ đơn hủy)</div>
    <div><b><?= $n('SELECT COUNT(*) FROM lien_he WHERE da_xu_ly = 0') ?></b>Liên hệ chưa xử lý</div>
</div>
<div class="box"><h3 style="margin-bottom:10px">Top 5 sản phẩm bán chạy</h3>
<?php if (!$top): ?>Chưa có dữ liệu bán hàng.<?php endif; ?>
<?php foreach ($top as $t): ?><p><?= e($t['ten_sp']) ?> — <b><?= $t['sl'] ?></b> sản phẩm</p><?php endforeach; ?></div>
<?php require '_foot.php'; ?>
