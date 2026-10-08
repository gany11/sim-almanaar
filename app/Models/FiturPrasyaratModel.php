<?php

namespace App\Models;

use CodeIgniter\Model;

class FiturPrasyaratModel extends Model
{
    protected $table            = 'fitur_prasyarat';
    protected $primaryKey       = 'id_fitur_prasyarat';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_fitur', 'id_prasyarat', 'created_by', 'updated_by', 'deleted_by'];
    
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}