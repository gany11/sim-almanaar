<?php

namespace App\Models;

use CodeIgniter\Model;

class LogLoginModel extends Model
{
    protected $table              = 'log_login';
    protected $primaryKey         = 'id_log';
    protected $useAutoIncrement = true;
    protected $returnType         = 'array';
    protected $useSoftDeletes   = false; // Log aktivitas biasanya dicatat permanen
    protected $allowedFields      = [
        'id_akun', 'login_at', 'logout_at', 'ip_address', 'user_agent', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public $validationRules = [
        'id_akun' => [
            'rules'  => 'required|numeric',
            'errors' => ['required' => 'ID Akun wajib diisi.']
        ],
        'status' => [
            'rules'  => 'required|in_list[aktif,logout,expired]',
            'errors' => ['required' => 'Status log wajib dipilih.']
        ]
    ];
}