<?php
$whatsapp  = setting('whatsapp', '');
$tagLabels = ['yeni' => 'Yeni', 'firsat' => 'Fırsat', 'krediye_uygun' => 'Krediye Uygun'];
$baseUrl   = SITE_URL . '/' . ($listing_type === 'satilik' ? 'satilik' : ($listing_type === 'kiralik' ? 'kiralik' : 'ticari'));

// Fiyat slider aralıkları
$isSatilik  = $listing_type === 'satilik';
$priceMax   = $isSatilik ? 30000000 : 150000;
$priceStep  = $isSatilik ? 50000 : 500;
$priceStart = (int)($filters['min_fiyat'] ?? 0) ?: 0;
$priceEnd   = (int)($filters['max_fiyat'] ?? 0) ?: $priceMax;
$odalar     = ['1+1','2+1','3+1','4+1','5+1'];
$activeOda  = $filters['oda'] ?? '';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Portföy</span>
    <h1><?= e($page_title) ?></h1>
    <p><?= $paginator['total'] ?> ilan listeleniyor</p>
  </div>
</section>

<div class="container listing-page-layout">

  <!-- SIDEBAR FİLTRE -->
  <aside class="listing-filter-sidebar">
    <form method="GET" action="<?= e($baseUrl) ?>" id="filterForm">
      <div class="filter-block">
        <h4>Konum</h4>
        <select name="konum" onchange="this.form.submit()">
          <option value="">Tüm Bölgeler</option>
          <?php foreach (['Bolu / Merkez','Bolu / Mudurnu','Bolu / Gerede','Bolu / Karacasu'] as $k): ?>
          <option <?= ($filters['konum'] ?? '') === $k ? 'selected' : '' ?>><?= $k ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-block">
        <h4>Oda Sayısı</h4>
        <div class="filter-chips">
          <a href="<?= e($baseUrl . '?' . http_build_query(array_merge($filters, ['oda' => '', 'sayfa' => '']))) ?>"
             class="fchip <?= $activeOda === '' ? 'active' : '' ?>">Tümü</a>
          <?php foreach ($odalar as $oda): ?>
          <a href="<?= e($baseUrl . '?' . http_build_query(array_merge($filters, ['oda' => $oda, 'sayfa' => '']))) ?>"
             class="fchip <?= $activeOda === $oda ? 'active' : '' ?>"><?= $oda ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="filter-block">
        <h4>Fiyat Aralığı</h4>
        <div id="priceSlider" style="margin:12px 4px 18px;"></div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);font-weight:600;">
          <span id="priceMinLabel">0</span>
          <span id="priceMaxLabel">Max</span>
        </div>
        <input type="hidden" name="min_fiyat" id="minFiyatInput" value="<?= e($priceStart ?: '') ?>">
        <input type="hidden" name="max_fiyat" id="maxFiyatInput" value="<?= e($priceEnd < $priceMax ? $priceEnd : '') ?>">
      </div>

      <div class="filter-block">
        <h4>Alan (m²)</h4>
        <div style="display:flex;gap:8px;align-items:center;">
          <input name="min_m2" value="<?= e($filters['min_m2'] ?? '') ?>" placeholder="Min" inputmode="numeric" style="width:50%;">
          <span style="color:var(--muted);">–</span>
          <input name="max_m2" value="<?= e($filters['max_m2'] ?? '') ?>" placeholder="Max" inputmode="numeric" style="width:50%;">
        </div>
      </div>

      <button class="btn dark filter-submit-btn" type="submit">
        <i class="fa-solid fa-magnifying-glass"></i> Filtrele
      </button>

      <?php if (array_filter(array_intersect_key($filters, array_flip(['konum','oda','min_fiyat','max_fiyat','min_m2','max_m2'])))): ?>
      <a href="<?= e($baseUrl) ?>" class="filter-reset-link">
        <i class="fa-solid fa-xmark"></i> Filtreleri Temizle
      </a>
      <?php endif; ?>
    </form>
  </aside>

  <!-- İLAN LİSTESİ -->
  <div class="listing-results">
    <?php if (empty($listings)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--muted);">
      <i class="fa-solid fa-house-circle-xmark" style="font-size:48px;margin-bottom:18px;display:block;opacity:.3;"></i>
      <p>Bu kriterlere uygun ilan bulunamadı.</p>
      <a href="<?= e($baseUrl) ?>" class="btn" style="margin-top:16px;">Filtreleri Temizle</a>
    </div>
    <?php else: ?>
    <div class="listing-grid">
      <?php foreach ($listings as $l):
        $waMsgText = $l['whatsapp_msg'] ?: SITE_NAME . ' - ' . $l['title'] . ' hakkında bilgi almak istiyorum.';
        $tags = $l['status_tag'] ? explode(',', $l['status_tag']) : [];
      ?>
      <article class="listing-card">
        <div class="card-image">
          <img loading="lazy" src="<?= $l['cover_image'] ? e(uploadUrl($l['cover_image'])) : 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=900&q=80' ?>" alt="<?= e($l['title']) ?>">
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
    <?= paginationLinks($paginator, $baseUrl . '?' . http_build_query(array_filter($filters))) ?>
    <?php endif; ?>
  </div>
</div>

<!-- noUiSlider -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const slider = document.getElementById('priceSlider');
  if (!slider || typeof noUiSlider === 'undefined') return;

  const priceMax  = <?= $priceMax ?>;
  const priceStep = <?= $priceStep ?>;
  const startMin  = <?= $priceStart ?>;
  const startMax  = <?= $priceEnd ?>;

  noUiSlider.create(slider, {
    start: [startMin, startMax],
    connect: true,
    step: priceStep,
    range: { min: 0, max: priceMax },
    tooltips: false,
  });

  const minInput  = document.getElementById('minFiyatInput');
  const maxInput  = document.getElementById('maxFiyatInput');
  const minLabel  = document.getElementById('priceMinLabel');
  const maxLabel  = document.getElementById('priceMaxLabel');

  function fmt(v) {
    if (v >= 1000000) return (v/1000000).toFixed(v%1000000===0?0:1) + ' M₺';
    if (v >= 1000)    return (v/1000).toFixed(0) + ' B₺';
    return v.toLocaleString('tr-TR') + ' ₺';
  }

  slider.noUiSlider.on('update', function (values) {
    const lo = Math.round(values[0]);
    const hi = Math.round(values[1]);
    minLabel.textContent = lo > 0 ? fmt(lo) : '0';
    maxLabel.textContent = hi < priceMax ? fmt(hi) : 'Max';
    minInput.value = lo > 0 ? lo : '';
    maxInput.value = hi < priceMax ? hi : '';
  });
});
</script>
