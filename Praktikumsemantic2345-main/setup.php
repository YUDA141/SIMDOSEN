<?php
// Jalankan SEKALI lewat browser (http://localhost/simdosen/setup.php), lalu HAPUS file ini.
require 'config/koneksi.php';
$users = [['admin','admin123','Administrator',1],['operator','operator123','Operator Data',2],['pimpinan','pimpinan123','Pimpinan',3]];
$st = $pdo->prepare('INSERT IGNORE INTO users(username,password,nama,role_id,api_token) VALUES(?,?,?,?,?)');
foreach ($users as [$u,$p,$n,$r]) $st->execute([$u, password_hash($p, PASSWORD_DEFAULT), $n, $r, bin2hex(random_bytes(16))]);
echo 'User dibuat. Login: admin/admin123, operator/operator123, pimpinan/pimpinan123. <b>Hapus setup.php sekarang!</b><br>';
foreach ($pdo->query('SELECT username, api_token FROM users') as $r) echo e($r['username']).' token API: '.e($r['api_token']).'<br>';
