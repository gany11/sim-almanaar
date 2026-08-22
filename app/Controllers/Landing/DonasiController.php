<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DonasiController extends BaseController
{
    public function index()
    {
        return view('landing/donation/v_donation', [
            'title' => 'Donasi'
        ]);
    }
}
