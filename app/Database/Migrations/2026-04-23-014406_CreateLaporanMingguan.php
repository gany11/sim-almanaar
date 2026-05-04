<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanMingguan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_laporan_mingguan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'started_at' => ['type' => 'DATETIME'],
            'ended_at'   => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_by'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_laporan_mingguan', true);
        $this->forge->createTable('laporan_mingguan');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_mingguan');
    }
}
