<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ambil id_peran dari session
        $userRole = session()->get('id_peran');

        // $arguments berisi daftar id_peran yang diizinkan dari Routes
        if (!in_array($userRole, $arguments)) {
            // Jika tidak diizinkan, arahkan ke halaman 403 atau dashboard dengan pesan
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut (403).');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan
    }
}
