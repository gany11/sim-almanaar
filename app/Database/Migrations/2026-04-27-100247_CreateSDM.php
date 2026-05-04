<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSDM extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sdm'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'telepon'    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'     => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_sdm', true);
        $this->forge->createTable('sdm');
    }

    public function down()
    {
        $this->forge->dropTable('sdm');
    }
}
