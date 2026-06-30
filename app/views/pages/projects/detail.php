<?php
$phone    = setting('phone', '');
$whatsapp = setting('whatsapp', '');
$statusLabels = ['satiasta' => 'Satışta', 'yakinda' => 'Yakında', 'teslim_edildi' => 'Tamamlandı'];
$statusLabel  = $statusLabels[$project['status']] ?? $project['status'];
?>

<!-- HERO -->
<section class="hero" style="min-height:720px;display:flex;align-items:flex-end;padding:160px 0 80px;background:linear-gradient(90deg,rgba(10,31,68,.55),rgba(10,31,68,.08)),url('<?= $project['cover_image'] ? e(uploadUrl($project['cover_image'])) : 'https://images.unsplash.com/photo-1600607687644-c7171b42498f?auto=format&fit=crop&w=2200&q=85' ?>') center/cover;">
  <div class="container">
    <span class="eyebrow"><?= e($statusLabel) ?></span>
    <h1 style="color:#fff;margin:14px 0 16px;"><?= e($project['title']) ?></h1>
    <p style="max-width:580px;color:rgba(255,255,255,.8);"><?= e($project['short_desc']) ?></p>
    <div style="display:flex;gap:12px;margin-top:26px;flex-wrap:wrap;">
      <a class="btn" href="#galeri">Galeriyi Gör</a>
      <?php if ($project['tour_url'] || $project['tour_embed']): ?>
      <a class="btn ghost" href="<?= SITE_URL ?>/3d-ev-gez/<?= e($project['slug']) ?>"><i class="fa-solid fa-cube"></i> 360° Turu Başlat</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- SUBNAV -->
<nav class="project-subnav" id="projectSubnav">
  <div class="container project-subnav-inner">
    <a class="psnav-link" href="#hakkinda">Hakkında</a>
    <?php if (!empty($floor_plans)): ?><a class="psnav-link" href="#planlar">Kat Planları</a><?php endif; ?>
    <?php if (!empty($images)): ?><a class="psnav-link" href="#galeri">Galeri</a><?php endif; ?>
    <?php if (!empty($listings)): ?><a class="psnav-link" href="#ilanlar">Bu Projedeki İlanlar</a><?php endif; ?>
    <a class="psnav-link" href="#iletisim">Bizi Arayın</a>
  </div>
</nav>

<main>
<!-- HAKKINDA -->
<section class="project-about-section" id="hakkinda">
  <div class="container">
    <span class="eyebrow">Proje hakkında</span>
    <p class="project-lead">
      <strong><?= e($project['title']) ?></strong><?php if ($project['short_desc']): ?>, <?= e($project['short_desc']) ?><?php endif; ?>
    </p>
    <?php if ($project['description']): ?>
    <div class="project-about-body">
      <?= $project['description'] ?>
    </div>
    <?php endif; ?>
    <div class="project-specs">
      <?php if ($project['location']): ?>
      <div class="pspec"><strong><?= e($project['location']) ?></strong>Konum</div>
      <?php endif; ?>
      <div class="pspec"><strong style="color:var(--turquoise);"><?= e($statusLabel) ?></strong>Durum</div>
      <?php if (!empty($floor_plans)): ?>
      <div class="pspec"><strong><?= count($floor_plans) ?></strong>Kat Planı Tipi</div>
      <?php endif; ?>
      <?php if (!empty($listings)): ?>
      <div class="pspec"><strong><?= count($listings) ?></strong>Aktif İlan</div>
      <?php endif; ?>
      <?php if ($project['tour_url'] || $project['tour_embed']): ?>
      <div class="pspec"><strong>360°</strong>Sanal Tur</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- GALERİ -->
<?php if (!empty($images)): ?>
<section style="padding:80px 0;" id="galeri">
  <div class="container">
    <span class="eyebrow">Galeri</span>
    <h2 style="margin-top:10px;">Dış Mekan ve İç Mekan Görselleri</h2>
    <div style="display:grid;grid-template-columns:1.3fr .7fr;gap:18px;margin-top:32px;">
      <div style="height:520px;border-radius:var(--radius);overflow:hidden;">
        <img loading="lazy" src="<?= e(uploadUrl($images[0]['image'])) ?>" alt="<?= e($images[0]['alt'] ?? $project['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
      </div>
      <div style="display:grid;gap:18px;">
        <?php foreach (array_slice($images, 1, 2) as $img): ?>
        <div style="height:<?= count($images) > 1 ? '251' : '520' ?>px;border-radius:var(--radius);overflow:hidden;">
          <img loading="lazy" src="<?= e(uploadUrl($img['image'])) ?>" alt="<?= e($img['alt'] ?? $project['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if (count($images) > 3): ?>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:18px;">
      <?php foreach (array_slice($images, 3) as $img): ?>
      <div style="height:220px;border-radius:var(--radius);overflow:hidden;">
        <img loading="lazy" src="<?= e(uploadUrl($img['image'])) ?>" alt="<?= e($img['alt'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<!-- KAT PLANLARI -->
<?php if (!empty($floor_plans)): ?>
<section style="padding:80px 0;background:var(--soft);" id="planlar">
  <div class="container">
    <span class="eyebrow">Kat planları</span>
    <h2 style="margin-top:10px;">Daire Seçenekleri</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-top:32px;">
      <?php foreach ($floor_plans as $plan): ?>
      <div style="border:1px solid var(--line);border-radius:var(--radius);padding:26px;background:#fff;">
        <?php if ($plan['image']): ?>
        <div style="height:180px;border-radius:6px;overflow:hidden;margin-bottom:16px;">
          <img loading="lazy" src="<?= e(uploadUrl($plan['image'])) ?>" alt="<?= e($plan['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endif; ?>
        <h3 style="font-family:Syne,sans-serif;font-size:28px;color:var(--navy);"><?= e($plan['title']) ?></h3>
        <?php if ($plan['area_m2']): ?><p style="font-weight:600;color:var(--turquoise);"><?= e($plan['area_m2']) ?> m²</p><?php endif; ?>
        <?php if ($plan['desc']): ?><p style="margin-top:8px;"><?= e($plan['desc']) ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- 3D TUR -->
<?php if ($project['tour_url'] || $project['tour_embed']): ?>
<section style="padding:80px 0;background:radial-gradient(circle at 20% 0%,rgba(24,198,195,.18),transparent 34%),#0A1F44;color:#fff;">
  <div class="container" style="display:grid;grid-template-columns:1fr 1.3fr;gap:48px;align-items:center;">
    <div>
      <span class="eyebrow">Sanal tur</span>
      <h2 style="color:#fff;margin:14px 0 18px;">360° 3D Sanal Tur</h2>
      <?php if ($project['tour_desc']): ?><p style="color:rgba(255,255,255,.76);"><?= e($project['tour_desc']) ?></p><?php endif; ?>
      <?php if ($project['tour_url']): ?>
      <a class="btn" style="margin-top:28px;display:inline-flex;" href="<?= e($project['tour_url']) ?>" target="_blank" rel="noopener">
        <i class="fa-solid fa-street-view"></i> Turu Başlat
      </a>
      <?php endif; ?>
    </div>
    <div>
      <?php if ($project['tour_embed']): ?>
      <div style="border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;">
        <?= $project['tour_embed'] ?>
      </div>
      <?php elseif ($project['tour_url']): ?>
      <div style="border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;">
        <a href="<?= e($project['tour_url']) ?>" target="_blank" class="btn" style="font-size:18px;padding:0 30px;min-height:56px;">
          <i class="fa-solid fa-cube"></i> Sanal Tura Git
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- PROJEDEKİ İLANLAR -->
<?php if (!empty($listings)): ?>
<section style="padding:80px 0;" id="ilanlar">
  <div class="container">
    <span class="eyebrow"><?= e($project['title']) ?>'da satışta</span>
    <h2 style="margin-top:10px;">Bu Projedeki İlanlar</h2>
    <div class="listing-grid" style="margin-top:32px;">
      <?php foreach ($listings as $l):
        $waMsgText = $l['whatsapp_msg'] ?: SITE_NAME . ' - ' . $l['title'] . ' hakkında bilgi almak istiyorum.';
        $tags = $l['status_tag'] ? explode(',', $l['status_tag']) : [];
        $tagLabels = ['yeni' => 'Yeni', 'firsat' => 'Fırsat', 'krediye_uygun' => 'Krediye Uygun'];
      ?>
      <article class="listing-card">
        <div class="card-image">
          <img loading="lazy" src="<?= $l['cover_image'] ? e(uploadUrl($l['cover_image'])) : 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=900&q=80' ?>" alt="<?= e($l['title']) ?>">
        </div>
        <div class="listing-body">
          <h3><?= e($l['title']) ?></h3>
          <div class="meta">
            <?php if ($l['area_m2']): ?><span><?= e($l['area_m2']) ?> m²</span><?php endif; ?>
            <?php if ($l['room_count']): ?><span><?= e($l['room_count']) ?></span><?php endif; ?>
            <?php if ($l['floor']): ?><span><?= e($l['floor']) ?>. Kat</span><?php endif; ?>
          </div>
          <div class="price"><?= $l['price'] ? formatPrice($l['price'], $l['price_unit']) : 'Fiyat sorunuz' ?></div>
          <div class="card-actions">
            <?php if ($whatsapp): ?>
            <a class="mini-btn whatsapp-action" href="<?= e(whatsappUrl($whatsapp, $waMsgText)) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            <?php endif; ?>
            <a class="mini-btn detail-action" href="<?= SITE_URL ?>/ilan/<?= e($l['slug']) ?>">Detay <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- İLETİŞİM CTA -->
<section style="padding:80px 0;background:radial-gradient(circle at 78% 28%,rgba(24,198,195,.10),transparent 30%),#0A1F44;color:#fff;" id="iletisim">
  <div class="container" style="display:grid;grid-template-columns:1.15fr auto;gap:32px;align-items:center;">
    <div>
      <span class="eyebrow">Satış ofisi</span>
      <h2 style="color:#fff;margin:14px 0 18px;">Projeyi birlikte gezelim.</h2>
      <p style="color:rgba(255,255,255,.76);">Proje hakkında bilgi almak, örnek daire ziyareti veya ön talep için bizi arayın.</p>
    </div>
    <div style="display:flex;flex-direction:column;gap:12px;">
      <?php if ($phone): ?>
      <a class="btn" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" style="min-height:54px;padding:0 28px;font-size:16px;">
        <i class="fa-solid fa-phone"></i> <?= e($phone) ?>
      </a>
      <?php endif; ?>
      <?php if ($whatsapp): ?>
      <a class="btn" href="<?= e(whatsappUrl($whatsapp, 'Merhaba, ' . $project['title'] . ' projesi hakkında bilgi almak istiyorum.')) ?>" target="_blank" style="background:#138C65;min-height:54px;padding:0 28px;font-size:16px;">
        <i class="fa-brands fa-whatsapp"></i> WhatsApp
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>
</main>
