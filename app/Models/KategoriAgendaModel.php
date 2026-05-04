<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriAgendaModel extends Model
{
    protected $table            = 'kategori_agenda';
    protected $primaryKey       = 'id_kategori_agenda';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori', 'class_color'];

    // Dates
    protected $useTimestamps = false;
}
