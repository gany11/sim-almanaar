<?php

namespace App\Models;

use CodeIgniter\Model;

class CatatanKeuanganModel extends Model
{
    protected $table              = 'catatan_keuangan';
    protected $primaryKey         = 'id_catatan';
    protected $useAutoIncrement = true;
    protected $returnType         = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields      = [
        'tanggal', 'keterangan', 'nominal', 'status', 'alasan',
        'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public $validationRules = [
        'tanggal' => [
            'rules'  => 'required|valid_date',
            'errors' => ['required' => 'Tanggal catatan wajib diisi.', 'valid_date' => 'Format tanggal tidak valid.']
        ],
        'keterangan' => [
            'rules'  => 'required',
            'errors' => ['required' => 'Keterangan wajib diisi.']
        ],
        'nominal' => [
            'rules'  => 'required|numeric|greater_than[0]',
            'errors' => [
                'required'     => 'Nominal wajib diisi.',
                'numeric'      => 'Nominal harus berupa angka.',
                'greater_than' => 'Nominal harus lebih besar dari 0.'
            ]
        ],
        'status' => [
            'rules'  => 'required|in_list[tersedia,sudah_digunakan,dibatalkan]',
            'errors' => ['required' => 'Status wajib dipilih.']
        ],
        'alasan' => [
            'rules'  => 'permit_empty|string',
            'errors' => ['string' => 'Alasan harus berupa teks.']
        ]
    ];
}