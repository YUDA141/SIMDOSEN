CARA MENJALANKAN (XAMPP/Laragon)
1. Salin folder "simdosen" ke htdocs (XAMPP) atau www (Laragon).
2. Buka phpMyAdmin -> Import -> pilih database.sql (otomatis membuat database "simdosen").
3. Cek config/koneksi.php (user/password MySQL, BASE_URL).
4. Buka http://localhost/simdosen/setup.php sekali untuk membuat user, lalu HAPUS setup.php.
5. Buka http://localhost/simdosen/  -> Login.
Akun: admin/admin123 (semua akses), operator/operator123 (CRUD dosen), pimpinan/pimpinan123 (lihat + laporan).

CONTOH TES API (ganti TOKEN dari halaman setup.php):
curl -H "X-API-Key: TOKEN" http://localhost/simdosen/api/dosen.php
curl -X POST -H "X-API-Key: TOKEN" -H "Content-Type: application/json" -d '{"nomor_registrasi":"REG002","nama":"Dosen Baru","prodi_id":1}' http://localhost/simdosen/api/dosen.php
