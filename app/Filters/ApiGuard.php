<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $referer = $request->getServer('HTTP_REFERER');
        
        $baseHost = parse_url(base_url(), PHP_URL_HOST);
        
        $refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;

        if ($refererHost && strcasecmp($refererHost, $baseHost) === 0) {
            return;
        }

        return service('response')->setJSON([
            'status'  => 403,
            'message' => 'Akses ditolak. API ini hanya untuk penggunaan internal website.'
        ])->setStatusCode(403);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}