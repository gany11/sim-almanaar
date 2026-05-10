<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailAlokasiModel extends Model
{
    protected $table            = 'detail_alokasi';
    protected $primaryKey       = 'id_detail_alokasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_alokasi', 'detail_alokasi', 'class_color'];

    protected $useTimestamps = false;
}