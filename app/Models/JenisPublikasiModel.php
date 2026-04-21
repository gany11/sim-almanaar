<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisPublikasiModel extends Model
{
    protected $table            = 'jenis_publikasi';
    protected $primaryKey       = 'id_jenis_publikasi';
    protected $returnType       = 'object';
    protected $allowedFields    = ['jenis_publikasi'];
}