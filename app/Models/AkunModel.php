<?php

namespace App\Models;

use CodeIgniter\Model;

class AkunModel extends Model
{
    protected $table            = 'akun';
    protected $primaryKey       = 'id_akun';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_peran', 'username', 'password', 'nama', 
        'email', 'telepon', 'status', 'created_at', 
        'updated_at', 'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_akun'  => 'permit_empty|max_length[11]',

        'username' => 'required|alpha_numeric|min_length[5]|regex_match[/^[a-z0-9]+$/]|is_unique[akun.username,id_akun,{id_akun}]',
        'email'    => 'required|valid_email|is_unique[akun.email,id_akun,{id_akun}]',
        'nama'     => 'required|min_length[3]',
        'telepon'  => 'required|numeric|min_length[10]',
        'id_peran' => 'required'
    ];

    protected $validationMessages = [
        'username' => [
            'required'     => 'Username harus diisi.',
            'alpha_numeric'=> 'Username hanya boleh berisi huruf dan angka.',
            'min_length'   => 'Username minimal 5 karakter.',
            'regex_match'  => 'Username harus huruf kecil semua (a-z) dan angka tanpa spasi.',
            'is_unique'    => 'Username ini sudah digunakan.'
        ],
        'email' => [
            'required'    => 'Email harus diisi.',
            'is_unique'   => 'Alamat email ini sudah terdaftar.',
            'valid_email' => 'Format email tidak valid.'
        ],
        'nama' => [
            'required'   => 'Nama harus diisi.',
            'min_length' => 'Nama minimal 3 karakter.'
        ],
        'telepon' => [
            'required'   => 'Telepon harus diisi.',
            'numeric'    => 'Nomor telepon harus berupa angka.',
            'min_length' => 'Nomor telepon minimal 10 digit.'
        ],
        'id_peran' => [
            'required' => 'Silakan pilih peran user.'
        ]
    ];

    /**
     * Mengambil user berdasarkan email
     */
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)
                    ->where('deleted_at', null)
                    ->first();
    }

    public function getLoginData($username)
    {
        return $this->select('akun.*, peran.nama AS nama_peran')
                    ->join('peran', 'peran.id_peran = akun.id_peran')
                    ->where('akun.username', $username)
                    ->where('akun.deleted_at', null)
                    ->first();
    }
}