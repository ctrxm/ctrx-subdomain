<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class InstallController extends BaseController
{
    public function index()
    {
        // Jika sudah terinstall, redirect ke dashboard
        if (file_exists(WRITEPATH . '.installed')) {
            return redirect()->to('/dashboard');
        }

        return view('install/index');
    }

    public function testDB()
    {
        // Pastikan method POST dan request JSON
        $json = $this->request->getJSON(true);

        $host = $json['host'] ?? 'localhost';
        $name = $json['name'] ?? '';
        $user = $json['user'] ?? '';
        $pass = $json['pass'] ?? '';

        try {
            $pdo = new \PDO("mysql:host=$host;dbname=$name", $user, $pass);
            return $this->response->setJSON(['success' => true]);
        } catch (\PDOException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function process()
    {
        $dbHost = $this->request->getPost('db_host');
        $dbName = $this->request->getPost('db_name');
        $dbUser = $this->request->getPost('db_user');
        $dbPass = $this->request->getPost('db_pass');
        $licenseKey = $this->request->getPost('license_key');
        $baseURL = $this->request->getPost('base_url');

        // Auto deteksi baseURL jika kosong
        if (empty($baseURL)) {
            $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                "://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
        }

        $env = ROOTPATH . '.env';
        $envContent = file_exists($env) ? file_get_contents($env) : '';

        $newEnv = [
            'app.baseURL'               => $baseURL,
            'database.default.hostname' => $dbHost,
            'database.default.database' => $dbName,
            'database.default.username' => $dbUser,
            'database.default.password' => $dbPass,
            'license.apiKey'            => $licenseKey,
        ];

        foreach ($newEnv as $key => $val) {
            $pattern = "/^$key\s*=.*$/m";
            $line = "$key = \"$val\"";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $line, $envContent);
            } else {
                $envContent .= "\n$line";
            }
        }

        if (file_put_contents($env, $envContent) === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan file konfigurasi .env']);
        }

        if (file_put_contents(WRITEPATH . '.installed', 'OK') === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menandai instalasi selesai']);
        }

        // Comment auto-hapus installer agar tidak langsung terhapus
        /*
       // @unlink(APPPATH . 'Controllers/InstallController.php');
       // @unlink(APPPATH . 'Views/install/index.php');
        */

        return $this->response->setJSON(['success' => true]);
    }
}

