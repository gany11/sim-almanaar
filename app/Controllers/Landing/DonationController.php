<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

use App\Models\PemasukanDonasiModel;
use App\Models\DonasiModel;

use CodeIgniter\HTTP\ResponseInterface;

class DonationController extends BaseController
{
    protected PemasukanDonasiModel $pemasukanModel;
    protected DonasiModel $donasiModel;

    public function __construct()
    {
        $this->pemasukanModel = new PemasukanDonasiModel();
        $this->donasiModel    = new DonasiModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Donasi',
        ];

        // Jika belum login, hanya tampilkan halaman
        if (! session()->get('logged_in')) {
            return view('landing/donation/v_donation', $data);
        }

        // Leaderboard 5 pemasukan terbaru
        $data['donationLeaderboard'] =
            $this->pemasukanModel->getLatestDonationLeaderboard(5);

        // Donasi yang masih aktif
        $data['activeDonations'] =
            $this->donasiModel->getActiveDonations();

        return view('landing/donation/v_donation', $data);
    }

    public function detail(int $idDonasi)
    {
        /*
         * Ambil detail donasi.
         */
        $donation =
            $this->donasiModel
                ->getPublicDonationDetail($idDonasi);

        /*
         * Donasi tidak ada / tidak aktif.
         */
        if (! $donation) {

            return redirect()
                ->to(base_url('donasi'))
                ->with(
                    'error',
                    'Donasi tidak ditemukan atau tidak dipublikasikan.'
                );
        }

        /*
         * Ambil daftar pemasukan donatur.
         */
        $donationIncomes =
            $this->pemasukanModel
                ->getPublicDonationIncomes($idDonasi);


        return view(
            'landing/donation/v_detail',
            [
                'title' => 'Detail Donasi',
                'donation' => $donation,
                'donationIncomes' => $donationIncomes,
            ]
        );
    }
}
