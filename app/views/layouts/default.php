<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
    $seoTitle = e($meta_title ?? setting('seo_title', SITE_NAME));
    $seoDesc  = e($meta_desc  ?? setting('seo_desc', ''));
    $seoUrl   = e(SITE_URL . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'));
    $seoImg   = !empty($og_image) ? e(uploadUrl($og_image)) : e(SITE_URL . '/public/assets/img/og-default.jpg');
    $siteName = e(setting('site_name', SITE_NAME));
  ?>
  <title><?= $seoTitle ?></title>
  <meta name="description" content="<?= $seoDesc ?>">
  <meta name="robots" content="<?= !empty($meta_noindex) ? 'noindex,nofollow' : 'index,follow' ?>">
  <link rel="canonical" href="<?= $seoUrl ?>">

  <!-- Open Graph -->
  <meta property="og:site_name" content="<?= $siteName ?>">
  <meta property="og:title"       content="<?= $seoTitle ?>">
  <meta property="og:description" content="<?= $seoDesc ?>">
  <meta property="og:type"        content="<?= $og_type ?? 'website' ?>">
  <meta property="og:url"         content="<?= $seoUrl ?>">
  <meta property="og:image"       content="<?= $seoImg ?>">
  <meta property="og:locale"      content="tr_TR">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= $seoTitle ?>">
  <meta name="twitter:description" content="<?= $seoDesc ?>">
  <meta name="twitter:image"       content="<?= $seoImg ?>">

  <!-- Favicon -->
  <?php $favicon = setting('favicon', ''); ?>
  <?php if ($favicon): ?>
  <link rel="icon" type="image/png" href="<?= e(uploadUrl($favicon)) ?>">
  <?php else: ?>
  <link rel="icon" type="image/png" href="<?= SITE_URL ?>/public/assets/img/placeholder.jpg">
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Ana CSS -->
  <link rel="stylesheet" href="<?= SITE_URL ?>/public/assets/css/main.css">

  <!-- JSON-LD: LocalBusiness (tüm sayfalarda) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "RealEstateAgent",
    "name": "<?= e(setting('site_name', SITE_NAME)) ?>",
    "url": "<?= SITE_URL ?>",
    "logo": "<?= $seoImg ?>",
    "telephone": "<?= e(setting('phone', '')) ?>",
    "email": "<?= e(setting('email', '')) ?>",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Bolu",
      "addressCountry": "TR",
      "streetAddress": "<?= e(setting('address', 'Bolu Merkez')) ?>"
    },
    "sameAs": [
      "<?= e(setting('facebook', '')) ?>",
      "<?= e(setting('instagram', '')) ?>"
    ]
  }
  </script>
  <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>

<?php require APP_PATH . '/views/layouts/header.php'; ?>

<main>
<?= $content ?>
</main>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>

<!-- Ana JS -->
<script src="<?= SITE_URL ?>/public/assets/js/main.js"></script>
<?php if (!empty($extra_js)) echo $extra_js; ?>

</body>
</html>
