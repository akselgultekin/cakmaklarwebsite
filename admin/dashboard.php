<?php
$pageTitle    = 'Dashboard';
$activeModule = 'dashboard';

// İstatistikler
$totalProjects = Database::queryOne("SELECT COUNT(*) AS cnt FROM projects WHERE is_active=1")['cnt'];
$totalListings = Database::queryOne("SELECT COUNT(*) AS cnt FROM listings WHERE is_active=1")['cnt'];
$totalVehicles = Database::queryOne("SELECT COUNT(*) AS cnt FROM vehicles WHERE is_active=1")['cnt'];
$totalForms    = Database::queryOne("SELECT COUNT(*) AS cnt FROM contact_messages WHERE is_read=0")['cnt'];

// Son başvurular
$recentForms = Database::query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 8"
);

ob_start();
?>
<div class="stats-grid">
  <div class="stat-box">
    <div class="stat-icon blue"><i class="fa-solid fa-building"></i></div>
    <div>
      <span class="stat-value"><?= $totalProjects ?></span>
      <span class="stat-label">Aktif Proje</span>
    </div>
  </div>
  <div class="stat-box">
    <div class="stat-icon teal"><i class="fa-solid fa-house"></i></div>
    <div>
      <span class="stat-value"><?= $totalListings ?></span>
      <span class="stat-label">Aktif İlan</span>
    </div>
  </div>
  <div class="stat-box">
    <div class="stat-icon green"><i class="fa-solid fa-car"></i></div>
    <div>
      <span class="stat-value"><?= $totalVehicles ?></span>
      <span class="stat-label">Araç İlanı</span>
    </div>
  </div>
  <div class="stat-box">
    <div class="stat-icon orange"><i class="fa-solid fa-inbox"></i></div>
    <div>
      <span class="stat-value"><?= $totalForms ?></span>
      <span class="stat-label">Okunmamış Başvuru</span>
    </div>
  </div>
</div>

<!-- Hızlı eylemler -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=projects&action=create"><i class="fa-solid fa-plus"></i> Proje Ekle</a>
  <a class="btn btn-teal" href="<?= ADMIN_URL ?>/?module=listings&action=create"><i class="fa-solid fa-plus"></i> İlan Ekle</a>
  <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=vehicles&action=create"><i class="fa-solid fa-plus"></i> Araç Ekle</a>
  <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=news&action=create"><i class="fa-solid fa-plus"></i> Haber Ekle</a>
</div>

<!-- Son başvurular -->
<div class="card">
  <div class="card-header">
    <h3><i class="fa-solid fa-inbox" style="color:var(--teal-2);"></i> Son Form Başvuruları</h3>
    <a class="btn btn-outline btn-sm" href="<?= ADMIN_URL ?>/?module=forms">Tümünü Gör</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Ad Soyad</th>
          <th>Telefon</th>
          <th>Konu / İlan</th>
          <th>Tarih</th>
          <th>Durum</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentForms)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px;">Henüz başvuru yok.</td></tr>
        <?php else: foreach ($recentForms as $f): ?>
        <tr>
          <td><strong><?= e($f['name']) ?></strong></td>
          <td><?= e($f['phone']) ?></td>
          <td><?= e($f['subject'] ?? $f['ref_title'] ?? '-') ?></td>
          <td style="white-space:nowrap;"><?= formatDate($f['created_at'], 'd.m.Y H:i') ?></td>
          <td>
            <?php if (!$f['is_read']): ?>
            <span class="badge badge-warning">Yeni</span>
            <?php else: ?>
            <span class="badge badge-muted">Okundu</span>
            <?php endif; ?>
          </td>
          <td>
            <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=forms&action=view&id=<?= $f['id'] ?>" title="Görüntüle">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
require __DIR__ . '/layout.php';
