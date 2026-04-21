<?php

namespace App\Models;

use CodeIgniter\Model;

class PublikasiModel extends Model
{
    protected $table            = 'publikasi';
    protected $primaryKey       = 'id_publikasi';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id_jenis_publikasi', 'judul', 'slug', 'deskripsi', 
        'sampul', 'lampiran', 'sumber_penulis', 'status', 
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Aturan Validasi untuk Berita (ID Jenis: 1)
     */
    public $validationBerita = [
        'judul' => [
            'rules'  => 'required|min_length[5]|max_length[255]',
            'errors' => [
                'required'   => 'Judul berita harus diisi.',
                'min_length' => 'Judul berita minimal 5 karakter.',
                'max_length' => 'Judul berita maksimal 255 karakter.'
            ]
        ],
        'deskripsi' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Isi berita tidak boleh kosong.'
            ]
        ],
        'sampul' => [
            'rules'  => 'max_size[sampul,2048]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
            'errors' => [
                'max_size' => 'Ukuran gambar maksimal 2MB.',
                'is_image' => 'Yang Anda pilih bukan gambar.',
                'mime_in'  => 'Format gambar harus JPG, JPEG, atau PNG.'
            ]
        ],
    ];

    /**
     * Aturan Validasi untuk Artikel (ID Jenis: 2)
     */
    public $validationArtikel = [
        'judul' => [
            'rules'  => 'required|min_length[5]',
            'errors' => [
                'required' => 'Judul artikel wajib diisi.'
            ]
        ],
        'deskripsi' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Konten artikel harus diisi.'
            ]
        ],
        'sumber_penulis' => [
            'rules'  => 'required',
            'errors' => [
                'required' => 'Sumber atau Penulis artikel wajib dicantumkan.'
            ]
        ],
        'lampiran' => [
            'rules'  => 'max_size[lampiran,5120]|ext_in[lampiran,pdf,doc,docx]',
            'errors' => [
                'max_size' => 'Ukuran file lampiran maksimal 5MB.',
                'ext_in'   => 'Lampiran hanya diperbolehkan format PDF atau DOCX.'
            ]
        ]
    ];

    public function getBerita($slug = false) {
        if ($slug === false) return $this->where('id_jenis_publikasi', 1)->findAll();
        return $this->where(['slug' => $slug, 'id_jenis_publikasi' => 1])->first();
    }

    public function getArtikel($slug = false) {
        if ($slug === false) return $this->where('id_jenis_publikasi', 2)->findAll();
        return $this->where(['slug' => $slug, 'id_jenis_publikasi' => 2])->first();
    }
}