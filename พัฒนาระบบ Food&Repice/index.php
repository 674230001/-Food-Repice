<?php
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Product.php';
require_once __DIR__ . '/app/helpers.php';

$productModel = new Product();
$action = $_GET['action'] ?? 'list';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$error = '';
$success = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $mode = $_POST['mode'] ?? '';
    try {
        if ($mode === 'create' || $mode === 'update') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'price' => trim($_POST['price'] ?? ''),
                'stock' => trim($_POST['stock'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];
            if ($data['name'] === '' || $data['category'] === '' || !is_numeric($data['price']) || $data['price'] < 0 || filter_var($data['stock'], FILTER_VALIDATE_INT) === false || $data['stock'] < 0) {
                throw new RuntimeException('กรุณากรอกชื่อ หมวดหมู่ ราคา และจำนวนสินค้าให้ถูกต้อง');
            }
            if ($mode === 'create') {
                $productModel->create($data);
                $_SESSION['flash'] = 'เพิ่มสินค้าเรียบร้อยแล้ว';
            } else {
                $productModel->update((int)($_POST['id'] ?? 0), $data);
                $_SESSION['flash'] = 'แก้ไขสินค้าเรียบร้อยแล้ว';
            }
        } elseif ($mode === 'delete') {
            $productModel->delete((int)($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'ลบสินค้าเรียบร้อยแล้ว';
        }
        header('Location: index.php');
        exit;
    } catch (Throwable $e) {
        $error = $e instanceof PDOException ? 'บันทึกข้อมูลไม่สำเร็จ กรุณาตรวจสอบข้อมูลและฐานข้อมูล' : $e->getMessage();
    }
}

$editing = null;
if ($action === 'edit' && $id) {
    $editing = $productModel->find($id);
    if (!$editing) { $error = 'ไม่พบรายการที่ต้องการแก้ไข'; $action = 'list'; }
}
$q = trim($_GET['q'] ?? '');
$products = $productModel->all($q);
$total = $productModel->count();
$stockValue = $productModel->stockValue();
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NOIRÉ — Product Studio</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php"><span class="brand-mark">N.</span><span>NOIRÉ <small>PRODUCT STUDIO</small></span></a>
  <nav><a class="nav-active" href="index.php">ภาพรวม</a><a href="#inventory">คลังสินค้า</a></nav>
  <a class="top-action" href="index.php?action=create">＋ เพิ่มสินค้า</a>
</header>
<main class="container">
  <section class="hero">
    <div><p class="eyebrow">INVENTORY MANAGEMENT / 01</p><h1>จัดการสินค้า<br><em>ในแบบของคุณ</em></h1><p class="hero-copy">พื้นที่จัดการคลังสินค้า เรียบง่าย ชัดเจน และเป็นระบบ</p></div>
    <div class="hero-stamp"><span>EST.</span><strong>2026</strong><small>LESS, BUT BETTER.</small></div>
  </section>
  <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <section class="stats">
    <article class="stat-card"><span class="stat-label">รายการสินค้าทั้งหมด</span><strong><?= number_format($total) ?></strong><span class="stat-foot">TOTAL PRODUCTS <b>↗</b></span></article>
    <article class="stat-card cream-card"><span class="stat-label">มูลค่าสินค้าในคลัง</span><strong>฿<?= number_format($stockValue, 2) ?></strong><span class="stat-foot">ESTIMATED STOCK VALUE <b>↗</b></span></article>
    <article class="stat-card"><span class="stat-label">ระบบจัดการ</span><strong>CRUD<span class="tiny-dot">.</span></strong><span class="stat-foot">CREATE · READ · UPDATE · DELETE</span></article>
  </section>

  <?php if ($action === 'create' || $action === 'edit'): ?>
  <section class="form-panel">
    <div class="section-heading"><div><p class="eyebrow">PRODUCT DETAILS</p><h2><?= $action === 'edit' ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่' ?></h2></div><a class="text-link" href="index.php">← กลับรายการ</a></div>
    <form method="post" class="product-form">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="mode" value="<?= $action === 'edit' ? 'update' : 'create' ?>">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
      <label>ชื่อสินค้า <input name="name" required maxlength="150" value="<?= e($editing['name'] ?? '') ?>" placeholder="เช่น กระเป๋าผ้าแคนวาส"></label>
      <label>หมวดหมู่ <input name="category" required maxlength="100" value="<?= e($editing['category'] ?? '') ?>" placeholder="เช่น Accessories"></label>
      <label>ราคา (บาท) <input name="price" type="number" min="0" step="0.01" required value="<?= e($editing['price'] ?? '') ?>" placeholder="0.00"></label>
      <label>จำนวนคงเหลือ <input name="stock" type="number" min="0" step="1" required value="<?= e($editing['stock'] ?? '0') ?>" placeholder="0"></label>
      <label class="full">รายละเอียด <textarea name="description" rows="3" placeholder="รายละเอียดสินค้าเพิ่มเติม..."><?= e($editing['description'] ?? '') ?></textarea></label>
      <div class="form-actions"><button class="button primary" type="submit"><?= $action === 'edit' ? 'บันทึกการแก้ไข' : 'บันทึกสินค้า' ?> <span>↗</span></button><a class="button secondary" href="index.php">ยกเลิก</a></div>
    </form>
  </section>
  <?php endif; ?>

  <section id="inventory" class="inventory">
    <div class="section-heading"><div><p class="eyebrow">YOUR COLLECTION</p><h2>รายการสินค้า <span class="count"><?= count($products) ?></span></h2></div>
      <form class="search" method="get"><input type="search" name="q" value="<?= e($q) ?>" placeholder="ค้นหาสินค้า / หมวดหมู่"><button aria-label="ค้นหา">⌕</button></form>
    </div>
    <div class="table-wrap">
      <table><thead><tr><th>สินค้า</th><th>หมวดหมู่</th><th>ราคา</th><th>คงเหลือ</th><th>สถานะ</th><th class="right">จัดการ</th></tr></thead>
      <tbody>
      <?php if (!$products): ?><tr><td colspan="6" class="empty">ยังไม่มีข้อมูลสินค้า — เพิ่มรายการแรกของคุณได้เลย</td></tr><?php endif; ?>
      <?php foreach ($products as $p): ?>
        <tr><td><div class="product-name"><span class="product-icon"><?= e(mb_strtoupper(mb_substr($p['name'],0,1,'UTF-8'),'UTF-8')) ?></span><div><strong><?= e($p['name']) ?></strong><small><?= e($p['description'] ?: 'ไม่มีรายละเอียด') ?></small></div></div></td>
        <td><span class="category"><?= e($p['category']) ?></span></td><td class="price">฿<?= number_format((float)$p['price'],2) ?></td><td><?= (int)$p['stock'] ?></td>
        <td><?php if ((int)$p['stock'] === 0): ?><span class="status out">หมดสินค้า</span><?php elseif ((int)$p['stock'] <= 5): ?><span class="status low">ใกล้หมด</span><?php else: ?><span class="status active">พร้อมขาย</span><?php endif; ?></td>
        <td class="right"><div class="row-actions"><a class="edit-link" href="index.php?action=edit&id=<?= (int)$p['id'] ?>">แก้ไข</a><form method="post" onsubmit="return confirm('ยืนยันลบสินค้า <?= e($p['name']) ?> ?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="mode" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="delete-link" type="submit">ลบ</button></form></div></td></tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
    <p class="table-note">แสดง <?= count($products) ?> รายการ<?= $q ? ' สำหรับคำค้น “'.e($q).'”' : '' ?> <span>ข้อมูลจัดเก็บด้วย MySQL · PHP OOP</span></p>
  </section>
  <footer><span>© 2026 NOIRÉ PRODUCT STUDIO</span><span>DESIGNED WITH INTENTION <b>✳</b></span></footer>
</main>
</body>
</html>
