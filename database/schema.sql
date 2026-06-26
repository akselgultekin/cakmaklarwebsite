-- ============================================================
-- Çakmaklar İnşaat - Veritabanı Şeması
-- Karakter seti: utf8mb4 (emoji + Türkçe tam destek)
-- Oluşturma: 2026
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── Admins ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('super','editor') NOT NULL DEFAULT 'editor',
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Site Ayarları ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `settings` (
  `id`    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key`   VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT NULL,
  `label` VARCHAR(200) NULL COMMENT 'Admin panelde gösterilecek etiket',
  `group` VARCHAR(50) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Slider ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `sliders` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(200) NOT NULL,
  `subtitle`    VARCHAR(300) NULL,
  `description` TEXT NULL,
  `image`       VARCHAR(300) NULL,
  `btn1_text`   VARCHAR(80) NULL,
  `btn1_url`    VARCHAR(300) NULL,
  `btn2_text`   VARCHAR(80) NULL,
  `btn2_url`    VARCHAR(300) NULL,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Projeler ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `projects` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`         VARCHAR(200) NOT NULL,
  `slug`          VARCHAR(220) NOT NULL UNIQUE,
  `short_desc`    VARCHAR(400) NULL,
  `description`   LONGTEXT NULL,
  `location`      VARCHAR(200) NULL,
  `status`        ENUM('satiasta','yakinda','teslim_edildi') NOT NULL DEFAULT 'satiasta',
  `cover_image`   VARCHAR(300) NULL,
  `video_url`     VARCHAR(500) NULL,
  `tour_url`      VARCHAR(500) NULL   COMMENT '3D / 360 tur URL',
  `tour_embed`    TEXT NULL           COMMENT 'iframe embed kodu',
  `tour_desc`     TEXT NULL           COMMENT '3D tur açıklaması',
  `is_featured`   TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`    SMALLINT NOT NULL DEFAULT 0,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`    VARCHAR(200) NULL,
  `meta_desc`     VARCHAR(400) NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Proje görselleri
CREATE TABLE IF NOT EXISTS `project_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(300) NOT NULL,
  `alt`        VARCHAR(200) NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Proje kat planları
CREATE TABLE IF NOT EXISTS `project_floor_plans` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `title`      VARCHAR(100) NOT NULL COMMENT 'Örn: 2+1, 3+1, Teras',
  `desc`       VARCHAR(300) NULL,
  `image`      VARCHAR(300) NULL,
  `area_m2`    SMALLINT NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── İlanlar ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `listings` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type`          ENUM('satilik','kiralik','dukkan','ofis','arsa') NOT NULL DEFAULT 'satilik',
  `project_id`    INT UNSIGNED NULL COMMENT 'Projeye bağlı ilan',
  `title`         VARCHAR(200) NOT NULL,
  `slug`          VARCHAR(220) NOT NULL UNIQUE,
  `price`         DECIMAL(15,2) NULL,
  `price_unit`    VARCHAR(10) NOT NULL DEFAULT 'TL',
  `location`      VARCHAR(200) NULL,
  `area_m2`       SMALLINT NULL,
  `room_count`    VARCHAR(20) NULL  COMMENT 'Örn: 3+1',
  `bathroom`      TINYINT NULL,
  `floor`         VARCHAR(50) NULL  COMMENT 'Örn: 5 / 9',
  `heating`       VARCHAR(80) NULL,
  `building_age`  TINYINT NULL,
  `status_tag`    SET('yeni','firsat','krediye_uygun') NULL,
  `description`   LONGTEXT NULL,
  `cover_image`   VARCHAR(300) NULL,
  `whatsapp_msg`  VARCHAR(400) NULL COMMENT 'WhatsApp mesaj şablonu',
  `tour_url`      VARCHAR(500) NULL,
  `tour_embed`    TEXT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`    SMALLINT NOT NULL DEFAULT 0,
  `meta_title`    VARCHAR(200) NULL,
  `meta_desc`     VARCHAR(400) NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İlan görselleri
CREATE TABLE IF NOT EXISTS `listing_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `listing_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(300) NOT NULL,
  `alt`        VARCHAR(200) NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Araç İlanları ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand`       VARCHAR(100) NOT NULL,
  `model`       VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(220) NOT NULL UNIQUE,
  `year`        SMALLINT NOT NULL,
  `km`          INT NULL,
  `fuel`        VARCHAR(50) NULL  COMMENT 'Dizel, Benzin, Elektrik, Hybrid',
  `transmission`VARCHAR(50) NULL  COMMENT 'Manuel, Otomatik',
  `color`       VARCHAR(50) NULL,
  `price`       DECIMAL(15,2) NULL,
  `price_unit`  VARCHAR(10) NOT NULL DEFAULT 'TL',
  `description` LONGTEXT NULL,
  `cover_image` VARCHAR(300) NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Araç görselleri
CREATE TABLE IF NOT EXISTS `vehicle_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(300) NOT NULL,
  `alt`        VARCHAR(200) NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Haberler / Duyurular ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `news` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(200) NOT NULL,
  `slug`        VARCHAR(220) NOT NULL UNIQUE,
  `summary`     VARCHAR(400) NULL,
  `content`     LONGTEXT NULL,
  `cover_image` VARCHAR(300) NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`  SMALLINT NOT NULL DEFAULT 0,
  `meta_title`  VARCHAR(200) NULL,
  `meta_desc`   VARCHAR(400) NULL,
  `published_at`DATETIME NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Sayfa İçerikleri ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pages` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_key`    VARCHAR(100) NOT NULL UNIQUE COMMENT 'Örn: about, faq',
  `title`       VARCHAR(200) NOT NULL,
  `subtitle`    VARCHAR(400) NULL,
  `cover_image` VARCHAR(500) NULL,
  `content`     LONGTEXT NULL,
  `meta_title`  VARCHAR(200) NULL,
  `meta_desc`   VARCHAR(400) NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── İletişim Formu Başvuruları ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `form_type`  ENUM('contact','quick_apply') NOT NULL DEFAULT 'contact',
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NULL,
  `phone`      VARCHAR(30) NULL,
  `subject`    VARCHAR(200) NULL,
  `message`    TEXT NULL,
  `ref_type`   VARCHAR(50) NULL  COMMENT 'listing, project, vehicle',
  `ref_id`     INT UNSIGNED NULL COMMENT 'İlgili kayıt ID',
  `ref_title`  VARCHAR(200) NULL COMMENT 'İlgili kayıt başlığı',
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `ip`         VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Medya ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `media` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `file_path`   VARCHAR(500) NOT NULL COMMENT 'uploads/ altındaki göreli yol',
  `file_name`   VARCHAR(300) NOT NULL,
  `file_size`   INT UNSIGNED NULL,
  `mime_type`   VARCHAR(100) NULL,
  `uploaded_by` INT UNSIGNED NULL COMMENT 'admins.id',
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
