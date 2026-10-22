<?php
require 'config/koneksi.php';
$_SESSION = []; session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;
