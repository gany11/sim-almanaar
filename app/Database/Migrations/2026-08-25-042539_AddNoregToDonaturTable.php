<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoregToDonaturTable extends Migration
{
    public function up()
    {
        $fields = [
            'noreg' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'id_donatur',
            ],
        ];

        $this->forge->addColumn('donatur', $fields);
        
        // Opsional: Jika ingin kolom noreg bersifat unik (unique)
        // $this->forge->addUniqueKey('donatur', 'noreg');
    }

    public function down()
    {
        $this->forge->dropColumn('donatur', 'noreg');
    }
}