<?php

namespace App\Models;

use CodeIgniter\Model;

class FiturModel extends Model
{
    protected $table            = 'fitur';
    protected $primaryKey       = 'id_fitur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['kode_fitur', 'kategori', 'nama_fitur', 'jenis', 'deskripsi', 'is_maintenance', 'created_by', 'updated_by', 'deleted_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}