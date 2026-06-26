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
