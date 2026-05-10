<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailAlokasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail_alokasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_alokasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'detail_alokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_detail_alokasi', true);
        $this->forge->addForeignKey('id_alokasi', 'alokasi', 'id_alokasi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_alokasi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_alokasi');
    }
}