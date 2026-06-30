<?php
// ── İSTATİSTİKLER ───────────────────────────────────────────────────────────

// Genel sayılar
$totalListings  = Database::queryOne("SELECT COUNT(*) c FROM listings WHERE is_active=1")['c'] ?? 0;
$totalProjects  = Database::queryOne("SELECT COUNT(*) c FROM projects WHERE is_active=1")['c'] ?? 0;
$totalMessages  = Database::queryOne("SELECT COUNT(*) c FROM contact_messages")['c'] ?? 0;
$totalViews     = Database::queryOne("SELECT COUNT(*) c FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['c'] ?? 0;
$todayViews     = Database::queryOne("SELECT COUNT(*) c FROM page_views WHERE DATE(created_at)=CURDATE()")['c'] ?? 0;
$weekViews      = Database::queryOne("SELECT COUNT(*) c FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['c'] ?? 0;

// Son 14 gün — günlük görüntülenme
$dailyViews = Database::query(
    "SELECT DATE(created_at) as d, COUNT(*) as c FROM page_views
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
     GROUP BY DATE(created_at) ORDER BY d ASC"
);

// En çok görüntülenen ilanlar (30 gün)
$topListings = Database::query(
    "SELECT pv.page_slug, COUNT(*) as views, l.title, l.type, l.price, l.price_unit
     FROM page_views pv
     LEFT JOIN listings l ON l.slug = pv.page_slug
     WHERE pv.page_type='listing' AND pv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
     GROUP BY pv.page_slug ORDER BY views DESC LIMIT 8"
);

// En çok görüntülenen projeler (30 gün)
$topProjects = Database::query(
    "SELECT pv.page_slug, COUNT(*) as views, p.title
     FROM page_views pv
     LEFT JOIN projects p ON p.slug = pv.page_slug
     WHERE pv.page_type='project' AND pv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
     GROUP BY pv.page_slug ORDER BY views DESC LIMIT 5"
);

// Son form başvuruları
$recentMessages = Database::query(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 8"
);

// Grafik verisi JSON
$chartLabels = [];
$chartData   = [];
$dateMap     = [];
foreach ($dailyViews as $row) $dateMap[$row['d']] = (int)$row['c'];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $chartLabels[] = date('d/m', strtotime($d));
    $chartData[]   = $dateMap[$d] ?? 0;
}
?>

<div style="padding:32px 32px 60px;">

<!-- BAŞLIK -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
  <div>
    <h1 style="font-size:26px;font-weight:700;color:#0A1F44;margin:0 0 4px;">İstatistikler</h1>
    <p style="color:#65758A;font-size:13px;margin:0;">Son 30 günlük veriler · Güncelleme: <?= date('d.m.Y H:i') ?></p>
  </div>
</div>

<!-- KARTlar -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
  <?php $cards = [
    ['Bugün Görüntülenme', $todayViews,    '#18C6C3', 'fa-eye'],
    ['Bu Hafta',           $weekViews,     '#174A83', 'fa-chart-line'],
    ['30 Günde Toplam',    $totalViews,    '#0A1F44', 'fa-calendar'],
    ['Form Başvuruları',   $totalMessages, '#138C65', 'fa-envelope'],
  ];
  foreach ($cards as $c): ?>
  <div style="background:#fff;border:1px solid #E5EAF0;border-radius:12px;padding:22px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 16px rgba(10,31,68,.05);">
    <div style="width:48px;height:48px;background:<?= $c[2] ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="fa-solid <?= $c[3] ?>" style="color:#fff;font-size:18px;"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:#0A1F44;font-family:Syne,sans-serif;"><?= number_format($c[1]) ?></div>
      <div style="font-size:12px;color:#65758A;font-weight:600;"><?= $c[0] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- GRAFİK -->
<div style="background:#fff;border:1px solid #E5EAF0;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 4px 16px rgba(10,31,68,.05);">
  <h3 style="font-size:15px;font-weight:700;color:#0A1F44;margin:0 0 20px;">Son 14 Gün — Günlük Görüntülenme</h3>
  <canvas id="viewsChart" height="80"></canvas>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

  <!-- EN ÇOK GÖRÜNTÜLENEN İLANLAR -->
  <div style="background:#fff;border:1px solid #E5EAF0;border-radius:12px;padding:22px;box-shadow:0 4px 16px rgba(10,31,68,.05);">
    <h3 style="font-size:15px;font-weight:700;color:#0A1F44;margin:0 0 16px;">En Çok Görüntülenen İlanlar (30 gün)</h3>
    <?php if (empty($topListings)): ?>
    <p style="color:#65758A;font-size:13px;">Henüz veri yok.</p>
    <?php else: foreach ($topListings as $row): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #F0F4F8;">
      <div>
        <div style="font-weight:600;font-size:13px;color:#0A1F44;"><?= e($row['title'] ?? $row['page_slug']) ?></div>
        <div style="font-size:11px;color:#65758A;"><?= $row['price'] ? formatPrice((float)$row['price'], $row['price_unit']) : '' ?></div>
      </div>
      <span style="background:#EAF3FF;color:#174A83;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;"><?= $row['views'] ?> görüntü</span>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <!-- EN ÇOK GÖRÜNTÜLENEN PROJELER -->
  <div style="background:#fff;border:1px solid #E5EAF0;border-radius:12px;padding:22px;box-shadow:0 4px 16px rgba(10,31,68,.05);">
    <h3 style="font-size:15px;font-weight:700;color:#0A1F44;margin:0 0 16px;">En Çok Görüntülenen Projeler (30 gün)</h3>
    <?php if (empty($topProjects)): ?>
    <p style="color:#65758A;font-size:13px;">Henüz veri yok.</p>
    <?php else: foreach ($topProjects as $row): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #F0F4F8;">
      <div style="font-weight:600;font-size:13px;color:#0A1F44;"><?= e($row['title'] ?? $row['page_slug']) ?></div>
      <span style="background:rgba(24,198,195,.12);color:#0A7F7D;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;"><?= $row['views'] ?> görüntü</span>
    </div>
    <?php endforeach; endif; ?>
  </div>

</div>

<!-- SON BAŞVURULAR -->
<div style="background:#fff;border:1px solid #E5EAF0;border-radius:12px;padding:22px;box-shadow:0 4px 16px rgba(10,31,68,.05);">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h3 style="font-size:15px;font-weight:700;color:#0A1F44;margin:0;">Son Form Başvuruları</h3>
    <a href="<?= ADMIN_URL ?>/?module=forms" style="font-size:12px;color:#174A83;font-weight:600;">Tümünü Gör →</a>
  </div>
  <?php if (empty($recentMessages)): ?>
  <p style="color:#65758A;font-size:13px;">Henüz başvuru yok.</p>
  <?php else: ?>
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #E5EAF0;">
          <th style="text-align:left;padding:8px 10px;color:#65758A;font-weight:600;">Ad Soyad</th>
          <th style="text-align:left;padding:8px 10px;color:#65758A;font-weight:600;">Konu</th>
          <th style="text-align:left;padding:8px 10px;color:#65758A;font-weight:600;">Tarih</th>
          <th style="text-align:left;padding:8px 10px;color:#65758A;font-weight:600;">Telefon</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentMessages as $msg): ?>
        <tr style="border-bottom:1px solid #F0F4F8;">
          <td style="padding:10px 10px;font-weight:600;color:#0A1F44;"><?= e($msg['name']) ?></td>
          <td style="padding:10px 10px;color:#65758A;"><?= e($msg['subject'] ?? ($msg['form_type'] ?? '—')) ?></td>
          <td style="padding:10px 10px;color:#65758A;white-space:nowrap;"><?= date('d.m.Y H:i', strtotime($msg['created_at'])) ?></td>
          <td style="padding:10px 10px;"><a href="tel:<?= e($msg['phone']) ?>" style="color:#174A83;font-weight:600;"><?= e($msg['phone']) ?></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('viewsChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($chartLabels) ?>,
    datasets: [{
      label: 'Görüntülenme',
      data: <?= json_encode($chartData) ?>,
      backgroundColor: 'rgba(24,198,195,.18)',
      borderColor: '#18C6C3',
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F0F4F8' } },
      x: { grid: { display: false } }
    }
  }
});
</script>
