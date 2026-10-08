<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMaintenanceToFiturTable extends Migration
{
    public function up()
    {
        $fields = [
            'is_maintenance' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'deskripsi', // Sesuaikan posisi kolom jika ingin ditaruh setelah kolom tertentu (opsional)
            ],
        ];

        $this->forge->addColumn('fitur', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fitur', 'is_maintenance');
    }
}