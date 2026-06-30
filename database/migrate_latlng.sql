-- Sayfa ziyaretleri tablosu — cPanel phpMyAdmin'de çalıştırın
CREATE TABLE IF NOT EXISTS `page_views` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_type`  VARCHAR(30) NOT NULL,
  `page_id`    INT UNSIGNED NULL,
  `page_slug`  VARCHAR(220) NULL,
  `ip`         VARCHAR(45) NULL,
  `user_agent` VARCHAR(300) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_page` (`page_type`, `page_id`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
