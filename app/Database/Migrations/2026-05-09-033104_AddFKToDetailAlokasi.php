<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFKToDetailAlokasi extends Migration
{
    public function up()
    {
        $fields = [
            'id_ringkasan_protokol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_alokasi'
            ],
        ];

        $this->forge->addColumn('detail_alokasi', $fields);

        // Menambahkan Constraint Foreign Key
        $this->db->query("ALTER TABLE detail_alokasi ADD CONSTRAINT fk_detail_ringkasan 
                          FOREIGN KEY (id_ringkasan_protokol) 
                          REFERENCES ringkasan_protokol(id_ringkasan_protokol) 
                          ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        // Hapus Foreign Key terlebih dahulu
        $this->db->query("ALTER TABLE detail_alokasi DROP FOREIGN KEY fk_detail_ringkasan");
        
        // Hapus Kolom
        $this->forge->dropColumn('detail_alokasi', 'id_ringkasan_protokol');
    }
}