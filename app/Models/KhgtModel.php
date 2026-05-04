<?php

namespace App\Models;

use CodeIgniter\Model;

class KhgtModel extends Model
{
    protected $table            = 'khgt';
    protected $primaryKey       = 'masehi';
    protected $returnType       = 'array';
    protected $allowedFields    = ['masehi', 'hijriah'];
}