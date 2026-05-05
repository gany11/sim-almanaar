<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('id_peran');

        if (!$userRole) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($arguments)) {
            return;
        }

        $allowedRoles = array_map('intval', $arguments);

        if (!in_array((int)$userRole, $allowedRoles, true)) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk peran ini (403).');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan
    }
}
