<?php

namespace App\Models;

use CodeIgniter\Model;

class PeranModel extends Model
{
    protected $table            = 'peran';
    protected $primaryKey       = 'id_peran';
    protected $returnType       = 'object';
    protected $allowedFields    = ['nama'];
}
