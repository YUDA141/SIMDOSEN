<?php
require_once __DIR__ . '/../config/koneksi.php';

function login_required() {
    if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . '/login.php'); exit; }
}
function can(array $roles): bool {
    return !empty($_SESSION['user']) && in_array($_SESSION['user']['role'], $roles, true);
}
// RBAC: hentikan akses jika role tidak termasuk
function role_required(array $roles) {
    login_required();
    if (!can($roles)) { http_response_code(403); exit('Akses ditolak (403): role Anda tidak diizinkan.'); }
}
