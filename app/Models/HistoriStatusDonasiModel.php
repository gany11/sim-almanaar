<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriStatusDonasiModel extends Model
{
    protected $table            = 'histori_status_donasi';
    protected $primaryKey       = 'id_histori_status_donasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id_status_donasi', 'id_pemasukan_donasi', 'waktu', 
        'nama_pengurus', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_status_donasi'    => 'required|integer',
        'id_pemasukan_donasi' => 'required|integer',
        'waktu'               => 'required|valid_date',
        'nama_pengurus'       => 'required|max_length[100]',
    ];

    protected $validationMessages = [
        'id_status_donasi' => [
            'required' => 'Status donasi wajib diisi.',
        ],
        'id_pemasukan_donasi' => [
            'required' => 'ID pemasukan donasi wajib diisi.',
        ],
        'waktu' => [
            'required'   => 'Waktu perubahan status wajib diisi.',
            'valid_date' => 'Format tanggal dan waktu tidak valid.',
        ],
        'nama_pengurus' => [
            'required'   => 'Nama pengurus wajib diisi.',
            'max_length' => 'Nama pengurus maksimal 100 karakter.',
        ],
    ];

    protected $skipValidation = false;
}