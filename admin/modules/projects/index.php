<?php
require_once APP_PATH . '/models/ProjectModel.php';
$model  = new ProjectModel();
$action = $_GET['action'] ?? 'index';

// ── DELETE ─────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    if (!isset($_SESSION['csrf_token']) || ($_GET['_token'] ?? '') !== $_SESSION['csrf_token']) {
        setFlash('error', 'Güvenlik hatası.'); header('Location: ' . ADMIN_URL . '/?module=projects'); exit;
    }
    $id = (int) $_GET['id'];
    // Görselleri sil
    $imgs = $model->getImages($id);
    foreach ($imgs as $img) {
        $f = UPLOAD_PATH . '/' . ltrim($img['image'], '/');
        if (file_exists($f)) unlink($f);
    }
    $model->delete($id);
    setFlash('success', 'Proje silindi.');
    header('Location: ' . ADMIN_URL . '/?module=projects'); exit;
}

// ── TOGGLE ACTIVE ───────────────────────────────────────────
if ($action === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $p  = $model->find($id);
    if ($p) $model->update($id, ['is_active' => $p['is_active'] ? 0 : 1]);
    header('Location: ' . ADMIN_URL . '/?module=projects'); exit;
}

// ── SAVE (create / edit) ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $csrf = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
        setFlash('error', 'Güvenlik hatası.'); header('Location: ' . ADMIN_URL . '/?module=projects'); exit;
    }

    $slug = slugify($_POST['slug'] ?: $_POST['title']);
    // Slug benzersizlik kontrolü
    $existing = Database::queryOne(
        "SELECT id FROM projects WHERE slug=? AND id!=?", [$slug, $id]
    );
    if ($existing) $slug = $slug . '-' . time();

    $data = [
        'title'       => trim($_POST['title']),
        'slug'        => $slug,
        'short_desc'  => trim($_POST['short_desc'] ?? ''),
        'description' => $_POST['description'] ?? '',
        'location'    => trim($_POST['location'] ?? ''),
        'status'      => $_POST['status'] ?? 'satiasta',
        'video_url'   => trim($_POST['video_url'] ?? ''),
        'tour_url'    => trim($_POST['tour_url'] ?? ''),
        'tour_embed'  => $_POST['tour_embed'] ?? '',
        'tour_desc'   => trim($_POST['tour_desc'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        'meta_title'  => trim($_POST['meta_title'] ?? ''),
        'meta_desc'   => trim($_POST['meta_desc'] ?? ''),
    ];

    // Kapak görseli
    if (!empty($_FILES['cover_image']['name'])) {
        try {
            $path = uploadImage($_FILES['cover_image'], 'projects');
            $data['cover_image'] = $path;
        } catch (Exception $e) {
            setFlash('error', $e->getMessage());
            header('Location: ' . ADMIN_URL . '/?module=projects&action=' . ($id ? 'edit&id='.$id : 'create')); exit;
        }
    }

    if ($id) {
        $model->update($id, $data);
        $newId = $id;
        setFlash('success', 'Proje güncellendi.');
    } else {
        $newId = $model->create($data);
        setFlash('success', 'Proje oluşturuldu.');
    }

    // Galeri görselleri
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $k => $name) {
            if (!$name) continue;
            $file = ['name'=>$name,'tmp_name'=>$_FILES['gallery']['tmp_name'][$k],
                     'size'=>$_FILES['gallery']['size'][$k],'error'=>$_FILES['gallery']['error'][$k],
                     'type'=>$_FILES['gallery']['type'][$k]];
            try {
                $path = uploadImage($file, 'projects');
                Database::execute(
                    "INSERT INTO project_images (project_id,image,sort_order) VALUES (?,?,?)",
                    [$newId, $path, $k]
                );
            } catch (Exception $e) {}
        }
    }

    // Galeri görsel sil
    if (!empty($_POST['delete_images'])) {
        foreach ((array)$_POST['delete_images'] as $imgId) {
            $img = Database::queryOne("SELECT image FROM project_images WHERE id=?", [(int)$imgId]);
            if ($img) {
                $f = UPLOAD_PATH . '/' . ltrim($img['image'], '/');
                if (file_exists($f)) unlink($f);
                Database::execute("DELETE FROM project_images WHERE id=?", [(int)$imgId]);
            }
        }
    }

    // Kat planları
    if (!empty($_POST['plan_title'])) {
        foreach ($_POST['plan_title'] as $k => $planTitle) {
            if (!$planTitle) continue;
            $planData = [
                'project_id'  => $newId,
                'title'       => $planTitle,
                'desc'        => $_POST['plan_desc'][$k] ?? '',
                'area_m2'     => (int) ($_POST['plan_area'][$k] ?? 0) ?: null,
                'sort_order'  => $k,
            ];
            $planId = (int) ($_POST['plan_id'][$k] ?? 0);
            if ($planId) {
                Database::execute("UPDATE project_floor_plans SET title=?,`desc`=?,area_m2=?,sort_order=? WHERE id=? AND project_id=?",
                    [$planData['title'],$planData['desc'],$planData['area_m2'],$planData['sort_order'],$planId,$newId]);
            } else {
                Database::execute("INSERT INTO project_floor_plans (project_id,title,`desc`,area_m2,sort_order) VALUES (?,?,?,?,?)",
                    array_values($planData));
            }
        }
    }

    header('Location: ' . ADMIN_URL . '/?module=projects'); exit;
}

// ── LIST ────────────────────────────────────────────────────
$pageTitle    = 'Projeler';
$activeModule = 'projects';

if ($action === 'create' || $action === 'edit') {
    $project    = $action === 'edit' ? $model->find((int)($_GET['id'] ?? 0)) : null;
    $images     = $project ? $model->getImages($project['id']) : [];
    $floorPlans = $project ? $model->getFloorPlans($project['id']) : [];
    $pageTitle  = $project ? 'Projeyi Düzenle' : 'Yeni Proje';

    ob_start(); ?>
    <div class="page-header">
      <h1><?= e($pageTitle) ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=projects"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
      <?= csrfField() ?>
      <?php if ($project): ?><input type="hidden" name="id" value="<?= $project['id'] ?>"><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">
        <div>
          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>Proje Bilgileri</h3></div>
            <div class="card-body">
              <div class="form-group">
                <label>Başlık <span class="required">*</span></label>
                <input class="form-control" name="title" required value="<?= e($project['title'] ?? '') ?>">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Slug (URL)</label>
                  <input class="form-control" name="slug" value="<?= e($project['slug'] ?? '') ?>" placeholder="otomatik-olusur">
                </div>
                <div class="form-group">
                  <label>Konum</label>
                  <input class="form-control" name="location" value="<?= e($project['location'] ?? '') ?>">
                </div>
              </div>
              <div class="form-group">
                <label>Kısa Açıklama</label>
                <textarea class="form-control" name="short_desc" rows="3"><?= e($project['short_desc'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label>Detay Açıklama (HTML destekli)</label>
                <textarea class="form-control" name="description" rows="8"><?= e($project['description'] ?? '') ?></textarea>
              </div>
            </div>
          </div>

          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>3D Sanal Tur</h3></div>
            <div class="card-body">
              <div class="form-group">
                <label>3D Tur URL</label>
                <input class="form-control" name="tour_url" value="<?= e($project['tour_url'] ?? '') ?>" placeholder="https://...">
              </div>
              <div class="form-group">
                <label>İframe Embed Kodu</label>
                <textarea class="form-control" name="tour_embed" rows="4" placeholder="<iframe ...>"><?= e($project['tour_embed'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label>Tur Açıklaması</label>
                <textarea class="form-control" name="tour_desc" rows="2"><?= e($project['tour_desc'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label>Video URL (YouTube/Vimeo)</label>
                <input class="form-control" name="video_url" value="<?= e($project['video_url'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="card" style="margin-bottom:18px;">
            <div class="card-header"><h3>Galeri Görselleri</h3></div>
            <div class="card-body">
              <?php if (!empty($images)): ?>
              <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:14px;">
                <?php foreach ($images as $img): ?>
                <div style="position:relative;">
                  <img src="<?= e(uploadUrl($img['image'])) ?>" style="width:100%;height:80px;object-fit:cover;border-radius:6px;border:1px solid var(--line);">
                  <label style="position:absolute;top:4px;right:4px;background:rgba(192,57,43,.9);color:#fff;border-radius:4px;padding:2px 6px;font-size:11px;cursor:pointer;">
                    <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="display:none;"> Sil
                  </label>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <div class="form-group">
                <label>Yeni Görseller Ekle (çoklu seçim)</label>
                <input class="form-control" type="file" name="gallery[]" multiple accept="image/*">
              </div>
            </div>
          </div>

          <div class="card" style="margin-bottom:18px;">
            <div class="card-header">
              <h3>Kat Planları</h3>
              <button type="button" class="btn btn-outline btn-sm" id="addPlan"><i class="fa-solid fa-plus"></i> Plan Ekle</button>
            </div>
            <div class="card-body" id="plansWrap">
              <?php $planIdx = 0; foreach ($floorPlans as $plan): ?>
              <div class="plan-row" style="border:1px solid var(--line);border-radius:8px;padding:14px;margin-bottom:10px;">
                <input type="hidden" name="plan_id[<?= $planIdx ?>]" value="<?= $plan['id'] ?>">
                <div class="form-row-3">
                  <div class="form-group"><label>Başlık</label><input class="form-control" name="plan_title[<?= $planIdx ?>]" value="<?= e($plan['title']) ?>"></div>
                  <div class="form-group"><label>m²</label><input class="form-control" name="plan_area[<?= $planIdx ?>]" type="number" value="<?= e($plan['area_m2'] ?? '') ?>"></div>
                  <div class="form-group"><label>Açıklama</label><input class="form-control" name="plan_desc[<?= $planIdx ?>]" value="<?= e($plan['desc'] ?? '') ?>"></div>
                </div>
              </div>
              <?php $planIdx++; endforeach; ?>
            </div>
          </div>

          <div class="card">
            <div class="card-header"><h3>SEO</h3></div>
            <div class="card-body">
              <div class="form-group"><label>Meta Başlık</label><input class="form-control" name="meta_title" value="<?= e($project['meta_title'] ?? '') ?>"></div>
              <div class="form-group"><label>Meta Açıklama</label><textarea class="form-control" name="meta_desc" rows="2"><?= e($project['meta_desc'] ?? '') ?></textarea></div>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="margin-bottom:14px;">
            <div class="card-header"><h3>Durum & Ayarlar</h3></div>
            <div class="card-body" style="display:grid;gap:14px;">
              <div class="form-group">
                <label>Durum</label>
                <select class="form-control" name="status">
                  <option value="satiasta" <?= ($project['status']??'')=='satiasta' ? 'selected' : '' ?>>Satışta</option>
                  <option value="yakinda" <?= ($project['status']??'')=='yakinda' ? 'selected' : '' ?>>Yakında</option>
                  <option value="teslim_edildi" <?= ($project['status']??'')=='teslim_edildi' ? 'selected' : '' ?>>Teslim Edildi</option>
                </select>
              </div>
              <div class="form-group">
                <label>Sıralama</label>
                <input class="form-control" name="sort_order" type="number" value="<?= e($project['sort_order'] ?? 0) ?>">
              </div>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_featured" <?= !empty($project['is_featured']) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
                Öne Çıkan
              </label>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <label class="toggle"><input type="checkbox" name="is_active" <?= !isset($project['is_active']) || $project['is_active'] ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
                Aktif
              </label>
            </div>
          </div>

          <div class="card">
            <div class="card-header"><h3>Kapak Görseli</h3></div>
            <div class="card-body">
              <?php if (!empty($project['cover_image'])): ?>
              <img src="<?= e(uploadUrl($project['cover_image'])) ?>" class="img-preview img-preview-lg" style="margin-bottom:10px;">
              <?php endif; ?>
              <input class="form-control" type="file" name="cover_image" accept="image/*">
            </div>
          </div>

          <div style="margin-top:14px;">
            <button class="btn btn-primary" type="submit" style="width:100%;min-height:46px;">
              <i class="fa-solid fa-floppy-disk"></i> Kaydet
            </button>
          </div>
        </div>
      </div>
    </form>

    <script>
    let planIdx = <?= $planIdx ?>;
    document.getElementById('addPlan').addEventListener('click', () => {
      const html = `<div class="plan-row" style="border:1px solid var(--line);border-radius:8px;padding:14px;margin-bottom:10px;">
        <div class="form-row-3">
          <div class="form-group"><label>Başlık</label><input class="form-control" name="plan_title[${planIdx}]"></div>
          <div class="form-group"><label>m²</label><input class="form-control" name="plan_area[${planIdx}]" type="number"></div>
          <div class="form-group"><label>Açıklama</label><input class="form-control" name="plan_desc[${planIdx}]"></div>
        </div>
      </div>`;
      document.getElementById('plansWrap').insertAdjacentHTML('beforeend', html);
      planIdx++;
    });
    </script>
    <?php
    $pageContent = ob_get_clean();
    require dirname(__DIR__, 2) . '/layout.php';
    return;
}

// ── LIST VIEW ────────────────────────────────────────────────
$page     = max(1, (int)($_GET['sayfa'] ?? 1));
$projects = $model->paginateActive($page, 20);

ob_start(); ?>
<div class="page-header">
  <h1>Projeler (<?= $projects['total'] ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=projects&action=create">
    <i class="fa-solid fa-plus"></i> Yeni Proje
  </a>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Görsel</th><th>Başlık</th><th>Konum</th><th>Durum</th>
          <th>Öne Çıkan</th><th>Sıra</th><th>Aktif</th><th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($projects['data'])): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:30px;">Proje yok.</td></tr>
        <?php else: foreach ($projects['data'] as $p): ?>
        <tr>
          <td>
            <?php if ($p['cover_image']): ?>
            <img class="td-img" src="<?= e(uploadUrl($p['cover_image'])) ?>" alt="">
            <?php else: ?><span style="color:var(--muted);">—</span><?php endif; ?>
          </td>
          <td><strong><?= e($p['title']) ?></strong><br><small style="color:var(--muted);">/projeler/<?= e($p['slug']) ?></small></td>
          <td><?= e($p['location'] ?? '—') ?></td>
          <td>
            <?php $sl=['satiasta'=>['Satışta','success'],'yakinda'=>['Yakında','warning'],'teslim_edildi'=>['Tamamlandı','info']];
            $si=$sl[$p['status']]??[$p['status'],'muted']; ?>
            <span class="badge badge-<?= $si[1] ?>"><?= $si[0] ?></span>
          </td>
          <td><?= $p['is_featured'] ? '<span class="badge badge-success">Evet</span>' : '—' ?></td>
          <td><?= e($p['sort_order']) ?></td>
          <td>
            <a href="<?= ADMIN_URL ?>/?module=projects&action=toggle&id=<?= $p['id'] ?>">
              <label class="toggle"><input type="checkbox" <?= $p['is_active'] ? 'checked' : '' ?> onclick="return false;"><span class="toggle-slider"></span></label>
            </a>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= SITE_URL ?>/projeler/<?= e($p['slug']) ?>" target="_blank" title="Önizle"><i class="fa-solid fa-eye"></i></a>
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=projects&action=edit&id=<?= $p['id'] ?>" title="Düzenle"><i class="fa-solid fa-pen"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=projects&action=delete&id=<?= $p['id'] ?>&_token=<?= e(csrfToken()) ?>"
                 onclick="return confirm('Projeyi silmek istediğinizden emin misiniz?')" title="Sil"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= paginationLinks($projects, ADMIN_URL . '/?module=projects') ?>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
