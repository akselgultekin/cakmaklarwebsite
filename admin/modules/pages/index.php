<?php
$pageTitle    = 'Sayfa İçerikleri';
$activeModule = 'pages';
$action       = $_GET['action'] ?? 'index';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=pages'); exit;
    }
    $id      = (int) ($_POST['id'] ?? 0);
    $pageKey = trim($_POST['page_key'] ?? '');
    if (!$pageKey) { setFlash('error','Sayfa anahtarı zorunludur.'); header('Location: '.ADMIN_URL.'/?module=pages'); exit; }

    $data = [
        'page_key'    => $pageKey,
        'title'       => trim($_POST['title'] ?? ''),
        'subtitle'    => trim($_POST['subtitle'] ?? ''),
        'content'     => $_POST['content'] ?? '',
        'meta_title'  => trim($_POST['meta_title'] ?? ''),
        'meta_desc'   => trim($_POST['meta_desc'] ?? ''),
    ];

    if (!empty($_FILES['cover_image']['name'])) {
        try {
            $data['cover_image'] = uploadImage($_FILES['cover_image'], 'pages');
            if ($id) {
                $old = Database::queryOne("SELECT cover_image FROM pages WHERE id=?", [$id]);
                if ($old && $old['cover_image']) { $f = UPLOAD_PATH.'/'.ltrim($old['cover_image'],'/'); if (file_exists($f)) unlink($f); }
            }
        } catch (Exception $e) { setFlash('error', $e->getMessage()); }
    }

    if ($id) {
        $cols = implode(',', array_map(fn($k)=>"`$k`=?", array_keys($data)));
        Database::execute("UPDATE pages SET $cols WHERE id=?", array_merge(array_values($data), [$id]));
        setFlash('success','Sayfa güncellendi.');
    } else {
        $cols = implode(',', array_map(fn($k)=>"`$k`", array_keys($data)));
        $phs  = implode(',', array_fill(0, count($data), '?'));
        Database::execute("INSERT INTO pages ($cols) VALUES ($phs)", array_values($data));
        setFlash('success','Sayfa oluşturuldu.');
    }
    header('Location: '.ADMIN_URL.'/?module=pages'); exit;
}

if ($action === 'create' || $action === 'edit') {
    $page = $action === 'edit' ? Database::queryOne("SELECT * FROM pages WHERE id=?", [(int)($_GET['id']??0)]) : null;

    // Önceden tanımlı sayfa anahtarları
    $predefinedKeys = [
        'about'       => 'Biz Kimiz',
        'services'    => 'Hizmetlerimiz',
        'kvkk'        => 'KVKK / Gizlilik',
        'contact'     => 'İletişim Hero',
        'home_intro'  => 'Ana Sayfa Tanıtım',
    ];

    ob_start(); ?>
    <div class="page-header">
      <h1><?= $page ? 'Sayfa Düzenle' : 'Yeni Sayfa' ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=pages"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($page): ?><input type="hidden" name="id" value="<?= $page['id'] ?>"><?php endif; ?>
      <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
        <div class="card">
          <div class="card-header"><h3>Sayfa Bilgileri</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label>Sayfa Anahtarı (page_key) <span class="required">*</span></label>
              <?php if ($page): ?>
              <input class="form-control" name="page_key" value="<?= e($page['page_key']) ?>" readonly style="background:var(--soft);">
              <?php else: ?>
              <select class="form-control" name="page_key">
                <?php foreach ($predefinedKeys as $k => $l): ?>
                <option value="<?= $k ?>"><?= $k ?> — <?= $l ?></option>
                <?php endforeach; ?>
              </select>
              <div class="form-hint">Sayfanın kodda kullanılan benzersiz anahtarı.</div>
              <?php endif; ?>
            </div>
            <div class="form-group"><label>Sayfa Başlığı</label><input class="form-control" name="title" value="<?= e($page['title'] ?? '') ?>"></div>
            <div class="form-group"><label>Alt Başlık</label><input class="form-control" name="subtitle" value="<?= e($page['subtitle'] ?? '') ?>"></div>
            <div class="form-group">
              <label>İçerik (HTML destekli)</label>
              <textarea class="form-control" name="content" rows="14"><?= e($page['content'] ?? '') ?></textarea>
            </div>
            <div class="form-group"><label>Meta Başlık</label><input class="form-control" name="meta_title" value="<?= e($page['meta_title'] ?? '') ?>"></div>
            <div class="form-group"><label>Meta Açıklama</label><textarea class="form-control" name="meta_desc" rows="2"><?= e($page['meta_desc'] ?? '') ?></textarea></div>
          </div>
        </div>
        <div>
          <div class="card">
            <div class="card-header"><h3>Hero Görseli</h3></div>
            <div class="card-body">
              <?php if (!empty($page['cover_image'])): ?>
              <img src="<?= e(uploadUrl($page['cover_image'])) ?>" class="img-preview img-preview-lg" style="margin-bottom:10px;">
              <?php endif; ?>
              <input class="form-control" type="file" name="cover_image" accept="image/*">
            </div>
          </div>
          <div style="margin-top:14px;"><button class="btn btn-primary" type="submit" style="width:100%;min-height:46px;"><i class="fa-solid fa-floppy-disk"></i> Kaydet</button></div>
        </div>
      </div>
    </form>
    <?php
    $pageContent = ob_get_clean();
    require dirname(__DIR__, 2) . '/layout.php';
    return;
}

$pages = Database::query("SELECT * FROM pages ORDER BY page_key");
ob_start(); ?>
<div class="page-header">
  <h1>Sayfa İçerikleri (<?= count($pages) ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=pages&action=create"><i class="fa-solid fa-plus"></i> Yeni Sayfa</a>
</div>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Anahtar</th><th>Başlık</th><th>Alt Başlık</th><th>Güncelleme</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($pages)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">Sayfa içeriği yok. Seed.sql çalıştırılmış mı?</td></tr>
        <?php else: foreach ($pages as $p): ?>
        <tr>
          <td><code style="background:var(--soft);padding:3px 8px;border-radius:4px;font-size:12px;"><?= e($p['page_key']) ?></code></td>
          <td><?= e($p['title'] ?? '—') ?></td>
          <td><?= e(excerpt($p['subtitle'] ?? '', 60)) ?></td>
          <td><?= formatDate($p['updated_at'] ?? $p['created_at'] ?? date('Y-m-d'), 'd.m.Y') ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=pages&action=edit&id=<?= $p['id'] ?>"><i class="fa-solid fa-pen"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
