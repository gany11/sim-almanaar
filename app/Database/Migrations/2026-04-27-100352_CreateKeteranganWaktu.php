<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeteranganWaktu extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_keterangan_waktu' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'keterangan'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_keterangan_waktu', true);
        $this->forge->createTable('keterangan_waktu');
    }

    public function down()
    {
        $this->forge->dropTable('keterangan_waktu');
    }
}
