<?php
// Query JOIN yang dipakai halaman daftar, laporan, dan API
const DOSEN_SQL = "SELECT d.*, jf.nama AS jabatan, kp.nama AS kepangkatan, pd.nama AS pendidikan, ri.nama AS rumpun,
  ss.nama AS status_serdos, sp.nama AS status_pegawai, ik.nama AS ikatan_kerja, sa.nama AS status_aktivitas,
  pr.nama AS prodi, f.nama AS fakultas, u.nama AS universitas
  FROM dosen d
  LEFT JOIN jabatan_fungsional jf ON jf.id=d.jabatan_id
  LEFT JOIN kepangkatan kp ON kp.id=d.kepangkatan_id
  LEFT JOIN pendidikan pd ON pd.id=d.pendidikan_id
  LEFT JOIN rumpun_ilmu ri ON ri.id=d.rumpun_id
  LEFT JOIN status_serdos ss ON ss.id=d.serdos_id
  LEFT JOIN status_pegawai sp ON sp.id=d.pegawai_id
  LEFT JOIN ikatan_kerja ik ON ik.id=d.ikatan_id
  LEFT JOIN status_aktivitas sa ON sa.id=d.aktivitas_id
  JOIN prodi pr ON pr.id=d.prodi_id
  JOIN fakultas f ON f.id=pr.fakultas_id
  JOIN universitas u ON u.id=f.universitas_id";
const DOSEN_FIELDS = ['nomor_registrasi','nuptk','nip','nama','jabatan_id','kepangkatan_id','pendidikan_id',
  'rumpun_id','serdos_id','pegawai_id','ikatan_id','aktivitas_id','prodi_id'];
