<?php

namespace App\Controllers\Landing;

use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ArticleController extends BaseController
{
    protected $publikasiModel;

    public function __construct()
    {
        $this->publikasiModel = new PublikasiModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Artikel Islami',
            'article'  => $this->publikasiModel
                            ->where(['id_jenis_publikasi' => 2, 'status' => 'aktif'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(9, 'article'),
            'pager'    => $this->publikasiModel->pager,
        ];

        return view('landing/article/v_article_list', $data);
    }

    public function detail($date, $slug)
    {
        $article = $this->publikasiModel->select('publikasi.*, akun.nama as nama_admin')
            ->join('akun', 'akun.id_akun = publikasi.created_by', 'left')
            ->where('slug', $slug)
            ->where('id_jenis_publikasi', 2)
            ->where('publikasi.status', 'aktif')
            ->where("DATE_FORMAT(publikasi.created_at, '%Y%m%d') =", $date)
            ->first();

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        $data = [
            'title'       => $article['judul'],
            'article'     => $article,
            'terbaru'     => $this->publikasiModel->getTerbaru(5, $article['id_publikasi']),
            'current_url' => current_url()
        ];

        return view('landing/article/v_article_detail', $data);
    }
}
