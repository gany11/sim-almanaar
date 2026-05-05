<?php

namespace App\Models;

use CodeIgniter\Model;

class CarouselModel extends Model
{
    protected $table            = 'carousel';
    protected $primaryKey       = 'id_carousel';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['file', 'status', 'started_at', 'ended_at', 'created_by', 'updated_by', 'deleted_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Ambil carousel yang aktif dan dalam rentang waktu yang valid
     */
    public function getActiveCarousel()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('status', 'aktif')
            ->groupStart()
                ->where('started_at <=', $now)
                ->orWhere('started_at', null)
            ->groupEnd()
            ->groupStart()
                ->where('ended_at >=', $now)
                ->orWhere('ended_at', null)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
