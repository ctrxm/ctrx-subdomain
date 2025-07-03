<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DomainModel;
use App\Models\SubdomainModel;
use CodeIgniter\Shield\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel      = new UserModel();
        $domainModel    = new DomainModel();
        $subdomainModel = new SubdomainModel();
        $db             = \Config\Database::connect();

        // --- PENGAMBILAN DATA UNTUK GRAFIK ---
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
        
        // Query untuk menghitung jumlah subdomain per hari
        $query = $db->table('subdomains')
            ->select("DATE(created_at) as date, COUNT(id) as count")
            ->where('created_at >=', $sevenDaysAgo)
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get();
            
        $dailyCounts = $query->getResultArray();
        
        // Siapkan array data untuk 7 hari terakhir, isi dengan 0 jika tidak ada data
        $chartData = [];
        $chartLabels = [];
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d M', strtotime($date));
            
            // Cari data untuk tanggal ini
            $found = false;
            foreach ($dailyCounts as $row) {
                if ($row['date'] === $date) {
                    $chartData[] = (int) $row['count'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $chartData[] = 0;
            }
        }
        
        // Data perlu dibalik agar urutan tanggalnya benar (dari 7 hari lalu ke hari ini)
        $chartData = array_reverse($chartData);
        $chartLabels = array_reverse($chartLabels);
        // --- AKHIR PENGAMBILAN DATA GRAFIK ---

        // Hitung total data dari setiap tabel
        $data = [
            'totalUsers'      => $userModel->countAllResults(),
            'totalDomains'    => $domainModel->countAllResults(),
            'totalSubdomains' => $subdomainModel->countAllResults(),
            'chartData'       => json_encode($chartData),    // Kirim data sebagai JSON
            'chartLabels'     => json_encode($chartLabels),  // Kirim label sebagai JSON
        ];

        // Kirim data ke view
        return view('admin/dashboard_view', $data);
    }
}
