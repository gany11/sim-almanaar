<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCarousel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_carousel' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'file'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'      => ['type' => 'ENUM', 'constraint' => ['aktif', 'pasif'], 'default' => 'aktif'],
            'started_at'  => ['type' => 'DATETIME', 'null' => true],
            'ended_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_by'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_by'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_by'  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_carousel', true);
        $this->forge->createTable('carousel');
    }

    public function down()
    {
        $this->forge->dropTable('carousel');
    }
}
