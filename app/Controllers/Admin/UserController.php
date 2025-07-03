<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

class UserController extends BaseController
{
    /**
     * Menampilkan halaman daftar semua pengguna.
     */
    public function index()
    {
        $userModel = new UserModel();
        
        $data = [
            // Ambil semua pengguna dan urutkan berdasarkan yang terbaru
            'users' => $userModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        
        return view('admin/users/index', $data);
    }

    /**
     * Menjadikan user sebagai admin, atau mencabut status admin.
     */
    public function toggleAdmin(int $userId)
    {
        $userModel = new UserModel();
        $user = $userModel->findById($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Jangan biarkan admin mencabut statusnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah status admin diri sendiri.');
        }

        // Cek apakah user sudah menjadi admin atau belum
        if ($user->inGroup('admin')) {
            // Jika sudah, cabut status adminnya
            $user->removeGroup('admin');
            return redirect()->back()->with('message', 'Status admin untuk ' . esc($user->username) . ' berhasil dicabut.');
        } else {
            // Jika belum, jadikan dia admin
            $user->addGroup('admin');
            return redirect()->back()->with('message', esc($user->username) . ' berhasil dijadikan admin.');
        }
    }
}
