<?php

namespace App\Models;

use CodeIgniter\Model;

class KeuanganModel extends Model
{
    protected $table            = 'keuangan';
    protected $primaryKey       = 'id_keuangan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kategori_keuangan', 'tanggal', 'jumlah', 
        'jenis', 'keterangan', 'bukti', 
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public $validationKeuangan = [
        'id_kategori_keuangan' => [
            'rules'  => 'required|is_not_unique[kategori_keuangan.id_kategori_keuangan]',
            'errors' => [
                'required'      => 'Pilih kategori kas terlebih dahulu.',
                'is_not_unique' => 'Kategori yang dipilih tidak valid.'
            ]
        ],
        'tanggal' => [
            'rules'  => 'required|valid_date',
            'errors' => [
                'required'   => 'Tanggal transaksi harus diisi.',
                'valid_date' => 'Format tanggal tidak valid.'
            ]
        ],
        'jumlah' => [
            'rules'  => 'required|numeric|greater_than[0]',
            'errors' => [
                'required'     => 'Nominal uang wajib diisi.',
                'numeric'      => 'Nominal harus berupa angka.',
                'greater_than' => 'Nominal harus lebih besar dari Rp 0.'
            ]
        ],
        'jenis' => [
            'rules'  => 'required|in_list[pemasukan,pengeluaran]',
            'errors' => [
                'required' => 'Pilih jenis transaksi (Pemasukan/Pengeluaran).',
                'in_list'  => 'Jenis transaksi tidak valid.'
            ]
        ],
        'keterangan' => [
            'rules'  => 'required|string|min_length[5]',
            'errors' => [
                'required'   => 'Keterangan transaksi wajib diisi.',
                'min_length' => 'Keterangan terlalu singkat (minimal 5 karakter).'
            ]
        ],
        'bukti' => [
            'rules'  => 'max_size[bukti,5120]|ext_in[bukti,pdf]',
            'errors' => [
                'max_size' => 'Ukuran file bukti maksimal 5MB.',
                'ext_in'   => 'Bukti hanya diperbolehkan dalam format PDF.'
            ]
        ],
    ];

    /**
     * Mendapatkan data keuangan beserta nama kategorinya
     */
    public function getKeuanganWithKategori()
    {
        return $this->select('keuangan.*, kategori_keuangan.kategori')
                    ->join('kategori_keuangan', 'kategori_keuangan.id_kategori_keuangan = keuangan.id_kategori_keuangan')
                    ->findAll();
    }

    /**
     * Mengambil saldo per kategori
     */
    public function getSummaryPerKategori()
    {
        return $this->db->table('kategori_keuangan k')
            ->select('k.kategori, k.class_color, 
                COALESCE(SUM(CASE WHEN keu.jenis = "pemasukan" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) - 
                COALESCE(SUM(CASE WHEN keu.jenis = "pengeluaran" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) as saldo')
            ->join('keuangan keu', 'keu.id_kategori_keuangan = k.id_kategori_keuangan', 'left')
            ->groupBy('k.id_kategori_keuangan')
            ->get()->getResultArray();
    }

    /**
     * Data Chart Total (Pemasukan vs Pengeluaran)
     */
    public function getChartTotal($limitBulan = 6)
    {
        return $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%M') as bulan,
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as keluar
            FROM keuangan
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL $limitBulan MONTH)
            AND deleted_at IS NULL
            GROUP BY MONTH(created_at), bulan
            ORDER BY created_at ASC
        ")->getResultArray();
    }

    /**
     * Data Neto per Kategori untuk Chart Detail
     */
    public function getNetoByKategori($idKategori, $limitBulan = 6)
    {
        return $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%M') as bulan,
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) - 
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as neto
            FROM keuangan
            WHERE id_kategori_keuangan = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND deleted_at IS NULL
            GROUP BY MONTH(created_at), bulan
            ORDER BY created_at ASC
        ", [$idKategori, $limitBulan])->getResultArray();
    }
}
