<?php
$phone     = setting('phone', '');
$whatsapp  = setting('whatsapp', '');
$tagLabels = ['yeni' => 'Yeni', 'firsat' => 'Fırsat', 'krediye_uygun' => 'Krediye Uygun'];
$tags      = $listing['status_tag'] ? explode(',', $listing['status_tag']) : [];
$waMsgText = $listing['whatsapp_msg'] ?: SITE_NAME . ' - ' . $listing['title'] . ' hakkında bilgi almak istiyorum.';
$allImages = array_filter(array_merge(
    $listing['cover_image'] ? [['image' => $listing['cover_image']]] : [],
    $images
), fn($i) => !empty($i['image']));
$allImages = array_values($allImages);
?>

<!-- HERO / GALERİ -->
<section style="padding:80px 0 34px;background:var(--soft);">
  <div class="container">
    <span class="eyebrow">
      <?php if ($listing['type'] === 'satilik'): ?>Satılık konut ilanı
      <?php elseif ($listing['type'] === 'kiralik'): ?>Kiralık konut ilanı
      <?php else: ?>Ticari ilan<?php endif; ?>
    </span>

    <div style="display:grid;grid-template-columns:1fr auto;gap:24px;align-items:end;margin-top:12px;flex-wrap:wrap;">
      <div>
        <h1 style="font-size:clamp(32px,5vw,60px);color:var(--navy);"><?= e($listing['title']) ?></h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;">
          <?php if ($listing['location']): ?>
          <span style="background:#EAF3FF;color:#18589C;border:1px solid #CFE1F7;border-radius:999px;padding:6px 12px;font-size:13px;font-weight:600;">
            <i class="fa-solid fa-location-dot"></i> <?= e($listing['location']) ?>
          </span>
          <?php endif; ?>
          <?php foreach ($tags as $t): if(!trim($t)) continue; ?>
          <span class="tag"><?= e($tagLabels[trim($t)] ?? trim($t)) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="font-family:Syne,sans-serif;color:var(--navy);font-size:clamp(26px,4vw,48px);font-weight:700;white-space:nowrap;">
        <?= $listing['price'] ? formatPrice($listing['price'], $listing['price_unit']) : 'Fiyat sorunuz' ?>
      </div>
    </div>

    <!-- GALERİ -->
    <div style="display:grid;grid-template-columns:1.35fr .65fr;gap:14px;margin:30px 0;">
      <div style="height:520px;border-radius:var(--radius);overflow:hidden;">
        <?php if (!empty($allImages[0])): ?>
        <img src="<?= e(uploadUrl($allImages[0]['image'])) ?>" alt="<?= e($listing['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
        <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1500&q=82" alt="<?= e($listing['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        <?php endif; ?>
      </div>
      <div style="display:grid;gap:14px;">
        <?php foreach (array_slice($allImages, 1, 2) as $img): ?>
        <div style="height:253px;border-radius:var(--radius);overflow:hidden;">
          <img src="<?= e(uploadUrl($img['image'])) ?>" alt="<?= e($listing['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- LAYOUT: İçerik + Sidebar -->
<div class="container" style="display:grid;grid-template-columns:1fr 360px;gap:34px;padding:60px 0 80px;">
  <article>
    <!-- ÖZELLİKLER -->
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:28px;box-shadow:0 16px 50px rgba(10,31,68,.06);" id="ozellikler">
      <span class="eyebrow">İlan özellikleri</span>
      <h2 style="margin-top:10px;margin-bottom:24px;">Detaylı Bilgiler</h2>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">
        <?php $specs = [
          ['label' => 'm²', 'value' => $listing['area_m2']],
          ['label' => 'Oda', 'value' => $listing['room_count']],
          ['label' => 'Kat', 'value' => $listing['floor']],
          ['label' => 'Banyo', 'value' => $listing['bathroom']],
          ['label' => 'Isıtma', 'value' => $listing['heating']],
          ['label' => 'Bina Yaşı', 'value' => $listing['building_age']],
        ];
        foreach ($specs as $spec): if (!$spec['value'] && $spec['value'] !== 0) continue; ?>
        <div style="background:var(--soft);border:1px solid var(--line);border-radius:var(--radius);padding:16px;">
          <small style="display:block;color:var(--muted);font-weight:600;margin-bottom:6px;"><?= e($spec['label']) ?></small>
          <strong style="color:var(--navy);font-size:20px;"><?= e($spec['value']) ?></strong>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($tags)): ?>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
        <?php foreach ($tags as $t): if(!trim($t)) continue; ?>
        <span class="tag"><?= e($tagLabels[trim($t)] ?? trim($t)) ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php if ($listing['description']): ?>
      <div style="color:var(--muted);line-height:1.75;"><?= $listing['description'] ?></div>
      <?php endif; ?>
    </div>

    <!-- 3D TUR -->
    <?php if ($listing['tour_url'] || $listing['tour_embed']): ?>
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#0A1F44;padding:28px;margin-top:18px;">
      <span class="eyebrow">Sanal tur</span>
      <h3 style="color:#fff;margin:10px 0 18px;">360° Sanal Tur</h3>
      <?php if ($listing['tour_embed']): ?>
      <div style="border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;"><?= $listing['tour_embed'] ?></div>
      <?php elseif ($listing['tour_url']): ?>
      <a class="btn" href="<?= e($listing['tour_url']) ?>" target="_blank" style="min-height:50px;padding:0 24px;">
        <i class="fa-solid fa-street-view"></i> Sanal Turu Başlat
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- KONUM -->
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:28px;margin-top:18px;" id="konum">
      <span class="eyebrow">Konum</span>
      <h3 style="margin:10px 0 16px;"><?= e($listing['location'] ?? 'Bolu') ?></h3>
      <?php $mapEmbed = setting('maps_embed'); ?>
      <?php if ($mapEmbed): ?>
      <div style="border-radius:var(--radius);overflow:hidden;height:280px;"><?= $mapEmbed ?></div>
      <?php else: ?>
      <div style="height:220px;border-radius:var(--radius);background:linear-gradient(135deg,rgba(10,31,68,.12),rgba(24,198,195,.12)),#EAF1F3;display:flex;align-items:center;justify-content:center;">
        <span style="background:var(--navy);color:#fff;border-radius:999px;padding:12px 18px;font-weight:600;">
          <?= e($listing['location'] ?? 'Bolu Merkez') ?>
        </span>
      </div>
      <?php endif; ?>
    </div>
  </article>

  <!-- SİDEBAR -->
  <aside style="position:sticky;top:100px;display:grid;gap:14px;align-self:start;">
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:24px;box-shadow:0 16px 50px rgba(10,31,68,.06);">
      <span class="eyebrow">İletişim</span>
      <h3 style="margin:10px 0 6px;">Satış Danışmanı</h3>
      <?php if ($phone): ?>
      <p style="font-weight:700;margin-bottom:12px;"><?= e($phone) ?></p>
      <a class="btn" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" style="width:100%;margin-bottom:10px;">
        <i class="fa-solid fa-phone"></i> Hemen Ara
      </a>
      <?php endif; ?>
      <?php if ($whatsapp): ?>
      <a class="btn" href="<?= e(whatsappUrl($whatsapp, $waMsgText)) ?>" target="_blank" style="width:100%;background:#138C65;border-color:#138C65;">
        <i class="fa-brands fa-whatsapp"></i> WhatsApp
      </a>
      <?php endif; ?>
    </div>

    <!-- HIZLI BAŞVURU FORMU -->
    <form style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:24px;display:grid;gap:10px;" id="quickApplyForm">
      <?= csrfField() ?>
      <input type="hidden" name="ref_type" value="listing">
      <input type="hidden" name="ref_id" value="<?= (int) $listing['id'] ?>">
      <input type="hidden" name="ref_title" value="<?= e($listing['title']) ?>">
      <span class="eyebrow">Hızlı başvuru</span>
      <div class="field"><label>Ad Soyad</label><input name="name" required placeholder="Adınız Soyadınız"></div>
      <div class="field"><label>Telefon</label><input name="phone" required placeholder="0 5XX XXX XX XX"></div>
      <div class="field"><label>Mesaj</label><textarea name="message" placeholder="Kısa mesajınız" rows="4" style="background:transparent;border:0;outline:0;width:100%;resize:vertical;"></textarea></div>
      <button class="btn dark" type="submit"><i class="fa-solid fa-paper-plane"></i> Talep Gönder</button>
      <div id="applyMsg" style="display:none;padding:10px;border-radius:8px;font-weight:600;text-align:center;"></div>
    </form>
  </aside>
</div>

<!-- BENZER İLANLAR -->
<?php if (!empty($similar)): ?>
<section style="padding:70px 0;background:var(--soft);" id="benzer">
  <div class="container">
    <span class="eyebrow">Benzer ilanlar</span>
    <h2 style="margin-top:10px;margin-bottom:28px;">Portföyden Seçilenler</h2>
    <div class="listing-grid">
      <?php foreach ($similar as $s): ?>
      <article class="listing-card">
        <div class="card-image">
          <img src="<?= $s['cover_image'] ? e(uploadUrl($s['cover_image'])) : 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?auto=format&fit=crop&w=800&q=82' ?>" alt="<?= e($s['title']) ?>">
        </div>
        <div class="listing-body">
          <h3><?= e($s['title']) ?></h3>
          <div class="price"><?= $s['price'] ? formatPrice($s['price'], $s['price_unit']) : 'Fiyat sorunuz' ?></div>
          <a class="mini-btn primary" href="<?= SITE_URL ?>/ilan/<?= e($s['slug']) ?>">İlanı İncele <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
document.getElementById('quickApplyForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Gönderiliyor...';
  const msg = document.getElementById('applyMsg');
  try {
    const resp = await fetch('<?= SITE_URL ?>/ajax/basvuru', {
      method: 'POST',
      headers: { 'X-CSRF-Token': '<?= e(csrfToken()) ?>' },
      body: new FormData(this)
    });
    const data = await resp.json();
    msg.style.display = 'block';
    msg.style.background = data.success ? '#E9FAF2' : '#FDECEA';
    msg.style.color = data.success ? '#087152' : '#C0392B';
    msg.textContent = data.message;
    if (data.success) this.reset();
  } catch {
    msg.style.display = 'block';
    msg.textContent = 'Bir hata oluştu.';
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Talep Gönder';
});
</script>
