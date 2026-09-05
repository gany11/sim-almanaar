<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranDonasiModel extends Model
{
    protected $table            = 'pengeluaran_donasi';
    protected $primaryKey       = 'id_pengeluaran_donasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id_donasi', 'tanggal', 'keterangan', 'jumlah', 
        'satuan', 'harga_satuan', 'sub_total', 'bukti', 
        'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_donasi'    => 'required|integer',
        'tanggal'      => 'required|valid_date',
        'keterangan'   => 'required',
        'jumlah'       => 'required|numeric|greater_than[0]',
        'satuan'       => 'required|max_length[50]',
        'harga_satuan' => 'required|decimal|greater_than[0]',
        'sub_total'    => 'required|decimal',
    ];

    protected $validationMessages = [
        'id_donasi' => [
            'required' => 'Program donasi wajib dipilih.',
        ],
        'tanggal' => [
            'required'   => 'Tanggal pengeluaran wajib diisi.',
            'valid_date' => 'Format tanggal tidak valid.',
        ],
        'keterangan' => [
            'required' => 'Keterangan pengeluaran wajib diisi.',
        ],
        'jumlah' => [
            'required'     => 'Jumlah/kuantitas barang wajib diisi.',
            'numeric'      => 'Jumlah harus berupa angka yang valid.',
            'greater_than' => 'Jumlah barang harus lebih besar dari 0.',
        ],
        'satuan' => [
            'required' => 'Satuan barang wajib diisi.',
        ],
        'harga_satuan' => [
            'required'     => 'Harga satuan wajib diisi.',
            'decimal'      => 'Format harga satuan tidak valid.',
            'greater_than' => 'Harga satuan harus lebih besar dari 0.',
        ],
        'sub_total' => [
            'required' => 'Sub total wajib diisi.',
            'decimal'  => 'Format sub total tidak valid.',
        ],
    ];

    protected $skipValidation = false;
}