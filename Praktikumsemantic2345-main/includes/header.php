<?php
require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/ui.php'; login_required();
$B = BASE_URL; $uri = $_SERVER['SCRIPT_NAME'];
$menu = [
  ['index.php','Beranda','home',['admin','operator','pimpinan'],'Beranda'],
  ['dashboard.php','Dashboard','grid',['admin','operator','pimpinan'],'Dashboard'],
  ['dosen/','Data Dosen','users',['admin','operator','pimpinan'],'Data Dosen'],
  ['master/','Data Master','db',['admin'],'Data Master'],
  ['laporan/daftar_dosen.php','Laporan','file',['admin','operator','pimpinan'],'Laporan Daftar Dosen'],
];
$judul = 'Dashboard';
foreach (['/dosen/'=>'Data Dosen','/master/'=>'Data Master','/laporan/'=>'Laporan Daftar Dosen'] as $k=>$v) if (strpos($uri,$k)!==false) $judul=$v;
$u = $_SESSION['user'];
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($judul) ?> | SIM Dosen UM Bengkulu</title><link rel="stylesheet" href="<?= $B ?>/assets/css/app.css"></head>
<body><div class="app">
<aside class="side">
  <a class="brand" href="<?= $B ?>/"><?= logo() ?><span>SIM Dosen<small>UM Bengkulu</small></span></a>
  <nav class="nav">
  <?php foreach ($menu as $m) { if (!in_array($u['role'], $m[3], true)) continue;
      $on = $m[0] === 'index.php' ? false : ($m[0] === 'dashboard.php' ? basename($uri) === 'dashboard.php' : strpos($uri, '/' . rtrim(explode('.php', $m[0])[0], '/')) !== false); ?>
    <a href="<?= $B . '/' . $m[0] ?>" class="<?= $on ? 'on' : '' ?>"><?= icon($m[2]) ?><?= e($m[1]) ?></a>
  <?php } ?>
  </nav>
  <div class="nav out"><a href="<?= $B ?>/logout.php"><?= icon('out') ?>Keluar</a></div>
</aside>
<div class="mainwrap">
<header class="top"><button class="burger" onclick="document.body.classList.toggle('open')" aria-label="Menu"><?= icon('menu') ?></button>
  <h1><?= e($judul) ?></h1><span class="sp"></span>
  <div class="user"><span class="av"><?= e(inisial($u['nama'])) ?></span><span><?= e($u['nama']) ?><small><?= e($u['role']) ?></small></span></div>
</header>
<main class="content">
