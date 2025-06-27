<?php

session_start();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';

use App\Database;
use App\LicenseManager;

$currentAppHost = $_SERVER['HTTP_HOST'] ?? '';
$currentAppHost = preg_replace('/:\d+$/', '', $currentAppHost);

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$licenseManagerCheck = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
$storedLicenseVerification = $licenseManagerCheck->getAndVerifyStoredLicense();

if (!$storedLicenseVerification['status']) {
    $_SESSION['status_message'] = $storedLicenseVerification['message'];
    $_SESSION['status_type'] = 'error';
    header('Location: /license');
    exit();
}

$_SESSION['is_licensed'] = true;
$_SESSION['license_data'] = $storedLicenseVerification['data'];
$_SESSION['license_code_raw'] = $storedLicenseVerification['code_raw'];

$licenseData = $_SESSION['license_data'] ?? null;
$licenseCodeRaw = $_SESSION['license_code_raw'] ?? '';

$licenseStatusMessage = "Status lisensi tidak diketahui.";
$licenseStatusCode = "info"; 

if ($licenseData) {
    $expirationTimestamp = strtotime($licenseData['expiration_date'] ?? '');
    if ($expirationTimestamp === false || $expirationTimestamp < time()) {
        $licenseStatusMessage = "Lisensi Kamu telah KEDALUWARSA.";
        $licenseStatusCode = "error";
    } else {
        $licenseStatusMessage = "Lisensi Kamu VALID hingga " . htmlspecialchars(date('d F Y', $expirationTimestamp)) . ".";
        $licenseStatusCode = "success";
    }
} else {
    $licenseStatusMessage = "Tidak ada detail lisensi aktif yang ditemukan.";
    $licenseStatusCode = "error";
}

?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lisensi - CTRX Subdomain</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --color-primary: 79 70 229; /* Indigo */
            --color-secondary: 107 114 128; /* Gray */
            --color-success: 22 163 74; /* Green */
            --color-danger: 220 38 38; /* Red */
            --color-warning: 245 158 11; /* Amber */
        }
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .focus\:ring-primary:focus {
            --tw-ring-color: rgb(var(--color-primary) / 0.5);
        }
    </style>
</head>
<body class="h-full">
<div class="min-h-full">
    <nav class="bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <img class="h-8 w-auto" src="/assets/logo.png" alt="CTRX Logo">
                        <span class="ml-3 text-xl font-bold text-gray-800">CTRX</span>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    <a href="/admin" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">Admin Dashboard</a>
                    <a href="/create" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">Buat Subdomain</a>
                    <a href="/logout" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                        <i class="fa-solid fa-arrow-right-from-bracket -ml-1 mr-2 h-5 w-5"></i> Logout
                    </a>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" id="hamburger-button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Buka menu</span>
                        <i id="hamburger-icon-open" class="fa-solid fa-bars h-6 w-6 block"></i>
                        <i id="hamburger-icon-close" class="fa-solid fa-xmark h-6 w-6 hidden"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="space-y-1 pb-3 pt-2">
                <a href="/admin" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Admin Dashboard</a>
                <a href="/license_details" class="block border-l-4 border-indigo-500 bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700">Detail Lisensi</a>
                <a href="/create" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Buat Subdomain</a>
            </div>
            <div class="border-t border-gray-200 pb-3 pt-4">
                <div class="mt-3 space-y-1">
                    <a href="/logout" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="py-10">
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900">Detail Lisensi</h1>
                <p class="mt-1 text-md text-gray-600">Informasi mengenai status dan detail lisensi aplikasi Anda.</p>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mt-8 space-y-8">

                    <?php
                        $statusColorClasses = 'bg-yellow-100 text-yellow-800'; // Default untuk 'info'
                        $statusIcon = 'fa-solid fa-circle-info';
                        if ($licenseStatusCode === 'success') {
                            $statusColorClasses = 'bg-green-100 text-green-800';
                            $statusIcon = 'fa-solid fa-check-circle';
                        } elseif ($licenseStatusCode === 'error') {
                            $statusColorClasses = 'bg-red-100 text-red-800';
                            $statusIcon = 'fa-solid fa-triangle-exclamation';
                        }
                    ?>
                    <div class="rounded-md p-4 <?php echo $statusColorClasses; ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="<?php echo $statusIcon; ?> h-5 w-5" aria-hidden="true"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?php echo $licenseStatusMessage; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-md sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Informasi Lisensi</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Detail yang terdaftar untuk lisensi ini.</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Kode Lisensi</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 break-all"><?php echo htmlspecialchars($licenseCodeRaw ?? 'N/A'); ?></dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Domain Terdaftar</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"><?php echo htmlspecialchars($licenseData['domain'] ?? 'N/A'); ?></dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"><?php echo isset($licenseData['created_at']) ? htmlspecialchars(date('d F Y', strtotime($licenseData['created_at']))) : 'ERROR'; ?></dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Kadaluarsa</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"><?php echo isset($licenseData['expiration_date']) ? htmlspecialchars(date('d F Y', strtotime($licenseData['expiration_date']))) : 'ERROR'; ?></dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Checksum</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 break-all"><?php echo htmlspecialchars($licenseData['checksum'] ?? 'N/A'); ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-x-6">
                        <a href="/admin" class="text-sm font-semibold leading-6 text-gray-900">Kembali</a>
                        <a href="https://t.me/useraib" target="_blank" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <i class="fa-solid fa-paper-plane mr-2"></i>Perbarui Lisensi
                        </a>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburgerButton = document.getElementById('hamburger-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('hamburger-icon-open');
        const iconClose = document.getElementById('hamburger-icon-close');

        hamburgerButton.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    });
</script>

</body>
</html>
