<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriKeuanganModel extends Model
{
    protected $table            = 'kategori_keuangan';
    protected $primaryKey       = 'id_kategori_keuangan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kategori', 'class_color'];

    // Dates
    protected $useTimestamps = false;
}
