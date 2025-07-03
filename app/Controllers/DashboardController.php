<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\CloudflareService;
use App\Models\DomainModel;
use App\Models\SubdomainModel;

class DashboardController extends BaseController
{
    /**
     * Menampilkan halaman utama dashboard pengguna.
     */
    public function index()
    {
        $domainModel    = new DomainModel();
        $subdomainModel = new SubdomainModel();
        
        $data = [
            'domains'    => $domainModel->where('is_active', true)->findAll(),
            'subdomains' => $subdomainModel->getSubdomainsByUser(auth()->id()),
        ];
        
        return view('dashboard/index', $data);
    }

    /**
     * Memproses pembuatan subdomain baru.
     */
    public function createSubdomain()
    {
        // 1. Dapatkan tipe record dari form
        $type = $this->request->getPost('type');
        
        // 2. Siapkan aturan validasi dasar
        $rules = [
            'subdomain' => 'required|alpha_dash|max_length[60]',
            'domain_id' => 'required|is_not_unique[domains.id]',
            'type'      => 'required|in_list[A,CNAME]',
        ];

        // 3. Tambahkan aturan validasi dinamis berdasarkan tipe
        if ($type === 'A') {
            $rules['content'] = 'required|valid_ip[ipv4]';
        } elseif ($type === 'CNAME') {
            // Menggunakan regex untuk validasi domain yang lebih fleksibel
            $rules['content'] = 'required|regex_match[/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Persiapan Data
        $subdomainName = $this->request->getPost('subdomain');
        $domainId      = $this->request->getPost('domain_id');
        $content       = $this->request->getPost('content');
        $userId        = auth()->id();
        
        $domainModel = new DomainModel();
        $domain = $domainModel->find($domainId);
        $fullDomain = $subdomainName . '.' . $domain['domain_name'];

        // Cek duplikasi di database lokal
        $subdomainModel = new SubdomainModel();
        $existing = $subdomainModel->where('name', $subdomainName)->where('domain_id', $domainId)->first();
        if($existing) {
            return redirect()->back()->withInput()->with('error', 'Subdomain tersebut sudah digunakan.');
        }

        // 5. Panggil Cloudflare Service
        $cloudflare = new CloudflareService();
        $result = $cloudflare->createDnsRecord($domain['zone_id'], $type, $fullDomain, $content, true);
        
        // 6. Proses Hasil
        if ($result && isset($result->success) && $result->success === true) {
            $subdomainModel->save([
                'user_id'              => $userId,
                'domain_id'            => $domainId,
                'cloudflare_record_id' => $result->result->id,
                'name'                 => $subdomainName,
                'type'                 => $type,
                'content'              => $content,
                'proxied'              => true
            ]);
            return redirect()->to('/dashboard')->with('message', 'Subdomain ' . esc($fullDomain) . ' berhasil dibuat!');
        } else {
            $errorMessages = [];
            if (!empty($result->errors)) {
                foreach ($result->errors as $error) {
                    $errorMessages[] = "Error code: {$error->code} - {$error->message}";
                }
            }
            if (empty($errorMessages)) {
                $errorMessages[] = 'Terjadi kesalahan tidak dikenal pada API Cloudflare.';
            }
            $fullErrorMessage = implode('<br>', $errorMessages);

            return redirect()->back()->withInput()->with('error', $fullErrorMessage);
        }
    }

    /**
     * Menghapus subdomain.
     */
    public function deleteSubdomain(int $subdomainId)
    {
        $subdomainModel = new SubdomainModel();
        $subdomain = $subdomainModel
            ->select('subdomains.*, domains.zone_id')
            ->join('domains', 'domains.id = subdomains.domain_id')
            ->where('subdomains.id', $subdomainId)
            ->where('subdomains.user_id', auth()->id())
            ->first();

        if (!$subdomain) {
            return redirect()->to('/dashboard')->with('error', 'Subdomain tidak ditemukan atau Anda tidak punya izin.');
        }

        $cloudflare = new CloudflareService();
        $result = $cloudflare->deleteDnsRecord($subdomain['zone_id'], $subdomain['cloudflare_record_id']);

        if ($result && isset($result->success) && $result->success === true) {
             $subdomainModel->delete($subdomainId);
             return redirect()->to('/dashboard')->with('message', 'Subdomain berhasil dihapus.');
        }
        
        if (isset($result->errors[0]->code) && $result->errors[0]->code !== 81044) { // 81044 = Record already deleted
             return redirect()->to('/dashboard')->with('error', 'Gagal menghapus subdomain dari Cloudflare.');
        }

        $subdomainModel->delete($subdomainId);
        return redirect()->to('/dashboard')->with('message', 'Subdomain berhasil dihapus dari database (record di Cloudflare sudah tidak ada).');
    }
}
