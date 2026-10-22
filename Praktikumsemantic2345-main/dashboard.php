<?php
require 'includes/auth.php'; login_required();
require 'includes/ui.php'; require 'includes/dosen_query.php';
$p = (int)($_GET['prodi'] ?? 0);               // filter program studi (0 = semua)
function rekap($pdo, $tbl, $fk, $p) {
    $on = $p ? ' AND d.prodi_id=' . (int)$p : '';
    return $pdo->query("SELECT t.nama, COUNT(d.id) jml FROM $tbl t LEFT JOIN dosen d ON d.$fk=t.id$on GROUP BY t.id, t.nama ORDER BY t.id")->fetchAll();
}
function n($pdo, $where, $p) {
    return (int)$pdo->query("SELECT COUNT(*) FROM dosen d LEFT JOIN status_aktivitas sa ON sa.id=d.aktivitas_id
      LEFT JOIN status_serdos ss ON ss.id=d.serdos_id LEFT JOIN pendidikan pd ON pd.id=d.pendidikan_id
      WHERE ($where)" . ($p ? ' AND d.prodi_id=' . (int)$p : ''))->fetchColumn();
}
$total = n($pdo, '1=1', $p);
$aktif = n($pdo, "sa.nama='Aktif'", $p);  $serdos = n($pdo, "ss.nama='Sudah'", $p);  $s3 = n($pdo, "pd.nama='S3'", $p);
$noNip = n($pdo, "d.nip IS NULL OR d.nip=''", $p); $noNuptk = n($pdo, "d.nuptk IS NULL OR d.nuptk=''", $p); $noJab = n($pdo, 'd.jabatan_id IS NULL', $p);
$pc = fn($a) => $total ? round($a / $total * 100) : 0;
$prodiList = $pdo->query('SELECT id, nama FROM prodi ORDER BY nama')->fetchAll();
$namaProdi = ''; foreach ($prodiList as $x) if ($x['id'] == $p) $namaProdi = $x['nama'];

// data grafik
$aktv = rekap($pdo, 'status_aktivitas', 'aktivitas_id', $p); $jab = rekap($pdo, 'jabatan_fungsional', 'jabatan_id', $p);
$srd = rekap($pdo, 'status_serdos', 'serdos_id', $p);        $pend = rekap($pdo, 'pendidikan', 'pendidikan_id', $p);
$peg = rekap($pdo, 'status_pegawai', 'pegawai_id', $p);      $rum = rekap($pdo, 'rumpun_ilmu', 'rumpun_id', $p);
$pkt = rekap($pdo, 'kepangkatan', 'kepangkatan_id', $p);     $prd = rekap($pdo, 'prodi', 'prodi_id', 0);

// heatmap prodi x jabatan fungsional
$jabNama = array_column($jab, 'nama'); $mx = []; $maxCell = 1;
foreach ($pdo->query('SELECT p.nama prodi, j.nama jab, COUNT(d.id) c FROM prodi p CROSS JOIN jabatan_fungsional j
   LEFT JOIN dosen d ON d.prodi_id=p.id AND d.jabatan_id=j.id GROUP BY p.id, j.id ORDER BY p.nama, j.id') as $r) {
    $mx[$r['prodi']][$r['jab']] = (int)$r['c']; $maxCell = max($maxCell, (int)$r['c']);
}

// ringkasan otomatis
$top = fn($rows) => array_reduce($rows, fn($c, $r) => (!$c || $r['jml'] > $c['jml']) ? $r : $c);
$ins = [];
if ($total) {
    $ins[] = [0, "$serdos dari $total dosen (" . $pc($serdos) . '%) sudah tersertifikasi; ' . ($total - $serdos) . ' dosen belum.'];
    $tj = $top($jab); if ($tj && $tj['jml']) $ins[] = [0, "Jabatan fungsional terbanyak adalah {$tj['nama']} ({$tj['jml']} dosen)."];
    if (!$p) { $tp = $top($prd); if ($tp && $tp['jml']) $ins[] = [0, "Program studi dengan dosen terbanyak: {$tp['nama']} ({$tp['jml']} dosen)."]; }
    $ins[] = [0, "Dosen berpendidikan S3: $s3 orang (" . $pc($s3) . '%).'];
    if ($noNip) $ins[] = [1, "$noNip dosen belum memiliki NIP."];
    if ($noNuptk) $ins[] = [1, "$noNuptk dosen belum memiliki NUPTK."];
    if ($noJab) $ins[] = [1, "$noJab dosen belum memiliki jabatan fungsional."];
} else $ins[] = [1, 'Belum ada data dosen untuk filter ini.'];

$terbaru = $pdo->prepare(DOSEN_SQL . ($p ? ' WHERE d.prodi_id=?' : ' WHERE ?=0') . ' ORDER BY d.id DESC LIMIT 6');
$terbaru->execute([$p]); $terbaru = $terbaru->fetchAll();
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')] . ', ' . date('d/m/Y');
require 'includes/header.php';
?>
<section class="hello"><h2>Halo, <?= e($_SESSION['user']['nama']) ?></h2>
<p><?= $hari ?>. Menampilkan <?= $total ?> dosen<?= $namaProdi ? ' di ' . e($namaProdi) : ' dari semua program studi' ?>.</p></section>

<form class="filter" method="get"><label for="f" style="margin:0">Filter program studi</label>
  <select id="f" name="prodi" onchange="this.form.submit()"><option value="0">Semua program studi</option>
  <?php foreach ($prodiList as $x) echo "<option value='{$x['id']}'" . ($x['id'] == $p ? ' selected' : '') . '>' . e($x['nama']) . '</option>'; ?></select>
  <?php if ($p): ?><a href="dashboard.php">Reset filter</a><?php endif; ?></form>

<div class="grid g4">
<?php foreach ([['Total dosen',$total,'',$total ? '100% dari data' : '','users','#eaf1ff','#2563eb'],
  ['Berstatus aktif',$aktif,'',$pc($aktif).'% dari total','pulse','#ede9fe','#6d28d9'],
  ['Sudah sertifikasi',$serdos,'',$pc($serdos).'% dari total','shield','#dcfce7','#15803d'],
  ['Pendidikan S3',$s3,'',$pc($s3).'% dari total','build','#fff4d6','#b7791f']] as [$l,$v,$x,$sub,$i,$bg,$fg]): ?>
  <div class="panel stat"><span class="ic" style="background:<?= $bg ?>;color:<?= $fg ?>"><?= icon($i, 24) ?></span>
  <div><b><?= $v ?></b><span><?= $l ?></span><br><span style="font-size:12px"><?= $sub ?></span></div></div>
<?php endforeach; ?></div>

<div class="gauto">
  <?= chartpanel('Status aktivitas', $aktv, 'donut') ?>
  <?= chartpanel('Jabatan fungsional', $jab, 'cols') ?>
  <?= chartpanel('Status sertifikasi dosen', $srd, 'donut') ?>
  <?= chartpanel('Pendidikan terakhir', $pend, 'bars') ?>
  <?= chartpanel('Status kepegawaian', $peg, 'donut') ?>
  <?= chartpanel('Rumpun ilmu', $rum, 'bars') ?>
  <?= chartpanel('Kepangkatan', $pkt, 'cols') ?>
  <?= chartpanel('Dosen per program studi (semua prodi)', $prd, 'bars') ?>
</div>

<div class="grid" style="grid-template-columns:2fr 1fr">
  <div class="panel"><h3>Peta sebaran: program studi × jabatan fungsional</h3>
    <table class="heat"><tr><th>Program studi</th><?php foreach ($jabNama as $j) echo '<th>' . e($j) . '</th>'; ?></tr>
    <?php foreach ($mx as $pr => $cells): ?><tr><td><?= e($pr) ?></td>
      <?php foreach ($jabNama as $j): $c = $cells[$j] ?? 0; ?><td style="background:rgba(37,99,235,<?= round($c / $maxCell * .75, 2) ?>);color:<?= $c / $maxCell > .5 ? '#fff' : 'inherit' ?>"><?= $c ?></td><?php endforeach; ?></tr>
    <?php endforeach; ?></table></div>
  <div class="panel"><h3>Ringkasan analisis</h3><ul class="ins">
    <?php foreach ($ins as [$w, $t]): ?><li class="<?= $w ? 'warn' : '' ?>"><?= icon($w ? 'pulse' : 'shield', 18) ?><span><?= e($t) ?></span></li><?php endforeach; ?></ul>
    <h3 style="margin-top:20px">Kelengkapan data</h3>
    <?php foreach ([['NIP terisi',$total-$noNip],['NUPTK terisi',$total-$noNuptk],['Jabatan terisi',$total-$noJab]] as [$l,$v]): ?>
      <div class="meter"><div><span><?= $l ?></span><b><?= $pc($v) ?>%</b></div><div class="trk" style="display:block"><div class="fill" style="--w:<?= $pc($v) ?>%;background:var(--mint)"></div></div></div>
    <?php endforeach; ?></div>
</div>

<div class="panel"><h3>Dosen terbaru</h3>
<?php if (!$terbaru): ?><p>Belum ada data dosen. <a href="dosen/form.php">Tambah dosen pertama</a>.</p><?php else: ?>
<table><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Program studi</th><th>Status</th></tr>
<?php foreach ($terbaru as $r): ?><tr>
<td><div style="display:flex;gap:10px;align-items:center"><span class="av"><?= e(inisial($r['nama'])) ?></span><?= e($r['nama']) ?></div></td>
<td><?= e($r['nip']) ?></td><td><?= e($r['jabatan']) ?></td><td><?= e($r['prodi']) ?></td><td><span class="chip"><?= e($r['status_aktivitas']) ?></span></td></tr>
<?php endforeach; ?></table><?php endif; ?></div>
<script>
document.querySelectorAll('.cp').forEach(function(p){p.querySelectorAll('.seg button').forEach(function(b){b.onclick=function(){
  p.querySelectorAll('.seg button').forEach(function(x){x.classList.toggle('on',x===b)});
  p.querySelectorAll('.v').forEach(function(v){v.hidden=!v.classList.contains('v-'+b.dataset.v)});};});});
</script>
<?php require 'includes/footer.php'; ?>
