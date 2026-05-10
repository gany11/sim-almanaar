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
    // Iterasi 1
    // protected $allowedFields    = [
    //     'id_kategori_keuangan', 'tanggal', 'jumlah', 
    //     'jenis', 'keterangan', 'bukti', 
    //     'created_by', 'updated_by', 'deleted_by'
    // ];

    // Iterasi 2
    protected $allowedFields    = [
        'id_kategori_keuangan', 'id_detail_alokasi', 'tanggal', 'jumlah', 
        'jenis', 'keterangan', 'pic', 'method_input', 'bukti', 
        'created_by', 'updated_by', 'deleted_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Iterasi 1
    // public $validationKeuangan = [
    //     'id_kategori_keuangan' => [
    //         'rules'  => 'required|is_not_unique[kategori_keuangan.id_kategori_keuangan]',
    //         'errors' => [
    //             'required'      => 'Pilih kategori kas terlebih dahulu.',
    //             'is_not_unique' => 'Kategori yang dipilih tidak valid.'
    //         ]
    //     ],
    //     'tanggal' => [
    //         'rules'  => 'required|valid_date',
    //         'errors' => [
    //             'required'   => 'Tanggal transaksi harus diisi.',
    //             'valid_date' => 'Format tanggal tidak valid.'
    //         ]
    //     ],
    //     'jumlah' => [
    //         'rules'  => 'required|numeric|greater_than[0]',
    //         'errors' => [
    //             'required'     => 'Nominal uang wajib diisi.',
    //             'numeric'      => 'Nominal harus berupa angka.',
    //             'greater_than' => 'Nominal harus lebih besar dari Rp 0.'
    //         ]
    //     ],
    //     'jenis' => [
    //         'rules'  => 'required|in_list[pemasukan,pengeluaran]',
    //         'errors' => [
    //             'required' => 'Pilih jenis transaksi (Pemasukan/Pengeluaran).',
    //             'in_list'  => 'Jenis transaksi tidak valid.'
    //         ]
    //     ],
    //     'keterangan' => [
    //         'rules'  => 'required|string|min_length[5]',
    //         'errors' => [
    //             'required'   => 'Keterangan transaksi wajib diisi.',
    //             'min_length' => 'Keterangan terlalu singkat (minimal 5 karakter).'
    //         ]
    //     ],
    //     'bukti' => [
    //         'rules'  => 'max_size[bukti,5120]|ext_in[bukti,pdf]',
    //         'errors' => [
    //             'max_size' => 'Ukuran file bukti maksimal 5MB.',
    //             'ext_in'   => 'Bukti hanya diperbolehkan dalam format PDF.'
    //         ]
    //     ],
    // ];

    // Iterasi 2
    public $validationKeuangan = [
        'id_kategori_keuangan' => [
            'rules'  => 'required|is_not_unique[kategori_keuangan.id_kategori_keuangan]',
            'errors' => [
                'required'      => 'Pilih kategori kas terlebih dahulu.',
                'is_not_unique' => 'Kategori kas tidak valid.'
            ]
        ],
        'id_detail_alokasi' => [
            'rules'  => 'required|is_not_unique[detail_alokasi.id_detail_alokasi]',
            'errors' => [
                'required'      => 'Pilih detail alokasi terlebih dahulu.',
                'is_not_unique' => 'Detail alokasi tidak valid.'
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
                'required' => 'Pilih jenis transaksi.',
                'in_list'  => 'Jenis transaksi tidak valid.'
            ]
        ],
        'keterangan' => [
            'rules'  => 'required|string|min_length[5]',
            'errors' => [
                'required'   => 'Keterangan transaksi wajib diisi.',
                'min_length' => 'Keterangan minimal 5 karakter.'
            ]
        ],
        'bukti' => [
            // Iterasi 2 - Diubah agar mendukung PDF dan Gambar (jpg, jpeg, png)
            'rules'  => 'max_size[bukti,5120]|ext_in[bukti,pdf,jpg,jpeg,png]',
            'errors' => [
                'max_size' => 'Ukuran file bukti maksimal 5MB.',
                'ext_in'   => 'Bukti harus berformat PDF atau Gambar (JPG/PNG).'
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
    // public function getSummaryPerKategori()
    // {
    //     return $this->db->table('kategori_keuangan k')
    //         ->select('k.kategori, k.class_color, 
    //             COALESCE(SUM(CASE WHEN keu.jenis = "pemasukan" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) - 
    //             COALESCE(SUM(CASE WHEN keu.jenis = "pengeluaran" AND keu.deleted_at IS NULL THEN keu.jumlah ELSE 0 END), 0) as saldo')
    //         ->join('keuangan keu', 'keu.id_kategori_keuangan = k.id_kategori_keuangan', 'left')
    //         ->groupBy('k.id_kategori_keuangan')
    //         ->get()->getResultArray();
    // }

    // Iterasi 2
    public function getSummaryPerKategori(bool $isCurrent = true)
    {
        $builder = $this->db->table('kategori_keuangan k');
        
        $tanggalSampai = date('Y-m-d');
        $waktuBatas = date('Y-m-d H:i:s');

        if (!$isCurrent) {
            $tanggalSampai = date('Y-m-d', strtotime('last thursday'));
            $waktuBatas = $tanggalSampai . ' 23:59:59';
        }

        $builder->select("k.kategori, k.class_color");
        
        $dateFilter = $isCurrent ? "" : "AND keu.created_at <= '$waktuBatas'";
        
        $builder->select("
            (COALESCE(SUM(CASE WHEN keu.jenis = 'pemasukan' AND keu.deleted_at IS NULL $dateFilter THEN keu.jumlah ELSE 0 END), 0) - 
            COALESCE(SUM(CASE WHEN keu.jenis = 'pengeluaran' AND keu.deleted_at IS NULL $dateFilter THEN keu.jumlah ELSE 0 END), 0)) as saldo
        ");

        $builder->select("'$tanggalSampai' as tanggal_penghitungan");

        return $builder->join('keuangan keu', 'keu.id_kategori_keuangan = k.id_kategori_keuangan', 'left')
            ->groupBy('k.id_kategori_keuangan')
            ->get()->getResultArray();
    }

    /**
     * Data Chart Total (Pemasukan vs Pengeluaran)
     */
    // public function getChartTotal($limitBulan = 6)
    // {
    //     return $this->db->query("
    //         SELECT 
    //             DATE_FORMAT(created_at, '%M') as bulan,
    //             SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) as masuk,
    //             SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as keluar
    //         FROM keuangan
    //         WHERE created_at >= DATE_SUB(NOW(), INTERVAL $limitBulan MONTH)
    //         AND deleted_at IS NULL
    //         GROUP BY MONTH(created_at), bulan
    //         ORDER BY created_at ASC
    //     ")->getResultArray();
    // }

    // Iterasi 2
    public function getChartTotal($limitBulan = 6, bool $isCurrent = true)
    {
        // Tentukan batas waktu
        $waktuBatas = date('Y-m-d H:i:s');
        if (!$isCurrent) {
            $waktuBatas = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
        }

        return $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%M') as bulan,
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as keluar
            FROM keuangan
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND created_at <= ?
            AND deleted_at IS NULL
            GROUP BY YEAR(created_at), MONTH(created_at), bulan
            ORDER BY created_at ASC
        ", [$limitBulan, $waktuBatas])->getResultArray();
    }


    /**
     * Data Neto per Kategori untuk Chart Detail
     */
    // public function getNetoByKategori($idKategori, $limitBulan = 6)
    // {
    //     return $this->db->query("
    //         SELECT 
    //             DATE_FORMAT(created_at, '%M') as bulan,
    //             SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) - 
    //             SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as neto
    //         FROM keuangan
    //         WHERE id_kategori_keuangan = ?
    //         AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
    //         AND deleted_at IS NULL
    //         GROUP BY MONTH(created_at), bulan
    //         ORDER BY created_at ASC
    //     ", [$idKategori, $limitBulan])->getResultArray();
    // }

    // Iterasi 2
    public function getNetoByKategori($idKategori, $limitBulan = 6, bool $isCurrent = true)
    {
        // Tentukan batas waktu
        $waktuBatas = date('Y-m-d H:i:s');
        if (!$isCurrent) {
            $waktuBatas = date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';
        }

        return $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%M') as bulan,
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) - 
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as neto
            FROM keuangan
            WHERE id_kategori_keuangan = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND created_at <= ?
            AND deleted_at IS NULL
            GROUP BY YEAR(created_at), MONTH(created_at), bulan
            ORDER BY created_at ASC
        ", [$idKategori, $limitBulan, $waktuBatas])->getResultArray();
    }

    public function getSaldoPerKategori($idKategori, $limitBulan = 6, bool $isCurrent = true)
    {
        $waktuBatas = $isCurrent ? date('Y-m-d H:i:s') : date('Y-m-d', strtotime('last thursday')) . ' 23:59:59';

        // 1. Ambil Saldo Awal (sebelum batas interval chart)
        $awal = $this->db->query("
            SELECT SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE -jumlah END) as saldo_awal
            FROM keuangan
            WHERE id_kategori_keuangan = ?
            AND created_at < DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND deleted_at IS NULL
        ", [$idKategori, $limitBulan])->getRowArray();
        
        $saldoBerjalan = $awal['saldo_awal'] ?? 0;

        // 2. Ambil mutasi bulanan
        $mutasi = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%M') as bulan,
                SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) as masuk,
                SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) as keluar,
                MONTH(created_at) as bulan_num,
                YEAR(created_at) as tahun_num
            FROM keuangan
            WHERE id_kategori_keuangan = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND created_at <= ?
            AND deleted_at IS NULL
            GROUP BY tahun_num, bulan_num, bulan
            ORDER BY tahun_num ASC, bulan_num ASC
        ", [$idKategori, $limitBulan, $waktuBatas])->getResultArray();

        // 3. Hitung Saldo Akhir Kumulatif tiap bulan
        foreach ($mutasi as &$m) {
            $saldoBerjalan += ($m['masuk'] - $m['keluar']);
            $m['saldo_akhir'] = $saldoBerjalan;
        }

        return $mutasi;
    }
}
