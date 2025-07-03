<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\LicenseManager;

class LicenseFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = $request->getUri()->getPath();

// ✅ CEK: Jika belum install, redirect ke /install
        if (
            !file_exists(WRITEPATH . '.installed') &&
            !preg_match('#^(install|activate)(/|$)#', $path)
        ) {
            return redirect()->to('/install');
        }

        // Abaikan filter untuk route penting agar tidak terjadi loop
        if (preg_match('#^(login|logout|register|install|activate|invalid)(/|$)#', $path)) {
            return;
        }

        $license = new LicenseManager();

        // Jika belum ada lisensi sama sekali
        if ($license->get_details() === null) {
            return redirect()->to('/activate');
        }

        // Lakukan validasi ulang ke server
        $reval_result = $license->rv_p();

        // Jika hasil validasi BUKAN true (berarti ada pesan error)
        if ($reval_result !== true) {
            $license->create_lockdown(); // Kunci sistem
            // Arahkan ke halaman invalid dengan membawa alasan spesifik
            return redirect()->to('/invalid?reason=' . urlencode($reval_result));
        }

        // Jika valid, pastikan tidak ada lockdown
        $license->remove_lockdown();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
