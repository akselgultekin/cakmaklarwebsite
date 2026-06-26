<?php
$whatsapp  = setting('whatsapp', '');
$tagLabels = ['yeni' => 'Yeni', 'firsat' => 'Fırsat', 'krediye_uygun' => 'Krediye Uygun'];
$baseUrl   = SITE_URL . '/' . ($listing_type === 'satilik' ? 'satilik' : ($listing_type === 'kiralik' ? 'kiralik' : 'ticari'));
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Portföy</span>
    <h1><?= e($page_title) ?></h1>
    <p><?= $paginator['total'] ?> ilan listeleniyor</p>
  </div>
</section>

<!-- FİLTRE -->
<section style="padding:22px 0;background:#fff;border-bottom:1px solid var(--line);">
  <div class="container">
    <form method="GET" action="<?= e($baseUrl) ?>" class="search-grid" style="grid-template-columns:1fr 1fr 1fr 1.4fr auto;">
      <div class="field">
        <label>Konum</label>
        <select name="konum">
          <option value="">Tüm Bölgeler</option>
          <option <?= ($filters['konum'] ?? '') === 'Bolu / Merkez' ? 'selected' : '' ?>>Bolu / Merkez</option>
          <option <?= ($filters['konum'] ?? '') === 'Bolu / Mudurnu' ? 'selected' : '' ?>>Bolu / Mudurnu</option>
          <option <?= ($filters['konum'] ?? '') === 'Bolu / Gerede' ? 'selected' : '' ?>>Bolu / Gerede</option>
          <option <?= ($filters['konum'] ?? '') === 'Bolu / Karacasu' ? 'selected' : '' ?>>Bolu / Karacasu</option>
        </select>
      </div>
      <div class="field">
        <label>Oda Sayısı</label>
        <select name="oda">
          <option value="">Tümü</option>
          <?php foreach (['1+1','2+1','3+1','4+1','5+1'] as $oda): ?>
          <option <?= ($filters['oda'] ?? '') === $oda ? 'selected' : '' ?>><?= $oda ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>Min Fiyat</label>
        <input name="min_fiyat" value="<?= e($filters['min_fiyat'] ?? '') ?>" placeholder="Min TL" inputmode="numeric">
      </div>
      <div class="field">
        <label>Max Fiyat</label>
        <input name="max_fiyat" value="<?= e($filters['max_fiyat'] ?? '') ?>" placeholder="Max TL" inputmode="numeric">
      </div>
      <button class="btn dark" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Filtrele</button>
    </form>
  </div>
</section>

<section style="padding:70px 0;">
  <div class="container">
    <?php if (empty($listings)): ?>
    <p style="text-align:center;color:var(--muted);padding:60px 0;">Bu kriterlere uygun ilan bulunamadı.</p>
    <?php else: ?>
    <div class="listing-grid">
      <?php foreach ($listings as $l):
        $waMsgText = $l['whatsapp_msg'] ?: SITE_NAME . ' - ' . $l['title'] . ' hakkında bilgi almak istiyorum.';
        $tags = $l['status_tag'] ? explode(',', $l['status_tag']) : [];
      ?>
      <article class="listing-card">
        <div class="card-image">
          <img src="<?= $l['cover_image'] ? e(uploadUrl($l['cover_image'])) : 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=900&q=80' ?>" alt="<?= e($l['title']) ?>">
        </div>
        <div class="listing-body">
          <h3><?= e($l['title']) ?></h3>
          <div class="meta">
            <?php if ($l['location']): ?><span><?= e($l['location']) ?></span><?php endif; ?>
            <?php if ($l['area_m2']): ?><span><?= e($l['area_m2']) ?> m²</span><?php endif; ?>
            <?php if ($l['room_count']): ?><span><?= e($l['room_count']) ?></span><?php endif; ?>
          </div>
          <?php if (!empty($tags)): ?>
          <div class="tags">
            <?php foreach ($tags as $t): ?><span class="tag"><?= e($tagLabels[trim($t)] ?? trim($t)) ?></span><?php endforeach; ?>
          </div>
          <?php endif; ?>
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
    <?= paginationLinks($paginator, $baseUrl . '?konum=' . urlencode($filters['konum'] ?? '') . '&oda=' . urlencode($filters['oda'] ?? '')) ?>
    <?php endif; ?>
  </div>
</section>
