<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMetodePemasukanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_metode_pemasukan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'metode_pemasukan' => [
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

        $this->forge->addKey('id_metode_pemasukan', true);
        $this->forge->createTable('metode_pemasukan');

        // --- SEEDING DATA AWAL ---
        $data = [
            [
                'metode_pemasukan' => 'Tunai',
                'class_color'      => 'bg-emerald-100 text-emerald-700',
            ],
            [
                'metode_pemasukan' => 'Qris',
                'class_color'      => 'bg-violet-100 text-violet-700',
            ],
            [
                'metode_pemasukan' => 'Transfer Bank X',
                'class_color'      => 'bg-sky-100 text-sky-700',
            ],
            [
                'metode_pemasukan' => 'Transfer Bank Y',
                'class_color'      => 'bg-blue-100 text-blue-700',
            ],
            [
                'metode_pemasukan' => 'Transfer Bank Z',
                'class_color'      => 'bg-indigo-100 text-indigo-700',
            ],
            [
                'metode_pemasukan' => 'Transfer Bank AA',
                'class_color'      => 'bg-teal-100 text-teal-700',
            ],
            [
                'metode_pemasukan' => 'Transfer Bank AB',
                'class_color'      => 'bg-fuchsia-100 text-fuchsia-700',
            ],
        ];

        $this->db->table('metode_pemasukan')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('metode_pemasukan');
    }
}