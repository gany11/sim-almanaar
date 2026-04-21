<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKhgt extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'masehi'     => ['type' => 'DATE'],
            'hijriah'    => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_by'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('masehi', true);
        $this->forge->createTable('khgt');
    }

    public function down()
    {
        $this->forge->dropTable('khgt');
    }
}
