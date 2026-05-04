<?php

namespace App\Models;

use CodeIgniter\Model;

class SdmModel extends Model
{
    protected $table            = 'sdm';
    protected $primaryKey       = 'id_sdm';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nama', 'email', 'telepon', 'alamat', 'created_by', 'updated_by', 'deleted_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public $validationRules = [
        'nama' => [
            'rules'  => 'required|min_length[3]|max_length[255]',
            'errors' => [
                'required'   => 'Nama SDM harus diisi.',
                'min_length' => 'Nama SDM minimal 3 karakter.',
                'max_length' => 'Nama SDM maksimal 255 karakter.',
            ]
        ],
        'email' => [
            'rules'  => 'permit_empty|valid_email|max_length[100]',
            'errors' => [
                'valid_email' => 'Format email tidak valid.',
                'max_length'  => 'Email maksimal 100 karakter.',
            ]
        ],
        'telepon' => [
            'rules'  => 'permit_empty|min_length[10]|max_length[20]',
            'errors' => [
                'min_length' => 'Nomor telepon minimal 10 digit.',
                'max_length' => 'Nomor telepon maksimal 20 digit.',
            ]
        ],
    ];
}
