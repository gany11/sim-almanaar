<?php

namespace App\Models;

use CodeIgniter\Model;

class SdmAgendaModel extends Model
{
    protected $table            = 'sdm_agenda';
    protected $primaryKey       = 'id_pengisi_agenda';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_agenda', 'id_sdm', 'id_kategori_sdm'];

    public $validationRules = [
        'id_agenda' => [
            'rules'  => 'required|is_not_unique[agenda.id_agenda]',
            'errors' => [
                'required'      => 'ID Agenda harus disertakan.',
                'is_not_unique' => 'Agenda tidak ditemukan.'
            ]
        ],
        'id_sdm' => [
            'rules'  => 'required|is_not_unique[sdm.id_sdm]',
            'errors' => [
                'required'      => 'Nama pengisi/SDM wajib dipilih.',
                'is_not_unique' => 'Data SDM tidak ditemukan.'
            ]
        ],
        'id_kategori_sdm' => [
            'rules'  => 'required|is_not_unique[kategori_sdm.id_kategori_sdm]',
            'errors' => [
                'required'      => 'Peran SDM dalam agenda wajib dipilih.',
                'is_not_unique' => 'Kategori peran SDM tidak valid.'
            ]
        ],
    ];
}
