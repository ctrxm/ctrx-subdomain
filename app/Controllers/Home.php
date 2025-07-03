<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Jika user sudah login, arahkan ke dashboard
        if (auth()->loggedIn()) {
            return redirect()->to('dashboard');
        }
        
        // Jika belum, tampilkan landing page
        return view('landing_page');
    }
}
