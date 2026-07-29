<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToRingkasanProtokol extends Migration
{
    public function up()
    {
        $fields = [
            'kalimat_pemasukan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'nama_ringkasan_protokol' // Menempatkan sebelum class_color
            ],
            'kalimat_pengeluaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'kalimat_pemasukan' // Menempatkan setelah kalimat_pemasukan
            ],
        ];

        $this->forge->addColumn('ringkasan_protokol', $fields);
    }

    public function down()
    {
        // Menghapus kolom jika di-rollback
        $this->forge->dropColumn('ringkasan_protokol', ['kalimat_pemasukan', 'kalimat_pengeluaran']);
    }
}