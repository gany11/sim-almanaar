<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriSdmModel extends Model
{
    protected $table            = 'kategori_sdm';
    protected $primaryKey       = 'id_kategori_sdm';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kategori', 'class_color'];

    // Dates
    protected $useTimestamps = false;
}
