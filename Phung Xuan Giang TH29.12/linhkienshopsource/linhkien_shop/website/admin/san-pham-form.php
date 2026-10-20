<?php
$title = 'Thêm / Sửa sản phẩm';
require '_head.php';
$id = (int)($_GET['id'] ?? 0);
$p = ['ten_sp' => '', 'danh_muc_id' => 0, 'hang_id' => 0, 'gia' => '', 'so_luong_ton' => '', 'mo_ta' => '', 'bieu_tuong' => '📦'];
if ($id) {
    $st = $pdo->prepare('SELECT * FROM san_pham WHERE id = ?'); $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) { flash('Không tìm thấy sản phẩm.', 'error'); redirect('san-pham.php'); }
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [trim($_POST['ten_sp'] ?? ''), (int)$_POST['danh_muc_id'], 0, (int)($_POST['gia'] ?? -1), (int)($_POST['so_luong_ton'] ?? -1),
          trim($_POST['mo_ta'] ?? ''), trim($_POST['bieu_tuong'] ?? '') ?: '📦'];
    // hãng: chọn có sẵn hoặc nhập mới
    $hang = trim($_POST['hang_moi'] ?? '');
    if ($hang !== '') {
        $pdo->prepare('INSERT IGNORE INTO nha_san_xuat (ten_hang) VALUES (?)')->execute([strtoupper($hang)]);
        $s = $pdo->prepare('SELECT id FROM nha_san_xuat WHERE ten_hang = ?'); $s->execute([strtoupper($hang)]);
        $d[2] = (int)$s->fetchColumn();
    } else { $d[2] = (int)$_POST['hang_id']; }
    if ($d[0] === '' || !$d[1] || !$d[2] || $d[3] < 0 || $d[4] < 0) {
        $err = 'Vui lòng nhập tên, chọn danh mục/hãng, giá và tồn kho hợp lệ (≥ 0).';
        $p = array_merge($p, ['ten_sp' => $d[0], 'danh_muc_id' => $d[1], 'hang_id' => $d[2], 'gia' => $_POST['gia'], 'so_luong_ton' => $_POST['so_luong_ton'], 'mo_ta' => $d[5], 'bieu_tuong' => $d[6]]);
    } else {
        if ($id) {                                                           // SỬA
            $pdo->prepare('UPDATE san_pham SET ten_sp=?, danh_muc_id=?, hang_id=?, gia=?, so_luong_ton=?, mo_ta=?, bieu_tuong=? WHERE id=?')
                ->execute([...$d, $id]);
            flash('Đã cập nhật sản phẩm.');
        } else {                                                             // THÊM
            $pdo->prepare('INSERT INTO san_pham (ten_sp, danh_muc_id, hang_id, gia, so_luong_ton, mo_ta, bieu_tuong) VALUES (?,?,?,?,?,?,?)')
                ->execute($d);
            flash('Đã thêm sản phẩm mới.');
        }
        redirect('san-pham.php');
    }
}
$cats = $pdo->query('SELECT * FROM danh_muc ORDER BY id')->fetchAll();
$brands = $pdo->query('SELECT * FROM nha_san_xuat ORDER BY ten_hang')->fetchAll();
?>
<h2 style="margin-bottom:15px"><?= $id ? 'Sửa sản phẩm #' . $id : 'Thêm sản phẩm mới' ?></h2>
<?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="box" style="max-width:700px">
    <div class="form-row"><label>Tên sản phẩm *</label><input name="ten_sp" required value="<?= e($p['ten_sp']) ?>"></div>
    <div class="form-row"><label>Danh mục *</label><select name="danh_muc_id" required><option value="">-- Chọn --</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $p['danh_muc_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['ten_danh_muc']) ?></option><?php endforeach; ?></select></div>
    <div class="form-row"><label>Hãng *</label><select name="hang_id"><option value="">-- Chọn hãng có sẵn --</option>
        <?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>" <?= $p['hang_id'] == $b['id'] ? 'selected' : '' ?>><?= e($b['ten_hang']) ?></option><?php endforeach; ?></select>
        <small>Hoặc nhập hãng mới:</small><input name="hang_moi" placeholder="Tên hãng mới (để trống nếu chọn ở trên)"></div>
    <div class="form-row"><label>Giá (VNĐ) *</label><input type="number" name="gia" min="0" required value="<?= e($p['gia']) ?>"></div>
    <div class="form-row"><label>Tồn kho *</label><input type="number" name="so_luong_ton" min="0" required value="<?= e($p['so_luong_ton']) ?>"></div>
    <div class="form-row"><label>Biểu tượng (emoji)</label><input name="bieu_tuong" maxlength="4" value="<?= e($p['bieu_tuong']) ?>"></div>
    <div class="form-row"><label>Mô tả</label><textarea name="mo_ta" rows="3"><?= e($p['mo_ta']) ?></textarea></div>
    <button class="btn green">Lưu</button> <a class="btn gray" href="san-pham.php">Hủy</a>
</form>
<?php require '_foot.php'; ?>
