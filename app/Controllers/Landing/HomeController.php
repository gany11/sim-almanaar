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

        // Ambil data referensi hijriah hari ini dari tabel khgt
        $currentKhgt = $khgtModel->where('masehi', $today)->first();
        
        // Fallback jika tanggal hari ini tidak persis ada di tabel, ambil yang terdekat sebelumnya
        if (!$currentKhgt) {
            $currentKhgt = $khgtModel->where('masehi <=', $today)->orderBy('masehi', 'DESC')->first();
        }

        $bulanHijriah = $currentKhgt['hijriah_bulan'] ?? null;
        $tahunHijriah = $currentKhgt['hijriah_tahun'] ?? null;

        $tarawihList = [];
        // Jika bulan saat ini Sya'ban (8) atau Ramadhan (9)
        if (in_array($bulanHijriah, [8, 9])) {
            $tarawihList = $khgtModel->getListTarawihDates($tahunHijriah);
        }

        $data = [
            'title'             => 'Beranda',
            'carousels'         => $carouselModel->getActiveCarousel(),
            'finance'           => $keuanganModel->getSummaryPerKategori(false),
            'agendas'           => $agendaModel->getAgendaMendatang(5),
            'latest_news'       => $publikasiModel->getTerbaru(3, null, 1),
            'latest_article'    => $publikasiModel->getTerbaru(3, null, 2),
            'upcoming_event'    => $khgtModel->getUpcomingIslamicEvent($today),
            'show_tarawih'      => in_array($bulanHijriah, [8, 9]), // Status penampil
            'tarawih_list'      => $tarawihList,                    // Data tabel tarawih
            'current_hijriah_l' => $bulanHijriah                    // Info bulan aktif
        ];

        return view('landing/v_home', $data);
    }

    public function privacy()
    {
        $data = [
            'title' => 'Kebijakan Privasi'
        ];
        return view('landing/v_privacy', $data);
    }

    public function terms()
    {
        $data = [
            'title' => 'Syarat & Ketentuan'
        ];
        return view('landing/v_terms', $data);
    }
}