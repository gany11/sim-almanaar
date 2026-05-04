<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriAgenda extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kategori_agenda' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kategori'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_kategori_agenda', true);
        $this->forge->createTable('kategori_agenda');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_agenda');
    }
}
