<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AkunModel;
use App\Models\LogLoginModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = session()->get('logged_in');
        
        $shouldBeLoggedIn = filter_var($arguments[0] ?? true, FILTER_VALIDATE_BOOLEAN);

        if ($shouldBeLoggedIn) {
            if (!$isLoggedIn) {
                $returnUrl = current_url();
                session()->set('redirect_after_login', $returnUrl);
                
                return redirect()->to(base_url('admin/login'))->with('error', 'Silakan login terlebih dahulu.');
            }

            // --- CEK BERKALA SETIAP 1 JAM ---
            $lastChecked = session()->get('last_checked_at');
            $oneHour = 3600;

            if (!$lastChecked || (time() - $lastChecked) > $oneHour) {
                $akunModel = new AkunModel();
                $idAkun = session()->get('id_akun');
                
                // Ambil data terbaru dari database
                $user = $akunModel->find($idAkun);

                // Jika akun tidak ditemukan atau status berubah menjadi tidak aktif
                if (!$user || $user['status'] !== 'aktif') {
                    $this->forceLogout();
                    return redirect()->to(base_url('admin/login'))->with('error', 'Akun Anda telah dinonaktifkan atau dihapus oleh Administrator.');
                }

                // Jika peran/role akun berubah
                if ($user['id_peran'] != session()->get('id_peran')) {
                    $this->forceLogout();
                    return redirect()->to(base_url('admin/login'))->with('error', 'Hak akses (peran) Anda telah diperbarui. Silakan login kembali.');
                }

                // Perbarui waktu cek menjadi waktu sekarang
                session()->set('last_checked_at', time());
            }

        } else {
            if ($isLoggedIn) {
                return redirect()->to(base_url('admin/dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function forceLogout()
    {
        $idLog = session()->get('id_log');
        if ($idLog) {
            $logLoginModel = new LogLoginModel();
            $logLoginModel->update($idLog, [
                'logout_at' => date('Y-m-d H:i:s'),
                'status'    => 'expired' // atau logout paksa
            ]);
        }
        session()->destroy();
    }
}