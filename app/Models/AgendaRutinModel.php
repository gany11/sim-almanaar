<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendaRutinModel extends Model
{
    protected $table            = 'agenda_rutin';
    protected $primaryKey       = 'id_agenda_rutin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    
    // Sesuaikan dengan field di tabel agenda_rutin
    protected $allowedFields    = [
        'id_kategori_agenda', 'tema', 'judul', 'deskripsi', 'tempat', 
        'waktu_mulai', 'waktu_selesai', 'id_keterangan_waktu_mulai', 
        'id_keterangan_waktu_selesai', 'status', 'looping_hari', 
        'looping_minggu', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public $validationRules = [
        'id_kategori_agenda' => [
            'rules'  => 'required|is_not_unique[kategori_agenda.id_kategori_agenda]',
            'errors' => [
                'required'      => 'Kategori agenda rutin wajib dipilih.',
                'is_not_unique' => 'Kategori agenda rutin yang dipilih tidak valid.'
            ]
        ],
        'tema' => [
            'rules'  => 'required|min_length[3]|max_length[255]',
            'errors' => [
                'required'   => 'Tema agenda rutin harus diisi.',
                'min_length' => 'Tema agenda rutin minimal 3 karakter.',
                'max_length' => 'Tema agenda rutin maksimal 255 karakter.',
            ]
        ],
        'judul' => [
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'Judul materi maksimal 255 karakter.',
            ]
        ],
        'waktu_mulai' => [
            'rules'  => 'required', 
            'errors' => ['required' => 'Waktu mulai harus diisi.']
        ],
        'status' => [
            'rules'  => 'permit_empty|in_list[aktif,pasif]',
            'errors' => ['in_list' => 'Status harus bernilai aktif atau pasif.']
        ]
    ];
}