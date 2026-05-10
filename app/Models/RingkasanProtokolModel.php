<?php

namespace App\Models;

use CodeIgniter\Model;

class RingkasanProtokolModel extends Model
{
    protected $table            = 'ringkasan_protokol';
    protected $primaryKey       = 'id_ringkasan_protokol';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_ringkasan_protokol', 
        'class_color'
    ];

    protected $useTimestamps = false;
}