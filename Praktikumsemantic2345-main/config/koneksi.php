<?php
session_start();
define('BASE_URL', '/simdosen');   // sesuaikan dgn nama folder di htdocs
try {
    $pdo = new PDO('mysql:host=localhost;dbname=simdosen;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) { http_response_code(500); exit('Koneksi database gagal.'); }

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf_token() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Token CSRF tidak valid.'); }
}
