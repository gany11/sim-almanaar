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
            9  => [1  => 'Bulan Ramadhan'],
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
}