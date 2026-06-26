# Çakmaklar İnşaat — Kurulum Kılavuzu

## Gereksinimler

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+
- cPanel / Apache (.htaccess desteği)
- `mod_rewrite` aktif

---

## 1. Dosyaları Sunucuya Yükle

cPanel → File Manager üzerinden `php/` klasörünün **içeriğini** domain root'una (örn: `public_html/`) kopyala:

```
public_html/
├── .htaccess          ← php/.htaccess
├── admin/
├── app/
├── database/
├── public/
```

---

## 2. Veritabanı Oluştur

cPanel → MySQL Databases:

1. Yeni veritabanı oluştur: `cakmaklar_db`
2. Yeni kullanıcı oluştur: `cakmaklar_user` + güçlü şifre
3. Kullanıcıyı veritabanına ekle (ALL PRIVILEGES)
4. phpMyAdmin → `cakmaklar_db` → **Import** → `database/schema.sql`
5. Sonra tekrar **Import** → `database/seed.sql`

---

## 3. Konfigürasyon

`app/config/config.php` dosyasını düzenle:

```php
define('DB_HOST',  'localhost');
define('DB_NAME',  'cakmaklar_db');
define('DB_USER',  'cakmaklar_user');
define('DB_PASS',  'BURAYA_GUCLU_SIFRE');

define('SITE_URL', 'https://www.siteadresi.com'); // sondaki / olmadan
define('SITE_NAME','Çakmaklar İnşaat');
```

Canlıya geçince:
```php
define('DEBUG_MODE', false);
```

---

## 4. Upload Klasörü İzinleri

SSH veya cPanel File Manager ile:

```bash
chmod 755 public/uploads
chmod 755 public/uploads/projects
chmod 755 public/uploads/listings
chmod 755 public/uploads/vehicles
chmod 755 public/uploads/news
chmod 755 public/uploads/sliders
chmod 755 public/uploads/site
chmod 755 public/uploads/pages
chmod 755 public/uploads/media
```

---

## 5. Admin Panele Giriş

**URL:** `https://siteadresi.com/admin/`

| Alan   | Değer                  |
|--------|------------------------|
| E-posta| admin@cakmaklar.com    |
| Şifre  | Admin2026!             |

> **İlk girişten sonra şifreyi değiştir!**
> Admin → Kullanıcılar → Kendi şifremi değiştir

---

## 6. Site Ayarları

Admin → Site Ayarları:

- **Site Başlığı** — Tarayıcı sekmesi ve başlıklarda görünür
- **Telefon / WhatsApp** — WhatsApp: `905XXXXXXXXX` formatında (+ veya 0 olmadan)
- **Logo** — PNG/SVG, şeffaf arka plan, max 5MB
- **Google Maps Embed** — `<iframe>` kodunu yapıştır
- **Sosyal medya URL'leri**

---

## 7. İlk İçerikleri Ekle

Önerilen sıra:
1. **Slider** → Homepage hero görselleri
2. **Projeler** → En az 1 öne çıkan proje ekle
3. **İlanlar** → Satılık / Kiralık ilanlar
4. **Haberler** → Duyurular

---

## Proje Yapısı

```
php/
├── .htaccess                  ← URL yönlendirme + güvenlik
├── admin/
│   ├── index.php              ← Admin giriş noktası
│   ├── login.php              ← Standalone login sayfası
│   ├── logout.php
│   ├── layout.php             ← Admin layout (sidebar + topbar)
│   ├── dashboard.php
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/admin.css
│   │   └── js/admin.js
│   └── modules/
│       ├── settings/index.php ← Site ayarları
│       ├── sliders/index.php  ← Homepage slider
│       ├── projects/index.php ← Proje CRUD + galeri + kat planları
│       ├── listings/index.php ← İlan CRUD (satılık/kiralık/ticari)
│       ├── vehicles/index.php ← Araç ilanı CRUD
│       ├── news/index.php     ← Haber/duyuru CRUD
│       ├── pages/index.php    ← Sayfa içerikleri (Biz Kimiz vb.)
│       ├── forms/index.php    ← Form başvuruları (okundu/sil/yanıt)
│       ├── media/index.php    ← Medya kütüphanesi
│       └── users/index.php    ← Admin kullanıcı yönetimi
├── app/
│   ├── config/config.php      ← VT bağlantısı, URL, sabitler
│   ├── core/
│   │   ├── Database.php       ← PDO Singleton
│   │   ├── Router.php         ← URL routing
│   │   ├── Controller.php     ← Base controller
│   │   └── Model.php          ← Base model (CRUD + paginate)
│   ├── helpers/functions.php  ← e(), slugify(), uploadImage(), setting()...
│   ├── models/                ← ProjectModel, ListingModel, vb.
│   ├── controllers/           ← HomeController, ProjectController, vb.
│   └── views/
│       ├── layouts/           ← default.php, header.php, footer.php
│       └── pages/             ← Tüm frontend görünümleri
├── database/
│   ├── schema.sql             ← Tüm tablolar (14 tablo)
│   └── seed.sql               ← Varsayılan admin + örnek içerik
└── public/
    ├── index.php              ← Frontend giriş noktası (router)
    ├── .htaccess
    ├── assets/
    │   ├── css/main.css       ← Tüm frontend CSS
    │   ├── js/main.js         ← Frontend JS (slider, animasyon vb.)
    │   └── img/
    │       ├── placeholder.jpg
    │       └── site-logo.png  ← Buraya logonu kopyala
    └── uploads/               ← Yüklenen görseller (yazılabilir olmalı)
```

---

## Güvenlik Notları

| Önlem | Uygulama |
|-------|----------|
| SQL Injection | PDO prepared statements (tüm sorgular) |
| XSS | `e()` helper — tüm çıktılar escape edilir |
| CSRF | Her form ve AJAX isteğinde token doğrulama |
| Dosya Yükleme | MIME + uzantı + boyut kontrolü |
| Şifre | `password_hash(BCRYPT, cost:12)` |
| Session | 2 saat timeout, güvenli session adı |
| Admin | `.htaccess` ile /app/ ve /database/ erişimi engellendi |
| Uploads | PHP çalıştırma `.htaccess` ile engellendi |

---

## Sık Karşılaşılan Sorunlar

**404 hataları:** `mod_rewrite` aktif mi? `.htaccess` sunucuya yüklendi mi?
```apache
AllowOverride All  # Apache VirtualHost ayarında bu satır olmalı
```

**Veritabanı bağlanamıyor:** `config.php`'deki DB_* sabitlerini kontrol et.

**Görseller yüklenmiyor:** `public/uploads/` klasörüne yazma izni ver (chmod 755).

**Admin boş sayfa:** `DEBUG_MODE = true` yap, hata mesajını gör.

**WhatsApp linki çalışmıyor:** Numara `905XXXXXXXXX` formatında olmalı (başında + veya 0 yok).

---

## Güncelleme Notları

cPanel hosting'de doğrudan dosya değiştirebilirsin. Herhangi bir cache mekanizması yok — değişiklikler anında aktif olur.

SEO: `https://siteadresi.com/sitemap.xml` adresini Google Search Console'a ekle.
