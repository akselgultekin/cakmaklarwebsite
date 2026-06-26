<?php
/**
 * Yardımcı fonksiyonlar - Global kullanım
 */

// ─── XSS güvenliği ──────────────────────────────────────────────────
function e(mixed $val): string
{
    return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ─── Slug oluştur ───────────────────────────────────────────────────
function slugify(string $text): string
{
    $tr = ['ş'=>'s','Ş'=>'S','ı'=>'i','İ'=>'I','ğ'=>'g','Ğ'=>'G',
           'ü'=>'u','Ü'=>'U','ö'=>'o','Ö'=>'O','ç'=>'c','Ç'=>'C'];
    $text = strtr($text, $tr);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', trim($text));
    return trim($text, '-');
}

// ─── Para formatla ──────────────────────────────────────────────────
function formatPrice(float|int $price, string $currency = 'TL'): string
{
    return number_format($price, 0, ',', '.') . ' ' . $currency;
}

// ─── Tarih formatla ─────────────────────────────────────────────────
function formatDate(string $date, string $format = 'd.m.Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

// ─── Kısa metin ─────────────────────────────────────────────────────
function excerpt(string $text, int $length = 160): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

// ─── CSRF token üret ────────────────────────────────────────────────
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

// ─── Flash mesaj ────────────────────────────────────────────────────
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ─── Resim URL ──────────────────────────────────────────────────────
function uploadUrl(string $path): string
{
    if (!$path) return SITE_URL . '/public/assets/img/placeholder.jpg';
    if (str_starts_with($path, 'http')) return $path;
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

// ─── Pagination HTML ────────────────────────────────────────────────
function paginationLinks(array $paginator, string $baseUrl): string
{
    if ($paginator['last_page'] <= 1) return '';

    $html = '<div class="pagination">';
    for ($i = 1; $i <= $paginator['last_page']; $i++) {
        $active = $i === $paginator['current_page'] ? ' active' : '';
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $html .= "<a href=\"{$baseUrl}{$sep}sayfa={$i}\" class=\"page-link{$active}\">{$i}</a>";
    }
    $html .= '</div>';
    return $html;
}

// ─── Dosya boyutu formatla ──────────────────────────────────────────
function formatFileSize(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

// ─── WhatsApp URL ───────────────────────────────────────────────────
function whatsappUrl(string $phone, string $message = ''): string
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (str_starts_with($phone, '0')) $phone = '90' . substr($phone, 1);
    $msg = $message ? '&text=' . urlencode($message) : '';
    return "https://wa.me/{$phone}{$msg}";
}

// ─── Site ayarı getir ───────────────────────────────────────────────
function setting(string $key, string $default = ''): string
{
    static $settings = null;
    if ($settings === null) {
        $rows = Database::query("SELECT `key`, `value` FROM settings");
        $settings = array_column($rows, 'value', 'key');
    }
    return $settings[$key] ?? $default;
}

// ─── Güvenli dosya adı ──────────────────────────────────────────────
function safeFileName(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    return strtolower($name);
}

// ─── Resim yükle ────────────────────────────────────────────────────
function uploadImage(array $file, string $folder): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('Dosya boyutu çok büyük (max 5MB).');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
        throw new RuntimeException('Geçersiz dosya türü. Sadece JPG, PNG, WebP, GIF kabul edilir.');
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        throw new RuntimeException('Geçersiz dosya uzantısı.');
    }

    $filename  = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetDir = UPLOAD_PATH . '/' . trim($folder, '/') . '/';
    $targetPath = $targetDir . $filename;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Dosya yüklenemedi.');
    }

    return $folder . '/' . $filename;
}

// ─── Active link kontrolü ───────────────────────────────────────────
function isActivePage(string $path): string
{
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    return $uri === $path ? 'active' : '';
}
