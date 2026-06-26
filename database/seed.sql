-- ============================================================
-- Çakmaklar İnşaat - Örnek / Başlangıç Verisi
-- schema.sql'den SONRA çalıştır
-- ============================================================

SET NAMES utf8mb4;

-- ─── Varsayılan Admin ────────────────────────────────────────────────
-- Şifre: Admin2026!  (password_hash ile üretildi)
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Süper Admin', 'admin@cakmaklar.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super');
-- NOT: Şifreyi ilk girişten sonra değiştir!

-- ─── Site Ayarları ───────────────────────────────────────────────────
INSERT INTO `settings` (`key`, `value`, `label`, `group`) VALUES
('site_title',       'Çakmaklar İnşaat',                          'Site Başlığı',           'general'),
('site_slogan',      'Güvenilir yapılar, kalıcı değerler.',        'Site Sloganı',           'general'),
('phone',            '0 374 XXX XX XX',                           'Telefon',                'contact'),
('whatsapp',         '90374XXXXXXX',                               'WhatsApp Numarası',      'contact'),
('email',            'info@cakmaklar.com',                         'E-posta',                'contact'),
('address',          'Bolu Merkez, Türkiye',                       'Adres',                  'contact'),
('facebook',         '',                                           'Facebook URL',           'social'),
('instagram',        '',                                           'Instagram URL',          'social'),
('twitter',          '',                                           'Twitter/X URL',          'social'),
('youtube',          '',                                           'YouTube URL',            'social'),
('seo_title',        'Çakmaklar İnşaat | Bolu Gayrimenkul',        'SEO Varsayılan Başlık',  'seo'),
('seo_desc',         'Bolu\'da güvenilir inşaat, satılık ve kiralık konut, proje ve araç ilanları için Çakmaklar İnşaat.', 'SEO Varsayılan Açıklama', 'seo'),
('logo',             '',                                           'Logo (upload yolu)',     'general'),
('favicon',          '',                                           'Favicon',                'general'),
('maps_embed',       '',                                           'Google Maps iframe',     'contact'),
('footer_text',      '© 2026 Çakmaklar İnşaat. Tüm hakları saklıdır.', 'Footer Metin',      'general');

-- ─── Slider ──────────────────────────────────────────────────────────
INSERT INTO `sliders` (`title`, `subtitle`, `description`, `image`, `btn1_text`, `btn1_url`, `btn2_text`, `btn2_url`, `sort_order`, `is_active`) VALUES
('Bolu\'nun En Prestijli Projesi',   'Çakmaklar Panorama', 'Şehir merkezinde geniş cepheli, sosyal alanları güçlü premium konut projesi.', '', 'Projeyi İncele', '/projeler/cakmaklar-panorama', '360° Tur Başlat', '/3d-ev-gez', 1, 1),
('Satılık ve Kiralık Konutlar',      '', 'Bolu genelinde seçkin portföyden uygun fiyatlı konut seçenekleri.',            '', 'Satılık İlanlar', '/satilik', 'Kiralık İlanlar', '/kiralik', 2, 1),
('Araç Portföyü',                    '', 'Premium araç ilanlarını inceleyin, güvenli ve şeffaf süreçle satın alın.',       '', 'Araçları Gör',   '/arac-ilanlari', '', '', 3, 1);

-- ─── Örnek Proje ─────────────────────────────────────────────────────
INSERT INTO `projects` (`title`, `slug`, `short_desc`, `description`, `location`, `status`, `is_featured`, `sort_order`, `is_active`, `meta_title`, `meta_desc`) VALUES
('Çakmaklar Panorama', 'cakmaklar-panorama',
 'Bolu Merkez\'de geniş cepheli premium konut projesi.',
 '<p>Çakmaklar Panorama, şehir merkezinde sakin ve prestijli bir yaşam isteyen aileler için tasarlandı. Geniş balkonlar, ferah planlar ve dijital satış deneyimiyle öne çıkan proje 3 blok ve 84 daireden oluşmaktadır.</p>',
 'Bolu Merkez', 'satiasta', 1, 1, 1,
 'Çakmaklar Panorama | Bolu Premium Konut Projesi',
 'Bolu Merkez\'de 3 blok, 84 daire, 360° sanal tur imkânıyla Çakmaklar Panorama projesi.');

-- Proje kat planları
INSERT INTO `project_floor_plans` (`project_id`, `title`, `desc`, `area_m2`, `sort_order`) VALUES
(1, '2+1', '110 m², balkonlu aile dairesi.', 110, 1),
(1, '3+1', '145 m², geniş salon ve ebeveyn alanı.', 145, 2),
(1, 'Teras', '210 m², şehir manzaralı özel plan.', 210, 3);

-- ─── Örnek İlanlar ───────────────────────────────────────────────────
INSERT INTO `listings` (`type`, `project_id`, `title`, `slug`, `price`, `location`, `area_m2`, `room_count`, `bathroom`, `floor`, `status_tag`, `description`, `is_active`, `sort_order`) VALUES
('satilik', 1, 'Panorama 3+1 Daire', 'panorama-3-1-daire', 4250000, 'Bolu Merkez', 145, '3+1', 1, '5 / 9', 'yeni,krediye_uygun', '<p>Modern mimari, geniş salon, manzaralı balkon.</p>', 1, 1),
('satilik', 1, 'Teraslı 2+1 Daire',  'panorama-terasli-2-1', 3950000, 'Bolu Merkez', 110, '2+1', 1, '7 / 9', 'yeni', '<p>Şehir manzaralı teras daire.</p>', 1, 2),
('satilik', NULL, 'Bolu Merkez 3+1 Satılık', 'bolu-merkez-3-1-satilik', 3200000, 'Bolu Merkez', 130, '3+1', 2, '4 / 7', 'krediye_uygun', '<p>Geniş mutfak, ebeveyn banyosu, güneş alan konut.</p>', 1, 3),
('kiralik', NULL, 'Merkez 2+1 Kiralık', 'merkez-2-1-kiralik', 15000, 'Bolu Merkez', 95, '2+1', 1, '3 / 5', '', '<p>Merkezi konumda kiralık daire.</p>', 1, 1),
('kiralik', NULL, 'Öğrenciye 1+1 Kiralık', 'ogrenciye-1-1-kiralik', 8500, 'Bolu Merkez', 55, '1+1', 1, '2 / 4', '', '<p>Üniversiteye yakın, asansörlü, öğrenciye uygun kiralık daire.</p>', 1, 2),
('ticari', NULL, 'Cadde Üzeri İşyeri', 'cadde-uzeri-isyeri', 22000, 'Bolu Merkez', 80, NULL, NULL, '1 / 1', 'kira', '<p>Yoğun cadde üzerinde işlek konumda dükkan / ofis.</p>', 1, 1);

-- ─── Örnek Proje (Tamamlanan) ────────────────────────────────────────
INSERT INTO `projects` (`title`, `slug`, `short_desc`, `description`, `location`, `status`, `is_featured`, `sort_order`, `is_active`, `meta_title`, `meta_desc`) VALUES
('Merkez Rezidans', 'merkez-rezidans',
 'Bolu şehir merkezinde yüksek standartlı rezidans projesi.',
 '<p>Merkez Rezidans, şehrin kalbinde konforlu ve modern bir yaşam alanı sunuyor. 1+1\'den 4+1\'e geniş kat planlarıyla tüm aile büyüklüklerine uygun daireler.</p>',
 'Bolu Merkez', 'satiasta', 1, 2, 1,
 'Merkez Rezidans | Çakmaklar İnşaat',
 'Bolu Merkez\'de konforlu rezidans daireleri.'),
('Çakmaklar Villa', 'cakmaklar-villa',
 'Müstakil bahçeli lüks villa projesi.',
 '<p>Çakmaklar Villa projesi; özel bahçesi, kapalı otoparkı ve geniş yaşam alanlarıyla Bolu\'nun en prestijli villa projesiydi. Proje teslim edilmiştir.</p>',
 'Bolu Merkez', 'tamamlandi', 0, 3, 1,
 'Çakmaklar Villa | Tamamlanan Projeler',
 'Çakmaklar İnşaat\'ın tamamladığı villa projesi.'),
('Park Evleri', 'park-evleri',
 'Yeşilin içinde huzurlu konut kompleksi.',
 '<p>Park Evleri, geniş yeşil alanları ve sosyal tesisleriyle ailelere huzurlu bir yaşam sunmuştur. Tüm daireler teslim edilmiştir.</p>',
 'Bolu', 'tamamlandi', 0, 4, 1,
 'Park Evleri | Çakmaklar İnşaat',
 'Çakmaklar İnşaat Park Evleri projesi, Bolu.'),
('Çakmaklar Konakları', 'cakmaklar-konaklari',
 'Geleneksel mimariyi modern konforla buluşturan konak projesi.',
 '<p>Çakmaklar Konakları; taş cephe ve ahşap detaylarıyla geleneksel Türk mimarisini modern yaşam konforu ile harmanlıyor. Proje tamamlanmıştır.</p>',
 'Bolu', 'tamamlandi', 0, 5, 1,
 'Çakmaklar Konakları | Tamamlanan Projeler',
 'Çakmaklar Konakları, Bolu. Geleneksel mimari.');

-- ─── Örnek Araç İlanı ────────────────────────────────────────────────
INSERT INTO `vehicles` (`brand`, `model`, `slug`, `year`, `km`, `fuel`, `transmission`, `price`, `description`, `is_active`) VALUES
('Volkswagen', 'Passat', 'volkswagen-passat-2021', 2021, 72000, 'Dizel', 'Otomatik', 1485000, '<p>Bakımlı, hasarsız Passat.</p>', 1),
('Mercedes',   'C200',   'mercedes-c200-2020',     2020, 58000, 'Benzin', 'Otomatik', 2240000, '<p>Servis kayıtlı C200.</p>', 1),
('Ford',       'Transit', 'ford-transit-2022',      2022, 45000, 'Dizel', 'Manuel',   1180000, '<p>Şirket aracı olarak kullanılmış, bakımlı Ford Transit. Frigo kasalı seçenek mevcuttur.</p>', 1);

-- ─── Örnek Haber ─────────────────────────────────────────────────────
INSERT INTO `news` (`title`, `slug`, `summary`, `content`, `is_active`, `published_at`) VALUES
('Çakmaklar Panorama Satışları Başladı', 'cakmaklar-panorama-satislari-basladi',
 'Bolu\'nun en prestijli konut projesinde daireler artık satışta.',
 '<p>Çakmaklar Panorama projesi, Bolu Merkez\'deki premium konumuyla satışa açıldı. Detaylı bilgi için satış ofisimizi arayabilirsiniz.</p>',
 1, NOW());

-- ─── Sayfa İçerikleri ────────────────────────────────────────────────
INSERT INTO `pages` (`page_key`, `title`, `subtitle`, `content`, `meta_title`, `meta_desc`) VALUES
('about', 'Biz Kimiz', 'Güven, Kalite ve Deneyimle İnşa Ediyoruz',
 '<p>Çakmaklar İnşaat, Bolu\'da yılların deneyimiyle güvenilir yapılar inşa eden bir inşaat ve gayrimenkul şirketidir. Müşteri memnuniyetini ön planda tutan anlayışımızla her projede kalite ve güveni bir arada sunuyoruz.</p><p>Modern mimari anlayışımız ve titiz işçiliğimizle her projede fark yaratıyoruz. Konut, ticari ve karma kullanım projelerinde sektörde önemli bir yer edinmiş olan firmamız, tüm projelerini zamanında ve söz verilen kalite standartlarında teslim etmektedir.</p>',
 'Biz Kimiz | Çakmaklar İnşaat',
 'Çakmaklar İnşaat hakkında bilgi edinin. Bolu\'da güvenilir inşaat ve gayrimenkul hizmetleri.'),
('home_intro', 'Ana Sayfa Tanıtım', 'Türkiye\'nin Güvenilir İnşaat Firması',
 '<p>Çakmaklar İnşaat olarak onlarca yıllık deneyimimizle konut ve ticari gayrimenkul sektöründe hizmet veriyoruz.</p>',
 NULL, NULL),
('contact', 'İletişim', 'Bize Ulaşın',
 '<p>Her türlü soru ve talebiniz için iletişim formunu doldurabilir ya da doğrudan arayabilirsiniz.</p>',
 'İletişim | Çakmaklar İnşaat',
 'Çakmaklar İnşaat ile iletişime geçin. Telefon, WhatsApp veya form aracılığıyla bize ulaşın.');
