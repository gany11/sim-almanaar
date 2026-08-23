<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgendaRutin extends Migration
{
    public function up()
    {
        // -------------------------------------------------------------------
        // 1. MEMBUAT TABEL AGENDA RUTIN
        // -------------------------------------------------------------------
        $this->forge->addField([
            'id_agenda_rutin' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kategori_agenda' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tema' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tempat' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'waktu_mulai' => [
                'type' => 'TIME',
            ],
            'waktu_selesai' => [
                'type' => 'TIME',
            ],
            'id_keterangan_waktu_mulai' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_keterangan_waktu_selesai' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'pasif'],
                'default'    => 'aktif',
            ],
            'looping_hari' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Contoh: 1,2,3 (Senin,Selasa,Rabu)',
            ],
            'looping_minggu' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'comment'    => 'Contoh: 1,2,3,4,5 (Minggu ke-1 s/d 5)',
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
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        
        $this->forge->addKey('id_agenda_rutin', true);
        $this->forge->createTable('agenda_rutin');


        // -------------------------------------------------------------------
        // 2. MENAMBAHKAN FIELD id_agenda_rutin DI TABEL AGENDA
        // -------------------------------------------------------------------
        $this->forge->addColumn('agenda', [
            'id_agenda_rutin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_kategori_agenda', // Menempatkan kolom setelah id_kategori_agenda
            ]
        ]);


        // -------------------------------------------------------------------
        // 3. MEMODIFIKASI TABEL SDM_AGENDA
        // -------------------------------------------------------------------
        // a. Mengubah id_agenda menjadi nullable
        $this->forge->modifyColumn('sdm_agenda', [
            'id_agenda' => [
                'name'       => 'id_agenda', // Nama kolom harus disertakan di CI4 saat modifyColumn
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,        // Ubah menjadi nullable
            ]
        ]);

        // b. Menambahkan kolom id_agenda_rutin
        $this->forge->addColumn('sdm_agenda', [
            'id_agenda_rutin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_agenda',
            ]
        ]);
    }

    public function down()
    {
        // -------------------------------------------------------------------
        // ROLLBACK / DOWN
        // -------------------------------------------------------------------
        
        // 1. Hapus kolom id_agenda_rutin dari tabel sdm_agenda
        $this->forge->dropColumn('sdm_agenda', 'id_agenda_rutin');

        // Kembalikan id_agenda agar NOT NULL (Opsional, tergantung kebijakan Anda)
        // Jika tabel sudah ada isinya, ini bisa menyebabkan error jika ada data dengan id_agenda = NULL
        $this->forge->modifyColumn('sdm_agenda', [
            'id_agenda' => [
                'name'       => 'id_agenda',
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ]
        ]);

        // 2. Hapus kolom id_agenda_rutin dari tabel agenda
        $this->forge->dropColumn('agenda', 'id_agenda_rutin');

        // 3. Hapus tabel agenda_rutin
        $this->forge->dropTable('agenda_rutin');
    }
}