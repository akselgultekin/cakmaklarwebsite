<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($listing['title']) ?> — Broşür | <?= e($siteName) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Elms+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Elms Sans',Arial,sans-serif;color:#0A1F44;background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  a{color:inherit;text-decoration:none}
  /* PAGE */
  .page{max-width:860px;margin:0 auto;padding:48px 48px 60px}
  /* HEADER */
  .bro-header{display:flex;justify-content:space-between;align-items:center;padding-bottom:24px;border-bottom:2px solid #18C6C3;margin-bottom:36px}
  .bro-badge{background:#18C6C3;color:#0A1F44;font-weight:700;font-size:12px;padding:6px 14px;border-radius:999px;letter-spacing:.08em;text-transform:uppercase}
  /* HERO */
  .bro-hero{width:100%;height:400px;object-fit:cover;border-radius:12px;display:block;margin-bottom:28px}
  .bro-hero-placeholder{width:100%;height:400px;background:linear-gradient(135deg,#0A1F44,#18C6C3);border-radius:12px;margin-bottom:28px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;opacity:.5}
  /* TITLE ROW */
  .bro-title-row{display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start;margin-bottom:28px}
  .bro-type{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#18C6C3;margin-bottom:8px}
  .bro-title{font-family:Syne,sans-serif;font-size:clamp(26px,4vw,38px);font-weight:800;line-height:1.1;color:#0A1F44}
  .bro-location{margin-top:10px;color:#65758A;font-size:14px;font-weight:600}
  .bro-price{font-family:Syne,sans-serif;font-size:clamp(24px,3vw,36px);font-weight:800;color:#0A1F44;text-align:right;white-space:nowrap}
  .bro-price small{display:block;font-size:12px;color:#65758A;font-weight:600;margin-bottom:4px}
  /* SPECS */
  .bro-specs{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:32px}
  .bro-spec{background:#FAFBFC;border:1px solid #E5EAF0;border-radius:8px;padding:14px 16px}
  .bro-spec small{display:block;font-size:11px;color:#65758A;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
  .bro-spec strong{font-family:Syne,sans-serif;font-size:18px;color:#0A1F44;font-weight:700}
  /* DESCRIPTION */
  .bro-desc-title{font-family:Syne,sans-serif;font-size:18px;font-weight:700;margin-bottom:12px;color:#0A1F44}
  .bro-desc{color:#65758A;line-height:1.75;font-size:14px;margin-bottom:36px}
  /* GALLERY */
  .bro-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:36px}
  .bro-gallery img{width:100%;height:140px;object-fit:cover;border-radius:8px;display:block}
  /* FOOTER */
  .bro-footer{border-top:1px solid #E5EAF0;padding-top:24px;display:flex;justify-content:space-between;align-items:center}
  .bro-contact strong{display:block;font-size:14px;font-weight:700;color:#0A1F44;margin-bottom:4px}
  .bro-contact span{font-size:13px;color:#65758A}
  .bro-qr-placeholder{width:64px;height:64px;border:2px solid #E5EAF0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#65758A;font-size:10px;text-align:center;padding:4px}
  /* PRINT */
  @media print{
    body{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .no-print{display:none!important}
    .page{padding:20px 24px;max-width:100%}
    .bro-hero{height:300px}
    .bro-gallery img{height:110px}
  }
  /* PRINT BUTTON */
  .print-bar{position:fixed;bottom:0;left:0;right:0;background:#0A1F44;color:#fff;padding:14px 32px;display:flex;justify-content:space-between;align-items:center;z-index:100}
  .print-bar p{font-size:14px;color:rgba(255,255,255,.75)}
  .print-btn{background:#18C6C3;color:#0A1F44;border:none;font-weight:700;font-size:15px;padding:12px 28px;border-radius:999px;cursor:pointer;font-family:inherit}
  .print-btn:hover{background:#14b0ad}
</style>
</head>
<body>

<div class="page">
  <!-- Header -->
  <header class="bro-header">
    <?= $logoHtml ?>
    <span class="bro-badge"><?= e($typeLabel) ?> İlanı</span>
  </header>

  <!-- Hero image -->
  <?php if (!empty($allImages[0])): ?>
  <img class="bro-hero" src="<?= e(uploadUrl($allImages[0]['image'])) ?>" alt="<?= e($listing['title']) ?>">
  <?php else: ?>
  <div class="bro-hero-placeholder"><i>📷</i></div>
  <?php endif; ?>

  <!-- Title + Price -->
  <div class="bro-title-row">
    <div>
      <div class="bro-type"><?= e($typeLabel) ?> Konut</div>
      <h1 class="bro-title"><?= e($listing['title']) ?></h1>
      <?php if ($listing['location']): ?>
      <div class="bro-location">📍 <?= e($listing['location']) ?></div>
      <?php endif; ?>
    </div>
    <div class="bro-price">
      <small>Fiyat</small>
      <?= $priceStr ?>
    </div>
  </div>

  <!-- Specs -->
  <?php if (!empty($specs)): ?>
  <div class="bro-specs">
    <?php foreach ($specs as $label => $val): ?>
    <div class="bro-spec"><small><?= $label ?></small><strong><?= e($val) ?><?= $label === 'm²' ? ' m²' : '' ?></strong></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Description -->
  <?php if ($listing['description']): ?>
  <div class="bro-desc-title">Açıklama</div>
  <div class="bro-desc"><?= strip_tags($listing['description']) ?></div>
  <?php endif; ?>

  <!-- Gallery (2-4 extra images) -->
  <?php $galleryImgs = array_slice($allImages, 1, 5); ?>
  <?php if (!empty($galleryImgs)): ?>
  <div class="bro-gallery">
    <?php foreach ($galleryImgs as $img): ?>
    <img src="<?= e(uploadUrl($img['image'])) ?>" alt="<?= e($listing['title']) ?>">
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Footer -->
  <footer class="bro-footer">
    <div class="bro-contact">
      <strong><?= e($siteName) ?></strong>
      <?php if ($phone): ?><span>📞 <?= e($phone) ?></span><?php endif; ?>
      <span style="margin-left:12px;">🌐 <?= e(str_replace(['https://','http://'], '', $siteUrl)) ?></span>
    </div>
    <div class="bro-qr-placeholder" style="font-size:9px;text-align:center;line-height:1.3;">İlan QR<br><?= e($listing['slug']) ?></div>
  </footer>
</div>

<!-- Print bar -->
<div class="print-bar no-print">
  <p>Sayfayı PDF olarak kaydetmek için "Yazdır → PDF Kaydet" seçeneğini kullanın.</p>
  <button class="print-btn" onclick="window.print()">🖨️ Broşürü İndir / Yazdır</button>
</div>

<script>
  // Sayfayı açar açmaz tarayıcı print dialog'unu tetikle
  window.addEventListener('load', function() {
    // 600ms bekle ki görseller yüklensin
    setTimeout(function() {
      if (window.location.search.includes('auto=1')) window.print();
    }, 600);
  });
</script>
</body>
</html>
