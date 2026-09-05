<?php

namespace App\Models;

use CodeIgniter\Model;

class TokenPinDonaturModel extends Model
{
    protected $table            = 'token_pin_donatur';
    protected $primaryKey       = 'id_token_pin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id_donatur',
        'token',
        'created_at',
        'expired_at',
        'used_at',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_donatur' => 'required|integer',
        'token'      => 'required|max_length[255]',
        'created_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'expired_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        'used_at'    => 'permit_empty|valid_date[Y-m-d H:i:s]',
    ];

    protected $validationMessages = [
        'id_donatur' => [
            'required' => 'ID donatur wajib diisi.',
            'integer'  => 'ID donatur tidak valid.',
        ],
        'token' => [
            'required'   => 'Token wajib diisi.',
            'max_length' => 'Token maksimal 255 karakter.',
        ],
    ];
}