<?php
require_once APP_PATH . '/models/ListingModel.php';
require_once APP_PATH . '/models/ProjectModel.php';

$model   = new ListingModel();
$projMdl = new ProjectModel();
$action  = $_GET['action'] ?? 'index';

// ── DELETE ──────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        setFlash('error', 'Güvenlik hatası.'); header('Location: ' . ADMIN_URL . '/?module=listings'); exit;
    }
    $id   = (int) $_GET['id'];
    $imgs = $model->getImages($id);
    foreach ($imgs as $img) {
        $f = UPLOAD_PATH . '/' . ltrim($img['image'], '/');
        if (file_exists($f)) unlink($f);
    }
    $model->delete($id);
    setFlash('success', 'İlan silindi.');
    header('Location: ' . ADMIN_URL . '/?module=listings'); exit;
}

// ── TOGGLE ──────────────────────────────────────────────────
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $l  = $model->find($id);
    if ($l) $model->update($id, ['is_active' => $l['is_active'] ? 0 : 1]);
    header('Location: ' . ADMIN_URL . '/?module=listings'); exit;
}

// ── SAVE ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int) ($_POST['id'] ?? 0);
    $csrf = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
        setFlash('error', 'Güvenlik hatası.'); header('Location: ' . ADMIN_URL . '/?module=listings'); exit;
    }

    $slug = slugify($_POST['slug'] ?: $_POST['title']);
    $ex   = Database::queryOne("SELECT id FROM listings WHERE slug=? AND id!=?", [$slug, $id]);
    if ($ex) $slug = $slug . '-' . time();

    $data = [
        'title'       => trim($_POST['title']),
        'slug'        => $slug,
        'type'        => $_POST['type'],
        'project_id'  => (int)($_POST['project_id'] ?? 0) ?: null,
        'price'       => (float) str_replace(['.', ','], ['', '.'], $_POST['price'] ?? 0),
        'currency'    => $_POST['currency'] ?? 'TL',
        'location'    => trim($_POST['location'] ?? ''),
        'area_m2'     => (int) ($_POST['area_m2'] ?? 0) ?: null,
        'room_count'  => trim($_POST['room_count'] ?? ''),
        'floor'       => trim($_POST['floor'] ?? ''),
        'total_floors'=> (int) ($_POST['total_floors'] ?? 0) ?: null,
        'age'         => (int) ($_POST['age'] ?? 0) ?: null,
        'heating'     => trim($_POST['heating'] ?? ''),
        'description' => $_POST['description'] ?? '',
        'tags'        => trim($_POST['tags'] ?? ''),
        'tour_url'    => trim($_POST['tour_url'] ?? ''),
        'tour_embed'  => $_POST['tour_embed'] ?? '',
        'map_embed'   => $_POST['map_embed'] ?? '',
        'lat'         => !empty($_POST['lat']) ? (float)$_POST['lat'] : null,
        'lng'         => !empty($_POST['lng']) ? (float)$_POST['lng'] : null,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        'meta_title'  => trim($_POST['meta_title'] ?? ''),
        'meta_desc'   => trim($_POST['meta_desc'] ?? ''),
    ];

    if (!empty($_FILES['cover_image']['name'])) {
        try { $data['cover_image'] = uploadImage($_FILES['cover_image'], 'listings'); }
        catch (Exception $e) { setFlash('error', $e->getMessage()); header('Location: ' . ADMIN_URL . '/?module=listings&action=' . ($id ? 'edit&id='.$id : 'create')); exit; }
    }

    if ($id) { $model->update($id, $data); $newId = $id; setFlash('success', 'İlan güncellendi.'); }
    else      { $newId = $model->create($data); setFlash('success', 'İlan oluşturuldu.'); }

    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $k => $name) {
            if (!$name) continue;
            $file = ['name'=>$name,'tmp_name'=>$_FILES['gallery']['tmp_name'][$k],'size'=>$_FILES['gallery']['size'][$k],'error'=>$_FILES['gallery']['error'][$k],'type'=>$_FILES['gallery']['type'][$k]];
            try { $path = uploadImage($file, 'listings'); Database::execute("INSERT INTO listing_images (listing_id,image,sort_order) VALUES (?,?,?)", [$newId, $path, $k]); } catch (Exception $e) {}
        }
    }
    if (!empty($_POST['delete_images'])) {
        foreach ((array)$_POST['delete_images'] as $imgId) {
            $img = Database::queryOne("SELECT image FROM listing_images WHERE id=?", [(int)$imgId]);
            if ($img) { $f = UPLOAD_PATH . '/' . ltrim($img['image'], '/'); if (file_exists($f)) unlink($f); Database::execute("DELETE FROM listing_images WHERE id=?", [(int)$imgId]); }
        }
    }

    header('Location: ' . ADMIN_URL . '/?module=listings'); exit;
}

// ── FORM (create/edit) ───────────────────────────────────────
$allProjects = $projMdl->activeAll();

if ($action === 'create' || $action === 'edit') {
    $listing    = $action === 'edit' ? $model->find((int)($_GET['id'] ?? 0)) : null;
    $images     = $listing ? $model->getImages($listing['id']) : [];
    $pageTitle  = $listing ? 'İlanı Düzenle' : 'Yeni İlan';
    $activeModule = 'listings';

    ob_start(); ?>
    <div class="page-header">
      <h1><?= e($pageTitle) ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=listings"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($listing): ?><input type="hidden" name="id" value="<?= $listing['id'] ?>"><?php endif; ?>
      <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>İlan Bilgileri</h3></div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group"><label>İlan Başlığı <span class="required">*</span></label><input class="form-control" name="title" required value="<?= e($listing['title'] ?? '') ?>"></div>
                <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="<?= e($listing['slug'] ?? '') ?>"></div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>İlan Tipi <span class="required">*</span></label>
                  <select class="form-control" name="type" required>
                    <?php foreach (['satilik'=>'Satılık','kiralik'=>'Kiralık','dukkan'=>'Dükkan','ofis'=>'Ofis','arsa'=>'Arsa'] as $v=>$l): ?>
                    <option value="<?= $v ?>" <?= ($listing['type']??'')===$v?'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Bağlı Proje</label>
                  <select class="form-control" name="project_id">
                    <option value="">— Proje seçin —</option>
                    <?php foreach ($allProjects as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($listing['project_id']??'')==$p['id']?'selected':'' ?>><?= e($p['title']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-row-3">
                <div class="form-group"><label>Fiyat</label><input class="form-control" name="price" type="text" value="<?= e($listing['price'] ?? '') ?>"></div>
                <div class="form-group">
                  <label>Para Birimi</label>
                  <select class="form-control" name="currency">
                    <?php foreach (['TL','USD','EUR','GBP'] as $c): ?>
                    <option <?= ($listing['currency']??'TL')===$c?'selected':'' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group"><label>Konum</label><input class="form-control" name="location" value="<?= e($listing['location'] ?? '') ?>"></div>
              </div>
              <div class="form-row-3">
                <div class="form-group"><label>Alan (m²)</label><input class="form-control" name="area_m2" type="number" value="<?= e($listing['area_m2'] ?? '') ?>"></div>
                <div class="form-group"><label>Oda Sayısı</label><input class="form-control" name="room_count" value="<?= e($listing['room_count'] ?? '') ?>" placeholder="3+1"></div>
                <div class="form-group"><label>Kat</label><input class="form-control" name="floor" value="<?= e($listing['floor'] ?? '') ?>"></div>
              </div>
              <div class="form-row-3">
                <div class="form-group"><label>Toplam Kat</label><input class="form-control" name="total_floors" type="number" value="<?= e($listing['total_floors'] ?? '') ?>"></div>
                <div class="form-group"><label>Bina Yaşı</label><input class="form-control" name="age" type="number" value="<?= e($listing['age'] ?? '') ?>"></div>
                <div class="form-group"><label>Isıtma</label><input class="form-control" name="heating" value="<?= e($listing['heating'] ?? '') ?>"></div>
              </div>
              <div class="form-group"><label>Etiketler (virgülle)</label><input class="form-control" name="tags" value="<?= e($listing['tags'] ?? '') ?>" placeholder="Deniz manzarası, Yeni bina"></div>
              <div class="form-group"><label>Açıklama</label><textarea class="form-control" name="description" rows="6"><?= e($listing['description'] ?? '') ?></textarea></div>
            </div>
          </div>

          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>3D Tur & Harita</h3></div>
            <div class="card-body">
              <div class="form-group"><label>Tur URL</label><input class="form-control" name="tour_url" value="<?= e($listing['tour_url'] ?? '') ?>"></div>
              <div class="form-group"><label>Tur Embed</label><textarea class="form-control" name="tour_embed" rows="3"><?= e($listing['tour_embed'] ?? '') ?></textarea></div>
              <div class="form-group"><label>Harita Embed (Google Maps)</label><textarea class="form-control" name="map_embed" rows="3"><?= e($listing['map_embed'] ?? '') ?></textarea></div>
              <div class="form-group">
                <label>Koordinat — Leaflet Haritada Pin (tıklayarak seç)</label>
                <div id="adminMapPicker" style="height:280px;border-radius:8px;border:1px solid #dee2e6;margin-bottom:8px;"></div>
                <div style="display:flex;gap:8px;">
                  <input class="form-control" name="lat" id="latInput" placeholder="Enlem (ör: 40.7370)" value="<?= e($listing['lat'] ?? '') ?>" step="any">
                  <input class="form-control" name="lng" id="lngInput" placeholder="Boylam (ör: 31.6070)" value="<?= e($listing['lng'] ?? '') ?>" step="any">
                </div>
                <small class="text-muted">Haritaya tıklayarak veya yukarıdaki alanlara manuel girerek koordinat belirleyin.</small>
              </div>
              <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
              <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
              <script>
              document.addEventListener('DOMContentLoaded', function() {
                var lat = parseFloat(document.getElementById('latInput').value) || 40.7370;
                var lng = parseFloat(document.getElementById('lngInput').value) || 31.6070;
                var map = L.map('adminMapPicker').setView([lat, lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
                var marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                function updateInputs(latlng) {
                  document.getElementById('latInput').value = latlng.lat.toFixed(7);
                  document.getElementById('lngInput').value = latlng.lng.toFixed(7);
                }
                marker.on('dragend', function(e) { updateInputs(e.target.getLatLng()); });
                map.on('click', function(e) { marker.setLatLng(e.latlng); updateInputs(e.latlng); });
                document.getElementById('latInput').addEventListener('change', function() {
                  var la = parseFloat(this.value), ln = parseFloat(document.getElementById('lngInput').value);
                  if (!isNaN(la) && !isNaN(ln)) { marker.setLatLng([la,ln]); map.setView([la,ln]); }
                });
              });
              </script>
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
              <div class="form-group"><label>Meta Başlık</label><input class="form-control" name="meta_title" value="<?= e($listing['meta_title'] ?? '') ?>"></div>
              <div class="form-group"><label>Meta Açıklama</label><textarea class="form-control" name="meta_desc" rows="2"><?= e($listing['meta_desc'] ?? '') ?></textarea></div>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="margin-bottom:14px;">
            <div class="card-header"><h3>Durum</h3></div>
            <div class="card-body" style="display:grid;gap:14px;">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_featured" <?= !empty($listing['is_featured']) ? 'checked' : '' ?>><span class="toggle-slider"></span></label> Öne Çıkan
              </label>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_active" <?= !isset($listing['is_active']) || $listing['is_active'] ? 'checked' : '' ?>><span class="toggle-slider"></span></label> Aktif
              </label>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Kapak Görseli</h3></div>
            <div class="card-body">
              <?php if (!empty($listing['cover_image'])): ?>
              <img src="<?= e(uploadUrl($listing['cover_image'])) ?>" class="img-preview img-preview-lg" style="margin-bottom:10px;">
              <?php endif; ?>
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

// ── LIST ────────────────────────────────────────────────────
$pageTitle    = 'İlanlar';
$activeModule = 'listings';
$typeFilter   = $_GET['type'] ?? '';
$page         = max(1, (int) ($_GET['sayfa'] ?? 1));

$where = $typeFilter ? "WHERE type=?" : "";
$params = $typeFilter ? [$typeFilter] : [];
$total  = Database::queryOne("SELECT COUNT(*) AS cnt FROM listings $where", $params)['cnt'];
$offset = ($page - 1) * 20;
$data   = Database::query("SELECT * FROM listings $where ORDER BY created_at DESC LIMIT 20 OFFSET $offset", $params);
$paginator = ['data'=>$data,'total'=>$total,'per_page'=>20,'current_page'=>$page,'last_page'=>ceil($total/20)];

$types = [''=>'Tümü','satilik'=>'Satılık','kiralik'=>'Kiralık','dukkan'=>'Dükkan','ofis'=>'Ofis','arsa'=>'Arsa'];

ob_start(); ?>
<div class="page-header">
  <h1>İlanlar (<?= $total ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=listings&action=create"><i class="fa-solid fa-plus"></i> Yeni İlan</a>
</div>

<div style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;">
  <?php foreach ($types as $v => $l): ?>
  <a class="btn btn-sm <?= $typeFilter===$v ? 'btn-primary' : 'btn-outline' ?>" href="<?= ADMIN_URL ?>/?module=listings<?= $v ? '&type='.$v : '' ?>"><?= $l ?></a>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Görsel</th><th>Başlık</th><th>Tip</th><th>Fiyat</th><th>Konum</th><th>Öne Çıkan</th><th>Aktif</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($data)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:30px;">İlan yok.</td></tr>
        <?php else: foreach ($data as $l): ?>
        <tr>
          <td><?php if ($l['cover_image']): ?><img class="td-img" src="<?= e(uploadUrl($l['cover_image'])) ?>"><?php else: ?>—<?php endif; ?></td>
          <td><strong><?= e($l['title']) ?></strong></td>
          <td><span class="badge badge-info"><?= e(strtoupper($l['type'])) ?></span></td>
          <td><?= $l['price'] ? formatPrice((float)$l['price']) : '—' ?></td>
          <td><?= e($l['location'] ?? '—') ?></td>
          <td><?= ($l['is_featured'] ?? 0) ? '<span class="badge badge-success">Evet</span>' : '—' ?></td>
          <td><a href="<?= ADMIN_URL ?>/?module=listings&action=toggle&id=<?= $l['id'] ?>"><label class="toggle"><input type="checkbox" <?= $l['is_active']?'checked':'' ?> onclick="return false;"><span class="toggle-slider"></span></label></a></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= SITE_URL ?>/satilik/<?= e($l['slug']) ?>" target="_blank"><i class="fa-solid fa-eye"></i></a>
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=listings&action=edit&id=<?= $l['id'] ?>"><i class="fa-solid fa-pen"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=listings&action=delete&id=<?= $l['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('İlanı silmek istediğinizden emin misiniz?')"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= paginationLinks($paginator, ADMIN_URL . '/?module=listings' . ($typeFilter ? '&type='.$typeFilter : '')) ?>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
