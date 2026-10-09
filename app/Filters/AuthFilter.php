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
        
        $mode = $arguments[0] ?? 'auth';

        if ($mode === 'true' || $mode === true) $mode = 'auth';
        if ($mode === 'false' || $mode === false) $mode = 'public';

        switch ($mode) {
            case 'auth':
                // Wajib Login
                if (!$isLoggedIn) {
                    $currentUrl = current_url();
                    
                    // Jangan simpan URL logout atau halaman tertentu ke session redirect
                    if (!str_contains($currentUrl, 'admin/logout')) {
                        session()->set('redirect_after_login', $currentUrl);
                    }
                    
                    return redirect()->to(base_url('admin/login'))->with('error', 'Silakan login terlebih dahulu.');
                }
                
                // Jalankan cek berkala & refresh fitur
                $this->checkAndRefreshUserSession();
                break;

            case 'public':
                // Wajib TIDAK Login
                if ($isLoggedIn) {
                    return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda sudah login.');
                }
                break;

            case 'hybrid':
                // Bebas diakses (login atau belum login)
                // Jika user TERNYATA sedang dalam keadaan login, kita boleh jalankan cek berkala 
                // agar jika statusnya dinonaktifkan admin, dia langsung ter-force logout.
                if ($isLoggedIn) {
                    $this->checkAndRefreshUserSession();
                }
                break;
        }
    }

    /**
     * Helper privat untuk mengecek status akun dan memperbarui izin fitur secara berkala
     */
    private function checkAndRefreshUserSession()
    {
        $lastChecked = session()->get('last_checked_at');
        $oneHour = 3600;
        // $oneHour = 10;

        // Eksekusi hanya jika sudah lewat 1 jam atau belum pernah dicek di sesi ini
        if (!$lastChecked || (time() - $lastChecked) > $oneHour) {
            $akunModel = new AkunModel();
            $idAkun = session()->get('id_akun');
            
            $user = $akunModel->find($idAkun);

            if (!$user || $user->status !== 'aktif') {
                $this->forceLogout();
                return redirect()->to(base_url('admin/login'))->with('error', 'Akun Anda telah dinonaktifkan atau dihapus oleh Administrator.');
            }

            if ($user->id_peran != session()->get('id_peran')) {
                $this->forceLogout();
                return redirect()->to(base_url('admin/login'))->with('error', 'Hak akses (peran) Anda telah diperbarui. Silakan login kembali.');
            }

            $db = \Config\Database::connect();
            
            $roleFeatures = $db->table('peran_fitur pf')
                ->distinct()
                ->select('f.kode_fitur')
                ->join('fitur f', 'f.id_fitur = pf.id_fitur')
                ->where('pf.id_peran', $user->id_peran)
                ->where('pf.deleted_at IS NULL')
                ->get()
                ->getResultArray();

            $accountFeatures = $db->table('akun_fitur af')
                ->distinct()
                ->select('f.kode_fitur')
                ->join('fitur f', 'f.id_fitur = af.id_fitur')
                ->where('af.id_akun', $user->id_akun)
                ->where('af.deleted_at IS NULL')
                ->get()
                ->getResultArray();

            $allCodes = array_merge(
                array_column($roleFeatures, 'kode_fitur'), 
                array_column($accountFeatures, 'kode_fitur')
            );
            $allowedFeatures = array_unique($allCodes);

            session()->set([
                'allowed_features' => $allowedFeatures,
                'last_checked_at'  => time()
            ]);
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
                'status'    => 'expired' 
            ]);
        }
        session()->destroy();
    }
}