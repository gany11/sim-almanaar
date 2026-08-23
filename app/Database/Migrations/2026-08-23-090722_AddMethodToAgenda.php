<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMethodToAgenda extends Migration
{
    public function up()
    {
        $fields = [
            'method' => [
                'type'       => 'ENUM',
                'constraint' => ['manual', 'import', 'rutin'],
                'default'    => 'manual', // Nilai default jika tidak diisi
                'null'       => false,
                'after'      => 'id_keterangan_waktu_selesai',
            ],
        ];

        // Menambahkan kolom ke tabel 'agenda'
        $this->forge->addColumn('agenda', $fields);
    }

    public function down()
    {
        // Menghapus kolom jika di-rollback
        $this->forge->dropColumn('agenda', 'method');
    }
}