<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlokasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_alokasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'nama_alokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_alokasi', true);
        $this->forge->createTable('alokasi');
    }

    public function down()
    {
        $this->forge->dropTable('alokasi');
    }
}