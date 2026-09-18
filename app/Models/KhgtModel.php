<?php

namespace App\Models;

use CodeIgniter\Model;

class KhgtModel extends Model
{
    protected $table            = 'khgt';
    protected $primaryKey       = 'masehi';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    
    protected $allowedFields    = [
        'masehi', 
        'hijriah_tanggal', 
        'hijriah_bulan', 
        'hijriah_tahun', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}