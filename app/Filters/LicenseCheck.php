<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\LicenseManager;

class LicenseCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = trim($request->getPath(), '/');

        // ✅ CEGAH AKSES SEMUA HALAMAN JIKA BELUM INSTALL
        if (!file_exists(WRITEPATH . '.installed') &&
    !in_array($uri, ['install', 'install/process', 'activate', 'activate/process', 'invalid'])) {
    return redirect()->to('/install');
}

        // ✅ BIARKAN HALAMAN AKTIVASI DIIJINKAN
        if (in_array($uri, ['activate', 'activate/process', 'invalid', 'install', 'install/process'])) {
            return;
        }

        // ✅ CEK LISENSI (SIMPLES AJA DULU)
        $licenseManager = new LicenseManager();
        if ($licenseManager->get_details() === null) {
            return redirect()->to('/activate');
        }

        // Untuk sementara belum pakai revalidasi atau lockdown
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}
