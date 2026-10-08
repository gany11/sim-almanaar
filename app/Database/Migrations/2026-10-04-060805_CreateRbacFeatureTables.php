<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRbacFeatureTables extends Migration
{
    public function up()
    {
        // 1. Tabel `fitur`
        $this->forge->addField([             
            'id_fitur' => [                 
                'type'           => 'INT',                 
                'constraint'     => 11,                 
                'unsigned'       => true,                 
                'auto_increment' => true,             
            ],             
            'kode_fitur' => [                 
                'type'       => 'VARCHAR',                 
                'constraint' => 100,             
            ],
            'kategori' => [                 
                'type'       => 'VARCHAR',                 
                'constraint' => 100,             
            ],
            'nama_fitur' => [                 
                'type'       => 'VARCHAR',                 
                'constraint' => 150,             
            ],             
            'jenis' => [                 
                'type'       => 'ENUM',                 
                'constraint' => ['public', 'auth', 'hybrid'],                 
                'default'    => 'auth',                 
                'comment'    => 'public = tanpa login, auth = perlu login, hybrid = bisa public & auth',             
            ],             
            'deskripsi' => [                 
                'type' => 'TEXT',                 
                'null' => true,             
            ],             
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
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],         
        ]);
        $this->forge->addKey('id_fitur', true);
        $this->forge->addUniqueKey('kode_fitur');$this->forge->createTable('fitur', true);

        // 2. Tabel `fitur_prasyarat` (Many-to-Many Ketergantungan Fitur / Multiple Parents)
        $this->forge->addField([
            'id_fitur_prasyarat' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_fitur' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true,
                'comment'    => 'Fitur turunan/anak (contoh: Edit Pemasukan Donasi)',
            ],
            'id_prasyarat' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true,
                'comment'    => 'Fitur syarat/induk (contoh: Lihat dari Donasi OR Lihat dari Donatur)',
            ],
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
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_fitur_prasyarat', true);$this->forge->addForeignKey('id_fitur', 'fitur', 'id_fitur', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_prasyarat', 'fitur', 'id_fitur', 'CASCADE', 'CASCADE');$this->forge->createTable('fitur_prasyarat', true);

        // 3. Tabel Pivot `peran_fitur` (Relasi Peran bawaan ke Fitur)
        $this->forge->addField([
            'id_peran_fitur' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_peran' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true, // Pastikan unsigned ini sinkron dengan PK tabel `peran` Anda
            ],
            'id_fitur' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true,
            ],
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
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_peran_fitur', true);$this->forge->addForeignKey('id_peran', 'peran', 'id_peran', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_fitur', 'fitur', 'id_fitur', 'CASCADE', 'CASCADE');$this->forge->createTable('peran_fitur', true);

        // 4. Tabel Pivot `akun_fitur` (Relasi Tambahan Akun ke Fitur Khusus)
        $this->forge->addField([
            'id_akun_fitur' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_akun' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true, // Pastikan unsigned ini sinkron dengan PK tabel `akun` Anda
            ],
            'id_fitur' => [
                'type'       => 'INT',
                'constraint'     => 11,
                'unsigned'   => true,
            ],
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
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_akun_fitur', true);$this->forge->addForeignKey('id_akun', 'akun', 'id_akun', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_fitur', 'fitur', 'id_fitur', 'CASCADE', 'CASCADE');$this->forge->createTable('akun_fitur', true);
    }

    public function down()
    {
        // Hapus tabel dengan urutan terbalik untuk mencegah error Foreign Key Constraint
        $this->forge->dropTable('akun_fitur', true);$this->forge->dropTable('peran_fitur', true);
        $this->forge->dropTable('fitur_prasyarat', true);$this->forge->dropTable('fitur', true);
    }
}