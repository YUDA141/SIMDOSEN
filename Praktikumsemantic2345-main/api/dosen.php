<?php
/* REST API Dosen
   Auth : header "X-API-Key: <token>" (token ada di tabel users.api_token) atau sesi login.
   GET    /api/dosen.php            -> semua dosen   (?q=cari, ?prodi_id=1)
   GET    /api/dosen.php?id=1       -> satu dosen
   POST   /api/dosen.php            -> tambah (body JSON)      [admin, operator]
   PUT    /api/dosen.php?id=1       -> ubah   (body JSON)      [admin, operator]
   DELETE /api/dosen.php?id=1       -> hapus                   [admin, operator] */
require '../config/koneksi.php';
require '../includes/dosen_query.php';
header('Content-Type: application/json; charset=utf-8');
function out($code, $data) { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); exit; }

$role = $_SESSION['user']['role'] ?? null;
$key = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($key) {
    $st = $pdo->prepare('SELECT r.nama FROM users u JOIN roles r ON r.id=u.role_id WHERE u.api_token=?');
    $st->execute([$key]); $role = $st->fetchColumn() ?: null;
}
if (!$role) out(401, ['status'=>false, 'message'=>'Unauthorized: API key tidak valid']);

$m = $_SERVER['REQUEST_METHOD']; $id = (int)($_GET['id'] ?? 0);
if ($m !== 'GET' && !in_array($role, ['admin','operator'], true)) out(403, ['status'=>false,'message'=>'Forbidden: role tidak diizinkan']);
$body = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($m === 'GET') {
        if ($id) {
            $st = $pdo->prepare(DOSEN_SQL . ' WHERE d.id=?'); $st->execute([$id]);
            $r = $st->fetch(); $r ? out(200, ['status'=>true,'data'=>$r]) : out(404, ['status'=>false,'message'=>'Tidak ditemukan']);
        }
        $q = '%' . ($_GET['q'] ?? '') . '%'; $p = (int)($_GET['prodi_id'] ?? 0);
        $st = $pdo->prepare(DOSEN_SQL . ' WHERE (d.nama LIKE ? OR d.nip LIKE ? OR d.nuptk LIKE ?)' . ($p ? ' AND d.prodi_id=?' : '') . ' ORDER BY d.nama');
        $st->execute($p ? [$q,$q,$q,$p] : [$q,$q,$q]);
        $rows = $st->fetchAll(); out(200, ['status'=>true,'total'=>count($rows),'data'=>$rows]);
    }
    if ($m === 'POST') {
        if (empty($body['nomor_registrasi']) || empty($body['nama']) || empty($body['prodi_id'])) out(422, ['status'=>false,'message'=>'nomor_registrasi, nama, prodi_id wajib']);
        $v = []; foreach (DOSEN_FIELDS as $f) $v[$f] = ($body[$f] ?? '') === '' ? null : $body[$f];
        $pdo->prepare('INSERT INTO dosen(' . implode(',', DOSEN_FIELDS) . ') VALUES(:' . implode(',:', DOSEN_FIELDS) . ')')->execute($v);
        out(201, ['status'=>true,'message'=>'Dosen ditambahkan','id'=>(int)$pdo->lastInsertId()]);
    }
    if ($m === 'PUT') {
        if (!$id) out(400, ['status'=>false,'message'=>'Parameter id wajib']);
        $set = []; $v = ['id'=>$id];
        foreach (DOSEN_FIELDS as $f) if (array_key_exists($f, $body)) { $set[] = "$f=:$f"; $v[$f] = $body[$f] === '' ? null : $body[$f]; }
        if (!$set) out(422, ['status'=>false,'message'=>'Tidak ada field untuk diubah']);
        $s = $pdo->prepare('UPDATE dosen SET ' . implode(',', $set) . ' WHERE id=:id'); $s->execute($v);
        out(200, ['status'=>true,'message'=>'Dosen diperbarui']);
    }
    if ($m === 'DELETE') {
        if (!$id) out(400, ['status'=>false,'message'=>'Parameter id wajib']);
        $s = $pdo->prepare('DELETE FROM dosen WHERE id=?'); $s->execute([$id]);
        $s->rowCount() ? out(200, ['status'=>true,'message'=>'Dosen dihapus']) : out(404, ['status'=>false,'message'=>'Tidak ditemukan']);
    }
    out(405, ['status'=>false,'message'=>'Method tidak didukung']);
} catch (PDOException $ex) { out(400, ['status'=>false,'message'=>'Data duplikat atau tidak valid']); }
