<?php
class AiController extends Controller
{
    private string $systemPrompt = <<<PROMPT
Sen Çakmaklar İnşaat'ın yapay zeka asistanısın. Türkiye'nin Bolu şehrinde faaliyet gösteren bu köklü inşaat ve gayrimenkul firmasında müşterilere yardımcı oluyorsun.

Şirket Hakkında:
- Adı: Çakmaklar Grup İnşaat
- Web: cakmaklargrup.com
- Konum: Tabaklar Mh. Haznedarlar Sk. No: 63/2, Bolu Merkez
- Telefon: +90 533 622 74 93
- E-posta: info@cakmaklargrup.com
- Çalışma saatleri: Hafta içi 09:00 – 18:30

Sunduğumuz Hizmetler:
- Satılık konutlar (daire, villa)
- Kiralık konutlar
- Ticari gayrimenkuller (dükkan, ofis, arsa)
- Konut projeleri
- Araç ilanları
- 3D sanal ev gezisi

Yanıt Kuralları:
- Her zaman Türkçe konuş
- Kısa ve net yanıtlar ver (maksimum 3-4 cümle)
- Müşterileri doğru sayfaya yönlendir: ilanlar için /satilik veya /kiralik, projeler için /projeler, 3D tur için /3d-ev-gez, iletişim için /iletisim
- Fiyat garantisi verme, "güncel fiyatlar için iletişime geçin" de
- Samimi ve profesyonel ol
- Eğer bilmiyorsan, müşteriyi telefon veya iletişim formuyla yönlendir
PROMPT;

    public function chat(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }

        $apiKey = defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : '';
        if (!$apiKey) {
            $this->json(['error' => 'AI servisi henüz yapılandırılmamış.'], 503);
        }

        $input    = json_decode(file_get_contents('php://input'), true);
        $history  = $input['history'] ?? [];
        $userMsg  = trim($input['message'] ?? '');

        if (!$userMsg || mb_strlen($userMsg) > 1000) {
            $this->json(['error' => 'Geçersiz mesaj.'], 422);
        }

        // Build messages array
        $messages = [];
        foreach ($history as $h) {
            $role = ($h['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = trim($h['content'] ?? '');
            if ($content) {
                $messages[] = ['role' => $role, 'content' => $content];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMsg];

        // Keep last 10 exchanges max
        if (count($messages) > 20) {
            $messages = array_slice($messages, -20);
        }

        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 400,
            'system'     => $this->systemPrompt,
            'messages'   => $messages,
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200) {
            error_log('[AiController] API error: ' . $httpCode . ' ' . $curlErr . ' ' . substr($result, 0, 200));
            $this->json(['error' => 'AI servisi şu an yanıt vermiyor. Lütfen tekrar deneyin.'], 502);
        }

        $data    = json_decode($result, true);
        $reply   = $data['content'][0]['text'] ?? '';

        if (!$reply) {
            $this->json(['error' => 'Yanıt alınamadı.'], 502);
        }

        $this->json(['reply' => $reply]);
    }
}
