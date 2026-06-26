<?php
$phone    = setting('phone', '');
$whatsapp = setting('whatsapp', '');
$email    = setting('email', '');
$address  = setting('address', '');
$mapEmbed = setting('maps_embed', '');
?>
<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Bize ulaşın</span>
    <h1>İletişim</h1>
    <p>Sorularınız ve talepleriniz için buradayız.</p>
  </div>
</section>

<section class="contact" style="padding:80px 0;">
  <div class="container contact-grid">
    <div class="map">
      <?php if ($mapEmbed): ?>
        <?= $mapEmbed ?>
      <?php else: ?>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.572543455789!2d31.599927899999997!3d40.72742559999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x409d3f13b35aaa4f%3A0xa1f3aded24cd043f!2zw4dBS01BS0xBUiBHUlVQIMSwTsWeQUFU!5e0!3m2!1str!2str!4v1781880215415!5m2!1str!2str"
        title="Çakmaklar İnşaat konumu" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
      <?php endif; ?>
      <div class="map-overlay">
        <h3>Bolu Merkez Satış Ofisi</h3>
        <p style="color:rgba(255,255,255,.78)">Satış ofisimize kolayca ulaşın.</p>
        <a class="map-link" href="https://maps.app.goo.gl/RQS1WNkmpcZCuh9v8" target="_blank" rel="noopener noreferrer">
          <i class="fa-solid fa-location-dot"></i> Google Maps'te aç
        </a>
      </div>
    </div>

    <div class="contact-card">
      <span class="eyebrow">İletişim</span>
      <h2>Size Ulaşalım</h2>
      <div class="contact-lines">
        <?php if ($phone): ?>
        <div class="contact-line">
          <small>Telefon</small>
          <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a>
        </div>
        <?php endif; ?>
        <?php if ($email): ?>
        <div class="contact-line">
          <small>E-posta</small>
          <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
        </div>
        <?php endif; ?>
        <?php if ($address): ?>
        <div class="contact-line">
          <small>Adres</small>
          <a href="https://www.google.com/maps/place/%C3%87AKMAKLAR+GRUP+%C4%B0N%C5%9EAAT/@40.7274256,31.5999279,17z" target="_blank" rel="noopener"><?= e($address) ?></a>
        </div>
        <?php endif; ?>
        <div class="contact-line"><small>Çalışma Saatlerimiz</small>Hafta içi 09:00 – 18:30</div>
      </div>

      <form class="contact-form" method="POST" action="<?= SITE_URL ?>/iletisim">
        <?= csrfField() ?>
        <div class="field"><label>Ad Soyad</label><input name="name" required placeholder="Adınız Soyadınız"></div>
        <div class="field"><label>Telefon</label><input name="phone" required placeholder="0 5XX XXX XX XX"></div>
        <div class="field"><label>E-posta</label><input name="email" type="email" placeholder="ornek@mail.com"></div>
        <div class="field">
          <label>Konu</label>
          <select name="subject">
            <option>Proje Satışı</option>
            <option>Satılık İlan</option>
            <option>Kiralık İlan</option>
            <option>Araç İlanı</option>
            <option>3D Tur</option>
            <option>Diğer</option>
          </select>
        </div>
        <div class="field"><label>Mesaj</label><textarea name="message" placeholder="Mesajınız" style="min-height:100px;"></textarea></div>
        <button class="btn dark" type="submit"><i class="fa-solid fa-paper-plane"></i> Formu Gönder</button>
      </form>
    </div>
  </div>
</section>
