<?php

namespace App\Controllers;

use App\Libraries\LicenseManager; // Pastikan library ini ada

class LicenseController extends BaseController
{
    /**
     * Menampilkan halaman form aktivasi lisensi.
     */
    public function activate()
    {
        // Jika sudah ada lisensi valid, arahkan ke dashboard
        $license = new LicenseManager();
        if ($license->iall()) {
            return redirect()->to('dashboard');
        }
        
        return view('license/activate_view');
    }

    /**
     * Memproses kunci lisensi yang dikirim dari form.
     */
    public function processActivation()
    {
        $licenseKey = $this->request->getPost('license_key');
        if (empty($licenseKey)) {
            return redirect()->back()->with('error', 'Kunci lisensi tidak boleh kosong.');
        }

        $license = new LicenseManager();
        $validation = $license->_v_l_s($licenseKey); // Memvalidasi ke server

        if (isset($validation->status) && $validation->status === 'VALID') {
            $license->_s_l_d($licenseKey, $validation); // Simpan lisensi lokal
            $license->remove_lockdown(); // Hapus file lockdown jika ada
            return redirect()->to('dashboard')->with('message', 'Lisensi berhasil diaktifkan!');
        }
        
        $errorMessage = $validation->message ?? 'Kunci lisensi tidak valid atau terjadi kesalahan.';
        return redirect()->back()->with('error', $errorMessage);
    }

    /**
     * Menampilkan halaman jika lisensi tidak valid.
     */
    public function invalid()
    {
        // Tampilkan pesan error yang dikirim lewat URL
        $data['reason'] = $this->request->getGet('reason') ?? 'Lisensi tidak valid atau telah kedaluwarsa.';
        return view('license/invalid_view', $data);
    }
}

