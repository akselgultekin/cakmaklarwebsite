<?php
$statusLabels = ['satiasta' => 'Satışta', 'yakinda' => 'Yakında', 'teslim_edildi' => 'Tamamlandı'];
?>
<section class="hero" style="min-height:340px;padding:140px 0 80px;background:linear-gradient(90deg,rgba(10,31,68,.68),rgba(10,31,68,.18)),url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2200&q=85') center/cover;color:#fff;">
  <div class="container">
    <span class="eyebrow">Proje portföyü</span>
    <h1 style="color:#fff;margin:12px 0 16px;">Projelerimiz</h1>
    <p style="color:rgba(255,255,255,.8);max-width:560px;">Bolu\'da geliştirdiğimiz konut ve karma kullanım projelerini inceleyin.</p>
  </div>
</section>

<section style="padding:28px 0;background:#fff;border-bottom:1px solid var(--line);">
  <div class="container" style="display:flex;gap:10px;flex-wrap:wrap;">
    <a class="tab-btn <?= !$current_status ? 'active' : '' ?>" href="<?= SITE_URL ?>/projeler">Tümü</a>
    <a class="tab-btn <?= $current_status==='satiasta' ? 'active' : '' ?>" href="<?= SITE_URL ?>/projeler?durum=satiasta">Satışta</a>
    <a class="tab-btn <?= $current_status==='yakinda' ? 'active' : '' ?>" href="<?= SITE_URL ?>/projeler?durum=yakinda">Yakında</a>
    <a class="tab-btn <?= $current_status==='teslim_edildi' ? 'active' : '' ?>" href="<?= SITE_URL ?>/projeler?durum=teslim_edildi">Tamamlanan</a>
  </div>
</section>

<section style="padding:80px 0;">
  <div class="container">
    <?php if (empty($projects)): ?>
    <p style="color:var(--muted);text-align:center;padding:60px 0;">Henüz proje eklenmemiş.</p>
    <?php else: ?>
    <div class="project-grid">
      <?php foreach ($projects as $p): ?>
      <article class="project-card feature">
        <div class="card-image">
          <img src="<?= $p['cover_image'] ? e(uploadUrl($p['cover_image'])) : 'https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=1000&q=80' ?>" alt="<?= e($p['title']) ?>">
          <span class="status"><?= e($statusLabels[$p['status']] ?? $p['status']) ?></span>
          <div class="project-overlay">
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['location']) ?></p>
          </div>
        </div>
        <div class="project-body">
          <p><?= e(excerpt($p['short_desc'] ?? '', 120)) ?></p>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a class="mini-btn primary project-action" href="<?= SITE_URL ?>/projeler/<?= e($p['slug']) ?>">
              Projeyi İncele <i class="fa-solid fa-arrow-right"></i>
            </a>
            <?php if ($p['tour_url'] || $p['tour_embed']): ?>
            <a class="mini-btn" href="<?= SITE_URL ?>/3d-ev-gez/<?= e($p['slug']) ?>">
              <i class="fa-solid fa-cube"></i> 3D Tur
            </a>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?= paginationLinks($paginator, SITE_URL . '/projeler' . ($current_status ? '?durum=' . $current_status : '')) ?>
    <?php endif; ?>
  </div>
</section>
