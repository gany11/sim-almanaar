<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePublikasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_publikasi'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_jenis_publikasi' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'judul'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'               => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'          => ['type' => 'TEXT', 'null' => true],
            'sampul'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'lampiran'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sumber_penulis'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'             => ['type' => 'ENUM', 'constraint' => ['aktif', 'pasif'], 'default' => 'aktif'],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'created_by'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_by'          => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_by'         => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_publikasi', true);
        $this->forge->addForeignKey('id_jenis_publikasi', 'jenis_publikasi', 'id_jenis_publikasi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('publikasi');
    }

    public function down()
    {
        $this->forge->dropTable('publikasi');
    }
}
