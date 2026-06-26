<?php
$key   = $legal_key   ?? 'privacy';
$title = $legal_title ?? 'Yasal';
$siteName = SITE_NAME;
$siteUrl  = SITE_URL;

$content = [
  'privacy' => [
    'sections' => [
      ['Gizlilik Politikası Hakkında', "<p>{$siteName} olarak web sitemizi kullanan ziyaretçilerimizin gizliliğini korumak temel önceliğimizdir. Bu Gizlilik Politikası, <strong>{$siteUrl}</strong> adresinde yer alan web sitemiz aracılığıyla toplanan kişisel verilerin nasıl işlendiğini açıklamaktadır.</p>"],
      ['Toplanan Bilgiler', "<p>Sitemizi ziyaret ettiğinizde veya iletişim formunu doldurduğunuzda aşağıdaki bilgiler toplanabilir:</p><ul><li>Ad, soyad ve iletişim bilgileri (telefon, e-posta)</li><li>Mesaj içerikleri ve talep detayları</li><li>IP adresi ve tarayıcı bilgileri (oturum kayıtları)</li></ul>"],
      ['Bilgilerin Kullanım Amacı', "<p>Toplanan kişisel veriler yalnızca şu amaçlarla kullanılmaktadır:</p><ul><li>Talep ve sorularınızı yanıtlamak</li><li>Hizmet kalitesini geliştirmek</li><li>Yasal yükümlülükleri yerine getirmek</li></ul>"],
      ['Üçüncü Taraflarla Paylaşım', "<p>Kişisel verileriniz; yasal zorunluluklar dışında üçüncü taraflarla paylaşılmaz, satılmaz veya kiralanmaz.</p>"],
      ['Güvenlik', "<p>Verilerinizin güvenliği için teknik ve idari önlemler alınmaktadır. Ancak internet üzerinden yapılan iletişimin tam güvenliği garanti edilemez.</p>"],
      ['Haklarınız', "<p>6698 sayılı KVKK kapsamında verilerinize erişim, düzeltme ve silme hakkına sahipsiniz. Talepleriniz için <a href='mailto:info@cakmaklargrup.com'>info@cakmaklargrup.com</a> adresine ulaşabilirsiniz.</p>"],
      ['İletişim', "<p>Bu politikaya ilişkin sorularınız için: <a href='mailto:info@cakmaklargrup.com'>info@cakmaklargrup.com</a></p>"],
    ],
  ],
  'kvkk' => [
    'sections' => [
      ['Veri Sorumlusu', "<p><strong>{$siteName}</strong> olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu (\"KVKK\") kapsamında veri sorumlusu sıfatıyla hareket etmekteyiz.</p>"],
      ['İşlenen Kişisel Veriler', "<ul><li><strong>Kimlik Verileri:</strong> Ad, soyad</li><li><strong>İletişim Verileri:</strong> Telefon numarası, e-posta adresi</li><li><strong>İşlem Verileri:</strong> Form içerikleri, talep bilgileri</li><li><strong>Teknik Veriler:</strong> IP adresi, tarayıcı bilgisi, çerez verileri</li></ul>"],
      ['İşleme Amaçları ve Hukuki Dayanaklar', "<p>Kişisel verileriniz aşağıdaki amaçlarla ve hukuki dayanaklarla işlenmektedir:</p><ul><li>İletişim taleplerinin yanıtlanması (Sözleşmenin ifası)</li><li>Yasal yükümlülüklerin yerine getirilmesi (Kanuni zorunluluk)</li><li>İş süreçlerinin yürütülmesi (Meşru menfaat)</li></ul>"],
      ['Verilerin Aktarılması', "<p>Kişisel verileriniz; hizmet alınan iş ortakları, kanunen yetkili kurum ve kuruluşlar ile paylaşılabilir. Yurt dışına veri aktarımı yapılmamaktadır.</p>"],
      ['Saklama Süresi', "<p>Kişisel verileriniz, işlenme amacının gerektirdiği süre boyunca veya yasal yükümlülükler çerçevesinde saklanmaktadır.</p>"],
      ['KVKK Kapsamındaki Haklarınız', "<p>KVKK Madde 11 uyarınca aşağıdaki haklara sahipsiniz:</p><ul><li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li><li>İşlenmişse buna ilişkin bilgi talep etme</li><li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li><li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li><li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li><li>Kanun'un 7. maddesi çerçevesinde silinmesini isteme</li><li>İşlemenin otomatik sistemler vasıtasıyla yapılması halinde aleyhine bir sonucun ortaya çıkmasına itiraz etme</li></ul>"],
      ['Başvuru Yöntemi', "<p>Haklarınızı kullanmak için kimliğinizi tevsik edici belgelerle birlikte <a href='mailto:info@cakmaklargrup.com'>info@cakmaklargrup.com</a> adresine yazılı olarak başvurabilirsiniz. Başvurular 30 gün içinde yanıtlanır.</p>"],
    ],
  ],
  'cookie' => [
    'sections' => [
      ['Çerez Nedir?', "<p>Çerezler, web sitelerinin tarayıcınıza yerleştirdiği küçük metin dosyalarıdır. Sitenin doğru çalışması, kullanıcı deneyiminin iyileştirilmesi ve istatistiksel analizler için kullanılırlar.</p>"],
      ['Kullandığımız Çerez Türleri', "<ul><li><strong>Zorunlu Çerezler:</strong> Sitenin temel işlevleri için gereklidir; oturum yönetimi, güvenlik. Bu çerezler devre dışı bırakılamaz.</li><li><strong>Performans Çerezleri:</strong> Siteyi nasıl kullandığınıza dair anonim istatistikler toplar (sayfa görüntüleme, hata kayıtları vb.).</li><li><strong>İşlevsellik Çerezleri:</strong> Tercihlerinizi (dil, bölge) hatırlamak için kullanılır.</li></ul>"],
      ['Çerez Yönetimi', "<p>Tarayıcı ayarlarınızdan çerezleri yönetebilir, silebilir veya engelleyebilirsiniz. Ancak zorunlu çerezlerin devre dışı bırakılması sitenin işlevselliğini olumsuz etkileyebilir.</p><ul><li><a href='https://support.google.com/chrome/answer/95647' target='_blank' rel='noopener'>Chrome</a></li><li><a href='https://support.mozilla.org/tr/kb/cerezleri-etkinlestirme-devre-disi-birakma' target='_blank' rel='noopener'>Firefox</a></li><li><a href='https://support.apple.com/tr-tr/guide/safari/sfri11471' target='_blank' rel='noopener'>Safari</a></li></ul>"],
      ['Üçüncü Taraf Çerezleri', "<p>Google Maps ve analitik hizmetlerinden kaynaklanan üçüncü taraf çerezler de kullanılabilir. Bu çerezler, ilgili hizmet sağlayıcılarının gizlilik politikalarına tabidir.</p>"],
      ['Onayınız', "<p>Sitemizi kullanmaya devam ederek veya çerez onay bannerındaki \"Kabul Et\" butonuna tıklayarak çerez kullanımına onay vermiş olursunuz. Onayınızı istediğiniz zaman tarayıcı ayarlarından geri alabilirsiniz.</p>"],
      ['İletişim', "<p>Çerez politikamıza ilişkin sorularınız için: <a href='mailto:info@cakmaklargrup.com'>info@cakmaklargrup.com</a></p>"],
    ],
  ],
];

$sections = $content[$key]['sections'] ?? [];
?>

<section class="page-hero">
  <div class="container">
    <nav style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.65);margin-bottom:24px;">
      <a href="<?= SITE_URL ?>/" style="color:rgba(255,255,255,.65);">Ana Sayfa</a>
      <span>›</span>
      <span style="color:#fff;"><?= e($title) ?></span>
    </nav>
    <h1><?= e($title) ?></h1>
    <p style="color:rgba(255,255,255,.8);margin-top:12px;">Son güncelleme: <?= date('d.m.Y') ?></p>
  </div>
</section>

<section style="padding:70px 0 100px;background:#fff;">
  <div class="container">
    <div style="max-width:760px;margin:0 auto;">
      <?php foreach ($sections as [$heading, $body]): ?>
      <div style="margin-bottom:40px;">
        <h2 style="font-size:20px;color:var(--navy);margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid var(--turquoise);display:inline-block;"><?= e($heading) ?></h2>
        <div style="color:#4a5568;line-height:1.9;font-size:15px;">
          <?= $body ?>
        </div>
      </div>
      <?php endforeach; ?>

      <div style="margin-top:48px;padding:24px;background:var(--soft);border-radius:12px;border-left:4px solid var(--turquoise);">
        <p style="color:var(--muted);font-size:13px;margin:0;">
          Bu sayfa <?= date('d.m.Y') ?> tarihinde güncellenmiştir. Sorularınız için
          <a href="<?= SITE_URL ?>/iletisim" style="color:var(--turquoise);font-weight:600;">iletişim formumuzu</a> kullanabilirsiniz.
        </p>
      </div>
    </div>
  </div>
</section>
