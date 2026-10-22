<?php
require '../includes/auth.php'; role_required(['admin','operator','pimpinan']);
require '../includes/dosen_query.php';
$prodi = (int)($_GET['prodi'] ?? 0);
$st = $pdo->prepare(DOSEN_SQL . ($prodi ? ' WHERE d.prodi_id=?' : ' WHERE ?=0') . ' ORDER BY d.nama');
$st->execute([$prodi]);
$rows = $st->fetchAll();
$info = $rows[0] ?? null;
require '../includes/header.php';
?>
<h2>Daftar Dosen Aktif Universitas</h2>
<form class="noprint" style="display:flex;gap:6px"><select name="prodi"><option value="0">Semua Prodi</option>
<?php foreach ($pdo->query('SELECT id,nama FROM prodi ORDER BY nama') as $p) echo "<option value='{$p['id']}'".($p['id']==$prodi?' selected':'').">".e($p['nama'])."</option>"; ?>
</select><button class="btn">Tampilkan</button><button type="button" class="btn" onclick="window.print()">Cetak</button></form>
<?php if ($info): ?><p>Universitas : <?= e($info['universitas']) ?><br>
Fakultas : <?= $prodi ? e($info['fakultas']) : 'Semua' ?><br>Program Studi : <?= $prodi ? e($info['prodi']) : 'Semua' ?></p><?php endif; ?>
<table><tr><th>No</th><th>Nomor Registrasi</th><th>NUPTK</th><th>Nama</th><th>NIP</th><th>Jabatan Fungsional</th><th>Kepangkatan</th>
<th>Pendidikan Terakhir</th><th>Rumpun Ilmu</th><th>Status Serdos</th><th>Status Pegawai</th><th>Ikatan Kerja</th><th>Status Aktivitas</th><th>Penempatan</th></tr>
<?php foreach ($rows as $i => $r): ?><tr><td><?= $i+1 ?></td><td><?= e($r['nomor_registrasi']) ?></td><td><?= e($r['nuptk']) ?></td>
<td><?= e($r['nama']) ?></td><td><?= e($r['nip']) ?></td><td><?= e($r['jabatan']) ?></td><td><?= e($r['kepangkatan']) ?></td>
<td><?= e($r['pendidikan']) ?></td><td><?= e($r['rumpun']) ?></td><td><?= e($r['status_serdos']) ?></td><td><?= e($r['status_pegawai']) ?></td>
<td><?= e($r['ikatan_kerja']) ?></td><td><?= e($r['status_aktivitas']) ?></td><td><?= e($r['prodi']) ?></td></tr><?php endforeach; ?></table>
<?php require '../includes/footer.php'; ?>
