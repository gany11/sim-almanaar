<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisPublikasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jenis_publikasi' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true, 
                'auto_increment' => true
            ],
            'jenis_publikasi'    => [
                'type' => 'VARCHAR', 
                'constraint' => 100
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);
        $this->forge->addKey('id_jenis_publikasi', true);
        $this->forge->createTable('jenis_publikasi');
    }

    public function down()
    {
        $this->forge->dropTable('jenis_publikasi');
    }
}
