<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    /**
     * Melakukan logout total: menghancurkan sesi,
     * menghapus cookie remember-me, dan mengarahkan ke login.
     */
    public function logout()
    {
        $auth = service('auth');

        // 1. Panggil logout resmi dari Shield
        $auth->logout();

        // 2. Hapus cookie "remember-me" secara manual
        $rememberCookie = config('Auth')->rememberCookieName;
        if (isset($_COOKIE[$rememberCookie])) {
            unset($_COOKIE[$rememberCookie]);
            setcookie($rememberCookie, '', time() - 3600, '/');
        }

        // 3. Hancurkan seluruh data sesi PHP untuk memastikan
        session()->destroy();

        // 4. Arahkan ke halaman login dengan pesan sukses
        return redirect()->to('/login')->with('message', 'Anda telah berhasil logout.');
    }
}
