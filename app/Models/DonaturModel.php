<?php

namespace App\Models;

use CodeIgniter\Model;

class DonaturModel extends Model
{
    protected $table            = 'donatur';
    protected $primaryKey       = 'id_donatur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nama', 'noreg', 'email', 'telepon', 'alamat', 
        'rt', 'rw', 'kelurahan', 'token', 'pin',
        'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'nama'      => 'required|max_length[100]',
        'email'     => 'permit_empty|valid_email|max_length[100]',
        'rt'        => 'permit_empty|max_length[10]',
        'rw'        => 'permit_empty|max_length[10]',
        'kelurahan' => 'permit_empty|max_length[100]',
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama donatur wajib diisi.',
            'max_length' => 'Nama donatur maksimal 100 karakter.',
        ],
        'email' => [
            'valid_email' => 'Format alamat email tidak valid.',
            'max_length'  => 'Email maksimal 100 karakter.',
        ],
        'rt' => [
            'max_length' => 'RT maksimal 10 karakter.',
        ],
        'rw' => [
            'max_length' => 'RW maksimal 10 karakter.',
        ],
        'kelurahan' => [
            'max_length' => 'Kelurahan maksimal 100 karakter.',
        ],
    ];

    protected $skipValidation = false;
}