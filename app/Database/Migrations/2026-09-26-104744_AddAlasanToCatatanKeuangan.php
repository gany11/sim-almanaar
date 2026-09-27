<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanToCatatanKeuangan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('catatan_keuangan', [
            'alasan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('catatan_keuangan', 'alasan');
    }
}