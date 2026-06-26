<?php
$pageTitle    = 'Kullanıcı Yönetimi';
$activeModule = 'users';
$action       = $_GET['action'] ?? 'index';
$currentId    = (int) ($_SESSION['admin_id'] ?? 0);

// ── DELETE ────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    if (($_GET['_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) { setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=users'); exit; }
    $id = (int) $_GET['id'];
    if ($id === $currentId) { setFlash('error','Kendinizi silemezsiniz.'); header('Location: '.ADMIN_URL.'/?module=users'); exit; }
    Database::execute("DELETE FROM admins WHERE id=?", [$id]);
    setFlash('success','Kullanıcı silindi.'); header('Location: '.ADMIN_URL.'/?module=users'); exit;
}

// ── SAVE (create / edit) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        setFlash('error','Güvenlik hatası.'); header('Location: '.ADMIN_URL.'/?module=users'); exit;
    }
    $id       = (int) ($_POST['id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = in_array($_POST['role']??'', ['admin','editor']) ? $_POST['role'] : 'editor';
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error','Ad ve geçerli e-posta zorunludur.'); header('Location: '.ADMIN_URL.'/?module=users&action='.($id?'edit&id='.$id:'create')); exit;
    }

    // E-posta benzersizlik
    $ex = Database::queryOne("SELECT id FROM admins WHERE email=? AND id!=?", [$email, $id]);
    if ($ex) { setFlash('error','Bu e-posta zaten kullanılıyor.'); header('Location: '.ADMIN_URL.'/?module=users&action='.($id?'edit&id='.$id:'create')); exit; }

    if ($id) {
        $data   = ['name'=>$name,'email'=>$email,'role'=>$role];
        $params = array_values($data);
        $set    = '`name`=?,`email`=?,`role`=?';
        if ($password) {
            if (strlen($password) < 8) { setFlash('error','Şifre en az 8 karakter olmalıdır.'); header('Location: '.ADMIN_URL.'/?module=users&action=edit&id='.$id); exit; }
            $set     .= ',`password`=?';
            $params[] = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
        }
        $params[] = $id;
        Database::execute("UPDATE admins SET $set WHERE id=?", $params);
        setFlash('success','Kullanıcı güncellendi.');
    } else {
        if (strlen($password) < 8) { setFlash('error','Şifre en az 8 karakter olmalıdır.'); header('Location: '.ADMIN_URL.'/?module=users&action=create'); exit; }
        Database::execute("INSERT INTO admins (name,email,password,role) VALUES (?,?,?,?)",
            [$name, $email, password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]), $role]);
        setFlash('success','Kullanıcı oluşturuldu.');
    }
    header('Location: '.ADMIN_URL.'/?module=users'); exit;
}

// ── FORM ─────────────────────────────────────────────────
if ($action === 'create' || $action === 'edit') {
    $user = $action === 'edit' ? Database::queryOne("SELECT id,name,email,role FROM admins WHERE id=?", [(int)($_GET['id']??0)]) : null;

    ob_start(); ?>
    <div class="page-header">
      <h1><?= $user ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı' ?></h1>
      <a class="btn btn-outline" href="<?= ADMIN_URL ?>/?module=users"><i class="fa-solid fa-arrow-left"></i> Geri</a>
    </div>
    <form method="POST" style="max-width:560px;">
      <?= csrfField() ?>
      <?php if ($user): ?><input type="hidden" name="id" value="<?= $user['id'] ?>"><?php endif; ?>
      <div class="card">
        <div class="card-header"><h3><?= $user ? 'Bilgileri Düzenle' : 'Kullanıcı Bilgileri' ?></h3></div>
        <div class="card-body">
          <div class="form-group"><label>Ad Soyad <span class="required">*</span></label><input class="form-control" name="name" required value="<?= e($user['name'] ?? '') ?>"></div>
          <div class="form-group"><label>E-posta <span class="required">*</span></label><input class="form-control" name="email" type="email" required value="<?= e($user['email'] ?? '') ?>"></div>
          <div class="form-group">
            <label>Rol</label>
            <select class="form-control" name="role">
              <option value="admin"  <?= ($user['role']??'')==='admin'  ? 'selected' : '' ?>>Admin (Tam Yetki)</option>
              <option value="editor" <?= ($user['role']??'')==='editor' ? 'selected' : '' ?>>Editör (İçerik)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Şifre <?= $user ? '(boş bırakılırsa değişmez)' : '<span class="required">*</span>' ?></label>
            <input class="form-control" name="password" type="password" autocomplete="new-password" placeholder="En az 8 karakter" <?= !$user ? 'required' : '' ?>>
          </div>
          <button class="btn btn-primary" type="submit" style="min-height:46px;padding:0 28px;">
            <i class="fa-solid fa-floppy-disk"></i> Kaydet
          </button>
        </div>
      </div>
    </form>
    <?php
    $pageContent = ob_get_clean();
    require dirname(__DIR__, 2) . '/layout.php';
    return;
}

$users = Database::query("SELECT id,name,email,role,last_login,created_at FROM admins ORDER BY id");

ob_start(); ?>
<div class="page-header">
  <h1>Kullanıcılar (<?= count($users) ?>)</h1>
  <a class="btn btn-primary" href="<?= ADMIN_URL ?>/?module=users&action=create"><i class="fa-solid fa-plus"></i> Yeni Kullanıcı</a>
</div>
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Ad Soyad</th><th>E-posta</th><th>Rol</th><th>Son Giriş</th><th>Kayıt Tarihi</th><th>İşlem</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:34px;height:34px;border-radius:50%;background:var(--navy);color:#fff;display:grid;place-items:center;font-family:Syne,sans-serif;font-size:13px;font-weight:700;flex-shrink:0;">
                <?= strtoupper(mb_substr($u['name'],0,1)) ?>
              </div>
              <strong><?= e($u['name']) ?></strong>
              <?= $u['id']===$currentId ? '<span class="badge badge-info" style="margin-left:4px;">Siz</span>' : '' ?>
            </div>
          </td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge <?= $u['role']==='admin'?'badge-warning':'badge-muted' ?>"><?= ucfirst($u['role']) ?></span></td>
          <td><?= $u['last_login'] ? formatDate($u['last_login'],'d.m.Y H:i') : '—' ?></td>
          <td><?= formatDate($u['created_at'],'d.m.Y') ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <a class="btn btn-outline btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=users&action=edit&id=<?= $u['id'] ?>"><i class="fa-solid fa-pen"></i></a>
              <?php if ($u['id'] !== $currentId): ?>
              <a class="btn btn-danger btn-sm btn-icon" href="<?= ADMIN_URL ?>/?module=users&action=delete&id=<?= $u['id'] ?>&_token=<?= e(csrfToken()) ?>" onclick="return confirm('Kullanıcıyı silmek istediğinizden emin misiniz?')"><i class="fa-solid fa-trash"></i></a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Şifre değiştirme (kendi hesabım) -->
<div class="card" style="margin-top:24px;max-width:500px;">
  <div class="card-header"><h3><i class="fa-solid fa-key" style="color:var(--teal-2);"></i> Kendi Şifremi Değiştir</h3></div>
  <div class="card-body">
    <form method="POST">
      <?= csrfField() ?>
      <input type="hidden" name="id" value="<?= $currentId ?>">
      <input type="hidden" name="name" value="<?= e($_SESSION['admin_name'] ?? '') ?>">
      <input type="hidden" name="email" value="<?= e($_SESSION['admin_email'] ?? '') ?>">
      <input type="hidden" name="role" value="<?= e($_SESSION['admin_role'] ?? 'admin') ?>">
      <div class="form-group"><label>Yeni Şifre</label><input class="form-control" name="password" type="password" autocomplete="new-password" placeholder="En az 8 karakter" required></div>
      <button class="btn btn-teal" type="submit"><i class="fa-solid fa-key"></i> Şifreyi Güncelle</button>
    </form>
  </div>
</div>
<?php
$pageContent = ob_get_clean();
require dirname(__DIR__, 2) . '/layout.php';
