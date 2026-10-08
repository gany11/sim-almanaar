<?php

namespace App\Models;

use CodeIgniter\Model;

class PeranFiturModel extends Model
{
    protected $table            = 'peran_fitur';
    protected $primaryKey       = 'id_peran_fitur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['id_peran', 'id_fitur', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
}