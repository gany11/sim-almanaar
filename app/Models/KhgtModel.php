<?php

namespace App\Models;

use CodeIgniter\Model;

class KhgtModel extends Model
{
    protected $table            = 'khgt';
    protected $primaryKey       = 'masehi';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    
    protected $allowedFields    = [
        'masehi', 
        'hijriah_tanggal', 
        'hijriah_bulan', 
        'hijriah_tahun', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Mencari perayaan hari besar Islam terdekat dalam rentang 100 hari ke depan
     * dari tanggal Masehi yang di-request.
     * 
     * @param string $requestDate Format 'YYYY-MM-DD'
     * @return array|null Mengembalikan data tanggal dan nama perayaan terdekat, atau null jika tidak ada.
     */
    public function getUpcomingIslamicEvent(string $requestDate)
    {
        // Daftar hari besar Islam yang dicari
        // [hijriah_bulan => [hijriah_tanggal => 'Nama Perayaan']]
        $eventsMap = [
            1  => [1  => 'Tahun Baru Islam'],
            9  => [1  => 'Ramadhan'],
            10 => [1  => 'Hari Raya Idul Fitri'],
            12 => [1  => 'Hari Raya Idul Adha'],
        ];

        // Hitung batas maksimal 100 hari ke depan
        $startDate = $requestDate;
        $endDate = date('Y-m-d', strtotime($requestDate . ' +100 days'));

        // Query data dari tabel khgt berdasarkan rentang tanggal Masehi
        $builder = $this->where('masehi >=', $startDate)
                        ->where('masehi <=', $endDate)
                        ->orderBy('masehi', 'ASC');

        $records = $builder->findAll();

        if (empty($records)) {
            return null;
        }

        // Lakukan iterasi untuk mencocokkan dengan list hari besar
        foreach ($records as $row) {
            $bulan = (int)$row['hijriah_bulan'];
            $tanggal = (int)$row['hijriah_tanggal'];
            $tahun = (int)$row['hijriah_tahun'];

            if (isset($eventsMap[$bulan][$tanggal])) {
                $namaPerayaan = $eventsMap[$bulan][$tanggal];
                
                $namaPerayaan .= ' ' . $tahun . ' H';
                
                return [
                    'masehi'          => $row['masehi'],
                    'hijriah_tanggal' => $tanggal,
                    'hijriah_bulan'   => $bulan,
                    'hijriah_tahun'   => $tahun,
                    'nama_perayaan'   => $namaPerayaan,
                    'selisih_hari'    => (strtotime($row['masehi']) - strtotime($requestDate)) / (60 * 60 * 24)
                ];
            }
        }

        return null;
    }

    /**
     * 1. Cek validasi apakah tanggal Masehi yang di-request merupakan waktu Sholat Tarawih
     * (Mulai dari Maghrib H-1 tanggal 1 Ramadhan hingga malam sebelum hari terakhir Ramadhan)
     * 
     * @param string $requestDate Format 'YYYY-MM-DD'
     * @return bool
     */
    public function isValidTarawihDate(string $requestDate): bool
    {
        // Cari data 1 Ramadhan tahun hijriah yang bersangkutan
        // Di database tercatat tanggal Masehi-nya, kita kurangi 1 hari untuk malam tarawih pertama
        
        // Ambil data tahun hijriah yang mencakup rentang tanggal ini (misal cari di sekitar 30 hari ke depan/belakang)
        $db = \Config\Database::connect();
        
        // Cari data 1 Ramadhan 
        $ramadhanPertama = $this->where('hijriah_bulan', 9)
                                ->where('hijriah_tanggal', 1)
                                ->orderBy('masehi', 'ASC')
                                ->findAll();

        if (empty($ramadhanPertama)) {
            return false;
        }

        // Tentukan rentang Masehi untuk Tarawih
        foreach ($ramadhanPertama as $row) {
            $tglMasehiRamadhan = $row['masehi']; // Misal: 2027-02-08
            $hijriahTahun = $row['hijriah_tahun'];

            // Malam Tarawih pertama dimulai pada Masehi H-1 dari tanggal 1 Ramadhan di DB
            $startTarawih = date('Y-m-d', strtotime($tglMasehiRamadhan . ' -1 day')); // 2027-02-07

            // Cari akhir Ramadhan (tanggal 29 atau 30 di bulan 9 tahun tersebut)
            $lastRamadhan = $this->where('hijriah_bulan', 9)
                                 ->where('hijriah_tahun', $hijriahTahun)
                                 ->orderBy('hijriah_tanggal', 'DESC')
                                 ->first();

            if (!$lastRamadhan) continue;

            // Malam tarawih terakhir adalah malam pada tanggal terakhir Ramadhan (sebelum hari Idul Fitri/Syawal)
            // Jadi batas akhirnya adalah tanggal Masehi hari terakhir Ramadhan itu sendiri (karena malamnya sudah malam Takbiran)
            $endTarawih = $lastRamadhan['masehi']; 

            // Cek apakah $requestDate berada di antara $startTarawih dan $endTarawih (inklusif)
            if ($requestDate >= $startTarawih && $requestDate < $endTarawih) {
                return true;
            }
        }

        return false;
    }

    /**
     * 2. Mendapatkan daftar lengkap tabel Sholat Tarawih beserta petugas SDM (Imam, Penceramah, Protokol)
     * 
     * @param int|null $hijriahYear Tahun Hijriah (opsional, misal: 1448)
     * @return array
     */
    public function getListTarawihDates($hijriahYear = null)
    {
        if (empty($hijriahYear)) {
            $todayKhgt = $this->where('masehi <=', date('Y-m-d'))
                              ->orderBy('masehi', 'DESC')
                              ->first();
            $hijriahYear = $todayKhgt['hijriah_tahun'] ?? (date('Y') + 579);
        }

        $ramadhanDays = $this->where('hijriah_bulan', 9)
                             ->where('hijriah_tahun', $hijriahYear)
                             ->orderBy('hijriah_tanggal', 'ASC')
                             ->findAll();

        if (empty($ramadhanDays)) {
            return [];
        }

        $db = \Config\Database::connect();
        $listTarawih = [];
        $malamKe = 1;

        foreach ($ramadhanDays as $row) {
            $tglMasehiDb = $row['masehi'];
            
            // Kurangi 1 hari dari data DB untuk malam Tarawih
            $tglTarawihMasehi = date('Y-m-d', strtotime($tglMasehiDb . ' -1 day'));

            $agenda = $db->table('agenda')
                         ->where('id_kategori_agenda', 9)
                         ->where('DATE(waktu_mulai)', $tglTarawihMasehi)
                         ->where('deleted_at IS NULL')
                         ->get()
                         ->getRowArray();

            $idAgenda = $agenda['id_agenda'] ?? null;
            $judulAgenda = $agenda['judul'] ?? null;
            $temaAgenda = $agenda['tema'] ?? null;

            $penceramah = ['id_sdm' => null, 'nama' => '-'];
            $imam       = ['id_sdm' => null, 'nama' => '-'];
            $protokol   = ['id_sdm' => null, 'nama' => '-'];

            if ($idAgenda) {
                $petugasList = $db->table('sdm_agenda sa')
                                  ->select('sa.id_sdm, sa.id_kategori_sdm, s.nama')
                                  ->join('sdm s', 's.id_sdm = sa.id_sdm')
                                  ->where('sa.id_agenda', $idAgenda)
                                  ->where('sa.deleted_at IS NULL')
                                  ->get()
                                  ->getResultArray();

                foreach ($petugasList as $p) {
                    if ($p['id_kategori_sdm'] == 1) {
                        $imam = ['id_sdm' => $p['id_sdm'], 'nama' => $p['nama']];
                    } elseif ($p['id_kategori_sdm'] == 7) {
                        $penceramah = ['id_sdm' => $p['id_sdm'], 'nama' => $p['nama']];
                    } elseif ($p['id_kategori_sdm'] == 5) {
                        $protokol = ['id_sdm' => $p['id_sdm'], 'nama' => $p['nama']];
                    }
                }
            }

            $listTarawih[] = [
                'malam_ke'         => $malamKe,
                'tanggal_ramadhan' => $malamKe . ' Ramadhan ' . $hijriahYear . ' H',
                'tanggal_masehi'   => $tglTarawihMasehi,
                'id_agenda'        => $idAgenda,
                'judul_agenda'     => $judulAgenda,
                'tema_agenda'      => $temaAgenda,
                'penceramah'       => $penceramah,
                'imam'             => $imam,
                'protokol'         => $protokol,
            ];

            $malamKe++;
        }

        return $listTarawih;
    }
}