<?php
/**
 * Kat Planları sayfası
 * $projects → tüm aktif projeler (floor plans dahil edilmiş değil, lazy load yapılacak)
 */
?>
<!-- HERO -->
<section class="hero-inner">
  <div class="hero-inner-overlay"></div>
  <div class="container">
    <div class="hero-inner-content">
      <h1>Kat Planları</h1>
      <p>Projelerimizin detaylı kat ve daire planlarını inceleyin</p>
      <nav class="breadcrumb-nav">
        <a href="<?= SITE_URL ?>/">Ana Sayfa</a>
        <span>›</span>
        <span>Kat Planları</span>
      </nav>
    </div>
  </div>
</section>

<!-- PROJELER -->
<section class="section-pad" style="background:#fff;">
  <div class="container">
    <?php if (empty($projects)): ?>
    <div style="text-align:center;padding:80px 0;color:#65758a;">
      <i class="fa-solid fa-drafting-compass" style="font-size:56px;margin-bottom:20px;display:block;opacity:.3;"></i>
      <p>Henüz yayımlanan kat planı bulunmamaktadır.</p>
    </div>
    <?php else: ?>
    <?php foreach ($projects as $prj):
      require_once APP_PATH . '/models/ProjectModel.php';
      static $pm; if (!$pm) $pm = new ProjectModel();
      $plans = $pm->getFloorPlans($prj['id']);
      if (empty($plans)) continue;
    ?>
    <div style="margin-bottom:60px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
        <div>
          <h2 style="font-size:24px;margin-bottom:6px;"><?= e($prj['title']) ?></h2>
          <p style="color:#65758a;"><?= e($prj['location'] ?? '') ?></p>
        </div>
        <a class="btn-link" href="<?= SITE_URL ?>/projeler/<?= e($prj['slug']) ?>" style="color:var(--teal,#18C6C3);font-weight:700;font-size:14px;">
          Projeyi Gör <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
        <?php foreach ($plans as $plan): ?>
        <div style="background:var(--bg-soft,#F4F6F9);border-radius:12px;overflow:hidden;border:1px solid #E5EAF0;">
          <?php if (!empty($plan['image'])): ?>
          <a href="<?= e(uploadUrl($plan['image'])) ?>" target="_blank">
            <img loading="lazy" src="<?= e(uploadUrl($plan['image'])) ?>" alt="<?= e($plan['title']) ?>"
                 style="width:100%;height:200px;object-fit:cover;display:block;">
          </a>
          <?php else: ?>
          <div style="height:120px;background:var(--navy,#0A1F44);display:grid;place-items:center;">
            <i class="fa-solid fa-drafting-compass" style="font-size:40px;color:rgba(24,198,195,.4);"></i>
          </div>
          <?php endif; ?>
          <div style="padding:18px 20px;">
            <h4 style="margin-bottom:6px;font-size:16px;"><?= e($plan['title']) ?></h4>
            <?php if ($plan['area_m2']): ?>
            <span style="background:rgba(24,198,195,.12);color:var(--teal,#18C6C3);padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;">
              <?= e($plan['area_m2']) ?> m²
            </span>
            <?php endif; ?>
            <?php if (!empty($plan['desc'])): ?>
            <p style="margin-top:10px;color:#65758a;font-size:13px;line-height:1.6;"><?= e($plan['desc']) ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
