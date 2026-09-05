<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDonasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_donasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'akronim_kwitansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'total_pemasukan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'total_pengeluaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'proposal' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'laporan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'pasif'],
                'default'    => 'aktif',
                'null'       => false,
            ],
            'jenis_donasi' => [
                'type'       => 'ENUM',
                'constraint' => ['Zakat', 'Infak', 'Sedekah', 'Wakaf', 'Donasi Umum'],
                'default'    => 'Donasi Umum',
            ],
            'closed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'closed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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

        $this->forge->addKey('id_donasi', true);
        $this->forge->createTable('donasi');
    }

    public function down()
    {
        $this->forge->dropTable('donasi');
    }
}