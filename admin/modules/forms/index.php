<?php
$pageTitle    = 'Form Başvuruları';
$activeModule = 'forms';
$action       = $_GET['action'] ?? 'index';

if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=forms'); exit; }
    Database::execute("DELETE FROM contact_messages WHERE id=?", [(int)$_GET['id']]);
    setFlash('success','Kayıt silindi.'); header('Location: '.ADMIN_URL.'/?module=forms'); exit;
}

// Tekil görüntüleme + okundu işaretle
if ($action === 'view' && isset($_GET['id'])) {
    $id  = (int) $_GET['id'];
    $msg = Database::queryOne("SELECT * FROM contact_messages WHERE id=?", [$id]);
    if (!$msg) { setFlash('error','Kayıt bulunamadı.'); header('Location: '.ADMIN_URL.'/?module=forms'); exit; }
    if (!$msg['is_read']) Database::execute("UPDATE contact_messages SET is_read=1 WHERE id=?", [$id]);

    ob_start(); ?>
    <div class="page-header">
      <h1>Başvuru Detayı</h1>
      <div style="display:flex;gap:8px;">
        <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=forms"><i class="fa-solid fa-arrow-left"></i> Geri</a>
        <a class="btn btn-danger btn-sm" href="<?= ADMIN_URL ?>/?module=forms&action=delete&id=<?= $id ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')"><i class="fa-solid fa-trash"></i> Sil</a>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <table style="width:100%;border-collapse:collapse;">
          <?php $fields = ['name'=>'Ad Soyad','phone'=>'Telefon','email'=>'E-posta','subject'=>'Konu','ref_type'=>'Başvuru Tipi','ref_title'=>'İlan/Proje','message'=>'Mesaj','created_at'=>'Tarih']; ?>
          <?php foreach ($fields as $key => $label): if (empty($msg[$key])) continue; ?>
          <tr style="border-bottom:1px solid var(--line);">
            <td style="padding:12px 16px;width:160px;font-weight:700;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;"><?= $label ?></td>
            <td style="padding:12px 16px;">
              <?php if ($key === 'message'): ?>
              <div style="white-space:pre-wrap;line-height:1.7;"><?= e($msg[$key]) ?></div>
              <?php elseif ($key === 'created_at'): ?>
              <?= formatDate($msg[$key], 'd.m.Y H:i') ?>
              <?php elseif ($key === 'phone'): ?>
              <a href="tel:<?= e($msg[$key]) ?>" style="color:var(--teal-2);font-weight:600;"><?= e($msg[$key]) ?></a>
              <?php elseif ($key === 'email'): ?>
              <a href="mailto:<?= e($msg[$key]) ?>" style="color:var(--teal-2);font-weight:600;"><?= e($msg[$key]) ?></a>
              <?php else: ?>
              <?= e($msg[$key]) ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
        <div style="padding:18px 16px;display:flex;gap:10px;flex-wrap:wrap;">
          <?php if (!empty($msg['phone'])): ?>
          <a class="btn btn-primary" href="tel:<?= e($msg['phone']) ?>"><i class="fa-solid fa-phone"></i> Ara</a>
          <a class="btn btn-teal" href="<?= e(whatsappUrl($msg['phone'], 'Merhaba '.$msg['name'].', başvurunuz için aradım.')) ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
          <?php endif; ?>
          <?php if (!empty($msg['email'])): ?>
          <a class="btn btn-outline" href="mailto:<?= e($msg['email']) ?>"><i class="fa-solid fa-envelope"></i> E-posta Gönder</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
    $pageContent = ob_get_clean();
    require dirname(__DIR__, 2) . '/layout.php';
    return;
}

// Toplu okundu işaretle
if ($action === 'mark_all_read') {
    Database::execute("UPDATE contact_messages SET is_read=1 WHERE is_read=0");
    setFlash('success','Tümü okundu olarak işaretlendi.'); header('Location: '.ADMIN_URL.'/?module=forms'); exit;
}

// Filtre
$filter = $_GET['filter'] ?? '';
$page   = max(1,(int)($_GET['sayfa']??1));
$where  = match($filter) { 'unread'=>'WHERE is_read=0', 'read'=>'WHERE is_read=1', default=>'' };
$total  = Database::queryOne("SELECT COUNT(*) AS cnt FROM contact_messages $where")['cnt'];
$offset = ($page-1)*20;
$msgs   = Database::query("SELECT * FROM contact_messages $where ORDER BY created_at DESC LIMIT 20 OFFSET $offset");
$paginator = ['data'=>$msgs,'total'=>$total,'per_page'=>20,'current_page'=>$page,'last_page'=>max(1,ceil($total/20))];
$unreadCnt = Database::queryOne("SELECT COUNT(*) AS cnt FROM contact_messages WHERE is_read=0")['cnt'];

ob_start(); ?>
<div class="page-header">
  <h1>Form Başvuruları (<?= $total ?>) <?php if ($unreadCnt): ?><span class="badge badge-warning" style="font-size:13px;"><?= $unreadCnt ?> Yeni</span><?php endif; ?></h1>
  <?php if ($unreadCnt): ?>
  <a class="btn btn-outline btn-sm" href="<?= ADMIN_URL ?>/?module=forms&action=mark_all_read">Tümünü Okundu İşaretle</a>
  <?php endif; ?>
</div>

<div style="display:flex;gap:8px;margin-bottom:16px;">
  <a class="btn btn-sm <?= !$filter?'btn-primary':'btn-outline' ?>" href="<?= ADMIN_URL ?>/?module=forms">Tümü</a>
  <a class="btn btn-sm <?= $filter==='unread'?'btn-primary':'btn-outline' ?>" href="<?= ADMIN_URL ?>/?module=forms&filter=unread">Okunmamış</a>
  <a class="btn btn-sm <?= $filter==='read'?'btn-primary':'btn-outline' ?>" href="<?= ADMIN_URL ?>/?module=forms&filter=read">Okunmuş</a>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Ad Soyad</th><th>Telefon</th><th>E-posta</th><th>Konu / İlan</th><th>Tarih</th><th>Durum</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php if (empty($msgs)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">Başvuru yok.</td></tr>
        <?php else: foreach ($msgs as $m): ?>
        <tr style="<?= !$m['is_read']?'font-weight:600;':'' ?>">
          <td><?= e($m['name']) ?></td>
          <td><a href="tel:<?= e($m['phone']) ?>" style="color:var(--teal-2);"><?= e($m['phone']) ?></a></td>
          <td><?= e($m['email'] ?? '—') ?></td>
          <td><?= e($m['subject'] ?? $m['ref_title'] ?? '—') ?></td>
          <td style="white-space:nowrap;"><?= formatDate($m['created_at'],'d.m.Y H:i') ?></td>
          <td><?= $m['is_read'] ? '<span class="badge badge-muted">Okundu</span>' : '<span class="badge badge-warning">Yeni</span>' ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=forms&action=view&id=<?= $m['id'] ?>" title="Görüntüle"><i class="fa-solid fa-eye"></i></a>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=forms&action=delete&id=<?= $m['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Silinsin mi?')" title="Sil"><i class="fa-solid fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= paginationLinks($paginator, ADMIN_URL . '/?module=forms' . ($filter ? '&filter='.$filter : '')) ?>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
