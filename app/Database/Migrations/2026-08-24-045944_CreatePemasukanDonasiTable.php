<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemasukanDonasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pemasukan_donasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_donasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_donatur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_metode_pemasukan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_status_donasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null'       => true,
            ],
            'no_kwitansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'samarkan' => [
                'type'       => 'ENUM',
                'constraint' => ['Y', 'N'],
                'default'    => 'N',
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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

        $this->forge->addKey('id_pemasukan_donasi', true);
        
        // Definisikan Foreign Key jika diperlukan
        $this->forge->addForeignKey('id_donasi', 'donasi', 'id_donasi', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_donatur', 'donatur', 'id_donatur', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_metode_pemasukan', 'metode_pemasukan', 'id_metode_pemasukan', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_status_donasi', 'status_donasi', 'id_status_donasi', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('pemasukan_donasi');
    }

    public function down()
    {
        $this->forge->dropTable('pemasukan_donasi');
    }
}