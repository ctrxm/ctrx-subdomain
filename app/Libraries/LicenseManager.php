<?php

namespace App\Libraries;

class LicenseManager
{
    private string $apiUrl;
    private string $apiKey;
    private string $secretKey;
    private string $licenseFile;
    private string $lockdownFile;

    public function __construct()
    {

        $this->apiUrl     = env('license.apiUrl', 'https://api.ctrxl.id/api/v1/verify');
        $this->apiKey     = env('license.apiKey', 'prod_lCJJKYOEhXKWsbRbCZAgpaSO5LxiDLrL');
        $this->secretKey  = env('license.secretKey', 'ClGJVwi7bXCPeZ3P+XTQuvJhZ+en1xVr92No+GMcyQq+HVJQyhbfOyEGvw2V7NgA');
        
        $this->licenseFile  = WRITEPATH . 'cache/._lhc';
        $this->lockdownFile = WRITEPATH . 'cache/._lckdwn';
    }

    private function _get_iv(): string { return openssl_random_pseudo_bytes(16); }
    private function _enc_data(?string $d): string { $i = $this->_get_iv(); $enc = openssl_encrypt($d, 'AES-256-CBC', $this->secretKey, 0, $i); return base64_encode($enc.'::'.$i); }
    private function _dec_data(?string $d): ?string { list($e_d, $i) = explode('::', base64_decode($d), 2); if (!isset($i) || !isset($e_d)) {return null;} return openssl_decrypt($e_d, 'AES-256-CBC', $this->secretKey, 0, $i); }
    private function _get_did(): string { return md5(ROOTPATH . ($_SERVER['SERVER_NAME'] ?? 'cli')); }
    public function _v_l_s(?string $l): object { $c = curl_init(); curl_setopt($c, CURLOPT_URL, $this->apiUrl); curl_setopt($c, CURLOPT_POST, 1); curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query(['api_key' => $this->apiKey, 'license_key' => $l, 'device_id' => $this->_get_did()])); curl_setopt($c, CURLOPT_RETURNTRANSFER, true); curl_setopt($c, CURLOPT_TIMEOUT, 30); curl_setopt($c, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded']); $r = curl_exec($c); $dr = json_decode($r); curl_close($c); return ($r === false || json_last_error() !== JSON_ERROR_NONE) ? (object)['status' => 'ERROR', 'message' => 'Invalid Response'] : $dr; }
    public function _s_l_d(?string $l, ?object $d): bool { $_d = ['lk' => $l, 'st' => $d->status, 'ex_dt' => $d->expires_at, 'lckd' => time()]; return file_put_contents($this->licenseFile, $this->_enc_data(json_encode($_d))) !== false; }
    public function get_details() { return $this->_get_ll_d(); }
    private function _get_ll_d(): ?object { if (!file_exists($this->licenseFile)) { return null; } $d = file_get_contents($this->licenseFile); $dec = $this->_dec_data($d); return ($dec === false) ? null : json_decode($dec); }
   public function iall(bool $forceCheck = false): bool
{
    $d = $this->_get_ll_d();
    if ($d === null || !isset($d->lk)) return false;

    // Validasi ulang jika forceCheck aktif atau cache kedaluwarsa (> 60 detik)
    $lastChecked = $d->lckd ?? 0;
    $shouldCheck = $forceCheck || (time() - $lastChecked > 60);

    if ($shouldCheck) {
        $check = $this->_v_l_s($d->lk);
        if ($check->status === 'VALID') {
            $this->_s_l_d($d->lk, $check);
            return true;
        } else {
            return false;
        }
    }

    // Jika tidak forceCheck, validasi berdasarkan data lokal
    if ($d->st !== 'VALID') return false;
    if (isset($d->ex_dt) && strtotime($d->ex_dt) < time()) return false;

    return true;
}

    public function cfi(): bool { return true; /* Implementasi hash check file jika diperlukan */ }
    public function rv_p(): bool|string { $d = $this->_get_ll_d(); if ($d === null || !isset($d->lk)) return true; $lckd = $d->lckd ?? 0; $sec = 60; /* Re-validasi setiap 1 jam */ if ((time() - $lckd) > $sec) { $sr = $this->_v_l_s($d->lk); if (isset($sr->status) && $sr->status === 'VALID') { $this->_s_l_d($d->lk, $sr); return true; } else { return $sr->message ?? 'Gagal memverifikasi lisensi dengan server.'; } } return true; }
    public function _del_ld(): void { if (file_exists($this->licenseFile)) { @unlink($this->licenseFile); } }
    
    // --- FUNGSI LOCKDOWN ---
    public function create_lockdown(): void { if (!file_exists($this->lockdownFile)) { @touch($this->lockdownFile); } }
    public function remove_lockdown(): void { if (file_exists($this->lockdownFile)) { @unlink($this->lockdownFile); } }
    public function is_lockdown(): bool { return file_exists($this->lockdownFile); }
}

