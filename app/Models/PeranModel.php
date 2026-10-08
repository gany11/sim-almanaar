<?php

namespace App\Models;

use CodeIgniter\Model;

class PeranModel extends Model
{
    protected $table            = 'peran';
    protected $primaryKey       = 'id_peran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Disarankan array agar konsisten dengan model lain
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nama', 'class_color', 'created_by', 'updated_by', 'deleted_by'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}