<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika pengguna tidak login atau bukan admin
        if (! auth()->loggedIn() || ! auth()->user()->is_admin) {
            // Arahkan ke halaman utama atau halaman 'tidak diizinkan'
            return redirect()->to('/')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa
    }
}
