<?php
require 'includes/auth.php'; require 'includes/ui.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $pdo->prepare('SELECT u.*, r.nama AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE username=?');
    $st->execute([trim($_POST['username'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$u['id'], 'nama'=>$u['nama'], 'role'=>$u['role']];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Username atau password tidak cocok. Periksa lagi lalu coba masuk.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk | SIM Dosen UM Bengkulu</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css"></head><body>
<div class="login">
  <section class="login-l">
    <a class="brand" href="<?= BASE_URL ?>/"><?= logo(44) ?><span>SIM Dosen<small>Universitas Muhammadiyah Bengkulu</small></span></a>
    <h1>Satu pintu untuk seluruh data dosen kampus.</h1>
    <p>Masuk untuk mengelola profil, jabatan, kepangkatan, dan penempatan dosen di setiap program studi.</p>
  </section>
  <section class="login-r"><div class="box">
    <p style="margin:0 0 28px"><a href="<?= BASE_URL ?>/">Kembali ke beranda</a></p><h2>Masuk ke akun Anda</h2><p>Gunakan username yang diberikan administrator.</p>
    <?php if ($error): ?><p class="err" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <label for="u">Username</label><input id="u" name="username" required autofocus autocomplete="username">
      <label for="p">Password</label>
      <div class="pw"><input id="p" type="password" name="password" required autocomplete="current-password">
        <button type="button" aria-label="Tampilkan password" onclick="var i=document.getElementById('p');i.type=i.type=='password'?'text':'password'"><?= icon('eye') ?></button></div>
      <br><button class="btn" style="width:100%;justify-content:center;padding:12px">Masuk</button>
    </form>
    <div class="demo">Akun contoh:
      <?php foreach (['admin'=>'admin123','operator'=>'operator123','pimpinan'=>'pimpinan123'] as $a=>$b): ?>
        <button type="button" onclick="u.value='<?= $a ?>';p.value='<?= $b ?>'"><?= $a ?></button><?php endforeach; ?>
    </div>
  </div></section>
</div></body></html>
