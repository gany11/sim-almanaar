<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdPemasukanDonasiToKeuanganTable extends Migration
{
    public function up()
    {
        $fields = [
            'id_pemasukan_donasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_detail_alokasi', 
            ],
        ];
        
        $this->forge->addColumn('keuangan', $fields);
        
        // Tambahkan foreign key menggunakan query SQL langsung agar penamaannya terkontrol
        $this->db->query('ALTER TABLE `keuangan` ADD CONSTRAINT `fk_keuangan_pemasukan_donasi` FOREIGN KEY (`id_pemasukan_donasi`) REFERENCES `pemasukan_donasi`(`id_pemasukan_donasi`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Hapus foreign key menggunakan nama constraint yang kita tentukan sendiri
        $this->db->query('ALTER TABLE `keuangan` DROP FOREIGN KEY `fk_keuangan_pemasukan_donasi`');
        
        // Hapus kolomnya
        $this->forge->dropColumn('keuangan', 'id_pemasukan_donasi');
    }
}