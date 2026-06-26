<?php
$phone     = setting('phone', '');
$whatsapp  = setting('whatsapp', '');
$waMsgText = SITE_NAME . ' - ' . $vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model'] . ' ilanı hakkında bilgi almak istiyorum.';
$allImages = array_filter(array_merge(
    $vehicle['cover_image'] ? [['image' => $vehicle['cover_image']]] : [],
    $images
), fn($i) => !empty($i['image']));
$allImages = array_values($allImages);
?>

<section style="padding:110px 0 60px;background:linear-gradient(100deg,rgba(6,20,46,.88),rgba(10,31,68,.45)),url('<?= $vehicle['cover_image'] ? e(uploadUrl($vehicle['cover_image'])) : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=2200&q=85' ?>') center/cover;">
  <div class="container">
    <span class="eyebrow">Araç ilanı</span>
    <h1 style="color:#fff;margin:14px 0;"><?= e($vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model']) ?></h1>
    <div style="font-family:Syne,sans-serif;color:#fff;font-size:clamp(26px,4vw,48px);font-weight:700;">
      <?= $vehicle['price'] ? formatPrice($vehicle['price'], $vehicle['price_unit']) : 'Fiyat sorunuz' ?>
    </div>
  </div>
</section>

<div class="container" style="display:grid;grid-template-columns:1fr 360px;gap:34px;padding:60px 0 80px;">
  <article>
    <!-- Galeri -->
    <?php if (!empty($allImages)): ?>
    <div style="display:grid;grid-template-columns:1.35fr .65fr;gap:14px;margin-bottom:28px;">
      <div style="height:420px;border-radius:var(--radius);overflow:hidden;">
        <img src="<?= e(uploadUrl($allImages[0]['image'])) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
      </div>
      <div style="display:grid;gap:14px;">
        <?php foreach (array_slice($allImages, 1, 2) as $img): ?>
        <div style="height:203px;border-radius:var(--radius);overflow:hidden;">
          <img src="<?= e(uploadUrl($img['image'])) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Özellikler -->
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:28px;">
      <span class="eyebrow">Araç Özellikleri</span>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:20px 0;">
        <?php $specs = [
          ['label'=>'Yıl','value'=>$vehicle['year']],
          ['label'=>'KM','value'=>$vehicle['km'] ? number_format($vehicle['km'],0,',','.') . ' KM' : null],
          ['label'=>'Yakıt','value'=>$vehicle['fuel']],
          ['label'=>'Vites','value'=>$vehicle['transmission']],
          ['label'=>'Renk','value'=>$vehicle['color']],
        ];
        foreach ($specs as $spec): if (!$spec['value']) continue; ?>
        <div style="background:var(--soft);border:1px solid var(--line);border-radius:var(--radius);padding:16px;">
          <small style="display:block;color:var(--muted);font-weight:600;margin-bottom:6px;"><?= e($spec['label']) ?></small>
          <strong style="color:var(--navy);font-size:18px;"><?= e($spec['value']) ?></strong>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if ($vehicle['description']): ?>
      <div style="color:var(--muted);line-height:1.75;margin-top:16px;"><?= $vehicle['description'] ?></div>
      <?php endif; ?>
    </div>
  </article>

  <aside style="position:sticky;top:100px;display:grid;gap:14px;align-self:start;">
    <div style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:24px;">
      <span class="eyebrow">İletişim</span>
      <div style="font-family:Syne,sans-serif;font-size:28px;font-weight:700;color:var(--navy);margin:12px 0;">
        <?= $vehicle['price'] ? formatPrice($vehicle['price'], $vehicle['price_unit']) : 'Fiyat sorunuz' ?>
      </div>
      <?php if ($phone): ?>
      <a class="btn" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" style="width:100%;margin-bottom:10px;display:flex;">
        <i class="fa-solid fa-phone"></i> Hemen Ara
      </a>
      <?php endif; ?>
      <?php if ($whatsapp): ?>
      <a class="btn" href="<?= e(whatsappUrl($whatsapp, $waMsgText)) ?>" target="_blank" style="width:100%;background:#138C65;border-color:#138C65;display:flex;">
        <i class="fa-brands fa-whatsapp"></i> WhatsApp
      </a>
      <?php endif; ?>
    </div>

    <form style="border:1px solid var(--line);border-radius:var(--radius);background:#fff;padding:24px;display:grid;gap:10px;" id="quickApplyForm">
      <?= csrfField() ?>
      <input type="hidden" name="ref_type" value="vehicle">
      <input type="hidden" name="ref_id" value="<?= (int) $vehicle['id'] ?>">
      <input type="hidden" name="ref_title" value="<?= e($vehicle['year'] . ' ' . $vehicle['brand'] . ' ' . $vehicle['model']) ?>">
      <span class="eyebrow">Hızlı başvuru</span>
      <div class="field"><label>Ad Soyad</label><input name="name" required placeholder="Adınız"></div>
      <div class="field"><label>Telefon</label><input name="phone" required placeholder="0 5XX XXX XX XX"></div>
      <button class="btn dark" type="submit"><i class="fa-solid fa-paper-plane"></i> Talep Gönder</button>
      <div id="applyMsg" style="display:none;padding:10px;border-radius:8px;font-weight:600;text-align:center;"></div>
    </form>
  </aside>
</div>

<script>
document.getElementById('quickApplyForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  const msg = document.getElementById('applyMsg');
  try {
    const resp = await fetch('<?= SITE_URL ?>/ajax/basvuru', {
      method: 'POST', headers: {'X-CSRF-Token': '<?= e(csrfToken()) ?>'},
      body: new FormData(this)
    });
    const data = await resp.json();
    msg.style.display = 'block';
    msg.style.background = data.success ? '#E9FAF2' : '#FDECEA';
    msg.style.color = data.success ? '#087152' : '#C0392B';
    msg.textContent = data.message;
    if (data.success) this.reset();
  } catch { msg.style.display='block'; msg.textContent='Hata oluştu.'; }
  btn.disabled = false;
});
</script>
