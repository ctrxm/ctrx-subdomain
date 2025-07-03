<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Untuk sementara, kita hanya tampilkan sebuah view sederhana
        return view('admin/dashboard_view');
    }
}
