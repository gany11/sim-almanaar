<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPinToDonatur extends Migration
{
    public function up()
    {
        $fields = [
            'pin' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'token',
            ],
        ];

        $this->forge->addColumn('donatur', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('donatur', 'pin');
    }
}