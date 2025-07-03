<?php

namespace App\Controllers;

use App\Libraries\LicenseManager;

class LicenseController extends BaseController
{

    public function activate()
    {

        $license = new LicenseManager();
        if ($license->iall()) {
            return redirect()->to('dashboard');
        }
        
        return view('license/activate_view');
    }

    public function processActivation()
    {
        $licenseKey = $this->request->getPost('license_key');
        if (empty($licenseKey)) {
            return redirect()->back()->with('error', 'Kunci lisensi tidak boleh kosong.');
        }

        $license = new LicenseManager();
        $validation = $license->_v_l_s($licenseKey);

        if (isset($validation->status) && $validation->status === 'VALID') {
            $license->_s_l_d($licenseKey, $validation);
            $license->remove_lockdown();
            return redirect()->to('dashboard')->with('message', 'Lisensi berhasil diaktifkan!');
        }
        
        $errorMessage = $validation->message ?? 'Kunci lisensi tidak valid atau terjadi kesalahan.';
        return redirect()->back()->with('error', $errorMessage);
    }


public function invalid()
{
    // Ambil pesan 'reason' dari query string URL.
    $reason = $this->request->getGet('reason');

    // Jika tidak ada pesan, gunakan pesan default.
    $data['reason'] = !empty($reason) 
        ? urldecode($reason) // Mengembalikan spasi, dll. ke bentuk normal
        : 'Lisensi Anda tidak valid atau telah kedaluwarsa.';

    return view('license/invalid_view', $data);
}
}

