<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class TokenModel extends Model
{
    protected $table            = 'token';
    protected $primaryKey       = 'id_token';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'id_akun', 'token', 'used_at', 'expired_at', 
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Cek apakah user sudah punya token aktif (biar tidak double kirim)
     */
    public function checkActiveToken($id_akun)
    {
        return $this->where('id_akun', $id_akun)
                    ->where('used_at', null)
                    ->where('expired_at >', Time::now('Asia/Jakarta')->toDateTimeString())
                    ->first();
    }

    /**
     * Validasi token saat user masuk ke halaman ganti password
     */
    public function validateToken($token)
    {
        return $this->where('token', $token)
                    ->where('used_at', null)
                    ->where('expired_at >', Time::now('Asia/Jakarta')->toDateTimeString())
                    ->first();
    }
}