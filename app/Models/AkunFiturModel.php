<?php

namespace App\Models;

use CodeIgniter\Model;

class AkunFiturModel extends Model
{
    protected $table            = 'akun_fitur';
    protected $primaryKey       = 'id_akun_fitur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_akun', 'id_fitur', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}