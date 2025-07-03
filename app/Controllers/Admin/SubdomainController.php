<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubdomainModel;
use App\Libraries\CloudflareService;

class SubdomainController extends BaseController
{
    /**
     * Menampilkan daftar semua subdomain dari semua pengguna.
     */
    public function index()
    {
        $subdomainModel = new SubdomainModel();

        $data = [
            'subdomains' => $subdomainModel
                ->select('subdomains.*, users.username, domains.domain_name')
                ->join('users', 'users.id = subdomains.user_id')
                ->join('domains', 'domains.id = subdomains.domain_id')
                ->orderBy('subdomains.created_at', 'DESC')
                ->findAll(),
        ];

        return view('admin/subdomains/index', $data);
    }

    /**
     * Menghapus subdomain (sebagai admin).
     */
    public function delete(int $subdomainId)
    {
        $subdomainModel = new SubdomainModel();

        // Ambil data lengkap untuk mendapatkan zone_id dan record_id
        $subdomain = $subdomainModel
            ->select('subdomains.*, domains.zone_id')
            ->join('domains', 'domains.id = subdomains.domain_id')
            ->find($subdomainId);

        if (!$subdomain) {
            return redirect()->to('/admin/subdomains')->with('error', 'Subdomain tidak ditemukan.');
        }

        // Hapus dari Cloudflare
        $cloudflare = new CloudflareService();
        $cloudflare->deleteDnsRecord($subdomain['zone_id'], $subdomain['cloudflare_record_id']);

        // Hapus dari database lokal
        $subdomainModel->delete($subdomainId);

        return redirect()->to('/admin/subdomains')->with('message', 'Subdomain berhasil dihapus.');
    }
}
