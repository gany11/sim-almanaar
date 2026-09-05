<?php

namespace App\Models;

use CodeIgniter\Model;

class MetodePemasukanModel extends Model
{
    protected $table            = 'metode_pemasukan';
    protected $primaryKey       = 'id_metode_pemasukan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['metode_pemasukan', 'class_color'];

    protected $validationRules = [
        'metode_pemasukan' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'metode_pemasukan' => [
            'required'   => 'Nama metode pemasukan wajib diisi.',
            'max_length' => 'Nama metode maksimal 50 karakter.',
        ],
    ];

    protected $skipValidation = false;
}