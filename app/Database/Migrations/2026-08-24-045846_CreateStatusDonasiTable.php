<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatusDonasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_status_donasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'status_donasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'class_color' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'default'    => 'bg-gray-100 text-gray-700',
            ],
        ]);

        $this->forge->addKey('id_status_donasi', true);
        $this->forge->createTable('status_donasi');

        // --- SEEDING DATA AWAL ---
        $data = [
            [
                'status_donasi' => 'Pembuatan Proposal',
                'class_color'   => 'bg-slate-100 text-slate-700',
            ],
            [
                'status_donasi' => 'Pengiriman Proposal',
                'class_color'   => 'bg-blue-100 text-blue-700',
            ],
            [
                'status_donasi' => 'Penerimaan Dana',
                'class_color'   => 'bg-indigo-100 text-indigo-700',
            ],
            [
                'status_donasi' => 'Pencatatan Dana',
                'class_color'   => 'bg-emerald-100 text-emerald-700',
            ],
            [
                'status_donasi' => 'Dikembalikan / Gagal Kirim',
                'class_color'   => 'bg-rose-100 text-rose-700',
            ],
            [
                'status_donasi' => 'Dibatalkan',
                'class_color'   => 'bg-gray-100 text-gray-700',
            ],
        ];

        $this->db->table('status_donasi')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('status_donasi');
    }
}