<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_peran' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_peran', true);
        $this->forge->createTable('peran');
    }

    public function down()
    {
        $this->forge->dropTable('peran');
    }
}