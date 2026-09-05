<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusDonasiModel extends Model
{
    protected $table            = 'status_donasi';
    protected $primaryKey       = 'id_status_donasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['status_donasi', 'class_color'];

    protected $validationRules = [
        'status_donasi' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'status_donasi' => [
            'required'   => 'Nama status donasi wajib diisi.',
            'max_length' => 'Nama status maksimal 50 karakter.',
        ],
    ];

    protected $skipValidation = false;
}