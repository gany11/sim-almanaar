<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAkun extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_akun' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_peran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'pasif'],
                'default'    => 'aktif',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_akun', true);
        $this->forge->addForeignKey('id_peran', 'peran', 'id_peran', 'CASCADE', 'CASCADE');
        $this->forge->createTable('akun');
    }

    public function down()
    {
        $this->forge->dropTable('akun');
    }
}