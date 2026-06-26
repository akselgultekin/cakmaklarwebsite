<?php $whatsapp = setting('whatsapp', ''); ?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Araç portföyü</span>
    <h1>Seçilmiş araçlar, güvenli ve şeffaf süreç.</h1>
    <p>Çakmaklar İnşaat'ın premium vitrininde yer alan güncel araç ilanlarını inceleyin.</p>
  </div>
</section>

<section style="padding:80px 0;">
  <div class="container">
    <div class="section-head">
      <div><span class="eyebrow">Güncel ilanlar</span><h2>Araç İlanları</h2></div>
      <p><?= $paginator['total'] ?> araç listeleniyor</p>
    </div>
    <?php if (empty($vehicles)): ?>
    <p style="text-align:center;color:var(--muted);padding:60px 0;">Henüz araç ilanı eklenmemiş.</p>
    <?php else: ?>
    <div class="vehicle-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
      <?php foreach ($vehicles as $v):
        $waMsgText = SITE_NAME . ' - ' . $v['year'] . ' ' . $v['brand'] . ' ' . $v['model'] . ' hakkında bilgi almak istiyorum.';
      ?>
      <article style="overflow:hidden;border:1px solid var(--line);border-radius:var(--radius);background:#fff;box-shadow:0 16px 42px rgba(10,31,68,.06);">
        <div style="height:260px;overflow:hidden;">
          <img src="<?= $v['cover_image'] ? e(uploadUrl($v['cover_image'])) : 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1000&q=80' ?>" alt="<?= e($v['brand'] . ' ' . $v['model']) ?>" style="width:100%;height:100%;object-fit:cover;transition:transform .78s ease;">
        </div>
        <div style="padding:22px;">
          <h3 style="font-size:22px;font-family:Syne,sans-serif;"><?= e($v['year'] . ' ' . $v['brand'] . ' ' . $v['model']) ?></h3>
          <div class="meta" style="margin:14px 0;">
            <?php if ($v['year']): ?><span><i class="fa-regular fa-calendar" style="color:var(--turquoise);margin-right:4px;"></i><?= e($v['year']) ?></span><?php endif; ?>
            <?php if ($v['km']): ?><span><i class="fa-solid fa-gauge-high" style="color:var(--turquoise);margin-right:4px;"></i><?= number_format($v['km'], 0, ',', '.') ?> KM</span><?php endif; ?>
            <?php if ($v['fuel']): ?><span><i class="fa-solid fa-gas-pump" style="color:var(--turquoise);margin-right:4px;"></i><?= e($v['fuel']) ?></span><?php endif; ?>
            <?php if ($v['transmission']): ?><span><i class="fa-solid fa-gear" style="color:var(--turquoise);margin-right:4px;"></i><?= e($v['transmission']) ?></span><?php endif; ?>
          </div>
          <div class="price" style="font-family:Syne,sans-serif;font-size:24px;font-weight:800;margin:16px 0;">
            <?= $v['price'] ? formatPrice($v['price'], $v['price_unit']) : 'Fiyat sorunuz' ?>
          </div>
          <div style="display:flex;gap:10px;">
            <?php if ($whatsapp): ?>
            <a class="mini-btn whatsapp-action" href="<?= e(whatsappUrl($whatsapp, $waMsgText)) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            <?php endif; ?>
            <a class="mini-btn primary" href="<?= SITE_URL ?>/arac-ilanlari/<?= e($v['slug']) ?>">Detay <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?= paginationLinks($paginator, SITE_URL . '/arac-ilanlari') ?>
    <?php endif; ?>
  </div>
</section>
