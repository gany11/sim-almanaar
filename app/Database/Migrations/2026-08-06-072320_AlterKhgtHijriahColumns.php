<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterKhgtHijriahColumns extends Migration
{
    public function up()
    {
        // Tambahkan kolom baru
        $this->forge->addColumn('khgt', [
            'hijriah_tanggal' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'masehi',
            ],
            'hijriah_bulan' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'hijriah_tanggal',
            ],
            'hijriah_tahun' => [
                'type'       => 'SMALLINT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'hijriah_bulan',
            ],
        ]);

        // Hapus kolom lama
        $this->forge->dropColumn('khgt', 'hijriah');
    }

    public function down()
    {
        // Kembalikan kolom lama
        $this->forge->addColumn('khgt', [
            'hijriah' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'masehi',
            ]
        ]);

        // Hapus kolom baru
        $this->forge->dropColumn('khgt', [
            'hijriah_tanggal',
            'hijriah_bulan',
            'hijriah_tahun',
        ]);
    }
}