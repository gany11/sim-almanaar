<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanMingguanModel extends Model
{
    protected $table            = 'laporan_mingguan';
    protected $primaryKey       = 'id_laporan_mingguan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul', 'catatan', 'started_at', 'ended_at', 
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'judul'      => 'required|min_length[5]|max_length[255]',
        'catatan'    => 'permit_empty|string',
        'started_at' => 'required|valid_date',
        'ended_at'   => 'required|valid_date',
    ];

    protected $validationMessages = [
        'judul' => [
            'required'   => 'Judul laporan tidak boleh kosong.',
            'min_length' => 'Judul laporan minimal 5 karakter.'
        ],
        'started_at' => [
            'required'   => 'Tanggal mulai laporan harus diisi.',
            'valid_date' => 'Format tanggal mulai tidak valid.'
        ],
        'ended_at' => [
            'required'   => 'Tanggal berakhir laporan harus diisi.',
            'valid_date' => 'Format tanggal berakhir tidak valid.'
        ],
    ];
}
