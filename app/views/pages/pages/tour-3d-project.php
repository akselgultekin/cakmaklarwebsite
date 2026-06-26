<section style="min-height:280px;padding:130px 0 50px;background:radial-gradient(circle at 20% 0%,rgba(24,198,195,.2),transparent 38%),#0A1F44;color:#fff;">
  <div class="container">
    <a href="<?= SITE_URL ?>/3d-ev-gez" style="color:rgba(255,255,255,.7);font-weight:600;display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;">
      <i class="fa-solid fa-arrow-left"></i> 3D Tur Listesi
    </a>
    <span class="eyebrow">Sanal tur</span>
    <h1 style="color:#fff;margin:12px 0 14px;"><?= e($project['title']) ?></h1>
    <?php if ($project['tour_desc']): ?>
    <p style="color:rgba(255,255,255,.8);max-width:560px;"><?= e($project['tour_desc']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section style="padding:60px 0 80px;background:#06142E;">
  <div class="container">
    <?php if ($project['tour_embed']): ?>
    <div style="border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;box-shadow:0 30px 80px rgba(0,0,0,.4);">
      <?= $project['tour_embed'] ?>
    </div>
    <?php elseif ($project['tour_url']): ?>
    <div style="border-radius:var(--radius);overflow:hidden;min-height:600px;background:rgba(255,255,255,.05);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:20px;">
      <i class="fa-solid fa-cube" style="font-size:64px;color:var(--turquoise);"></i>
      <h3 style="color:#fff;">3D Sanal Tur</h3>
      <a class="btn" href="<?= e($project['tour_url']) ?>" target="_blank" rel="noopener" style="min-height:56px;padding:0 32px;font-size:18px;">
        <i class="fa-solid fa-street-view"></i> Turu Dış Sekmede Aç
      </a>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;justify-content:center;margin-top:28px;flex-wrap:wrap;">
      <a class="btn" href="<?= SITE_URL ?>/projeler/<?= e($project['slug']) ?>" style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);">
        <i class="fa-solid fa-building"></i> Projeyi İncele
      </a>
      <a class="btn" href="<?= SITE_URL ?>/iletisim" style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);">
        <i class="fa-solid fa-phone"></i> Bizi Arayın
      </a>
    </div>
  </div>
</section>
