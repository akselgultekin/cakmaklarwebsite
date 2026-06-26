<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($meta_title ?? setting('seo_title', SITE_NAME)) ?></title>
  <meta name="description" content="<?= e($meta_desc ?? setting('seo_desc')) ?>">
  <?php if (!empty($meta_noindex)): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
  <link rel="canonical" href="<?= e(SITE_URL . ($_SERVER['REQUEST_URI'] ?? '/')) ?>">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Ana CSS -->
  <link rel="stylesheet" href="<?= SITE_URL ?>/public/assets/css/main.css">

  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($meta_title ?? setting('seo_title')) ?>">
  <meta property="og:description" content="<?= e($meta_desc ?? setting('seo_desc')) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e(SITE_URL . ($_SERVER['REQUEST_URI'] ?? '/')) ?>">
  <?php if (!empty($og_image)): ?>
  <meta property="og:image" content="<?= e(uploadUrl($og_image)) ?>">
  <?php endif; ?>

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
