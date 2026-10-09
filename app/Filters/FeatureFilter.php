<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class FeatureFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika tidak ada argumen fitur yang dikirim di rute, lewati
        if (empty($arguments)) {
            return;
        }

        // 1. Pecah argumen jika dipisahkan oleh koma
        $requiredFeatures = [];
        foreach ($arguments as $arg) {
            $split = explode(',', $arg);
            foreach ($split as $s) {
                $requiredFeatures[] = trim($s);
            }
        }

        $db = \Config\Database::connect();
        $allowedFeatures = session()->get('allowed_features') ?? [];

        // 2. CEK STATUS MAINTENANCE
        // Tambahkan pengecekan: Jika user memiliki izin 'sistem.maintenance', lewati blokir maintenance (untuk testing)
        $isBypassMaintenance = in_array('sistem.maintenance', $allowedFeatures);

        if (!$isBypassMaintenance) {
            $maintenanceFeature = $db->table('fitur')
                ->whereIn('kode_fitur', $requiredFeatures)
                ->where('is_maintenance', 1)
                ->where('deleted_at IS NULL')
                ->get()
                ->getRowArray();

            if ($maintenanceFeature) {
                return $this->blockMaintenance($request, $maintenanceFeature);
            }
        }

        // 3. AMBIL DATA FITUR UNTUK MENGECEK JENIS AKSESNYA (public, hybrid, auth)
        $fiturList = $db->table('fitur')
            ->whereIn('kode_fitur', $requiredFeatures)
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        // Cek apakah ada setidaknya satu fitur yang berjenis 'public' atau 'hybrid'
        $isPublicOrHybrid = false;
        foreach ($fiturList as $f) {
            if (isset($f['jenis']) && in_array($f['jenis'], ['public', 'hybrid'])) {
                $isPublicOrHybrid = true;
                break;
            }
        }

        // 4. JIKA BUKAN PUBLIC/HYBRID (Artinya berjenis 'auth'), LAKUKAN PENGECEKAN IZIN
        if (!$isPublicOrHybrid) {
            $isGranted = false;

            foreach ($requiredFeatures as $feature) {
                if (in_array($feature, $allowedFeatures)) {
                    $isGranted = true;
                    break;
                }
            }

            // Jika khusus 'auth' tapi tidak punya izin, blokir (403)
            if (!$isGranted) {
                return $this->blockAccess($request, 'Akses Ditolak: Anda tidak memiliki izin untuk akses fitur tersebut (403).');
            }
        }

        // Jika lolos semua validasi, izinkan akses berlanjut ke controller
        return;
    }

    /**
     * Penanganan blokir karena tidak punya izin (403)
     */
    private function blockAccess($request, $message)
    {
        if ($request->isAJAX()) {
            return service('response')
                ->setJSON([
                    'status'  => 'error',
                    'message' => $message
                ])
                ->setStatusCode(403);
        }

        return redirect()->to(base_url('admin/dashboard'))
            ->with('error', $message);
    }

    /**
     * Penanganan blokir karena fitur sedang Maintenance (503 / Custom View)
     */
    private function blockMaintenance($request, $f)
    {
        $namaFitur  = $f['deskripsi'] ??'yang anda akses';
        $jenisFitur = $f['jenis'] ?? 'auth';

        $pesan = "Mohon Maaf, fitur {$namaFitur} saat ini sedang dalam tahap pemeliharaan (Maintenance). Silakan coba beberapa saat lagi.";

        if ($request->isAJAX()) {
            return service('response')
                ->setJSON([
                    'status'  => 'maintenance',
                    'message' => $pesan
                ])
                ->setStatusCode(503); 
        }

        // Tentukan layout berdasarkan jenis fitur
        $layout = ($jenisFitur === 'auth') ? 'layout/admin/main' : 'layout/landing/main';

        // RENDER VIEW MENJADI HTML STRING, KEMUDIKAN KIRIM SEBAGAI RESPONSE HTTP 503
        $htmlOutput = view('errors/html/v_maintenance', [
            'title'   => 'Fitur Dalam Pemeliharaan',
            'message' => $pesan,
            'layout'  => $layout
        ]);

        return service('response')
            ->setBody($htmlOutput)
            ->setStatusCode(503);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}