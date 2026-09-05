<?php

namespace App\Models;

use CodeIgniter\Model;

class DonasiModel extends Model
{
    protected $table            = 'donasi';
    protected $primaryKey       = 'id_donasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'judul', 'deskripsi', 'akronim_kwitansi', 'total_pemasukan', 
        'total_pengeluaran', 'proposal', 'laporan', 'status', 
        'jenis_donasi', 'closed_at', 'closed_by', 'created_by', 
        'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_donasi'        => 'permit_empty|integer',
        'judul'            => 'required|max_length[150]',
        'akronim_kwitansi' => 'required|max_length[20]|alpha_numeric|regex_match[/^[A-Z0-9]+$/]|is_unique[donasi.akronim_kwitansi,id_donasi,{id_donasi}]',
        'status'           => 'required|in_list[aktif,pasif]',
        'jenis_donasi'     => 'required|in_list[Zakat,Infak,Sedekah,Wakaf,Donasi Umum]',
    ];

    protected $validationMessages = [
        'judul' => [
            'required'   => 'Judul program donasi wajib diisi.',
            'max_length' => 'Judul program donasi maksimal 150 karakter.',
        ],
        'akronim_kwitansi' => [
            'required'      => 'Akronim kwitansi wajib diisi.',
            'max_length'    => 'Akronim kwitansi maksimal 20 karakter.',
            'alpha_numeric' => 'Akronim kwitansi hanya boleh berisi huruf dan angka.',
            'regex_match'   => 'Akronim kwitansi harus berupa huruf besar (uppercase A-Z) dan angka tanpa spasi atau karakter khusus.',
            'is_unique'     => 'Akronim kwitansi ini sudah digunakan oleh program lain. Gunakan akronim yang berbeda.',
        ],
        'status' => [
            'required' => 'Status publikasi wajib dipilih.',
            'in_list'  => 'Status publikasi harus bernilai aktif atau pasif.',
        ],
        'jenis_donasi' => [
            'required' => 'Jenis donasi wajib dipilih.',
            'in_list'  => 'Pilihan jenis donasi tidak valid.',
        ],
    ];

    protected $skipValidation = false;

    public function tutupDonasi($idDonasi, $userId)
    {
        $db = \Config\Database::connect();
        
        $pemasukan = (float) $db->table('pemasukan_donasi')
            ->selectSum('jumlah')
            ->where('id_donasi', $idDonasi)
            ->where('deleted_at', null)
            ->get()->getRow()->jumlah ?? 0;

        $pengeluaran = (float) $db->table('pengeluaran_donasi')
            ->selectSum('sub_total')
            ->where('id_donasi', $idDonasi)
            ->where('deleted_at', null)
            ->get()->getRow()->sub_total ?? 0;

        // Validasi: Total pemasukan dan pengeluaran harus sama persis (balance)
        if ($pemasukan !== $pengeluaran) {
            return [
                'status'  => 'error',
                'message' => 'Gagal menutup program! Total pemasukan (Rp ' . number_format($pemasukan, 0, ',', '.') . ') dan total pengeluaran (Rp ' . number_format($pengeluaran, 0, ',', '.') . ') harus bernilai sama (balance).'
            ];
        }

        $update = $this->update($idDonasi, [
            'total_pemasukan'   => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'closed_at'         => date('Y-m-d H:i:s'),
            'closed_by'         => $userId,
        ]);

        if ($update) {
            return [
                'status'  => 'success',
                'message' => 'Program donasi berhasil ditutup (closed) dan total dana dikunci.'
            ];
        }

        return [
            'status'  => 'error',
            'message' => 'Gagal memperbarui status database program donasi.'
        ];
    }

    public function getActiveDonations()
    {
        return $this->select([
                'donasi.id_donasi',
                'donasi.judul',
                'donasi.closed_at',
                'COALESCE(SUM(pemasukan_donasi.jumlah), 0) AS pemasukan',
            ])
            ->join(
                'pemasukan_donasi',
                'pemasukan_donasi.id_donasi = donasi.id_donasi
                AND pemasukan_donasi.deleted_at IS NULL',
                'left',
                false
            )
            ->where('donasi.status', 'aktif')
            ->where('donasi.deleted_at IS NULL', null, false)
            ->groupBy([
                'donasi.id_donasi',
                'donasi.judul',
            ])
            ->orderBy('donasi.id_donasi', 'DESC')
            ->findAll();
    }

    public function getPublicDonationDetail(int $idDonasi): ?array
    {
        $donation = $this->select([
                'donasi.id_donasi',
                'donasi.judul',
                'donasi.deskripsi',
                'donasi.total_pemasukan',
                'donasi.total_pengeluaran',
                'donasi.proposal',
                'donasi.laporan',
                'donasi.status',
                'donasi.closed_at',
                'donasi.closed_by',

                'close.nama AS closed_by_nama',
            ])
            ->join(
                'akun as close',
                'close.id_akun = donasi.closed_by',
                'left'
            )
            ->where('donasi.id_donasi', $idDonasi)
            ->where('donasi.deleted_at IS NULL', null, false)
            ->first();

        if (! $donation) {
            return null;
        }

        /*
        * Hanya donasi dengan status aktif
        * yang boleh dipublikasikan.
        */
        if ($donation['status'] !== 'aktif') {
            return null;
        }

        /*
        * Jika sudah ditutup, gunakan nilai yang
        * sudah disimpan di tabel donasi.
        */
        if (! empty($donation['closed_at'])) {

            $donation['pemasukan'] =
                (float) ($donation['total_pemasukan'] ?? 0);

            $donation['pengeluaran'] =
                (float) ($donation['total_pengeluaran'] ?? 0);

        } else {

            /*
            * Belum ditutup:
            * hitung pemasukan secara manual.
            */
            $income = $this->db->table('pemasukan_donasi')
                ->selectSum('jumlah', 'total')
                ->where('id_donasi', $idDonasi)
                ->where('deleted_at IS NULL', null, false)
                ->get()
                ->getRow();

            /*
            * Belum ditutup:
            * hitung pengeluaran berdasarkan subtotal.
            */
            $expense = $this->db->table('pengeluaran_donasi')
                ->selectSum('sub_total', 'total')
                ->where('id_donasi', $idDonasi)
                ->where('deleted_at IS NULL', null, false)
                ->get()
                ->getRow();

            $donation['pemasukan'] =
                (float) ($income->total ?? 0);

            $donation['pengeluaran'] =
                (float) ($expense->total ?? 0);
        }

        return $donation;
    }

    /**
     * Mengambil 2 rekomendasi donasi terbaru yang aktif dan belum pernah 
     * didonasikan oleh donatur tertentu dengan status donasi 4.
     *
     * @param int $idDonatur
     * @return array
     */
    public function getPublicDonationRecommendations(int $idDonatur): array
    {
        // Subquery untuk mencari id_donasi yang sudah memiliki status_donasi = 4 oleh donatur ini
        $subquery = $this->db->table('pemasukan_donasi')
            ->select('id_donasi')
            ->where('id_donatur', $idDonatur)
            ->where('id_status_donasi', 4)
            ->where('deleted_at IS NULL', null, false);

        return $this->select([
                'id_donasi',
                'judul',
                'deskripsi',
            ])
            ->where('status', 'aktif')
            ->where('closed_at IS NULL', null, false)
            ->where('deleted_at IS NULL', null, false)
            // Memastikan id_donasi tidak ada di dalam subquery donatur dengan status 4
            ->whereNotIn('id_donasi', $subquery)
            ->orderBy('created_at', 'DESC')
            ->limit(2)
            ->findAll();
    }
}