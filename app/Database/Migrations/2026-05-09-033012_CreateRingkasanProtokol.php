<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRingkasanProtokol extends Migration
{
    public function up()
    {
        // Membuat Tabel ringkasan_protokol
        $this->forge->addField([
            'id_ringkasan_protokol' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_ringkasan_protokol' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ]
        ]);

        $this->forge->addKey('id_ringkasan_protokol', true);
        $this->forge->createTable('ringkasan_protokol');
    }

    public function down()
    {
        $this->forge->dropTable('ringkasan_protokol');
    }
}