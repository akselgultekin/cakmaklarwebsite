<section style="min-height:260px;padding:130px 0 60px;background:linear-gradient(90deg,rgba(10,31,68,.72),rgba(10,31,68,.22)),url('https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=2200&q=85') center/cover;">
  <div class="container">
    <span class="eyebrow">Güncel bilgiler</span>
    <h1 style="color:#fff;margin:12px 0;">Haberler & Duyurular</h1>
  </div>
</section>

<section style="padding:80px 0;">
  <div class="container">
    <?php if (empty($news)): ?>
    <p style="text-align:center;color:var(--muted);padding:60px 0;">Henüz haber eklenmemiş.</p>
    <?php else: ?>
    <div class="news-list">
      <?php foreach ($news as $n): ?>
      <article class="news-item">
        <div class="news-image">
          <img src="<?= $n['cover_image'] ? e(uploadUrl($n['cover_image'])) : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80' ?>" alt="<?= e($n['title']) ?>">
        </div>
        <div class="news-copy">
          <div class="news-date"><?= formatDate($n['published_at'] ?? $n['created_at']) ?></div>
          <h3><?= e($n['title']) ?></h3>
          <p><?= e(excerpt($n['summary'] ?? '', 120)) ?></p>
          <a class="news-link" href="<?= SITE_URL ?>/haberler/<?= e($n['slug']) ?>">Devamını oku <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?= paginationLinks($paginator, SITE_URL . '/haberler') ?>
    <?php endif; ?>
  </div>
</section>
