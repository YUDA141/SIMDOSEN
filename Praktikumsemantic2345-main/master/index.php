<?php
// CRUD generik untuk semua tabel referensi (khusus role admin)
require '../includes/auth.php'; role_required(['admin']);
$cfg = [
 'universitas'=>['Universitas',null], 'fakultas'=>['Fakultas',['universitas_id','universitas']],
 'prodi'=>['Program Studi',['fakultas_id','fakultas']], 'jabatan_fungsional'=>['Jabatan Fungsional',null],
 'kepangkatan'=>['Kepangkatan',null], 'pendidikan'=>['Pendidikan',null], 'rumpun_ilmu'=>['Rumpun Ilmu',null],
 'status_serdos'=>['Status Serdos',null], 'status_pegawai'=>['Status Pegawai',null],
 'ikatan_kerja'=>['Ikatan Kerja',null], 'status_aktivitas'=>['Status Aktivitas',null],
];
$t = $_GET['t'] ?? 'universitas';
if (!isset($cfg[$t])) exit('Tabel tidak dikenal');   // whitelist -> aman dari SQL injection
[$label, $parent] = $cfg[$t];
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $a = $_POST['aksi']; $id = (int)($_POST['id'] ?? 0); $nama = trim($_POST['nama'] ?? ''); $p = (int)($_POST['parent'] ?? 0);
        if ($a === 'tambah' && $nama) {
            if ($parent) $pdo->prepare("INSERT INTO $t(nama,{$parent[0]}) VALUES(?,?)")->execute([$nama,$p]);
            else $pdo->prepare("INSERT INTO $t(nama) VALUES(?)")->execute([$nama]);
        } elseif ($a === 'ubah' && $nama) {
            if ($parent) $pdo->prepare("UPDATE $t SET nama=?, {$parent[0]}=? WHERE id=?")->execute([$nama,$p,$id]);
            else $pdo->prepare("UPDATE $t SET nama=? WHERE id=?")->execute([$nama,$id]);
        } elseif ($a === 'hapus') $pdo->prepare("DELETE FROM $t WHERE id=?")->execute([$id]);
    } catch (PDOException $ex) { $msg = 'Gagal: data masih dipakai tabel lain atau tidak valid.'; }
}
$opsi = $parent ? $pdo->query("SELECT id,nama FROM {$parent[1]} ORDER BY nama")->fetchAll() : [];
$rows = $pdo->query("SELECT * FROM $t ORDER BY id")->fetchAll();
function sel($opsi, $v) { foreach ($opsi as $o) echo "<option value='{$o['id']}'".($o['id']==$v?' selected':'').">".e($o['nama'])."</option>"; }
require '../includes/header.php';
?>
<h2>Data Master: <?= e($label) ?></h2>
<div class="pills noprint"><?php foreach ($cfg as $k => $c) echo "<a href='?t=$k'".($k===$t?" class='on'":"").">".e($c[0])."</a>"; ?></div>
<?php if ($msg): ?><p class="err"><?= e($msg) ?></p><?php endif; ?>
<form method="post" style="display:flex;gap:6px"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="aksi" value="tambah">
<?php if ($parent): ?><select name="parent"><?php sel($opsi, 0); ?></select><?php endif; ?>
<input name="nama" placeholder="Nama baru" required><button class="btn">Tambah</button></form><br>
<table><tr><th>ID</th><?php if ($parent) echo '<th>Induk</th>'; ?><th>Nama</th><th>Aksi</th></tr>
<?php foreach ($rows as $r): ?><tr><form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<td><?= $r['id'] ?></td>
<?php if ($parent): ?><td><select name="parent"><?php sel($opsi, $r[$parent[0]]); ?></select></td><?php endif; ?>
<td><input name="nama" value="<?= e($r['nama']) ?>"></td>
<td><button class="btn" name="aksi" value="ubah">Simpan</button>
<button class="btn red" name="aksi" value="hapus" onclick="return confirm('Hapus?')">Hapus</button></td></form></tr><?php endforeach; ?></table>
<?php require '../includes/footer.php'; ?>
