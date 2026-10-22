<?php
require 'includes/auth.php';
require 'includes/ui.php';
require 'includes/dosen_query.php';
$masuk = !empty($_SESSION['user']);
$total  = (int)$pdo->query('SELECT COUNT(*) FROM dosen')->fetchColumn();
$nProdi = (int)$pdo->query('SELECT COUNT(*) FROM prodi')->fetchColumn();
$nFak   = (int)$pdo->query('SELECT COUNT(*) FROM fakultas')->fetchColumn();
$serdos = (int)$pdo->query("SELECT COUNT(*) FROM dosen d JOIN status_serdos s ON s.id=d.serdos_id WHERE s.nama='Sudah'")->fetchColumn();
$prodiList = $pdo->query('SELECT p.nama, f.nama fakultas, COUNT(d.id) jml FROM prodi p JOIN fakultas f ON f.id=p.fakultas_id LEFT JOIN dosen d ON d.prodi_id=p.id GROUP BY p.id, p.nama, f.nama ORDER BY f.nama, p.nama')->fetchAll();
$B = BASE_URL;
$pctSerdos = $total ? round($serdos / $total * 100) : 0;
$tj = $pdo->query('SELECT j.nama, COUNT(*) c FROM dosen d JOIN jabatan_fungsional j ON j.id=d.jabatan_id GROUP BY j.id ORDER BY c DESC LIMIT 1')->fetch();
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>SIM Dosen | Universitas Muhammadiyah Bengkulu</title>
  <link rel="stylesheet" href="<?= $B ?>/assets/css/app.css">
</head>

<body>
  <header class="pub-hero">
    <div class="wrap">
      <nav class="pnav"><a class="brand" href="<?= $B ?>/"><?= logo() ?><span>SIM Dosen<small>UM Bengkulu</small></span></a><span class="sp"></span>
        <a class="l" href="#peran">Peran</a><a class="l" href="#prodi">Program Studi</a><a class="l" href="#api">API</a>
        <a class="btn gold" href="<?= $B . ($masuk ? '/dashboard.php' : '/login.php') ?>"><?= $masuk ? 'Buka dashboard' : 'Masuk' ?></a>
      </nav>
      <div class="hero-grid">
        <div>
          <h1>Data dosen UM Bengkulu, rapi dari prodi sampai profil.</h1>
          <p>Catat jabatan fungsional, kepangkatan, sertifikasi, dan penempatan dosen sekali saja. Dashboard dan laporan langsung ikut terbarui.</p>
          <a class="btn gold" href="<?= $B . ($masuk ? '/dashboard.php' : '/login.php') ?>">Mulai kelola data</a>
          <a class="btn ghost" href="#prodi">Lihat program studi</a>
        </div>
        <div class="stack" aria-hidden="true">
          <div class="pcard"><span class="ic" style="background:#eaf1ff;color:#2563eb"><?= icon('users', 26) ?></span>
            <div><b><?= $total ?> dosen terdata</b><small>tersebar di <?= $nProdi ?> program studi</small></div>
          </div>
          <div class="pcard"><span class="ic" style="background:#dcfce7;color:#15803d"><?= icon('shield', 26) ?></span>
            <div><b><?= $pctSerdos ?>% sudah tersertifikasi</b><small><?= $serdos ?> dari <?= $total ?> dosen</small>
              <div class="mini"><i style="width:<?= $pctSerdos ?>%"></i></div>
            </div>
          </div>
          <div class="pcard"><span class="ic" style="background:#fff4d6;color:#b7791f"><?= icon('pulse', 26) ?></span>
            <div><b>Jabatan terbanyak</b><small><?= e($tj['nama'] ?? 'Belum ada data') ?><?= $tj ? ' · ' . $tj['c'] . ' dosen' : '' ?></small></div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="wrap">
    <div class="numbers">
      <?php foreach ([[$total, 'Dosen terdata'], [$nProdi, 'Program studi'], [$nFak, 'Fakultas'], [$serdos, 'Dosen tersertifikasi']] as [$n, $l]): ?>
        <div class="panel"><b><?= $n ?></b><span style="color:var(--mut)"><?= $l ?></span></div><?php endforeach; ?>
    </div>
  </div>

  <section class="sec" id="peran">
    <div class="wrap">
      <h2>Setiap peran punya ruang kerja sendiri</h2>
      <p>Hak akses diatur per peran, jadi data hanya bisa diubah oleh orang yang berwenang.</p>
      <div class="roles">
        <div class="role" style="background:linear-gradient(150deg,#1d4ed8,#0b1f4b)"><span class="ic"><?= icon('shield', 24) ?></span>
          <h3>Administrator</h3>
          <p>Mengatur seluruh sistem dan data acuan.</p>
          <ul>
            <li>Kelola fakultas, prodi, dan semua data master</li>
            <li>Tambah, ubah, hapus data dosen</li>
            <li>Akses laporan dan API</li>
          </ul>
        </div>
        <div class="role" style="background:linear-gradient(150deg,#0f9d74,#065f46)"><span class="ic"><?= icon('edit', 24) ?></span>
          <h3>Operator</h3>
          <p>Menjaga data dosen tetap mutakhir.</p>
          <ul>
            <li>Input dosen baru</li>
            <li>Perbarui jabatan, pangkat, dan status</li>
            <li>Cetak daftar dosen per prodi</li>
          </ul>
        </div>
        <div class="role" style="background:linear-gradient(150deg,#d99a0b,#92400e)"><span class="ic"><?= icon('pulse', 24) ?></span>
          <h3>Pimpinan</h3>
          <p>Memantau kondisi dosen tanpa mengubah data.</p>
          <ul>
            <li>Dashboard grafik ringkasan</li>
            <li>Daftar dan pencarian dosen</li>
            <li>Laporan siap cetak</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="sec" id="prodi">
    <div class="wrap">
      <h2>Program studi dan jumlah dosennya</h2>
      <p>Ringkasan sebaran dosen di setiap program studi.</p>
      <?php if (!$prodiList): ?><div class="panel">Belum ada program studi terdata.</div><?php else: ?>
        <div class="dir"><?php foreach ($prodiList as $i => $r): $c = PALET[$i % 6]; ?><div class="panel">
              <span class="ic" style="width:54px;height:54px;border-radius:16px;display:grid;place-items:center;flex:none;background:<?= $c ?>1f;color:<?= $c ?>"><?= icon('build', 26) ?></span>
              <div style="flex:1;min-width:0"><b><?= e($r['nama']) ?></b>
                <div style="color:var(--mut);font-size:13px">Fakultas <?= e($r['fakultas']) ?></div>
              </div>
              <div style="text-align:right"><b style="font-size:28px;line-height:1;display:block"><?= (int)$r['jml'] ?></b><span style="color:var(--mut);font-size:12px">dosen</span></div>
            </div><?php endforeach; ?></div><?php endif; ?>
    </div>
  </section>

  <section class="sec" id="api">
    <div class="wrap api">
      <div>
        <h2>Rest API</h2>
        <p style="color:var(--mut)">Data dosen tersedia dalam format JSON. Aplikasi lain mengirim API key untuk membaca atau mengelola data sesuai perannya.</p>
      </div>
      <div class="code"><em>GET</em> <?= e($B) ?>/api/dosen.php?q=contoh
        X-API-Key: <em>No token</em>

        {
        "status": true,
        "total": 1,
        "data": [{
        "id": 1,
        "nomor_registrasi": "REG001",
        "nama": "Contoh Dosen, M.Kom.",
        "jabatan": "Rektor",
        "status_serdos": "Sudah",
        "prodi": "Teknik Informatika"
        }]
        }</div>
    </div>
  </section>

  <footer class="pfoot">
    <div class="wrap" style="text-align:center">© <?= date('Y') ?> Sistem Informasi Dosen create by Arif Irawadi· Universitas Muhammadiyah Bengkulu</div>
  </footer>
</body>

</html>