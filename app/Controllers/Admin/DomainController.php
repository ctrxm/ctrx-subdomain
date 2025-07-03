<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DomainModel; // Pastikan untuk mengimpor Model

class DomainController extends BaseController
{
    /**
     * Menampilkan halaman daftar domain dasar.
     */
    public function index()
    {
        $domainModel = new DomainModel();
        $data = [
            'domains' => $domainModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        return view('admin/domains/index', $data);
    }

    /**
     * Menampilkan form untuk menambah domain baru.
     */
    public function create()
    {
        return view('admin/domains/create');
    }

    /**
     * Menyimpan data domain baru dari form ke database.
     */
    public function store()
    {
        $rules = [
            'domain_name' => 'required|is_unique[domains.domain_name]',
            'zone_id'     => 'required|alpha_numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $domainModel = new DomainModel();
        $domainModel->save([
            'domain_name' => $this->request->getPost('domain_name'),
            'zone_id'     => $this->request->getPost('zone_id'),
            'is_active'   => true, // Langsung aktifkan domain
        ]);

        return redirect()->to('/admin/domains')->with('message', 'Domain dasar berhasil ditambahkan!');
    }
    
    /**
     * Menghapus domain dasar.
     */
    public function delete(int $id)
    {
        $domainModel = new DomainModel();
        
        // Hapus domain dari database
        if ($domainModel->delete($id)) {
            return redirect()->to('/admin/domains')->with('message', 'Domain berhasil dihapus.');
        }

        return redirect()->to('/admin/domains')->with('error', 'Gagal menghapus domain.');
    }
}
