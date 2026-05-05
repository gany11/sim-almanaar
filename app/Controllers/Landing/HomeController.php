<?php

namespace App\Controllers\Landing;

use App\Models\CarouselModel;
use App\Models\KeuanganModel;
use App\Models\AgendaModel;
use App\Models\PublikasiModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    public function index()
    {
        $carouselModel = new CarouselModel();
        $keuanganModel = new KeuanganModel();
        $agendaModel   = new AgendaModel();
        $publikasiModel = new PublikasiModel();

        $data = [
            'title'      => 'Beranda - Masjid Al-Manaar Slipi',
            'carousels'  => $carouselModel->getActiveCarousel(),
            'finance'    => $keuanganModel->getSummaryPerKategori(),
            'agendas'    => $agendaModel->getAgendaMendatang(5),
            'latest_news'    => $publikasiModel->getTerbaru(3, null, 1),
            'latest_article' => $publikasiModel->getTerbaru(3, null, 2),
        ];

        return view('landing/v_home', $data);
    }
}
