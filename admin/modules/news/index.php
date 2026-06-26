<?php
require_once APP_PATH . '/models/NewsModel.php';
$model  = new NewsModel();
$action = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=news'); exit; }
    $id = (int) $_GET['id'];
    $n  = $model->find($id);
    if ($n && $n['cover_image']) { $f = UPLOAD_PATH.'/'.ltrim($n['cover_image'],'/'); if (file_exists($f)) unlink($f); }
    $model->delete($id);
    setFlash('success','Haber silindi.');
    header('Location: '.ADMIN_URL.'/?module=news'); exit;
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id']; $n = $model->find($id);
    if ($n) $model->update($id, ['is_active' => $n['is_active'] ? 0 : 1]);
    header('Location: '.ADMIN_URL.'/?module=news'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=news'); exit;
    }
    $slug = slugify($_POST['slug'] ?: $_POST['title']);
    $ex   = Database::queryOne("SELECT id FROM news WHERE slug=? AND id!=?", [$slug, $id]);
    if ($ex) $slug = $slug . '-' . time();

    $pubDate = !empty($_POST['published_at']) ? $_POST['published_at'] : date('Y-m-d H:i:s');
    $data = [
        'title'        => trim($_POST['title']),
        'slug'         => $slug,
        'summary'      => trim($_POST['summary'] ?? ''),
        'content'      => $_POST['content'] ?? '',
        'published_at' => $pubDate,
        'is_active'    => isset($_POST['is_active']) ? 1 : 0,
        'meta_title'   => trim($_POST['meta_title'] ?? ''),
        'meta_desc'    => trim($_POST['meta_desc'] ?? ''),
    ];
    if (!empty($_FILES['cover_image']['name'])) {
        try { $data['cover_image'] = uploadImage($_FILES['cover_image'], 'news'); }
        catch (Exception $e) { setFlash('error', $e->getMessage()); header('Location: '.ADMIN_URL.'/?module=news&action='.($id?'edit&id='.$id:'create')); exit; }
    }

    if ($id) { $model->update($id, $data); setFlash('success','Haber güncellendi.'); }
    else      { $model->create($data);      setFlash('success','Haber oluşturuldu.'); }
    header('Location: '.ADMIN_URL.'/?module=news'); exit;
}

if ($action === 'create' || $action === 'edit') {
    $news         = $action === 'edit' ? $model->find((int)($_GET['id'] ?? 0)) : null;
    $pageTitle    = $news ? 'Haberi Düzenle' : 'Yeni Haber / Duyuru';
    $activeModule = 'news';

    ob_start(); ?>
    <div class="page-header">
      <h1><?= e($pageTitle) ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=news"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($news): ?><input type="hidden" name="id" value="<?= $news['id'] ?>"><?php endif; ?>
      <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>Haber Bilgileri</h3></div>
            <div class="card-body">
              <div class="form-group"><label>Başlık <span class="required">*</span></label><input class="form-control" name="title" required value="<?= e($news['title'] ?? '') ?>"></div>
              <div class="form-row">
                <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="<?= e($news['slug'] ?? '') ?>"></div>
                <div class="form-group"><label>Yayın Tarihi</label><input class="form-control" type="datetime-local" name="published_at" value="<?= e(isset($news['published_at']) ? date('Y-m-d\TH:i', strtotime($news['published_at'])) : date('Y-m-d\TH:i')) ?>"></div>
              </div>
              <div class="form-group"><label>Özet</label><textarea class="form-control" name="summary" rows="3"><?= e($news['summary'] ?? '') ?></textarea></div>
              <div class="form-group"><label>İçerik (HTML destekli)</label><textarea class="form-control" name="content" rows="12"><?= e($news['content'] ?? '') ?></textarea></div>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>SEO</h3></div>
            <div class="card-body">
              <div class="form-group"><label>Meta Başlık</label><input class="form-control" name="meta_title" value="<?= e($news['meta_title'] ?? '') ?>"></div>
              <div class="form-group"><label>Meta Açıklama</label><textarea class="form-control" name="meta_desc" rows="2"><?= e($news['meta_desc'] ?? '') ?></textarea></div>
            </div>
          </div>
        </div>
        <div>
          <div class="card" style="margin-bottom:14px;">
            <div class="card-header"><h3>Durum</h3></div>
            <div class="card-body">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_active" <?= !isset($news['is_active']) || $news['is_active']?'checked':'' ?>><span class="toggle-slider"></span></label> Aktif / Yayında
              </label>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Kapak Görseli</h3></div>
            <div class="card-body">
              <?php if (!empty($news['cover_image'])): ?><img src="<?= e(uploadUrl($news['cover_image'])) ?>" class="img-preview img-preview-lg" style="margin-bottom:10px;"><?php endif; ?>
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

$pageTitle    = 'Haberler & Duyurular';
$activeModule = 'news';
$page         = max(1, (int)($_GET['sayfa'] ?? 1));
$result       = $model->paginateAll($page, 20);

ob_start(); ?>
<div class="page-header">
  <h1>Haberler (<?= $result['total'] ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=news&action=create"><i class="fa-solid fa-plus"></i> Yeni Haber</a>
</div>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Görsel</th><th>Başlık</th><th>Yayın Tarihi</th><th>Aktif</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($result['data'])): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">Haber yok.</td></tr>
        <?php else: foreach ($result['data'] as $n): ?>
        <tr>
          <td><?php if ($n['cover_image']): ?><img class="td-img" src="<?= e(uploadUrl($n['cover_image'])) ?>"><?php else: ?>—<?php endif; ?></td>
          <td><strong><?= e($n['title']) ?></strong></td>
          <td><?= formatDate($n['published_at'], 'd.m.Y') ?></td>
          <td><a href="<?= ADMIN_URL ?>/?module=news&action=toggle&id=<?= $n['id'] ?>"><label class="toggle"><input type="checkbox" <?= $n['is_active']?'checked':'' ?> onclick="return false;"><span class="toggle-slider"></span></label></a></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= SITE_URL ?>/haberler/<?= e($n['slug']) ?>" target="_blank"><i class="fa-solid fa-eye"></i></a>
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=news&action=edit&id=<?= $n['id'] ?>"><i class="fa-solid fa-pen"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=news&action=delete&id=<?= $n['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= paginationLinks($result, ADMIN_URL . '/?module=news') ?>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
