<?php
require_once APP_PATH . '/models/VehicleModel.php';
$model  = new VehicleModel();
$action = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=vehicles'); exit; }
    $id = (int) $_GET['id'];
    $imgs = $model->getImages($id);
    foreach ($imgs as $img) { $f = UPLOAD_PATH.'/'.ltrim($img['image'],'/'); if (file_exists($f)) unlink($f); }
    $model->delete($id);
    setFlash('success','Araç ilanı silindi.');
    header('Location: '.ADMIN_URL.'/?module=vehicles'); exit;
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id']; $v = $model->find($id);
    if ($v) $model->update($id, ['is_active' => $v['is_active'] ? 0 : 1]);
    header('Location: '.ADMIN_URL.'/?module=vehicles'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=vehicles'); exit;
    }
    $slug = slugify($_POST['slug'] ?: $_POST['title']);
    $ex   = Database::queryOne("SELECT id FROM vehicles WHERE slug=? AND id!=?", [$slug, $id]);
    if ($ex) $slug = $slug . '-' . time();

    $data = [
        'title'        => trim($_POST['title']),
        'slug'         => $slug,
        'brand'        => trim($_POST['brand'] ?? ''),
        'model'        => trim($_POST['model_name'] ?? ''),
        'year'         => (int) ($_POST['year'] ?? 0) ?: null,
        'km'           => (int) ($_POST['km'] ?? 0) ?: null,
        'fuel_type'    => trim($_POST['fuel_type'] ?? ''),
        'transmission' => trim($_POST['transmission'] ?? ''),
        'color'        => trim($_POST['color'] ?? ''),
        'engine_cc'    => trim($_POST['engine_cc'] ?? ''),
        'price'        => (float) str_replace(['.', ','], ['', '.'], $_POST['price'] ?? 0),
        'currency'     => $_POST['currency'] ?? 'TL',
        'description'  => $_POST['description'] ?? '',
        'is_active'    => isset($_POST['is_active']) ? 1 : 0,
        'meta_title'   => trim($_POST['meta_title'] ?? ''),
        'meta_desc'    => trim($_POST['meta_desc'] ?? ''),
    ];

    if (!empty($_FILES['cover_image']['name'])) {
        try { $data['cover_image'] = uploadImage($_FILES['cover_image'], 'vehicles'); }
        catch (Exception $e) { setFlash('error', $e->getMessage()); header('Location: '.ADMIN_URL.'/?module=vehicles&action='.($id?'edit&id='.$id:'create')); exit; }
    }

    if ($id) { $model->update($id, $data); $newId = $id; setFlash('success','Araç güncellendi.'); }
    else      { $newId = $model->create($data); setFlash('success','Araç oluşturuldu.'); }

    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $k => $name) {
            if (!$name) continue;
            $file = ['name'=>$name,'tmp_name'=>$_FILES['gallery']['tmp_name'][$k],'size'=>$_FILES['gallery']['size'][$k],'error'=>$_FILES['gallery']['error'][$k],'type'=>$_FILES['gallery']['type'][$k]];
            try { $path = uploadImage($file, 'vehicles'); Database::execute("INSERT INTO vehicle_images (vehicle_id,image,sort_order) VALUES (?,?,?)", [$newId, $path, $k]); } catch (Exception $e) {}
        }
    }
    if (!empty($_POST['delete_images'])) {
        foreach ((array)$_POST['delete_images'] as $imgId) {
            $img = Database::queryOne("SELECT image FROM vehicle_images WHERE id=?", [(int)$imgId]);
            if ($img) { $f = UPLOAD_PATH.'/'.ltrim($img['image'],'/'); if (file_exists($f)) unlink($f); Database::execute("DELETE FROM vehicle_images WHERE id=?", [(int)$imgId]); }
        }
    }
    header('Location: '.ADMIN_URL.'/?module=vehicles'); exit;
}

if ($action === 'create' || $action === 'edit') {
    $vehicle    = $action === 'edit' ? $model->find((int)($_GET['id'] ?? 0)) : null;
    $images     = $vehicle ? $model->getImages($vehicle['id']) : [];
    $pageTitle  = $vehicle ? 'Araç Düzenle' : 'Yeni Araç İlanı';
    $activeModule = 'vehicles';

    ob_start(); ?>
    <div class="page-header">
      <h1><?= e($pageTitle) ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=vehicles"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($vehicle): ?><input type="hidden" name="id" value="<?= $vehicle['id'] ?>"><?php endif; ?>
      <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>Araç Bilgileri</h3></div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group"><label>İlan Başlığı <span class="required">*</span></label><input class="form-control" name="title" required value="<?= e($vehicle['title'] ?? '') ?>"></div>
                <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="<?= e($vehicle['slug'] ?? '') ?>"></div>
              </div>
              <div class="form-row-3">
                <div class="form-group"><label>Marka</label><input class="form-control" name="brand" value="<?= e($vehicle['brand'] ?? '') ?>"></div>
                <div class="form-group"><label>Model</label><input class="form-control" name="model_name" value="<?= e($vehicle['model'] ?? '') ?>"></div>
                <div class="form-group"><label>Yıl</label><input class="form-control" name="year" type="number" value="<?= e($vehicle['year'] ?? '') ?>"></div>
              </div>
              <div class="form-row-3">
                <div class="form-group"><label>KM</label><input class="form-control" name="km" type="number" value="<?= e($vehicle['km'] ?? '') ?>"></div>
                <div class="form-group">
                  <label>Yakıt Tipi</label>
                  <select class="form-control" name="fuel_type">
                    <?php foreach (['Benzin','Dizel','Hibrit','Elektrik','LPG'] as $ft): ?>
                    <option <?= ($vehicle['fuel'] ?? '') === $ft ? 'selected' : '' ?>><?= $ft ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Vites</label>
                  <select class="form-control" name="transmission">
                    <?php foreach (['Otomatik','Manuel','Yarı Otomatik'] as $tr): ?>
                    <option <?= ($vehicle['transmission']??'')====$tr?'selected':'' ?>><?= $tr ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Renk</label><input class="form-control" name="color" value="<?= e($vehicle['color'] ?? '') ?>"></div>
                <div class="form-group"><label>Motor Hacmi</label><input class="form-control" name="engine_cc" value="<?= e($vehicle['engine_cc'] ?? '') ?>" placeholder="1.6"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label>Fiyat</label><input class="form-control" name="price" value="<?= e($vehicle['price'] ?? '') ?>"></div>
                <div class="form-group">
                  <label>Para Birimi</label>
                  <select class="form-control" name="currency">
                    <?php foreach (['TL','USD','EUR'] as $c): ?>
                    <option <?= ($vehicle['currency']??'TL')===$c?'selected':'' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-group"><label>Açıklama</label><textarea class="form-control" name="description" rows="5"><?= e($vehicle['description'] ?? '') ?></textarea></div>
            </div>
          </div>

          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>Galeri</h3></div>
            <div class="card-body">
              <?php if (!empty($images)): ?>
              <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:12px;">
                <?php foreach ($images as $img): ?>
                <div style="position:relative;">
                  <img src="<?= e(uploadUrl($img['image'])) ?>" style="width:100%;height:72px;object-fit:cover;border-radius:6px;border:1px solid var(--line);">
                  <label style="position:absolute;top:3px;right:3px;background:rgba(192,57,43,.9);color:#fff;border-radius:4px;padding:2px 5px;font-size:10px;cursor:pointer;">
                    <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="display:none;"> ✕
                  </label>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <input class="form-control" type="file" name="gallery[]" multiple accept="image/*">
            </div>
          </div>

          <div class="card">
            <div class="card-header"><h3>SEO</h3></div>
            <div class="card-body">
              <div class="form-group"><label>Meta Başlık</label><input class="form-control" name="meta_title" value="<?= e($vehicle['meta_title'] ?? '') ?>"></div>
              <div class="form-group"><label>Meta Açıklama</label><textarea class="form-control" name="meta_desc" rows="2"><?= e($vehicle['meta_desc'] ?? '') ?></textarea></div>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="margin-bottom:14px;">
            <div class="card-header"><h3>Durum</h3></div>
            <div class="card-body">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_active" <?= !isset($vehicle['is_active']) || $vehicle['is_active']?'checked':'' ?>><span class="toggle-slider"></span></label> Aktif
              </label>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Kapak Görseli</h3></div>
            <div class="card-body">
              <?php if (!empty($vehicle['cover_image'])): ?><img src="<?= e(uploadUrl($vehicle['cover_image'])) ?>" class="img-preview img-preview-lg" style="margin-bottom:10px;"><?php endif; ?>
              <input class="form-control" type="file" name="cover_image" accept="image/*">
            </div>
          </div>
          <div style="margin-top:14px;">
            <button class="btn btn-primary" type="submit" style="width:100%;min-height:46px;"><i class="fa-solid fa-floppy-disk"></i> Kaydet</button>
          </div>
        </div>
      </div>
    </form>
    <?php
    $pageContent = ob_get_clean();
    require dirname(__DIR__, 2) . '/layout.php';
    return;
}

$pageTitle    = 'Araç İlanları';
$activeModule = 'vehicles';
$page         = max(1, (int)($_GET['sayfa'] ?? 1));
$result       = $model->paginateAll($page, 20);

ob_start(); ?>
<div class="page-header">
  <h1>Araç İlanları (<?= $result['total'] ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=vehicles&action=create"><i class="fa-solid fa-plus"></i> Yeni Araç</a>
</div>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Görsel</th><th>Başlık</th><th>Marka/Model</th><th>Yıl</th><th>KM</th><th>Fiyat</th><th>Aktif</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($result['data'])): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:30px;">Araç ilanı yok.</td></tr>
        <?php else: foreach ($result['data'] as $v): ?>
        <tr>
          <td><?php if ($v['cover_image']): ?><img class="td-img" src="<?= e(uploadUrl($v['cover_image'])) ?>"><?php else: ?>—<?php endif; ?></td>
          <td><strong><?= e($v['title']) ?></strong></td>
          <td><?= e(($v['brand']??'').' '.($v['model']??'')) ?></td>
          <td><?= e($v['year'] ?? '—') ?></td>
          <td><?= $v['km'] ? number_format($v['km']) . ' km' : '—' ?></td>
          <td><?= formatPrice($v['price'], $v['currency']) ?></td>
          <td><a href="<?= ADMIN_URL ?>/?module=vehicles&action=toggle&id=<?= $v['id'] ?>"><label class="toggle"><input type="checkbox" <?= $v['is_active']?'checked':'' ?> onclick="return false;"><span class="toggle-slider"></span></label></a></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= SITE_URL ?>/arac-ilanlari/<?= e($v['slug']) ?>" target="_blank"><i class="fa-solid fa-eye"></i></a>
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=vehicles&action=edit&id=<?= $v['id'] ?>"><i class="fa-solid fa-pen"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=vehicles&action=delete&id=<?= $v['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= paginationLinks($result, ADMIN_URL . '/?module=vehicles') ?>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
