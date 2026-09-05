<?php

namespace App\Models;

use CodeIgniter\Model;

class PemasukanDonasiModel extends Model
{
    protected $table            = 'pemasukan_donasi';
    protected $primaryKey       = 'id_pemasukan_donasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id_donasi', 'id_donatur', 'id_metode_pemasukan', 'id_status_donasi', 
        'tanggal', 'no_kwitansi', 'jumlah', 'keterangan', 'samarkan', 
        'bukti', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'id_pemasukan_donasi' => 'permit_empty|integer', // <-- Tambahkan ini agar placeholder terbaca
        'id_donatur'          => 'required|integer',
        'id_status_donasi'    => 'required|integer',
        'samarkan'            => 'required|in_list[Y,N]',
        'jumlah'              => 'permit_empty|decimal',
        'no_kwitansi'         => 'permit_empty|max_length[50]|is_unique[pemasukan_donasi.no_kwitansi,id_pemasukan_donasi,{id_pemasukan_donasi}]',
    ];

    protected $validationMessages = [
        'id_pemasukan_donasi' => [
            'integer' => 'Format ID pemasukan tidak valid.',
        ],
        'id_donatur' => [
            'required' => 'Data donatur wajib dipilih.',
            'integer'  => 'Format ID donatur tidak valid.',
        ],
        'id_status_donasi' => [
            'required' => 'Status donasi wajib dipilih.',
            'integer'  => 'Format ID status tidak valid.',
        ],
        'samarkan' => [
            'required' => 'Opsi samarkan nama wajib ditentukan.',
            'in_list'  => 'Pilihan samarkan harus bernilai Y atau N.',
        ],
        'jumlah' => [
            'decimal' => 'Nominal jumlah harus berupa angka desimal.',
        ],
        'no_kwitansi' => [
            'max_length' => 'Nomor kwitansi maksimal 50 karakter.',
            'is_unique'  => 'Nomor kwitansi ini sudah pernah digunakan. Harap gunakan nomor yang berbeda.',
        ],
    ];

    protected $skipValidation = false;

    public function getPemasukanLengkap($id = null)
    {
        $builder = $this->select('pemasukan_donasi.*, donasi.judul as judul_donasi, donatur.nama as nama_donatur, donatur.telepon as telepon_donatur, metode_pemasukan.metode_pemasukan, status_donasi.status_donasi, status_donasi.class_color as status_color')
            ->join('donasi', 'donasi.id_donasi = pemasukan_donasi.id_donasi', 'left')
            ->join('donatur', 'donatur.id_donatur = pemasukan_donasi.id_donatur', 'left')
            ->join('metode_pemasukan', 'metode_pemasukan.id_metode_pemasukan = pemasukan_donasi.id_metode_pemasukan', 'left')
            ->join('status_donasi', 'status_donasi.id_status_donasi = pemasukan_donasi.id_status_donasi', 'left');

        if ($id !== null) {
            return $builder->where('pemasukan_donasi.id_pemasukan_donasi', $id)->first();
        }

        return $builder->findAll();
    }

    public function getLatestDonationLeaderboard(int $limit = 5)
    {
        return $this->select([
                'donatur.nama',
                'donatur.rt',
                'donatur.rw',
                'donatur.kelurahan',
                'pemasukan_donasi.samarkan',
                'pemasukan_donasi.jumlah',
                'histori_status_donasi.waktu',
            ])
            ->join(
                'donatur',
                'donatur.id_donatur = pemasukan_donasi.id_donatur',
                'inner'
            )
            ->join(
                'histori_status_donasi',
                'histori_status_donasi.id_pemasukan_donasi = pemasukan_donasi.id_pemasukan_donasi',
                'inner'
            )
            ->where('histori_status_donasi.id_status_donasi', 4)
            ->where('pemasukan_donasi.deleted_at IS NULL', null, false)
            ->where('histori_status_donasi.deleted_at IS NULL', null, false)
            ->orderBy('histori_status_donasi.waktu', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getPublicDonationIncomes(int $idDonasi): array
    {
        return $this->select([
                // 'pemasukan_donasi.id_pemasukan_donasi',
                'pemasukan_donasi.jumlah',
                'pemasukan_donasi.samarkan',
                'pemasukan_donasi.no_kwitansi',

                'donatur.nama',
                'donatur.rt',
                'donatur.rw',
                'donatur.kelurahan',

                'histori_status_donasi.waktu',
            ])
            ->join(
                'donatur',
                'donatur.id_donatur = pemasukan_donasi.id_donatur',
                'inner'
            )
            ->join(
                'histori_status_donasi',
                '
                    histori_status_donasi.id_pemasukan_donasi = pemasukan_donasi.id_pemasukan_donasi
                    AND histori_status_donasi.id_status_donasi = 4
                    AND histori_status_donasi.waktu = (
                        SELECT MAX(h2.waktu)
                        FROM histori_status_donasi h2
                        WHERE h2.id_pemasukan_donasi = pemasukan_donasi.id_pemasukan_donasi
                        AND h2.id_status_donasi = 4
                        AND h2.deleted_at IS NULL
                    )
                ',
                'inner',
                false
            )
            ->where(
                'pemasukan_donasi.id_donasi',
                $idDonasi
            )
            ->where(
                'pemasukan_donasi.deleted_at IS NULL',
                null,
                false
            )
            ->where(
                'histori_status_donasi.deleted_at IS NULL',
                null,
                false
            )
            ->orderBy(
                'histori_status_donasi.waktu',
                'DESC'
            )
            ->findAll();
    }

    /**
     * Mengambil daftar riwayat donasi milik donatur tertentu beserta histori statusnya
     * dengan pemilihan kolom yang spesifik dan efisien.
     *
     * @param int $idDonatur
     * @return array
     */
    public function getDonationsByDonor(int $idDonatur): array
    {
        // Ambil data utama pemasukan donasi yang berelasi
        $donations = $this->select([
                'pemasukan_donasi.id_pemasukan_donasi',
                'pemasukan_donasi.id_donasi',
                'pemasukan_donasi.tanggal',
                'pemasukan_donasi.no_kwitansi',
                'pemasukan_donasi.jumlah',
                'pemasukan_donasi.keterangan as keterangan_pemasukan',
                'donasi.judul as judul_donasi',
                'metode_pemasukan.metode_pemasukan',
                'status_donasi.id_status_donasi', // <-- Ditambahkan untuk referensi status
                'status_donasi.status_donasi',
                'status_donasi.class_color as status_color',
                'keuangan.id_keuangan',
                'keuangan.keterangan as keterangan_keuangan'
            ])
            ->join(
                'donasi',
                'donasi.id_donasi = pemasukan_donasi.id_donasi',
                'left'
            )
            ->join(
                'metode_pemasukan',
                'metode_pemasukan.id_metode_pemasukan = pemasukan_donasi.id_metode_pemasukan',
                'left'
            )
            ->join(
                'status_donasi',
                'status_donasi.id_status_donasi = pemasukan_donasi.id_status_donasi',
                'left'
            )
            ->join(
                'keuangan',
                'keuangan.id_pemasukan_donasi = pemasukan_donasi.id_pemasukan_donasi',
                'left'
            )
            ->where('pemasukan_donasi.id_donatur', $idDonatur)
            ->where('pemasukan_donasi.id_status_donasi', 4)
            ->where('pemasukan_donasi.deleted_at IS NULL', null, false)
            ->orderBy('pemasukan_donasi.tanggal', 'DESC')
            ->findAll();

        $historiModel = model('HistoriStatusDonasiModel');

        foreach ($donations as &$donation) {
            
            // 1. Logika Keterangan Tampilan
            if (!empty($donation['keterangan_keuangan'])) {
                $donation['keterangan_tampilan'] = $donation['keterangan_keuangan'];
            } elseif (!empty($donation['judul_donasi'])) {
                $donation['keterangan_tampilan'] = $donation['judul_donasi'];
            } else {
                $donation['keterangan_tampilan'] = $donation['keterangan_pemasukan'] ?? '-';
            }

            // 2. Logika Kategori (Kas / Donasi / Tercatat)
            if (!empty($donation['id_keuangan'])) {
                $donation['kategori_label'] = 'Kas';
                $donation['kategori_color'] = 'bg-blue-50 text-blue-600 border-blue-100';
            } elseif (!empty($donation['id_donasi'])) {
                $donation['kategori_label'] = 'Donasi';
                $donation['kategori_color'] = 'bg-emerald-50 text-emerald-600 border-emerald-100';
            } else {
                $donation['kategori_label'] = 'Tercatat';
                $donation['kategori_color'] = 'bg-gray-50 text-gray-600 border-gray-100';
            }

            // 3. Format Tanggal ke Format Indo Full
            if (!empty($donation['tanggal']) && function_exists('format_indo')) {
                $donation['tanggal_formatted'] = format_indo($donation['tanggal'], 'full');
            } else {
                $donation['tanggal_formatted'] = !empty($donation['tanggal']) ? date('d/m/Y', strtotime($donation['tanggal'])) : '-';
            }

            // Ambil data histori status beserta nama_pengurus
            $donation['histories'] = $historiModel
                ->select([
                    'histori_status_donasi.id_histori_status_donasi',
                    'histori_status_donasi.waktu',
                    'histori_status_donasi.nama_pengurus',
                    'status_donasi.status_donasi',
                    'status_donasi.class_color'
                ])
                ->join(
                    'status_donasi',
                    'status_donasi.id_status_donasi = histori_status_donasi.id_status_donasi',
                    'left'
                )
                ->where('histori_status_donasi.id_pemasukan_donasi', $donation['id_pemasukan_donasi'])
                ->orderBy('histori_status_donasi.waktu', 'DESC')
                ->orderBy('histori_status_donasi.id_status_donasi', 'DESC')
                ->findAll();

            // Format waktu histori
            foreach ($donation['histories'] as &$history) {
                $history['waktu_formatted'] = !empty($history['waktu']) && function_exists('format_indo')
                    ? format_indo($history['waktu'], 'full_datetime')
                    : ($history['waktu'] ?? '-');
            }
            unset($history);
        }
        unset($donation);

        return $donations;
    }
}