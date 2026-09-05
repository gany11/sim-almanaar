<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressDetailsToDonaturTable extends Migration
{
    public function up()
    {
        $fields = [
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'alamat', // Sesuaikan posisi kolom, ditaruh setelah kolom 'alamat' (atau hapus 'after' jika bebas)
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'rt',
            ],
            'kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'rw',
            ],
        ];

        $this->forge->addColumn('donatur', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('donatur', ['rt', 'rw', 'kelurahan']);
    }
}