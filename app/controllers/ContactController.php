<?php
class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('contact', [
            'meta_title' => 'İletişim | Bolu Gayrimenkul | ' . SITE_NAME,
            'meta_desc'  => 'Çakmaklar İnşaat ile iletişime geçin. Bolu\'da konut, proje ve gayrimenkul sorularınız için telefon, WhatsApp veya form aracılığıyla ulaşın.',
        ]);
    }

    public function send(): void
    {
        if (!$this->verifyCsrf()) {
            setFlash('error', 'Güvenlik hatası. Lütfen tekrar deneyin.');
            $this->redirect(SITE_URL . '/iletisim');
        }

        $name    = $this->post('name', '');
        $phone   = $this->post('phone', '');
        $subject = $this->post('subject', '');
        $message = $this->post('message', '');
        $email   = $this->post('email', '');

        if (empty($name) || empty($phone)) {
            setFlash('error', 'Ad Soyad ve Telefon alanları zorunludur.');
            $this->redirect(SITE_URL . '/iletisim');
        }

        Database::execute(
            "INSERT INTO contact_messages (form_type,name,email,phone,subject,message,ip,created_at)
             VALUES ('contact',?,?,?,?,?,?,NOW())",
            [$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? '']
        );

        // ── Mail gönder ──────────────────────────────────────────────────
        $to      = 'info@cakmaklargrup.com';
        $subLine = '[Web Formu] ' . ($subject ?: 'İletişim Talebi') . ' — ' . $name;

        $htmlBody = '<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:32px 16px;">
<tr><td align="center">
  <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);">
    <tr><td style="background:#0A1F44;padding:24px 32px;">
      <h2 style="margin:0;color:#fff;font-size:20px;">📩 Yeni İletişim Formu Mesajı</h2>
      <p style="margin:6px 0 0;color:rgba(255,255,255,.65);font-size:13px;">cakmaklargrup.com üzerinden gönderildi</p>
    </td></tr>
    <tr><td style="padding:32px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr><td style="padding:10px 0;border-bottom:1px solid #f0f0f0;"><strong style="color:#65758a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Ad Soyad</strong><br><span style="color:#0A1F44;font-size:15px;font-weight:600;">' . htmlspecialchars($name) . '</span></td></tr>
        <tr><td style="padding:10px 0;border-bottom:1px solid #f0f0f0;"><strong style="color:#65758a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Telefon</strong><br><a href="tel:' . htmlspecialchars($phone) . '" style="color:#18C6C3;font-size:15px;font-weight:600;">' . htmlspecialchars($phone) . '</a></td></tr>'
        . ($email ? '<tr><td style="padding:10px 0;border-bottom:1px solid #f0f0f0;"><strong style="color:#65758a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">E-posta</strong><br><a href="mailto:' . htmlspecialchars($email) . '" style="color:#18C6C3;font-size:15px;">' . htmlspecialchars($email) . '</a></td></tr>' : '')
        . '<tr><td style="padding:10px 0;border-bottom:1px solid #f0f0f0;"><strong style="color:#65758a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Konu</strong><br><span style="color:#0A1F44;font-size:15px;">' . htmlspecialchars($subject ?: '—') . '</span></td></tr>
        <tr><td style="padding:16px 0 0;"><strong style="color:#65758a;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Mesaj</strong><br><p style="color:#4a5568;font-size:15px;line-height:1.7;margin:8px 0 0;white-space:pre-wrap;">' . htmlspecialchars($message) . '</p></td></tr>
      </table>
    </td></tr>
    <tr><td style="background:#f8f9fb;padding:16px 32px;border-top:1px solid #eee;">
      <p style="margin:0;color:#aaa;font-size:12px;">Gönderim tarihi: ' . date('d.m.Y H:i') . ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? '') . '</p>
    </td></tr>
  </table>
</td></tr></table>
</body></html>';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Çakmaklar İnşaat Web <noreply@cakmaklargrup.com>\r\n";
        $headers .= "Reply-To: " . ($email ?: 'noreply@cakmaklargrup.com') . "\r\n";
        $headers .= "X-Mailer: PHP/" . PHP_VERSION;

        @mail($to, '=?UTF-8?B?' . base64_encode($subLine) . '?=', $htmlBody, $headers);
        // ────────────────────────────────────────────────────────────────

        setFlash('success', 'Mesajınız alındı. En kısa sürede size ulaşacağız.');
        $this->redirect(SITE_URL . '/iletisim');
    }

    /** POST /ajax/basvuru */
    public function quickApply(): void
    {
        if (!$this->verifyCsrf()) {
            $this->json(['success' => false, 'message' => 'Güvenlik hatası.'], 403);
        }

        $name     = $this->post('name', '');
        $phone    = $this->post('phone', '');
        $message  = $this->post('message', '');
        $refType  = $this->post('ref_type', '');
        $refId    = (int) $this->post('ref_id', 0);
        $refTitle = $this->post('ref_title', '');

        if (empty($name) || empty($phone)) {
            $this->json(['success' => false, 'message' => 'Ad ve telefon zorunludur.'], 422);
        }

        Database::execute(
            "INSERT INTO contact_messages (form_type,name,phone,message,ref_type,ref_id,ref_title,ip,created_at)
             VALUES ('quick_apply',?,?,?,?,?,?,?,NOW())",
            [$name, $phone, $message, $refType, $refId, $refTitle, $_SERVER['REMOTE_ADDR'] ?? '']
        );

        $this->json(['success' => true, 'message' => 'Talebiniz alındı, sizi arayacağız.']);
    }
}
