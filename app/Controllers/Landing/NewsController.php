<?php

namespace App\Controllers\Landing;

use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class NewsController extends BaseController
{
    protected $publikasiModel;

    public function __construct()
    {
        $this->publikasiModel = new PublikasiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Berita',
            'news'  => $this->publikasiModel
                            ->where(['id_jenis_publikasi' => 1, 'status' => 'aktif'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(9, 'news'),
            'pager' => $this->publikasiModel->pager,
        ];

        return view('landing/news/v_news_list', $data);
    }

   public function detail($date, $slug)
    {
        $news = $this->publikasiModel->select('publikasi.*, akun.nama as nama_admin')
            ->join('akun', 'akun.id_akun = publikasi.created_by', 'left')
            ->where('slug', $slug)
            ->where('id_jenis_publikasi', 1)
            ->where('publikasi.status', 'aktif')
            ->where("DATE_FORMAT(publikasi.created_at, '%Y%m%d') =", $date)
            ->first();

        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Berita tidak ditemukan.");
        }

        $data = [
            'title'   => $news['judul'],
            'news'    => $news,
            'terbaru'     => $this->publikasiModel->getTerbaru(5, $news['id_publikasi']),
            'current_url' => current_url()
        ];

        return view('landing/news/v_news_detail', $data);
    }
}
