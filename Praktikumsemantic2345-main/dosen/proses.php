<?php
require '../includes/auth.php'; role_required(['admin','operator']);
require '../includes/dosen_query.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ./'); exit; }
csrf_check();
$id = (int)($_POST['id'] ?? 0);
try {
    if (($_POST['aksi'] ?? '') === 'hapus') {
        $pdo->prepare('DELETE FROM dosen WHERE id=?')->execute([$id]);
    } else {
        $v = [];
        foreach (DOSEN_FIELDS as $f) { $x = trim($_POST[$f] ?? ''); $v[$f] = $x === '' ? null : $x; }
        if (!$v['nomor_registrasi'] || !$v['nama'] || !$v['prodi_id']) exit('Field wajib belum lengkap.');
        if ($id) {
            $set = implode(',', array_map(fn($f) => "$f=:$f", DOSEN_FIELDS));
            $pdo->prepare("UPDATE dosen SET $set WHERE id=:id")->execute($v + ['id' => $id]);
        } else {
            $cols = implode(',', DOSEN_FIELDS); $ph = ':' . implode(',:', DOSEN_FIELDS);
            $pdo->prepare("INSERT INTO dosen($cols) VALUES($ph)")->execute($v);
        }
    }
} catch (PDOException $ex) { exit('Gagal menyimpan: data duplikat atau tidak valid.'); }
header('Location: ./');
