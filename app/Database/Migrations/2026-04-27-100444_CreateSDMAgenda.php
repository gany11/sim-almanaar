<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSDMAgenda extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pengisi_agenda' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_agenda'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_sdm'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_kategori_sdm'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id_pengisi_agenda', true);
        $this->forge->addForeignKey('id_agenda', 'agenda', 'id_agenda', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_sdm', 'sdm', 'id_sdm', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kategori_sdm', 'kategori_sdm', 'id_kategori_sdm', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sdm_agenda');
    }

    public function down()
    {
        $this->forge->dropTable('sdm_agenda');
    }
}
