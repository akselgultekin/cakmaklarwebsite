<article style="padding:100px 0 80px;">
  <div class="container" style="max-width:820px;margin:0 auto;">
    <a href="<?= SITE_URL ?>/haberler" style="color:var(--turquoise);font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:28px;">
      <i class="fa-solid fa-arrow-left"></i> Tüm Haberler
    </a>
    <span class="eyebrow"><?= formatDate($news['published_at'] ?? $news['created_at']) ?></span>
    <h1 style="margin:14px 0 24px;"><?= e($news['title']) ?></h1>
    <?php if ($news['summary']): ?>
    <p style="font-size:18px;color:var(--navy);font-weight:500;line-height:1.6;margin-bottom:28px;border-left:4px solid var(--turquoise);padding-left:20px;">
      <?= e($news['summary']) ?>
    </p>
    <?php endif; ?>
    <?php if ($news['cover_image']): ?>
    <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:32px;height:400px;">
      <img src="<?= e(uploadUrl($news['cover_image'])) ?>" alt="<?= e($news['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <?php endif; ?>
    <div style="line-height:1.85;color:var(--muted);"><?= $news['content'] ?></div>
  </div>
</article>
