<?php

namespace App\Models;

use CodeIgniter\Model;

class KeteranganWaktuModel extends Model
{
    protected $table            = 'keterangan_waktu';
    protected $primaryKey       = 'id_keterangan_waktu';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['keterangan', 'class_color'];

    // Dates
    protected $useTimestamps = false;
}
