<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SkipDBFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $dbName = env('database.default.database');

        // Jika database belum dikonfigurasi dan bukan akses ke installer, arahkan ke installer
        if (empty($dbName) && stripos($request->getPath(), 'install') === false) {
            return redirect()->to('/install');
        }

        // Jika akses ke /install, biarkan lanjut (tanpa koneksi DB)
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}
