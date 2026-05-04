<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriSDM extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kategori_sdm' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kategori'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_kategori_sdm', true);
        $this->forge->createTable('kategori_sdm');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_sdm');
    }
}
