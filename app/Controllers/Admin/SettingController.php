<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        // Gunakan nama tabel baru 'app_settings'
        $setting = $db->table('app_settings')->where('key', 'max_subdomains_per_user')->get()->getRow();

        $data = [
            'max_subdomains' => $setting->value ?? '10',
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $db = \Config\Database::connect();
        // Gunakan nama tabel baru 'app_settings'
        $builder = $db->table('app_settings');

        $key   = 'max_subdomains_per_user';
        $value = $this->request->getPost('max_subdomains_per_user');
        
        $data = ['key' => $key, 'value' => $value];

        $builder->where('key', $key);
        $exists = $builder->countAllResults(false) > 0;

        if ($exists) {
            $builder->where('key', $key);
            $builder->update($data);
        } else {
            $builder->insert($data);
        }

        return redirect()->to('/admin/settings')->with('message', 'Pengaturan berhasil diperbarui.');
    }
}
