<?php
require '../includes/auth.php'; role_required(['admin','operator','pimpinan']);
require '../includes/dosen_query.php';
$q = trim($_GET['q'] ?? '');
$st = $pdo->prepare(DOSEN_SQL . " WHERE d.nama LIKE :q OR d.nip LIKE :q OR d.nuptk LIKE :q ORDER BY d.nama");
$st->execute([':q' => "%$q%"]);
$rows = $st->fetchAll();
$edit = can(['admin','operator']);
require '../includes/header.php';
?>
<h2>Data Dosen</h2>
<form class="noprint" style="display:flex;gap:6px"><input name="q" value="<?= e($q) ?>" placeholder="Cari nama / NIP / NUPTK">
<button class="btn">Cari</button><?php if ($edit): ?><a class="btn" href="form.php">+ Tambah</a><?php endif; ?></form><br>
<table><tr><th>No</th><th>No. Reg</th><th>NUPTK</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Pangkat</th><th>Pendidikan</th>
<th>Rumpun</th><th>Serdos</th><th>Pegawai</th><th>Ikatan</th><th>Aktivitas</th><th>Penempatan</th><?php if ($edit): ?><th>Aksi</th><?php endif; ?></tr>
<?php foreach ($rows as $i => $r): ?><tr>
<td><?= $i+1 ?></td><td><?= e($r['nomor_registrasi']) ?></td><td><?= e($r['nuptk']) ?></td><td><?= e($r['nama']) ?></td>
<td><?= e($r['nip']) ?></td><td><?= e($r['jabatan']) ?></td><td><?= e($r['kepangkatan']) ?></td><td><?= e($r['pendidikan']) ?></td>
<td><?= e($r['rumpun']) ?></td><td><?= e($r['status_serdos']) ?></td><td><?= e($r['status_pegawai']) ?></td>
<td><?= e($r['ikatan_kerja']) ?></td><td><?= e($r['status_aktivitas']) ?></td><td><?= e($r['prodi']) ?></td>
<?php if ($edit): ?><td><a class="btn" href="form.php?id=<?= $r['id'] ?>">Edit</a>
<form method="post" action="proses.php" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="aksi" value="hapus">
<input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn red">Hapus</button></form></td><?php endif; ?>
</tr><?php endforeach; ?></table>
<?php require '../includes/footer.php'; ?>
