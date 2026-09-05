<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoriStatusDonasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_histori_status_donasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_status_donasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_pemasukan_donasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'waktu' => [
                'type' => 'DATETIME',
            ],
            'nama_pengurus' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_histori_status_donasi', true);
        $this->forge->addForeignKey('id_status_donasi', 'status_donasi', 'id_status_donasi', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_pemasukan_donasi', 'pemasukan_donasi', 'id_pemasukan_donasi', 'CASCADE', 'CASCADE');

        $this->forge->createTable('histori_status_donasi');
    }

    public function down()
    {
        $this->forge->dropTable('histori_status_donasi');
    }
}