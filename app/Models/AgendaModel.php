<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendaModel extends Model
{
    protected $table            = 'agenda';
    protected $primaryKey       = 'id_agenda';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kategori_agenda', 'tema', 'judul', 'deskripsi', 'tempat', 
        'waktu_mulai', 'waktu_selesai', 'id_keterangan_waktu_mulai', 
        'id_keterangan_waktu_selesai', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public $validationRules = [
        'id_kategori_agenda' => [
            'rules'  => 'required|is_not_unique[kategori_agenda.id_kategori_agenda]',
            'errors' => [
                'required'      => 'Kategori agenda wajib dipilih.',
                'is_not_unique' => 'Kategori agenda yang dipilih tidak valid.'
            ]
        ],
        'tema' => [
            'rules'  => 'required|min_length[5]|max_length[255]',
            'errors' => [
                'required'   => 'Tema agenda harus diisi.',
                'min_length' => 'Tema agenda minimal 5 karakter.',
                'max_length' => 'Tema agenda maksimal 255 karakter.',
            ]
        ],
        'waktu_mulai' => [
            'rules'  => 'required', 
            'errors' => ['required' => 'Waktu mulai harus diisi.']
        ],
        // 'waktu_selesai' => [
        //     'rules'  => 'required',
        //     'errors' => ['required' => 'Waktu selesai harus diisi.']
        // ],
    ];

    public function getAgendaMendatang($limit = 5)
    {
        return $this->select('agenda.*, kategori_agenda.nama_kategori, kategori_agenda.class_color as kategori_color')
            ->join('kategori_agenda', 'kategori_agenda.id_kategori_agenda = agenda.id_kategori_agenda', 'left')
            ->where('agenda.deleted_at', null)
            ->where('agenda.waktu_mulai >=', date('Y-m-d H:i:s'))
            ->orderBy('agenda.waktu_mulai', 'ASC')
            ->findAll($limit);
    }
}
