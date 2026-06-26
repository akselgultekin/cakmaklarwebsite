<?php
$pageTitle    = 'Medya Kütüphanesi';
$activeModule = 'media';
$action       = $_GET['action'] ?? 'index';

// ── Upload ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload') {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=media'); exit;
    }
    $uploaded = 0; $errors = [];
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['name'] as $k => $name) {
            if (!$name) continue;
            $file = ['name'=>$name,'tmp_name'=>$_FILES['files']['tmp_name'][$k],'size'=>$_FILES['files']['size'][$k],'error'=>$_FILES['files']['error'][$k],'type'=>$_FILES['files']['type'][$k]];
            try {
                $path = uploadImage($file, 'media');
                Database::execute("INSERT INTO media (file_path,file_name,file_size,mime_type,uploaded_by) VALUES (?,?,?,?,?)",
                    [$path, $name, $file['size'], $file['type'], $_SESSION['admin_id'] ?? 0]);
                $uploaded++;
            } catch (Exception $e) { $errors[] = $e->getMessage(); }
        }
    }
    if ($uploaded) setFlash('success', "$uploaded dosya yüklendi.");
    if ($errors)   setFlash('error', implode(', ', $errors));
    header('Location: '.ADMIN_URL.'/?module=media'); exit;
}

// ── Delete ────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=media'); exit; }
    $id = (int) $_GET['id'];
    $m  = Database::queryOne("SELECT file_path FROM media WHERE id=?", [$id]);
    if ($m) {
        $f = UPLOAD_PATH . '/' . ltrim($m['file_path'], '/');
        if (file_exists($f)) unlink($f);
        Database::execute("DELETE FROM media WHERE id=?", [$id]);
    }
    setFlash('success','Dosya silindi.'); header('Location: '.ADMIN_URL.'/?module=media'); exit;
}

$page   = max(1, (int)($_GET['sayfa'] ?? 1));
$limit  = 24;
$offset = ($page - 1) * $limit;
$total  = Database::queryOne("SELECT COUNT(*) AS cnt FROM media")['cnt'] ?? 0;
$items  = Database::query("SELECT * FROM media ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$paginator = ['data'=>$items,'total'=>$total,'per_page'=>$limit,'current_page'=>$page,'last_page'=>max(1,ceil($total/$limit))];

ob_start(); ?>
<div class="page-header">
  <h1>Medya Kütüphanesi (<?= $total ?> dosya)</h1>
</div>

<!-- Yükleme Formu -->
<div class="card" style="margin-bottom:24px;">
  <div class="card-header"><h3><i class="fa-solid fa-cloud-arrow-up" style="color:var(--teal-2);"></i> Dosya Yükle</h3></div>
  <div class="card-body">
    <form method="POST" action="<?= ADMIN_URL ?>/?module=media&action=upload" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div style="border:2px dashed var(--line);border-radius:10px;padding:30px;text-align:center;background:var(--soft);">
        <i class="fa-solid fa-images" style="font-size:36px;color:var(--muted);margin-bottom:10px;display:block;"></i>
        <p style="margin-bottom:14px;">Görselleri buraya sürükleyin veya seçin</p>
        <input type="file" name="files[]" multiple accept="image/*" id="mediaUpload" style="display:none;">
        <label for="mediaUpload" class="btn btn-primary" style="cursor:pointer;">
          <i class="fa-solid fa-folder-open"></i> Dosya Seç
        </label>
        <div id="selectedFiles" style="margin-top:12px;color:var(--muted);font-size:13px;"></div>
      </div>
      <div style="margin-top:14px;text-align:right;">
        <button class="btn btn-teal" type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> Yükle</button>
      </div>
    </form>
  </div>
</div>

<!-- Galeri -->
<div class="card">
  <div class="card-header"><h3>Yüklü Dosyalar</h3></div>
  <div class="card-body">
    <?php if (empty($items)): ?>
    <p style="text-align:center;padding:30px;color:var(--muted);">Henüz dosya yüklenmemiş.</p>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;">
      <?php foreach ($items as $m): ?>
      <div class="media-item" style="border:1px solid var(--line);border-radius:8px;overflow:hidden;background:var(--soft);">
        <div style="position:relative;aspect-ratio:4/3;overflow:hidden;">
          <img src="<?= e(uploadUrl($m['file_path'])) ?>" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 75&quot;><rect width=&quot;100&quot; height=&quot;75&quot; fill=&quot;%23E5EAF0&quot;/></svg>'">
          <div style="position:absolute;inset:0;background:rgba(10,31,68,.5);opacity:0;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;" class="media-overlay">
            <button onclick="copyUrl('<?= e(uploadUrl($m['file_path'])) ?>')" class="btn btn-teal btn-sm btn-icon" title="URL Kopyala"><i class="fa-solid fa-copy"></i></button>
            <a href="<?= ADMIN_URL ?>/?module=media&action=delete&id=<?= $m['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')" class="btn btn-danger btn-sm btn-icon" title="Sil"><i class="fa-solid fa-trash"></i></a>
          </div>
        </div>
        <div style="padding:8px;font-size:11px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($m['file_name']) ?>">
          <?= e(substr($m['file_name'],0,22).(strlen($m['file_name'])>22?'…':'')) ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?= paginationLinks($paginator, ADMIN_URL . '/?module=media') ?>

<div id="copyToast" style="position:fixed;bottom:24px;right:24px;background:var(--navy);color:#fff;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:600;display:none;z-index:9999;box-shadow:0 4px 18px rgba(0,0,0,.25);">
  <i class="fa-solid fa-circle-check" style="color:var(--teal);"></i> URL kopyalandı!
</div>

<style>
.media-item:hover .media-overlay { opacity: 1 !important; }
</style>
<script>
function copyUrl(url) {
  navigator.clipboard.writeText(url).then(() => {
    const t = document.getElementById('copyToast');
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2000);
  });
}
document.getElementById('mediaUpload').addEventListener('change', function() {
  const n = this.files.length;
  document.getElementById('selectedFiles').textContent = n ? n + ' dosya seçildi' : '';
});
</script>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
