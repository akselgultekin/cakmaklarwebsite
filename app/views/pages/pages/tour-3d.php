<?php
/**
 * 3D Ev Gez sayfası — Proje listesi + interaktif oda önizlemesi
 */
?>

<!-- ══ HERO ══════════════════════════════════════════════════════════════ -->
<section class="page-hero tour" style="background:linear-gradient(135deg,#0A1F44 0%,#0d3272 55%,#18C6C3 100%);overflow:hidden;">
  <div class="container tour-grid" style="align-items:center;">
    <div class="tour-copy">
      <span class="eyebrow">3D Sanal Tur</span>
      <h1 style="color:#fff;margin:16px 0 20px;">Satış Ofisinde<br>3D Ev Gezme</h1>
      <p style="color:rgba(255,255,255,.78);font-size:17px;max-width:460px;line-height:1.75;">
        Daireleri TV ekranından, tabletten veya telefondan oda oda gezin —
        kat planları ve 360° sanal tur tek tıkla elinizin altında.
      </p>
      <div class="room-tabs" style="margin-top:32px;" id="roomTabs">
        <button class="room-btn active room-tab" data-room="salon"><i class="fa-solid fa-couch"></i> Salon</button>
        <button class="room-btn room-tab" data-room="mutfak"><i class="fa-solid fa-utensils"></i> Mutfak</button>
        <button class="room-btn room-tab" data-room="yatak"><i class="fa-solid fa-bed"></i> Yatak Odası</button>
        <button class="room-btn room-tab" data-room="banyo"><i class="fa-solid fa-bath"></i> Banyo</button>
      </div>
    </div>
    <div class="tv-device">
      <div class="tour-screen">
        <img id="roomImage"
             src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1400&q=80"
             alt="Panorama Salon">
        <div class="tour-ui">
          <div>
            <h3 id="roomTitle">Panorama Salon</h3>
            <p id="roomText">Geniş oturma alanı, doğal ışık ve şehir manzarası.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ NEREDE ÇALIŞIR ══════════════════════════════════════════════════════ -->
<section style="padding:80px 0;background:#fff;">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">Çoklu Cihaz Desteği</span>
        <h2>Her Ekranda Mükemmel Deneyim</h2>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:32px;margin-top:56px;">
      <?php
      $devices = [
        ['fa-tv',     'Satış Ofisi TV\'si',  'Büyük ekranda tam ekran 3D gezme deneyimi. Müşteriler projeyi yerinde hisseder.'],
        ['fa-tablet', 'Tablet',               'Satış temsilcisi tableti müşteriye vererek interaktif gezinti sağlar.'],
        ['fa-mobile', 'Mobil',                'QR kod ile müşteri kendi telefonundan daireleri inceler.'],
      ];
      foreach ($devices as $d): ?>
      <div class="service-card" style="text-align:center;">
        <div class="service-icon" style="margin:0 auto 20px;">
          <i class="fa-solid <?= $d[0] ?>"></i>
        </div>
        <h3><?= $d[1] ?></h3>
        <p><?= $d[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ PROJE LİSTESİ ══════════════════════════════════════════════════════ -->
<section style="padding:80px 0;background:var(--bg-soft,#F4F6F9);">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">Projeler</span>
        <h2>3D Tur Projeleri</h2>
      </div>
    </div>

    <?php if (empty($projects)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--muted);">
      <i class="fa-solid fa-cube" style="font-size:48px;color:var(--line);display:block;margin-bottom:16px;"></i>
      Henüz 3D tur içeriği eklenmemiş.
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;margin-top:48px;">
      <?php foreach ($projects as $p): ?>
      <article class="project-card">
        <div class="project-image">
          <?php if ($p['cover_image']): ?>
          <img src="<?= e(uploadUrl($p['cover_image'])) ?>" alt="<?= e($p['title']) ?>">
          <?php else: ?>
          <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=80" alt="<?= e($p['title']) ?>">
          <?php endif; ?>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;opacity:0;transition:.3s;" class="project-play-icon">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(24,198,195,.9);display:flex;align-items:center;justify-content:center;font-size:28px;color:#fff;">
              <i class="fa-solid fa-cube"></i>
            </div>
          </div>
        </div>
        <div class="project-info">
          <h3><?= e($p['title']) ?></h3>
          <?php if ($p['location']): ?>
          <p class="project-loc"><i class="fa-solid fa-location-dot"></i> <?= e($p['location']) ?></p>
          <?php endif; ?>
          <?php if ($p['tour_desc']): ?>
          <p style="font-size:14px;color:var(--muted);margin:8px 0 16px;"><?= e(excerpt($p['tour_desc'], 90)) ?></p>
          <?php endif; ?>
          <a class="btn" href="<?= SITE_URL ?>/3d-ev-gez/<?= e($p['slug']) ?>">
            <i class="fa-solid fa-street-view"></i> Turu Başlat
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

