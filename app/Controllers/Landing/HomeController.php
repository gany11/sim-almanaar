<?php

namespace App\Controllers\Landing;

use App\Models\CarouselModel;
use App\Models\KeuanganModel;
use App\Models\AgendaModel;
use App\Models\PublikasiModel;
use App\Models\KhgtModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    public function index()
    {
        $carouselModel  = new CarouselModel();
        $keuanganModel  = new KeuanganModel();
        $agendaModel    = new AgendaModel();
        $publikasiModel = new PublikasiModel();
        $khgtModel      = new KhgtModel();

        $today = date('Y-m-d');

        $data = [
            'title'             => 'Beranda',
            'carousels'         => $carouselModel->getActiveCarousel(),
            'finance'           => $keuanganModel->getSummaryPerKategori(false),
            'agendas'           => $agendaModel->getAgendaMendatang(5),
            'latest_news'       => $publikasiModel->getTerbaru(3, null, 1),
            'latest_article'    => $publikasiModel->getTerbaru(3, null, 2),
            'upcoming_event'    => $khgtModel->getUpcomingIslamicEvent($today),
        ];

        return view('landing/v_home', $data);
    }
}
