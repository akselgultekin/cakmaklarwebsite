<?php
$pageTitle    = 'Site Ayarları';
$activeModule = 'settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Güvenlik hatası.'); header('Location: ' . ADMIN_URL . '/?module=settings'); exit;
    }

    // Logo yükleme
    if (!empty($_FILES['logo']['name'])) {
        try {
            $logoPath = uploadImage($_FILES['logo'], 'site');
            $existing = Database::queryOne("SELECT id,value FROM settings WHERE `key`='logo'");
            if ($existing) {
                if ($existing['value']) { $f = UPLOAD_PATH.'/'.ltrim($existing['value'],'/'); if (file_exists($f)) unlink($f); }
                Database::execute("UPDATE settings SET value=? WHERE `key`='logo'", [$logoPath]);
            } else {
                Database::execute("INSERT INTO settings (`key`,value,label) VALUES ('logo',?,?)", [$logoPath, 'Site Logo']);
            }
        } catch (Exception $e) {
            setFlash('error', 'Logo yüklenemedi: ' . $e->getMessage());
        }
    }

    // Favicon yükleme
    if (!empty($_FILES['favicon']['name'])) {
        try {
            $favPath  = uploadImage($_FILES['favicon'], 'site');
            $existing = Database::queryOne("SELECT id,value FROM settings WHERE `key`='favicon'");
            if ($existing) {
                if ($existing['value']) { $f = UPLOAD_PATH.'/'.ltrim($existing['value'],'/'); if (file_exists($f)) unlink($f); }
                Database::execute("UPDATE settings SET value=? WHERE `key`='favicon'", [$favPath]);
            } else {
                Database::execute("INSERT INTO settings (`key`,value,label) VALUES ('favicon',?,?)", [$favPath, 'Favicon']);
            }
        } catch (Exception $e) {}
    }

    // Tüm diğer ayarları kaydet
    $keys = ['site_title','site_description','phone','phone2','whatsapp','email','address','map_embed',
             'google_analytics','facebook_url','instagram_url','youtube_url','twitter_url','linkedin_url',
             'footer_text','working_hours','seo_default_title','seo_default_desc','seo_keywords'];
    foreach ($keys as $k) {
        $val      = trim($_POST[$k] ?? '');
        $existing = Database::queryOne("SELECT id FROM settings WHERE `key`=?", [$k]);
        if ($existing) {
            Database::execute("UPDATE settings SET value=? WHERE `key`=?", [$val, $k]);
        } else {
            Database::execute("INSERT INTO settings (`key`,value,label) VALUES (?,?,?)", [$k, $val, ucwords(str_replace('_',' ',$k))]);
        }
    }

    setFlash('success', 'Ayarlar kaydedildi.');
    header('Location: ' . ADMIN_URL . '/?module=settings'); exit;
}

// Tüm ayarları çek
$rows = Database::query("SELECT `key`,value FROM settings");
$s    = [];
foreach ($rows as $r) $s[$r['key']] = $r['value'];

ob_start(); ?>
<div class="page-header">
  <h1>Site Ayarları</h1>
</div>

<form method="POST" enctype="multipart/form-data">
  <?= csrfField() ?>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Sol Sütun -->
    <div>
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><h3><i class="fa-solid fa-globe" style="color:var(--teal-2);"></i> Genel Bilgiler</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Site Başlığı</label>
            <input class="form-control" name="site_title" value="<?= e($s['site_title'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Site Açıklaması</label>
            <textarea class="form-control" name="site_description" rows="3"><?= e($s['site_description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Çalışma Saatleri</label>
            <input class="form-control" name="working_hours" value="<?= e($s['working_hours'] ?? '') ?>" placeholder="Hafta içi 09:00–18:00">
          </div>
          <div class="form-group">
            <label>Footer Metni</label>
            <input class="form-control" name="footer_text" value="<?= e($s['footer_text'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><h3><i class="fa-solid fa-phone" style="color:var(--teal-2);"></i> İletişim Bilgileri</h3></div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group"><label>Telefon 1</label><input class="form-control" name="phone" value="<?= e($s['phone'] ?? '') ?>" placeholder="+90 (212) 000 00 00"></div>
            <div class="form-group"><label>Telefon 2</label><input class="form-control" name="phone2" value="<?= e($s['phone2'] ?? '') ?>"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>WhatsApp Numarası</label><input class="form-control" name="whatsapp" value="<?= e($s['whatsapp'] ?? '') ?>" placeholder="905XXXXXXXXX"></div>
            <div class="form-group"><label>E-posta</label><input class="form-control" name="email" type="email" value="<?= e($s['email'] ?? '') ?>"></div>
          </div>
          <div class="form-group"><label>Adres</label><textarea class="form-control" name="address" rows="2"><?= e($s['address'] ?? '') ?></textarea></div>
          <div class="form-group">
            <label>Harita Embed (Google Maps iframe)</label>
            <textarea class="form-control" name="map_embed" rows="4"><?= e($s['map_embed'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3><i class="fa-solid fa-chart-line" style="color:var(--teal-2);"></i> Analytics</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Google Analytics / GTM Kodu</label>
            <textarea class="form-control" name="google_analytics" rows="4" placeholder="<script>...</script>"><?= e($s['google_analytics'] ?? '') ?></textarea>
            <div class="form-hint">Bu kod &lt;head&gt; etiketine eklenir.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sağ Sütun -->
    <div>
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><h3><i class="fa-solid fa-image" style="color:var(--teal-2);"></i> Logo & Favicon</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label>Logo</label>
            <?php if (!empty($s['logo'])): ?>
            <div style="margin-bottom:10px;padding:12px;background:var(--navy-2);border-radius:8px;display:inline-block;">
              <img src="<?= e(uploadUrl($s['logo'])) ?>" style="height:48px;object-fit:contain;">
            </div>
            <?php endif; ?>
            <input class="form-control" type="file" name="logo" accept="image/*">
            <div class="form-hint">Önerilen: PNG/SVG, şeffaf arka plan, max 5MB</div>
          </div>
          <div class="form-group">
            <label>Favicon</label>
            <?php if (!empty($s['favicon'])): ?>
            <div style="margin-bottom:10px;"><img src="<?= e(uploadUrl($s['favicon'])) ?>" style="width:32px;height:32px;object-fit:contain;border:1px solid var(--line);border-radius:4px;padding:2px;"></div>
            <?php endif; ?>
            <input class="form-control" type="file" name="favicon" accept="image/*,.ico">
            <div class="form-hint">Önerilen: 32×32 ICO veya PNG</div>
          </div>
        </div>
      </div>

      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><h3><i class="fa-solid fa-share-nodes" style="color:var(--teal-2);"></i> Sosyal Medya</h3></div>
        <div class="card-body">
          <div class="form-group"><label><i class="fa-brands fa-facebook"></i> Facebook URL</label><input class="form-control" name="facebook_url" value="<?= e($s['facebook_url'] ?? '') ?>"></div>
          <div class="form-group"><label><i class="fa-brands fa-instagram"></i> Instagram URL</label><input class="form-control" name="instagram_url" value="<?= e($s['instagram_url'] ?? '') ?>"></div>
          <div class="form-group"><label><i class="fa-brands fa-youtube"></i> YouTube URL</label><input class="form-control" name="youtube_url" value="<?= e($s['youtube_url'] ?? '') ?>"></div>
          <div class="form-group"><label><i class="fa-brands fa-twitter"></i> Twitter / X URL</label><input class="form-control" name="twitter_url" value="<?= e($s['twitter_url'] ?? '') ?>"></div>
          <div class="form-group"><label><i class="fa-brands fa-linkedin"></i> LinkedIn URL</label><input class="form-control" name="linkedin_url" value="<?= e($s['linkedin_url'] ?? '') ?>"></div>
        </div>
      </div>

      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><h3><i class="fa-solid fa-magnifying-glass" style="color:var(--teal-2);"></i> SEO Varsayılanları</h3></div>
        <div class="card-body">
          <div class="form-group"><label>Varsayılan Meta Başlık</label><input class="form-control" name="seo_default_title" value="<?= e($s['seo_default_title'] ?? '') ?>"></div>
          <div class="form-group"><label>Varsayılan Meta Açıklama</label><textarea class="form-control" name="seo_default_desc" rows="2"><?= e($s['seo_default_desc'] ?? '') ?></textarea></div>
          <div class="form-group"><label>Anahtar Kelimeler</label><input class="form-control" name="seo_keywords" value="<?= e($s['seo_keywords'] ?? '') ?>" placeholder="inşaat, konut, gayrimenkul"></div>
        </div>
      </div>

      <button class="btn btn-primary" type="submit" style="width:100%;min-height:48px;font-size:15px;">
        <i class="fa-solid fa-floppy-disk"></i> Ayarları Kaydet
      </button>
    </div>
  </div>
</form>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
