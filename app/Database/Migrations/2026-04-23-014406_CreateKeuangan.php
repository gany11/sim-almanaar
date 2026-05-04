<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeuangan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_keuangan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kategori_keuangan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['pemasukan', 'pengeluaran'],
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_keuangan', true);
        $this->forge->addForeignKey('id_kategori_keuangan', 'kategori_keuangan', 'id_kategori_keuangan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('keuangan');
    }

    public function down()
    {
        $this->forge->dropTable('keuangan');
    }
}
