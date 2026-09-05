<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTokenPinDonatur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_token_pin' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'id_donatur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'expired_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_token_pin', true);

        $this->forge->addKey('id_donatur');
        $this->forge->addKey('token');

        $this->forge->createTable('token_pin_donatur', true);
    }

    public function down()
    {
        $this->forge->dropTable('token_pin_donatur', true);
    }
}