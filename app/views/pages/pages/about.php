<?php
/**
 * Biz Kimiz sayfası
 * $page → pages tablosundan about page_key kaydı
 */
$heroTitle = e($page['title'] ?? 'Biz Kimiz');
$heroSub   = e($page['subtitle'] ?? 'Güven, Kalite, Deneyim');
?>

<!-- HERO -->
<section class="page-hero" style="<?= !empty($page['cover_image']) ? 'background-image:linear-gradient(135deg,rgba(10,31,68,.88) 0%,rgba(13,50,114,.75) 55%,rgba(24,198,195,.6) 100%),url('.e(uploadUrl($page['cover_image'])).');background-size:cover;background-position:center;' : '' ?>">
  <div class="container" style="position:relative;z-index:1;">
    <nav style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.65);margin-bottom:24px;">
      <a href="<?= SITE_URL ?>/" style="color:rgba(255,255,255,.65);text-decoration:none;">Ana Sayfa</a>
      <span>›</span>
      <span style="color:#fff;">Biz Kimiz</span>
    </nav>
    <h1><?= $heroTitle ?></h1>
    <p style="color:rgba(255,255,255,.8);max-width:560px;font-size:17px;"><?= $heroSub ?></p>
  </div>
</section>

<!-- HAKKIMIZDA İÇERİK -->
<section style="padding:80px 0;background:#fff;">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;">
      <div>
        <span class="eyebrow">Hakkımızda</span>
        <h2 style="margin:14px 0 24px;"><?= $heroTitle ?></h2>
        <?php if (!empty($page['content'])): ?>
        <div style="color:#4a5568;line-height:1.9;font-size:16px;">
          <?= $page['content'] ?>
        </div>
        <?php else: ?>
        <p style="color:#4a5568;line-height:1.9;font-size:16px;">
          Çakmaklar İnşaat olarak, yılların deneyimi ve güvenilirliğiyle
          konut ve ticari gayrimenkul sektöründe hizmet vermekteyiz.
          Müşteri memnuniyetini her zaman ön planda tutan anlayışımızla,
          kaliteli projeler hayata geçirmeyi sürdürmekteyiz.
        </p>
        <p style="color:#4a5568;line-height:1.9;font-size:16px;margin-top:16px;">
          Deneyimli ekibimiz ve güçlü altyapımızla; konut, ticari alan ve arazi
          projelerinde çözüm ortağınız olmaktan gurur duyuyoruz. Her projemizde
          şeffaflık, zamanında teslimat ve müşteri odaklı hizmet anlayışını benimsiyoruz.
        </p>
        <?php endif; ?>
      </div>
      <?php if (!empty($page['cover_image'])): ?>
      <div>
        <img src="<?= e(uploadUrl($page['cover_image'])) ?>"
             alt="<?= $heroTitle ?>"
             style="width:100%;border-radius:16px;box-shadow:0 20px 60px rgba(10,31,68,.15);">
      </div>
      <?php else: ?>
      <div style="background:var(--navy);border-radius:16px;min-height:360px;display:grid;place-items:center;">
        <i class="fa-solid fa-building" style="font-size:80px;color:rgba(24,198,195,.3);"></i>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- DEĞERLER / NEDEN BİZ -->
<section class="services" style="padding:80px 0;">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">Neden Biz?</span>
        <h2>Farkımız</h2>
      </div>
    </div>
    <div class="services-grid" style="margin-top:48px;">
      <?php
      $values = [
        ['fa-medal',              'Kalite Güvencesi',     'Her projemizde en yüksek malzeme ve işçilik kalitesini sunuyoruz.'],
        ['fa-handshake',          'Güven & Şeffaflık',    'Müşterilerimizle açık ve dürüst bir iletişim anlayışı benimsiyoruz.'],
        ['fa-clock-rotate-left',  'Zamanında Teslim',     'Proje takvimlerine uyum ve söz verilen tarihte teslim en önemli prensiplerimizdendir.'],
        ['fa-map-location-dot',   'Stratejik Konumlar',   'Değer kazanması yüksek lokasyonlarda yatırımlık projeler geliştiriyoruz.'],
        ['fa-headset',            '7/24 Destek',          'Satış sonrası destek hattımızla her zaman yanınızdayız.'],
        ['fa-star',               'Müşteri Memnuniyeti',  'Yüzlerce mutlu müşteri ve referans projemizle sektörde öne çıkıyoruz.'],
      ];
      foreach ($values as $v): ?>
      <div class="service-card">
        <div class="service-icon">
          <i class="fa-solid <?= $v[0] ?>"></i>
        </div>
        <h3><?= $v[1] ?></h3>
        <p><?= $v[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- İSTATİSTİKLER -->
<section style="background:var(--navy);padding:70px 0;color:#fff;">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:32px;text-align:center;">
      <?php
      $stats = [
        ['20+',  'Yıl Deneyim'],
        ['450+', 'Teslim Edilen Daire'],
        ['12',   'Aktif Proje'],
        ['99%',  'Müşteri Memnuniyeti'],
      ];
      foreach ($stats as $s): ?>
      <div>
        <div style="font-family:var(--font-head);font-size:clamp(36px,5vw,56px);font-weight:800;color:var(--teal);"><?= $s[0] ?></div>
        <div style="font-size:14px;color:rgba(255,255,255,.7);margin-top:8px;text-transform:uppercase;letter-spacing:.06em;"><?= $s[1] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="footer-cta" style="padding:80px 0;">
  <div class="container" style="text-align:center;">
    <h2 style="color:#fff;margin-bottom:16px;">Hayalinizdeki Eve Birlikte Ulaşalım</h2>
    <p style="color:rgba(255,255,255,.75);margin-bottom:36px;max-width:520px;margin-left:auto;margin-right:auto;">Projelerimiz ve gayrimenkul çözümlerimiz hakkında bilgi almak için bize ulaşın.</p>
    <a class="btn" href="<?= SITE_URL ?>/iletisim" style="background:#fff;color:var(--navy);">
      <i class="fa-solid fa-phone"></i> İletişime Geç
    </a>
  </div>
</section>
