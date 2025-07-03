<?php

namespace App\Controllers;

class AccountController extends BaseController
{
    public function index()
    {
        // Cukup tampilkan view halaman akun
        return view('account/index');
    }
}
