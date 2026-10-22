<?php
require '../includes/auth.php'; role_required(['admin','operator']);
require '../includes/dosen_query.php';
$id = (int)($_GET['id'] ?? 0);
$d = array_fill_keys(DOSEN_FIELDS, '');
if ($id) { $st = $pdo->prepare('SELECT * FROM dosen WHERE id=?'); $st->execute([$id]); $d = $st->fetch() ?: exit('Data tidak ditemukan'); }
function pilih($pdo, $name, $label, $tbl, $val) {
    echo "<label>$label</label><select name='$name'><option value=''>-- pilih --</option>";
    foreach ($pdo->query("SELECT id, nama FROM $tbl ORDER BY nama") as $o)
        echo "<option value='{$o['id']}'" . ($o['id']==$val?' selected':'') . ">" . e($o['nama']) . "</option>";
    echo "</select>";
}
require '../includes/header.php';
?>
<h2><?= $id ? 'Edit' : 'Tambah' ?> Dosen</h2>
<form method="post" action="proses.php" style="max-width:520px">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= $id ?>">
<label>Nomor Registrasi</label><input name="nomor_registrasi" required value="<?= e($d['nomor_registrasi']) ?>">
<label>NUPTK</label><input name="nuptk" value="<?= e($d['nuptk']) ?>">
<label>Nama</label><input name="nama" required value="<?= e($d['nama']) ?>">
<label>NIP</label><input name="nip" value="<?= e($d['nip']) ?>">
<?php
pilih($pdo,'jabatan_id','Jabatan Fungsional','jabatan_fungsional',$d['jabatan_id']);
pilih($pdo,'kepangkatan_id','Kepangkatan','kepangkatan',$d['kepangkatan_id']);
pilih($pdo,'pendidikan_id','Pendidikan Terakhir','pendidikan',$d['pendidikan_id']);
pilih($pdo,'rumpun_id','Rumpun Ilmu','rumpun_ilmu',$d['rumpun_id']);
pilih($pdo,'serdos_id','Status Serdos','status_serdos',$d['serdos_id']);
pilih($pdo,'pegawai_id','Status Pegawai','status_pegawai',$d['pegawai_id']);
pilih($pdo,'ikatan_id','Ikatan Kerja','ikatan_kerja',$d['ikatan_id']);
pilih($pdo,'aktivitas_id','Status Aktivitas','status_aktivitas',$d['aktivitas_id']);
pilih($pdo,'prodi_id','Penempatan (Prodi)','prodi',$d['prodi_id']);
?><br><br><button class="btn">Simpan</button> <a href="./">Batal</a>
</form>
<?php require '../includes/footer.php'; ?>
