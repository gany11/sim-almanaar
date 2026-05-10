<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToKeuangan extends Migration
{
    public function up()
    {
        $fields = [
            'pic' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
                'after'      => 'keterangan'
            ],
            'method_input' => [
                'type'       => 'ENUM',
                'constraint' => ['import', 'manual'],
                'default'    => 'manual',
                'after'      => 'pic'
            ],
            'id_detail_alokasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_kategori_keuangan'
            ],
        ];

        $this->forge->addColumn('keuangan', $fields);
        
        $this->db->query("ALTER TABLE keuangan ADD CONSTRAINT fk_keuangan_detail_alokasi 
                          FOREIGN KEY (id_detail_alokasi) REFERENCES detail_alokasi(id_detail_alokasi) 
                          ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE keuangan DROP FOREIGN KEY fk_keuangan_detail_alokasi");
        
        $this->forge->dropColumn('keuangan', ['pic', 'method_input', 'id_detail_alokasi']);
    }
}