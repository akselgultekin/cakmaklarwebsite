<?php
$pageTitle    = 'Slider Yönetimi';
$activeModule = 'sliders';
$action       = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=sliders'); exit; }
    $id = (int) $_GET['id'];
    $s  = Database::queryOne("SELECT image FROM sliders WHERE id=?", [$id]);
    if ($s && $s['image']) { $f = UPLOAD_PATH.'/'.ltrim($s['image'],'/'); if (file_exists($f)) unlink($f); }
    Database::execute("DELETE FROM sliders WHERE id=?", [$id]);
    setFlash('success','Slider silindi.'); header('Location: '.ADMIN_URL.'/?module=sliders'); exit;
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $s  = Database::queryOne("SELECT is_active FROM sliders WHERE id=?", [$id]);
    if ($s) Database::execute("UPDATE sliders SET is_active=? WHERE id=?", [$s['is_active'] ? 0 : 1, $id]);
    header('Location: '.ADMIN_URL.'/?module=sliders'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=sliders'); exit;
    }
    $id   = (int) ($_POST['id'] ?? 0);
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'subtitle'    => trim($_POST['subtitle'] ?? ''),
        'btn_text'    => trim($_POST['btn_text'] ?? ''),
        'btn_url'     => trim($_POST['btn_url'] ?? ''),
        'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']['name'])) {
        try {
            $data['image'] = uploadImage($_FILES['image'], 'sliders');
            if ($id) {
                $old = Database::queryOne("SELECT image FROM sliders WHERE id=?", [$id]);
                if ($old && $old['image']) { $f = UPLOAD_PATH.'/'.ltrim($old['image'],'/'); if (file_exists($f)) unlink($f); }
            }
        } catch (Exception $e) { setFlash('error', $e->getMessage()); header('Location: '.ADMIN_URL.'/?module=sliders'); exit; }
    } elseif (!$id) {
        setFlash('error','Slider için görsel zorunludur.'); header('Location: '.ADMIN_URL.'/?module=sliders&action=create'); exit;
    }

    if ($id) {
        Database::execute("UPDATE sliders SET title=?,subtitle=?,btn_text=?,btn_url=?,sort_order=?,is_active=?" . (isset($data['image'])?',image=?':'') . " WHERE id=?",
            array_merge([$data['title'],$data['subtitle'],$data['btn_text'],$data['btn_url'],$data['sort_order'],$data['is_active']], isset($data['image'])?[$data['image']]:[], [$id]));
        setFlash('success','Slider güncellendi.');
    } else {
        Database::execute("INSERT INTO sliders (title,subtitle,btn_text,btn_url,image,sort_order,is_active) VALUES (?,?,?,?,?,?,?)",
            [$data['title'],$data['subtitle'],$data['btn_text'],$data['btn_url'],$data['image']??'',$data['sort_order'],$data['is_active']]);
        setFlash('success','Slider eklendi.');
    }
    header('Location: '.ADMIN_URL.'/?module=sliders'); exit;
}

if ($action === 'create' || $action === 'edit') {
    $slider = $action === 'edit' ? Database::queryOne("SELECT * FROM sliders WHERE id=?", [(int)($_GET['id']??0)]) : null;
    ob_start(); ?>
    <div class="page-header">
      <h1><?= $slider ? 'Slider Düzenle' : 'Yeni Slider' ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=sliders"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($slider): ?><input type="hidden" name="id" value="<?= $slider['id'] ?>"><?php endif; ?>
      <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
        <div class="card">
          <div class="card-header"><h3>Slider Bilgileri</h3></div>
          <div class="card-body">
            <div class="form-group"><label>Başlık</label><input class="form-control" name="title" value="<?= e($slider['title'] ?? '') ?>"></div>
            <div class="form-group"><label>Alt Başlık / Açıklama</label><textarea class="form-control" name="subtitle" rows="2"><?= e($slider['subtitle'] ?? '') ?></textarea></div>
            <div class="form-row">
              <div class="form-group"><label>Buton Metni</label><input class="form-control" name="btn_text" value="<?= e($slider['btn_text'] ?? 'Keşfet') ?>"></div>
              <div class="form-group"><label>Buton Linki</label><input class="form-control" name="btn_url" value="<?= e($slider['btn_url'] ?? '/projeler') ?>"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Sıra</label><input class="form-control" type="number" name="sort_order" value="<?= e($slider['sort_order'] ?? 0) ?>"></div>
              <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px;">
                <label class="toggle"><input type="checkbox" name="is_active" <?= !isset($slider['is_active'])||$slider['is_active']?'checked':'' ?>><span class="toggle-slider"></span></label> <span>Aktif</span>
              </div>
            </div>
          </div>
        </div>
        <div>
          <div class="card">
            <div class="card-header"><h3>Arka Plan Görseli <?= !$slider?'<span class="required">*</span>':'' ?></h3></div>
            <div class="card-body">
              <?php if (!empty($slider['image'])): ?>
              <img src="<?= e(uploadUrl($slider['image'])) ?>" style="width:100%;height:140px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
              <?php endif; ?>
              <input class="form-control" type="file" name="image" accept="image/*" <?= !$slider ? 'required' : '' ?>>
              <div class="form-hint">Önerilen: 1920×1080px, JPG</div>
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

$sliders = Database::query("SELECT * FROM sliders ORDER BY sort_order, id");
ob_start(); ?>
<div class="page-header">
  <h1>Slider (<?= count($sliders) ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=sliders&action=create"><i class="fa-solid fa-plus"></i> Yeni Slider</a>
</div>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Görsel</th><th>Başlık</th><th>Alt Başlık</th><th>Buton</th><th>Sıra</th><th>Aktif</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($sliders)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">Slider yok.</td></tr>
        <?php else: foreach ($sliders as $s): ?>
        <tr>
          <td><img class="td-img" src="<?= e(uploadUrl($s['image'])) ?>" style="width:90px;" onerror="this.src='https://via.placeholder.com/90x50'"></td>
          <td><strong><?= e($s['title'] ?: '—') ?></strong></td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e(excerpt($s['subtitle'] ?? '', 60)) ?></td>
          <td><?= ($s['btn1_text'] ?? '') ? '<a href="'.e($s['btn1_url'] ?? '').'" target="_blank" class="badge badge-info">'.e($s['btn1_text']).'</a>' : '—' ?></td>
          <td><?= e($s['sort_order']) ?></td>
          <td><a href="<?= ADMIN_URL ?>/?module=sliders&action=toggle&id=<?= $s['id'] ?>"><label class="toggle"><input type="checkbox" <?= $s['is_active']?'checked':'' ?> onclick="return false;"><span class="toggle-slider"></span></label></a></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=sliders&action=edit&id=<?= $s['id'] ?>"><i class="fa-solid fa-pen"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=sliders&action=delete&id=<?= $s['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')"><i class="fa-solid fa-trash"></i></a>
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
