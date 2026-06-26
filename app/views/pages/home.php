<?php
// home.php - $sliders, $featured_projects, $listings (satilik), $recent_news, $settings değişkenleri controller'dan gelir
$phone    = setting('phone', '0 374 000 00 00');
$whatsapp = setting('whatsapp', '');
?>

<!-- ═══ HERO SLIDER ═══════════════════════════════════════════════════ -->
<section class="hero" id="hero">
  <div class="slides" id="slides">
    <?php if (empty($sliders)): ?>
    <article class="slide active">
      <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2200&q=85')"></div>
      <div class="container hero-content">
        <span class="eyebrow">Premium gayrimenkul deneyimi</span>
        <h1>Yaşam Alanlarını Geleceğe Taşıyoruz</h1>
        <p>Çakmaklar İnşaat; proje geliştirme, satış, kiralama ve yatırım danışmanlığını tek bir dijital vitrin içinde sunar.</p>
        <div class="hero-ctas">
          <a class="btn hero-primary" href="<?= SITE_URL ?>/projeler"><i class="fa-solid fa-compass"></i> Keşfet</a>
          <a class="btn ghost" href="<?= SITE_URL ?>/3d-ev-gez"><i class="fa-solid fa-vr-cardboard"></i> 3D Deneyimi Gör</a>
        </div>
      </div>
    </article>
    <?php else: $first = true; foreach ($sliders as $slide): ?>
    <article class="slide<?= $first ? ' active' : '' ?>">
      <?php if ($slide['image']): ?>
      <div class="slide-bg" style="background-image:url('<?= e(uploadUrl($slide['image'])) ?>')"></div>
      <?php else: ?>
      <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=2200&q=85')"></div>
      <?php endif; ?>
      <div class="container hero-content">
        <?php if ($slide['subtitle']): ?><span class="eyebrow"><?= e($slide['subtitle']) ?></span><?php endif; ?>
        <h1><?= e($slide['title']) ?></h1>
        <?php if ($slide['description']): ?><p><?= e($slide['description']) ?></p><?php endif; ?>
        <div class="hero-ctas">
          <?php if ($slide['btn1_text'] && $slide['btn1_url']): ?>
          <a class="btn hero-primary" href="<?= e($slide['btn1_url']) ?>"><?= e($slide['btn1_text']) ?></a>
          <?php endif; ?>
          <?php if ($slide['btn2_text'] && $slide['btn2_url']): ?>
          <a class="btn ghost" href="<?= e($slide['btn2_url']) ?>"><?= e($slide['btn2_text']) ?></a>
          <?php endif; ?>
        </div>
      </div>
    </article>
    <?php $first = false; endforeach; endif; ?>
  </div>
  <div class="dots" id="dots" aria-label="Slider noktaları"></div>
  <div class="slider-controls">
    <button class="slide-arrow" id="prevSlide" aria-label="Önceki slayt">‹</button>
    <button class="slide-arrow" id="nextSlide" aria-label="Sonraki slayt">›</button>
  </div>
</section>

<!-- ═══ BRAND STATEMENT ═══════════════════════════════════════════════ -->
<section class="brand-statement" aria-label="Çakmaklar İnşaat yaklaşımı">
  <div class="container brand-statement-inner">
    <p>Detaylarda saklı kalite, projelerde görünen fark. Kaliteyi ve güveni bir araya getiriyoruz.</p>
    <a class="btn section-cta" href="<?= SITE_URL ?>/projeler">Keşfet <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>

<!-- ═══ HIZLI ARAMA ═══════════════════════════════════════════════════ -->
<section class="quick-search" aria-label="Hızlı arama">
  <div class="container">
    <div class="search-panel">
      <div class="search-tabs">
        <button class="tab-btn active" data-search-tab="Satılık" onclick="location.href='<?= SITE_URL ?>/satilik'">Satılık</button>
        <button class="tab-btn" data-search-tab="Kiralık" onclick="location.href='<?= SITE_URL ?>/kiralik'">Kiralık</button>
        <button class="tab-btn" data-search-tab="Projeler" onclick="location.href='<?= SITE_URL ?>/projeler'">Projeler</button>
      </div>
      <form class="search-grid" id="quickSearch" action="<?= SITE_URL ?>/satilik" method="GET">
        <div class="field">
          <label for="konum">İl / İlçe</label>
          <select id="konum" name="konum">
            <option value="">Tüm Bölgeler</option>
            <option>Bolu / Merkez</option>
            <option>Bolu / Mudurnu</option>
            <option>Bolu / Gerede</option>
            <option>Bolu / Karacasu</option>
          </select>
        </div>
        <div class="field">
          <label for="tip">İlan Tipi</label>
          <select id="tip" name="tip">
            <option value="">Tüm Tipler</option>
            <option value="konut">Konut</option>
            <option value="dukkan">Dükkan / Ofis</option>
            <option value="arsa">Arsa</option>
          </select>
        </div>
        <div class="field">
          <label for="oda">Oda / Kategori</label>
          <select id="oda" name="oda">
            <option value="">Tümü</option>
            <option>1+1</option>
            <option>2+1</option>
            <option>3+1</option>
            <option>4+1</option>
            <option>5+1</option>
            <option>Villa</option>
            <option>Ticari</option>
          </select>
        </div>
        <div class="field">
          <label>Fiyat Aralığı</label>
          <div class="price-inputs">
            <input name="min_fiyat" inputmode="numeric" placeholder="Min TL">
            <input name="max_fiyat" inputmode="numeric" placeholder="Maks TL">
          </div>
        </div>
        <button class="btn dark" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Ara</button>
      </form>
    </div>
  </div>
</section>

<!-- ═══ BİZ KİMİZ ═════════════════════════════════════════════════════ -->
<section class="intro" id="biz-kimiz">
  <div class="container intro-grid">
    <div class="intro-copy">
      <span class="eyebrow">Kurumsal tanıtım</span>
      <h2>İnşaat, Gayrimenkul ve Yatırımı Tek Bir Dijital Deneyimde Buluşturuyoruz</h2>
      <?php
      $aboutPage = Database::queryOne("SELECT content FROM pages WHERE page_key='about'");
      if ($aboutPage && $aboutPage['content']):
      ?>
      <div><?= $aboutPage['content'] ?></div>
      <?php else: ?>
      <p>Çakmaklar İnşaat olarak yılların tecrübesiyle Bolu'da güvenilir yapılar inşa ediyoruz. Müşteri memnuniyetini ön planda tutan anlayışımızla her projede kalite ve güveni bir arada sunuyoruz.</p>
      <?php endif; ?>
      <div class="stats">
        <div class="stat-card"><strong data-count="25" data-suffix="+">0</strong><span>Yıllık Tecrübe</span></div>
        <div class="stat-card"><strong data-count="120" data-suffix="+">0</strong><span>Teslim Edilen Konut</span></div>
        <div class="stat-card"><strong data-count="15" data-suffix="+">0</strong><span>Aktif Portföy</span></div>
      </div>
    </div>
    <div class="media-frame">
      <img src="https://images.unsplash.com/photo-1600573472591-ee6b68d14c68?auto=format&fit=crop&w=1400&q=80" alt="Modern konut projesi dış cephe">
      <div class="portfolio-badge"><i class="fa-solid fa-building-shield"></i><span>Kurumsal Portföy</span></div>
    </div>
  </div>
</section>

<!-- ═══ HİZMETLER ════════════════════════════════════════════════════ -->
<section class="services" id="hizmetler">
  <div class="container">
    <div class="section-head">
      <div><span class="eyebrow">Uzmanlık alanlarımız</span><h2>Tek Markada Uçtan Uca Hizmet</h2></div>
      <p>Yatırım kararından satış ofisi deneyimine kadar, gayrimenkul yolculuğunu birbirine bağlı araçlarla yönetiyoruz.</p>
    </div>
    <div class="services-grid">
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-compass-drafting"></i></div><h3>Proje Geliştirme</h3><p>Konsept, hedef kitle, maliyet ve satış stratejisini tek bir proje vizyonunda buluşturuyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/projeler">Projeleri incele <i class="fa-solid fa-arrow-right"></i></a></article>
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-ruler-combined"></i></div><h3>Mimari Tasarım & Taahhüt</h3><p>Planlama, uygulama ve şantiye koordinasyonunu detay odaklı bir inşaat süreciyle yönetiyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/iletisim">Teklif al <i class="fa-solid fa-arrow-right"></i></a></article>
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-building-shield"></i></div><h3>Kentsel Dönüşüm</h3><p>Riskli yapıdan yeni yaşam alanına uzanan dönüşüm sürecini güvenle yürütüyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/iletisim">Süreç detayları <i class="fa-solid fa-arrow-right"></i></a></article>
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-house-chimney"></i></div><h3>Gayrimenkul Portföyü</h3><p>Satılık ve kiralık konut, ticari alan, arsa ve araç ilanlarını güçlü dijital vitrinle sunuyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/satilik">Portföye git <i class="fa-solid fa-arrow-right"></i></a></article>
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-handshake-angle"></i></div><h3>Satış Sonrası Destek</h3><p>Teslim, tapu, abonelik ve yaşam başlangıcı süreçlerinde müşterilerimizin yanında kalıyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/iletisim">Bize ulaşın <i class="fa-solid fa-arrow-right"></i></a></article>
      <article class="service-card"><div class="service-icon"><i class="fa-solid fa-cube"></i></div><h3>Dijital Satış Deneyimi</h3><p>3D tur, satış ofisi ekranı ve interaktif sunumlarla projeyi ilk görüşte anlaşılır kılıyoruz.</p><a class="service-link" href="<?= SITE_URL ?>/3d-ev-gez">3D sunumu aç <i class="fa-solid fa-arrow-right"></i></a></article>
    </div>
  </div>
</section>

<!-- ═══ PARALLAX ══════════════════════════════════════════════════════ -->
<section class="parallax-band" id="dijital-deneyim">
  <div class="container">
    <div class="parallax-card" id="parallaxCard">
      <span class="eyebrow">Dijital satış deneyimi</span>
      <h2>Projeleri sadece göstermiyoruz, müşteriye yaşatıyoruz.</h2>
      <p>Satış ofisindeki TV ekranı, tablet ve telefon deneyimleri aynı marka diliyle birleşir; müşteri projeyi görsel, plan, 360° tur ve hızlı iletişim akışıyla keşfeder.</p>
      <div style="margin-top:28px;"><a class="btn ghost" href="<?= SITE_URL ?>/3d-ev-gez"><i class="fa-solid fa-cube"></i> 3D Deneyimi Keşfet</a></div>
    </div>
  </div>
</section>

<!-- ═══ ÖNE ÇIKAN PROJELER ════════════════════════════════════════════ -->
<section class="projects" id="projeler">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">Proje portföyü</span>
        <h2>Öne Çıkan Projeler</h2>
      </div>
      <p>Satışta, yakında ve teslim edilen projeler; yüksek görsel etki, durum etiketleri ve net aksiyonlarla sunulur.</p>
    </div>
    <div class="project-grid">
      <?php
      $statusLabels = ['satiasta' => 'Satışta', 'yakinda' => 'Yakında', 'teslim_edildi' => 'Tamamlandı'];
      if (empty($featured_projects)): ?>
      <p style="color:var(--muted)">Henüz öne çıkan proje eklenmemiş.</p>
      <?php else: foreach ($featured_projects as $proj): ?>
      <article class="project-card feature">
        <div class="card-image">
          <?php if ($proj['cover_image']): ?>
          <img src="<?= e(uploadUrl($proj['cover_image'])) ?>" alt="<?= e($proj['title']) ?>">
          <?php else: ?>
          <img src="https://images.unsplash.com/photo-1605146769289-440113cc3d00?auto=format&fit=crop&w=1000&q=80" alt="<?= e($proj['title']) ?>">
          <?php endif; ?>
          <span class="status"><?= e($statusLabels[$proj['status']] ?? $proj['status']) ?></span>
          <div class="project-overlay">
            <h3><?= e($proj['title']) ?></h3>
            <p><?= e($proj['location']) ?></p>
          </div>
        </div>
        <div class="project-body">
          <p><?= e(excerpt($proj['short_desc'] ?? '', 120)) ?></p>
          <a class="mini-btn primary project-action" href="<?= SITE_URL ?>/projeler/<?= e($proj['slug']) ?>">
            Projeyi İncele <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </article>
      <?php endforeach; endif; ?>
    </div>
    <div style="margin-top:28px; display:flex; justify-content:center;">
      <a class="btn dark section-cta" href="<?= SITE_URL ?>/projeler"><i class="fa-solid fa-layer-group"></i> Tüm Projeleri Keşfet</a>
    </div>
  </div>
</section>

<!-- ═══ İLANLAR ══════════════════════════════════════════════════════ -->
<section class="listings" id="ilanlar">
  <div class="container">
    <div class="section-head">
      <div>
        <span class="eyebrow">Portföy</span>
        <h2>Satılık ve Kiralık İlanlar</h2>
      </div>
      <p>Portföy kartları hızlı karşılaştırma, etiketler ve doğrudan WhatsApp iletişim aksiyonlarıyla tasarlanmıştır.</p>
    </div>
    <div class="listings-toolbar">
      <div class="listing-tabs" id="listingTabs">
        <a class="tab-btn active" href="<?= SITE_URL ?>/satilik">Satılık Konut</a>
        <a class="tab-btn" href="<?= SITE_URL ?>/kiralik">Kiralık Konut</a>
        <a class="tab-btn" href="<?= SITE_URL ?>/ticari">Dükkan / Ofis</a>
      </div>
      <a class="mini-btn" href="<?= SITE_URL ?>/satilik"><i class="fa-solid fa-table-cells-large"></i> Tümünü Gör</a>
    </div>
    <div class="listing-grid">
      <?php if (empty($listings)): ?>
      <p style="color:var(--muted)">Henüz ilan eklenmemiş.</p>
      <?php else: foreach ($listings as $l):
        $waMsgText = $l['whatsapp_msg'] ?: SITE_NAME . ' - ' . $l['title'] . ' hakkında bilgi almak istiyorum.';
        $tags = $l['status_tag'] ? explode(',', $l['status_tag']) : [];
        $tagLabels = ['yeni' => 'Yeni', 'firsat' => 'Fırsat', 'krediye_uygun' => 'Krediye Uygun'];
      ?>
      <article class="listing-card">
        <div class="card-image">
          <?php if ($l['cover_image']): ?>
          <img src="<?= e(uploadUrl($l['cover_image'])) ?>" alt="<?= e($l['title']) ?>">
          <?php else: ?>
          <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=900&q=80" alt="<?= e($l['title']) ?>">
          <?php endif; ?>
        </div>
        <div class="listing-body">
          <h3><?= e($l['title']) ?></h3>
          <div class="meta">
            <?php if ($l['location']): ?><span><?= e($l['location']) ?></span><?php endif; ?>
            <?php if ($l['area_m2']): ?><span><?= e($l['area_m2']) ?> m²</span><?php endif; ?>
            <?php if ($l['room_count']): ?><span><?= e($l['room_count']) ?></span><?php endif; ?>
          </div>
          <?php if (!empty($tags)): ?>
          <div class="tags">
            <?php foreach ($tags as $t): ?><span class="tag"><?= e($tagLabels[trim($t)] ?? $t) ?></span><?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="price"><?= $l['price'] ? formatPrice($l['price'], $l['price_unit']) : 'Fiyat sorunuz' ?></div>
          <div class="card-actions">
            <?php if ($whatsapp): ?>
            <a class="mini-btn whatsapp-action" href="<?= e(whatsappUrl($whatsapp, $waMsgText)) ?>" target="_blank">
              <i class="fa-brands fa-whatsapp"></i> WhatsApp
            </a>
            <?php endif; ?>
            <a class="mini-btn detail-action" href="<?= SITE_URL ?>/ilan/<?= e($l['slug']) ?>">
              Detay <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<!-- ═══ FİNANSMAN ════════════════════════════════════════════════════ -->
<section class="finance" id="finans">
  <div class="container">
    <div class="section-head">
      <div><span class="eyebrow">Çakmaklar Finans</span><h2>Yatırımınıza Uygun Finansman Seçenekleri</h2></div>
      <p>Anlaşmalı banka ve tasarruf finansmanı kurumlarıyla farklı ödeme çözümleri sunuyoruz.</p>
    </div>
    <div class="finance-grid" aria-label="Anlaşmalı finans kuruluşları">
      <div class="finance-partner"><span class="partner-logo ziraat">Ziraat Bankası</span></div>
      <div class="finance-partner"><span class="partner-logo halk">Halkbank</span></div>
      <div class="finance-partner"><span class="partner-logo vakif">VakıfBank</span></div>
      <div class="finance-partner"><span class="partner-logo kuveyt">Kuveyt Türk</span></div>
      <div class="finance-partner"><span class="partner-logo eminevim">Eminevim</span></div>
      <div class="finance-partner"><span class="partner-logo fuzul">FuzulEv</span></div>
    </div>
  </div>
</section>

<!-- ═══ 3D TUR ════════════════════════════════════════════════════════ -->
<section class="tour" id="tur">
  <div class="container tour-grid">
    <div class="tour-copy">
      <span class="eyebrow">3D ev gezme</span>
      <h2>Satış Ofisinde 3D Ev Gezme Deneyimi</h2>
      <p>Müşterileriniz satış ofisindeki TV ekranından, tabletlerden veya telefonlarından projeleri oda oda gezebilir; kat planı ve 360° sanal tur içeriklerine ulaşabilir.</p>
      <div class="room-tabs" id="roomTabs">
        <button class="room-btn active" data-room="salon"><i class="fa-solid fa-couch"></i> Salon</button>
        <button class="room-btn" data-room="mutfak"><i class="fa-solid fa-utensils"></i> Mutfak</button>
        <button class="room-btn" data-room="yatak"><i class="fa-solid fa-bed"></i> Yatak Odası</button>
        <button class="room-btn" data-room="banyo"><i class="fa-solid fa-bath"></i> Banyo</button>
      </div>
      <div style="margin-top:28px;">
        <a class="btn ghost" href="<?= SITE_URL ?>/3d-ev-gez"><i class="fa-solid fa-cube"></i> 3D Turu Başlat</a>
      </div>
    </div>
    <div class="tv-device">
      <div class="tour-screen">
        <img id="roomImage" src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1400&q=80" alt="3D sanal tur oda görünümü">
        <div class="tour-ui">
          <div>
            <h3 id="roomTitle">Salon</h3>
            <p id="roomText">Geniş oturma alanı, doğal ışık ve şehir manzarası.</p>
          </div>
          <a class="btn ghost" href="<?= SITE_URL ?>/3d-ev-gez"><i class="fa-solid fa-street-view"></i> 360° Tur Başlat</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ HABERLER ══════════════════════════════════════════════════════ -->
<?php if (!empty($recent_news)): ?>
<section class="news" id="duyurular">
  <div class="container">
    <div class="section-head">
      <div><span class="eyebrow">Haberler</span><h2>Duyurular</h2></div>
      <a class="mini-btn" href="<?= SITE_URL ?>/haberler">Tüm Haberler <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="news-list">
      <?php foreach ($recent_news as $n): ?>
      <article class="news-item">
        <div class="news-image">
          <?php if ($n['cover_image']): ?>
          <img src="<?= e(uploadUrl($n['cover_image'])) ?>" alt="<?= e($n['title']) ?>">
          <?php else: ?>
          <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80" alt="<?= e($n['title']) ?>">
          <?php endif; ?>
        </div>
        <div class="news-copy">
          <div class="news-date"><?= formatDate($n['published_at'] ?? $n['created_at']) ?></div>
          <h3><?= e($n['title']) ?></h3>
          <p><?= e(excerpt($n['summary'] ?? '', 100)) ?></p>
          <a class="news-link" href="<?= SITE_URL ?>/haberler/<?= e($n['slug']) ?>">
            Devamını oku <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══ İLETİŞİM ════════════════════════════════════════════════════ -->
<section class="contact" id="iletisim">
  <div class="container contact-grid">
    <div class="map">
      <?php $mapEmbed = setting('maps_embed'); ?>
      <?php if ($mapEmbed): ?>
        <?= $mapEmbed ?>
      <?php else: ?>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.572543455789!2d31.599927899999997!3d40.72742559999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x409d3f13b35aaa4f%3A0xa1f3aded24cd043f!2zw4dBS01BS0xBUiBHUlVQIMSwTsWeQUFU!5e0!3m2!1str!2str!4v1781880215415!5m2!1str!2str"
        title="Çakmaklar Grup İnşaat konumu"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
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
        <div class="contact-line"><small>Telefon</small><?= e(setting('phone')) ?></div>
        <div class="contact-line"><small>E-posta</small><?= e(setting('email')) ?></div>
        <div class="contact-line"><small>Adres</small><?= e(setting('address')) ?></div>
        <div class="contact-line"><small>Çalışma</small>09:00 – 18:30</div>
      </div>

      <form class="contact-form" id="contactForm" method="POST" action="<?= SITE_URL ?>/iletisim">
        <?= csrfField() ?>
        <div class="field"><label for="c_name">Ad Soyad</label><input id="c_name" name="name" required placeholder="Adınız Soyadınız"></div>
        <div class="field"><label for="c_phone">Telefon</label><input id="c_phone" name="phone" required placeholder="0 5XX XXX XX XX"></div>
        <div class="field">
          <label for="c_subject">İlgilendiğiniz Konu</label>
          <select id="c_subject" name="subject">
            <option>Proje Satışı</option>
            <option>Satılık İlan</option>
            <option>Kiralık İlan</option>
            <option>Araç İlanı</option>
            <option>3D Tur</option>
            <option>Diğer</option>
          </select>
        </div>
        <div class="field"><label for="c_message">Mesaj</label><textarea id="c_message" name="message" placeholder="Kısa mesajınız"></textarea></div>
        <button class="btn dark" type="submit"><i class="fa-solid fa-paper-plane"></i> Formu Gönder</button>
      </form>
    </div>
  </div>
</section>

<!-- Toast -->
<div id="toast" aria-live="polite"></div>
