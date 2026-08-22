<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = session()->get('logged_in');
        
        // Ambil parameter pertama dari filter (misal: 'auth:true' atau 'auth:false')
        $shouldBeLoggedIn = filter_var($arguments[0] ?? true, FILTER_VALIDATE_BOOLEAN);

        if ($shouldBeLoggedIn) {
            // HALAMAN PRIVATE: Jika belum login, tendang ke login
            if (!$isLoggedIn) {
                $returnUrl = current_url();

                session()->set('redirect_after_login', $returnUrl);
                
                return redirect()->to(base_url('admin/login'))->with('error', 'Silakan login terlebih dahulu.');
            }
        } else {
            // HALAMAN GUEST (Login/Forgot): Jika sudah login, tendang ke dashboard
            if ($isLoggedIn) {
                return redirect()->to(base_url('admin/dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
