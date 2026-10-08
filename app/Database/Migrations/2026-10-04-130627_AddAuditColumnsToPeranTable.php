<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuditColumnsToPeranTable extends Migration
{
    public function up()
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];
        
        $this->forge->addColumn('peran', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('peran', ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by']);
    }
}
