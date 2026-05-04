<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgenda extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_agenda'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_kategori_agenda'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tema'                       => ['type' => 'VARCHAR', 'constraint' => 255],
            'judul'                      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'deskripsi'                  => ['type' => 'TEXT', 'null' => true],
            'tempat'                     => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Ruang Utama Masjid Al-Manaar Slipi'],
            'waktu_mulai'                => ['type' => 'DATETIME'],
            'waktu_selesai'              => ['type' => 'DATETIME'],
            'id_keterangan_waktu_mulai'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'id_keterangan_waktu_selesai' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'                 => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                 => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'                 => ['type' => 'DATETIME', 'null' => true],
            'created_by'                 => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'updated_by'                 => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'deleted_by'                 => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id_agenda', true);
        $this->forge->addForeignKey('id_kategori_agenda', 'kategori_agenda', 'id_kategori_agenda', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_keterangan_waktu_mulai', 'keterangan_waktu', 'id_keterangan_waktu', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('id_keterangan_waktu_selesai', 'keterangan_waktu', 'id_keterangan_waktu', 'SET NULL', 'CASCADE');
        $this->forge->createTable('agenda');
    }

    public function down()
    {
        $this->forge->dropTable('agenda');
    }
}
