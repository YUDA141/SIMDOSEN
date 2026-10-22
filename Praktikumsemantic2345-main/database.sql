CREATE DATABASE IF NOT EXISTS simdosen CHARACTER SET utf8mb4;
USE simdosen;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS dosen, users, roles, prodi, fakultas, universitas, jabatan_fungsional, kepangkatan, pendidikan, rumpun_ilmu, status_serdos, status_pegawai, ikatan_kerja, status_aktivitas;
SET FOREIGN_KEY_CHECKS=1;

-- Tabel referensi (hasil normalisasi: kolom berulang dipisah jadi tabel sendiri)
CREATE TABLE universitas (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL);
CREATE TABLE fakultas (id INT AUTO_INCREMENT PRIMARY KEY, universitas_id INT NOT NULL, nama VARCHAR(100) NOT NULL,
  FOREIGN KEY (universitas_id) REFERENCES universitas(id));
CREATE TABLE prodi (id INT AUTO_INCREMENT PRIMARY KEY, fakultas_id INT NOT NULL, nama VARCHAR(100) NOT NULL,
  FOREIGN KEY (fakultas_id) REFERENCES fakultas(id));
CREATE TABLE jabatan_fungsional (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL);
CREATE TABLE kepangkatan (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL);
CREATE TABLE pendidikan (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) NOT NULL);
CREATE TABLE rumpun_ilmu (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL);
CREATE TABLE status_serdos (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) NOT NULL);
CREATE TABLE status_pegawai (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) NOT NULL);
CREATE TABLE ikatan_kerja (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) NOT NULL);
CREATE TABLE status_aktivitas (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(50) NOT NULL);

-- Tabel utama
CREATE TABLE dosen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_registrasi VARCHAR(30) NOT NULL UNIQUE,
  nuptk VARCHAR(20) UNIQUE,
  nip VARCHAR(25) UNIQUE,
  nama VARCHAR(150) NOT NULL,
  jabatan_id INT, kepangkatan_id INT, pendidikan_id INT, rumpun_id INT,
  serdos_id INT, pegawai_id INT, ikatan_id INT, aktivitas_id INT,
  prodi_id INT NOT NULL,            -- kolom "Penempatan"
  FOREIGN KEY (jabatan_id) REFERENCES jabatan_fungsional(id),
  FOREIGN KEY (kepangkatan_id) REFERENCES kepangkatan(id),
  FOREIGN KEY (pendidikan_id) REFERENCES pendidikan(id),
  FOREIGN KEY (rumpun_id) REFERENCES rumpun_ilmu(id),
  FOREIGN KEY (serdos_id) REFERENCES status_serdos(id),
  FOREIGN KEY (pegawai_id) REFERENCES status_pegawai(id),
  FOREIGN KEY (ikatan_id) REFERENCES ikatan_kerja(id),
  FOREIGN KEY (aktivitas_id) REFERENCES status_aktivitas(id),
  FOREIGN KEY (prodi_id) REFERENCES prodi(id)
);

-- Autentikasi & RBAC
CREATE TABLE roles (id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(30) NOT NULL UNIQUE);
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL, nama VARCHAR(100) NOT NULL, role_id INT NOT NULL,
  api_token VARCHAR(64) UNIQUE, FOREIGN KEY (role_id) REFERENCES roles(id));

-- Data awal
INSERT INTO roles(nama) VALUES ('admin'),('operator'),('pimpinan');
INSERT INTO universitas(nama) VALUES ('Universitas Muhammadiyah Bengkulu');
INSERT INTO fakultas(universitas_id,nama) VALUES (1,'Teknik'),(1,'Ekonomi dan Bisnis');
INSERT INTO prodi(fakultas_id,nama) VALUES (1,'Teknik Informatika'),(1,'Teknik Sipil'),(2,'Manajemen');
INSERT INTO jabatan_fungsional(nama) VALUES ('Tenaga Pengajar'),('Asisten Ahli'),('Rektor'),('Rektor Kepala'),('Guru Besar');
INSERT INTO kepangkatan(nama) VALUES ('III/a'),('III/b'),('III/c'),('III/d'),('IV/a'),('IV/b');
INSERT INTO pendidikan(nama) VALUES ('S2'),('S3');
INSERT INTO rumpun_ilmu(nama) VALUES ('Ilmu Komputer'),('Teknik'),('Ekonomi');
INSERT INTO status_serdos(nama) VALUES ('Sudah'),('Belum');
INSERT INTO status_pegawai(nama) VALUES ('PNS'),('Dosen Tetap Yayasan'),('Dosen Tidak Tetap');
INSERT INTO ikatan_kerja(nama) VALUES ('Dosen Tetap'),('Dosen Tidak Tetap');
INSERT INTO status_aktivitas(nama) VALUES ('Aktif'),('Tugas Belajar'),('Cuti'),('Non Aktif');
INSERT INTO dosen(nomor_registrasi,nuptk,nip,nama,jabatan_id,kepangkatan_id,pendidikan_id,rumpun_id,serdos_id,pegawai_id,ikatan_id,aktivitas_id,prodi_id)
VALUES ('REG001','1234567890123456','198001012005011001','Contoh Dosen, M.Kom.',3,3,1,1,1,2,1,1,1);
-- User dibuat lewat setup.php (agar password di-hash dengan password_hash PHP)
