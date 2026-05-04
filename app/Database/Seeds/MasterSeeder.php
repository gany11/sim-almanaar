<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        /* `db_sim_almanaar`.`peran` */
        $peran = array(
            array('id_peran' => '1','nama' => 'Administrator'),
            array('id_peran' => '2','nama' => 'Ketua DKM'),
            array('id_peran' => '3','nama' => 'Sekertaris'),
            array('id_peran' => '4','nama' => 'Bendahara'),
            array('id_peran' => '5','nama' => 'Bidang Ibadah & Dakwah')
        );

        foreach ($peran as $data) {
            $this->db->table('peran')->replace($data);
        }
        
        /* `db_sim_almanaar`.`akun` */
        $akun = array(
            array('id_akun' => '1','id_peran' => '1','username' => 'administrator','password' => '$2y$10$5FZ5rL7ZO9D.nwzgHz8E6OXwVaLK95BTZmrrmJhoqVjpJcO9kzWRy','nama' => 'Gany Andisa','email' => 'ganylagi@gmail.com','telepon' => '087654321099','status' => 'aktif','created_at' => NULL,'updated_at' => '2026-04-16 16:04:58','deleted_at' => NULL),
            array('id_akun' => '2','id_peran' => '2','username' => 'ketuadkm','password' => '$2y$10$Jcjhsp2vCndL31rGkKK6Guhne3pjQ6Iu95tfhQxCOHmnWIXsZuUka','nama' => 'Gany Andisa','email' => 'tugasgany@gmail.com','telepon' => '087654321099','status' => 'aktif','created_at' => '2026-04-16 14:06:32','updated_at' => '2026-04-22 20:50:53','deleted_at' => NULL),
            array('id_akun' => '3','id_peran' => '3','username' => 'sekertaris','password' => '$2y$10$faqppwHCJ7WrOyNLc1slQ.sFUlfQUZCAwrj70u1hSQwlwXcI.Pj2e','nama' => 'Gany Andisa','email' => 'workgany@gmail.com','telepon' => '087654321099','status' => 'aktif','created_at' => '2026-04-16 14:07:50','updated_at' => '2026-04-17 11:16:48','deleted_at' => NULL),
            array('id_akun' => '4','id_peran' => '1','username' => 'bendahara','password' => '$2y$10$eYncXzZJXMk4ihPAYbpwXeLo4N//gd71WXN8kW801WFiiBO3hF/ta','nama' => 'Gany Andisa','email' => 'ganyandisa11@gmail.com','telepon' => '087654321099','status' => 'aktif','created_at' => '2026-04-16 16:48:42','updated_at' => '2026-04-16 16:48:42','deleted_at' => NULL)
        );

        foreach ($akun as $data) {
            $this->db->table('akun')->replace($data);
        }

        /* `db_sim_almanaar`.`carousel` */
        $carousel = array(
        );

        foreach ($carousel as $data) {
            $this->db->table('carousel')->replace($data);
        }

        /* `db_sim_almanaar`.`jenis_publikasi` */
        $jenis_publikasi = array(
            array('id_jenis_publikasi' => '1','jenis_publikasi' => 'Berita'),
            array('id_jenis_publikasi' => '2','jenis_publikasi' => 'Artikel')
        );

        foreach ($jenis_publikasi as $data) {
            $this->db->table('jenis_publikasi')->replace($data);
        }

        /* `db_sim_almanaar`.`kategori_keuangan` */
        $kategori_keuangan = array(
            array('id_kategori_keuangan' => '1','kategori' => 'Kas Masjid'),
            array('id_kategori_keuangan' => '2','kategori' => 'Kas Perawatan & Renovasi'),
            array('id_kategori_keuangan' => '3','kategori' => 'Kas Yatim'),
            array('id_kategori_keuangan' => '4','kategori' => 'Kas PKU')
        );

        foreach ($kategori_keuangan as $data) {
            $this->db->table('kategori_keuangan')->replace($data);
        }

        /* `db_sim_almanaar`.`keuangan` */
        $keuangan = array(
        );

        foreach ($keuangan as $data) {
            $this->db->table('keuangan')->replace($data);
        }

        /* `db_sim_almanaar`.`khgt` */
        $khgt = array(
            array('masehi' => '2026-01-01','hijriah' => '1447-07-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-02','hijriah' => '1447-07-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-03','hijriah' => '1447-07-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-04','hijriah' => '1447-07-15','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-05','hijriah' => '1447-07-16','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-06','hijriah' => '1447-07-17','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-07','hijriah' => '1447-07-18','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-08','hijriah' => '1447-07-19','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-09','hijriah' => '1447-07-20','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-10','hijriah' => '1447-07-21','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-11','hijriah' => '1447-07-22','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-12','hijriah' => '1447-07-23','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-13','hijriah' => '1447-07-24','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-14','hijriah' => '1447-07-25','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-15','hijriah' => '1447-07-26','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-16','hijriah' => '1447-07-27','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-17','hijriah' => '1447-07-28','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-18','hijriah' => '1447-07-29','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-19','hijriah' => '1447-07-30','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-20','hijriah' => '1447-08-01','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-21','hijriah' => '1447-08-02','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-22','hijriah' => '1447-08-03','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-23','hijriah' => '1447-08-04','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-24','hijriah' => '1447-08-05','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-25','hijriah' => '1447-08-06','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-26','hijriah' => '1447-08-07','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-27','hijriah' => '1447-08-08','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-28','hijriah' => '1447-08-09','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-29','hijriah' => '1447-08-10','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-30','hijriah' => '1447-08-11','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-01-31','hijriah' => '1447-08-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-01','hijriah' => '1447-08-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-02','hijriah' => '1447-08-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-03','hijriah' => '1447-08-15','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-04','hijriah' => '1447-08-16','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-05','hijriah' => '1447-08-17','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-06','hijriah' => '1447-08-18','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-07','hijriah' => '1447-08-19','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-08','hijriah' => '1447-08-20','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-09','hijriah' => '1447-08-21','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-10','hijriah' => '1447-08-22','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-11','hijriah' => '1447-08-23','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-12','hijriah' => '1447-08-24','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-13','hijriah' => '1447-08-25','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-14','hijriah' => '1447-08-26','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-15','hijriah' => '1447-08-27','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-16','hijriah' => '1447-08-28','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-17','hijriah' => '1447-08-29','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-18','hijriah' => '1447-09-01','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-19','hijriah' => '1447-09-02','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-20','hijriah' => '1447-09-03','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-21','hijriah' => '1447-09-04','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-22','hijriah' => '1447-09-05','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-23','hijriah' => '1447-09-06','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-24','hijriah' => '1447-09-07','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-25','hijriah' => '1447-09-08','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-26','hijriah' => '1447-09-09','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-27','hijriah' => '1447-09-10','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-02-28','hijriah' => '1447-09-11','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-01','hijriah' => '1447-09-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-02','hijriah' => '1447-09-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-03','hijriah' => '1447-09-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-04','hijriah' => '1447-09-15','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-05','hijriah' => '1447-09-16','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-06','hijriah' => '1447-09-17','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-07','hijriah' => '1447-09-18','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-08','hijriah' => '1447-09-19','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-09','hijriah' => '1447-09-20','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-10','hijriah' => '1447-09-21','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-11','hijriah' => '1447-09-22','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-12','hijriah' => '1447-09-23','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-13','hijriah' => '1447-09-24','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-14','hijriah' => '1447-09-25','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-15','hijriah' => '1447-09-26','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-16','hijriah' => '1447-09-27','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-17','hijriah' => '1447-09-28','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-18','hijriah' => '1447-09-29','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-19','hijriah' => '1447-09-30','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-20','hijriah' => '1447-10-01','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-21','hijriah' => '1447-10-02','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-22','hijriah' => '1447-10-03','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-23','hijriah' => '1447-10-04','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-24','hijriah' => '1447-10-05','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-25','hijriah' => '1447-10-06','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-26','hijriah' => '1447-10-07','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-27','hijriah' => '1447-10-08','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-28','hijriah' => '1447-10-09','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-29','hijriah' => '1447-10-10','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-30','hijriah' => '1447-10-11','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-03-31','hijriah' => '1447-10-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-01','hijriah' => '1447-10-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-02','hijriah' => '1447-10-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-03','hijriah' => '1447-10-15','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-04','hijriah' => '1447-10-16','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-05','hijriah' => '1447-10-17','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-06','hijriah' => '1447-10-18','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-07','hijriah' => '1447-10-19','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-08','hijriah' => '1447-10-20','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-09','hijriah' => '1447-10-21','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-10','hijriah' => '1447-10-22','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-11','hijriah' => '1447-10-23','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-12','hijriah' => '1447-10-24','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-13','hijriah' => '1447-10-25','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-14','hijriah' => '1447-10-26','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-15','hijriah' => '1447-10-27','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-16','hijriah' => '1447-10-28','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-17','hijriah' => '1447-10-29','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-18','hijriah' => '1447-11-01','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-19','hijriah' => '1447-11-02','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-20','hijriah' => '1447-11-03','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-21','hijriah' => '1447-11-04','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-22','hijriah' => '1447-11-05','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-23','hijriah' => '1447-11-06','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-24','hijriah' => '1447-11-07','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-25','hijriah' => '1447-11-08','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-26','hijriah' => '1447-11-09','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-27','hijriah' => '1447-11-10','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-28','hijriah' => '1447-11-11','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-29','hijriah' => '1447-11-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-04-30','hijriah' => '1447-11-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-01','hijriah' => '1447-11-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-02','hijriah' => '1447-11-15','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-03','hijriah' => '1447-11-16','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-04','hijriah' => '1447-11-17','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-05','hijriah' => '1447-11-18','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-06','hijriah' => '1447-11-19','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-07','hijriah' => '1447-11-20','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-08','hijriah' => '1447-11-21','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-09','hijriah' => '1447-11-22','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-10','hijriah' => '1447-11-23','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-11','hijriah' => '1447-11-24','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-12','hijriah' => '1447-11-25','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-13','hijriah' => '1447-11-26','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-14','hijriah' => '1447-11-27','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-15','hijriah' => '1447-11-28','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-16','hijriah' => '1447-11-29','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-17','hijriah' => '1447-11-30','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-18','hijriah' => '1447-12-01','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-19','hijriah' => '1447-12-02','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-20','hijriah' => '1447-12-03','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-21','hijriah' => '1447-12-04','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-22','hijriah' => '1447-12-05','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-23','hijriah' => '1447-12-06','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-24','hijriah' => '1447-12-07','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-25','hijriah' => '1447-12-08','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-26','hijriah' => '1447-12-09','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-27','hijriah' => '1447-12-10','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-28','hijriah' => '1447-12-11','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-29','hijriah' => '1447-12-12','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-30','hijriah' => '1447-12-13','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('masehi' => '2026-05-31','hijriah' => '1447-12-14','created_at' => NULL,'created_by' => NULL,'updated_at' => NULL,'updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL)
        );

        foreach ($khgt as $data) {
            $this->db->table('khgt')->replace($data);
        }

        /* `db_sim_almanaar`.`laporan_mingguan` */
        $laporan_mingguan = array(
        );

        foreach ($laporan_mingguan as $data) {
            $this->db->table('laporan_mingguan')->replace($data);
        }

        /* `db_sim_almanaar`.`migrations` */
        $migrations = array(
            array('id' => '9','version' => '2026-03-31-075832','class' => 'App\\Database\\Migrations\\CreatePeran','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '10','version' => '2026-03-31-075833','class' => 'App\\Database\\Migrations\\CreateAkun','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '11','version' => '2026-03-31-075939','class' => 'App\\Database\\Migrations\\CreateToken','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '12','version' => '2026-04-02-025543','class' => 'App\\Database\\Migrations\\CreateJenisPublikasi','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '13','version' => '2026-04-02-025545','class' => 'App\\Database\\Migrations\\CreatePublikasi','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '14','version' => '2026-04-02-025546','class' => 'App\\Database\\Migrations\\CreateKhgt','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '15','version' => '2026-04-02-025546','class' => 'App\\Database\\Migrations\\CreatePengumuman','group' => 'default','namespace' => 'App','time' => '1776312275','batch' => '1'),
            array('id' => '16','version' => '2026-04-02-025547','class' => 'App\\Database\\Migrations\\CreateCarousel','group' => 'default','namespace' => 'App','time' => '1776312276','batch' => '1'),
            array('id' => '20','version' => '2026-04-23-014404','class' => 'App\\Database\\Migrations\\CreateKategoriKeuangan','group' => 'default','namespace' => 'App','time' => '1776909521','batch' => '2'),
            array('id' => '21','version' => '2026-04-23-014406','class' => 'App\\Database\\Migrations\\CreateKeuangan','group' => 'default','namespace' => 'App','time' => '1776909522','batch' => '2'),
            array('id' => '22','version' => '2026-04-23-014406','class' => 'App\\Database\\Migrations\\CreateLaporanMingguan','group' => 'default','namespace' => 'App','time' => '1776909522','batch' => '2')
        );

        foreach ($migrations as $data) {
            $this->db->table('migrations')->replace($data);
        }

        /* `db_sim_almanaar`.`pengumuman` */
        $pengumuman = array(
        );

        foreach ($pengumuman as $data) {
            $this->db->table('pengumuman')->replace($data);
        }

        /* `db_sim_almanaar`.`publikasi` */
        $publikasi = array(
            array('id_publikasi' => '1','id_jenis_publikasi' => '1','judul' => 'Ada Lagi3','slug' => 'ada-lagi3','deskripsi' => '<h1><a href="https://ganyandisa.site/">Ausss</a></h1>','sampul' => '1776865613_485a99a754d53c2568a1.png','lampiran' => NULL,'sumber_penulis' => NULL,'status' => 'pasif','created_at' => '2026-04-22 19:27:44','created_by' => NULL,'updated_at' => '2026-04-23 12:27:14','updated_by' => '1','deleted_at' => '2026-04-23 12:27:14','deleted_by' => '1'),
            array('id_publikasi' => '2','id_jenis_publikasi' => '1','judul' => 'Ada Lagi','slug' => 'ada-lagi','deskripsi' => '<h1><a href="https://ganyandisa.site/">Ausss</a></h1>','sampul' => '1776861505_9ed7a4ba4b5d4c4f99f9.jpg','lampiran' => NULL,'sumber_penulis' => NULL,'status' => 'aktif','created_at' => '2026-04-22 19:38:25','created_by' => NULL,'updated_at' => '2026-04-22 22:53:51','updated_by' => '1','deleted_at' => NULL,'deleted_by' => NULL),
            array('id_publikasi' => '3','id_jenis_publikasi' => '1','judul' => 'Ada Lagi4','slug' => 'ada-lagi4','deskripsi' => '<p>sss</p>','sampul' => '1776865137_f9a77b9ce518d0e43e0f.png','lampiran' => NULL,'sumber_penulis' => NULL,'status' => 'pasif','created_at' => '2026-04-22 19:39:09','created_by' => NULL,'updated_at' => '2026-04-22 21:13:35','updated_by' => '1','deleted_at' => '2026-04-22 21:13:35','deleted_by' => '1'),
            array('id_publikasi' => '4','id_jenis_publikasi' => '1','judul' => 'Ada Lagi2','slug' => 'ada-lagi2','deskripsi' => '<p>sss</p>','sampul' => '1776865670_724b789da0679a3899f2.png','lampiran' => NULL,'sumber_penulis' => NULL,'status' => 'pasif','created_at' => '2026-04-22 19:45:45','created_by' => '1','updated_at' => '2026-04-23 12:27:22','updated_by' => '1','deleted_at' => '2026-04-23 12:27:22','deleted_by' => '1'),
            array('id_publikasi' => '5','id_jenis_publikasi' => '2','judul' => 'Ada Lagi Ajaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','slug' => 'ada-lagi-ajaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','deskripsi' => '<p>Adalah</p>','sampul' => '1776875430_9d6ae0bb3998e12e1be0.png','lampiran' => '1776875430_41aa688a3b01464f0d84.pdf','sumber_penulis' => 'Google','status' => 'pasif','created_at' => '2026-04-22 23:29:39','created_by' => '1','updated_at' => '2026-04-22 23:42:17','updated_by' => '1','deleted_at' => '2026-04-22 23:42:17','deleted_by' => '1'),
            array('id_publikasi' => '6','id_jenis_publikasi' => '2','judul' => 'Artikel1','slug' => 'artikel1','deskripsi' => '<p>Artikel1</p>','sampul' => 'default.png','lampiran' => NULL,'sumber_penulis' => 'Artikel1','status' => 'aktif','created_at' => '2026-04-22 23:59:35','created_by' => '1','updated_at' => '2026-04-22 23:59:35','updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('id_publikasi' => '7','id_jenis_publikasi' => '2','judul' => 'Artikel2','slug' => 'artikel2','deskripsi' => '<p>Artikel2</p>','sampul' => 'default.png','lampiran' => NULL,'sumber_penulis' => 'Artikel2','status' => 'aktif','created_at' => '2026-04-23 00:00:05','created_by' => '1','updated_at' => '2026-04-23 00:00:05','updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('id_publikasi' => '8','id_jenis_publikasi' => '2','judul' => 'Artikel3','slug' => 'artikel3','deskripsi' => '<p>Artikel3</p>','sampul' => 'default.png','lampiran' => NULL,'sumber_penulis' => 'Artikel3','status' => 'pasif','created_at' => '2026-04-23 00:00:28','created_by' => '1','updated_at' => '2026-04-23 12:33:14','updated_by' => NULL,'deleted_at' => '2026-04-23 12:33:14','deleted_by' => '1'),
            array('id_publikasi' => '9','id_jenis_publikasi' => '2','judul' => 'Artikel4','slug' => 'artikel4','deskripsi' => '<p>Artikel4</p>','sampul' => 'default.png','lampiran' => NULL,'sumber_penulis' => 'Artikel4','status' => 'aktif','created_at' => '2026-04-23 00:00:54','created_by' => '1','updated_at' => '2026-04-23 00:00:54','updated_by' => NULL,'deleted_at' => NULL,'deleted_by' => NULL),
            array('id_publikasi' => '10','id_jenis_publikasi' => '2','judul' => 'Artikel5','slug' => 'artikel5','deskripsi' => '<p>Artikel5</p>','sampul' => 'default.png','lampiran' => NULL,'sumber_penulis' => 'Artikel5','status' => 'aktif','created_at' => '2026-04-23 00:01:18','created_by' => '1','updated_at' => '2026-04-23 12:33:21','updated_by' => NULL,'deleted_at' => '2026-04-23 12:33:21','deleted_by' => '1')
        );

        foreach ($publikasi as $data) {
            $this->db->table('publikasi')->replace($data);
        }

        /* `db_sim_almanaar`.`token` */
        $token = array(
            array('id_otp' => '1','id_akun' => '1','token' => 'f1b6f4cff55dee9c10beeddd4c2bbf479ed67280434ea6f834ce11d554760407','used_at' => '2026-04-16 11:08:32','expired_at' => '2026-04-16 12:06:35','created_at' => '2026-04-16 11:06:35','updated_at' => '2026-04-16 11:08:32','deleted_at' => NULL),
            array('id_otp' => '2','id_akun' => '3','token' => 'df4abb8eba03b47fee09f287ed508aaf26103ee41ceee51669f894a4be96a682','used_at' => NULL,'expired_at' => '2026-04-17 14:07:50','created_at' => '2026-04-16 14:07:50','updated_at' => '2026-04-16 14:07:50','deleted_at' => NULL),
            array('id_otp' => '3','id_akun' => '4','token' => '8ccf5050d2190851215e31324687bf63a91e05f0768c7e6569b6dcac66547dfd','used_at' => NULL,'expired_at' => '2026-04-17 16:48:42','created_at' => '2026-04-16 16:48:42','updated_at' => '2026-04-16 16:48:42','deleted_at' => NULL)
        );

        foreach ($token as $data) {
            $this->db->table('token')->replace($data);
        }
    }
}
