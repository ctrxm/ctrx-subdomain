<?php

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php';
require_once __DIR__ . '/autoload.php';

use App\LicenseManager;
use App\TelegramBot;
use App\Database;

$licenseMessage = '';
$licenseMessageType = '';

if (isset($_SESSION[APP_SESSION_PREFIX . 'status_message'])) {
    $licenseMessage = $_SESSION[APP_SESSION_PREFIX . 'status_message'];
    $licenseMessageType = $_SESSION[APP_SESSION_PREFIX . 'status_type'] ?? 'error';
    unset($_SESSION[APP_SESSION_PREFIX . 'status_message'], $_SESSION[APP_SESSION_PREFIX . 'status_type']);
}

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$telegramBot = new App\TelegramBot(TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $licenseCode = trim(filter_input(INPUT_POST, 'license_code', FILTER_UNSAFE_RAW));

    if (empty($licenseCode)) {
        $licenseMessage = 'Kode lisensi harus diisi.';
        $licenseMessageType = 'error';
    } else {
        $currentAppHost = preg_replace('/:\d+$/', '', ($_SERVER['HTTP_HOST'] ?? ''));
        $licenseManager = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
        $verificationResult = $licenseManager->verifyLicenseWithServer($licenseCode);

        if (isset($verificationResult['status']) && $verificationResult['status'] === true && isset($verificationResult['data'])) {
            if ($licenseManager->storeLicense($licenseCode)) {
                $_SESSION[APP_SESSION_PREFIX . 'is_licensed'] = true;
                $_SESSION[APP_SESSION_PREFIX . 'license_data'] = $verificationResult['data'];
                $_SESSION[APP_SESSION_PREFIX . 'license_code_raw'] = $licenseCode;
                $_SESSION[APP_SESSION_PREFIX . 'status_message'] = 'Lisensi berhasil diaktifkan!';
                $_SESSION[APP_SESSION_PREFIX . 'status_type'] = 'success';
                
                $telegramBot->logEvent('Lisensi Baru Diaktifkan ✅', [
                    'Domain' => $verificationResult['data']['domain'],
                    'Expires' => $verificationResult['data']['expiration_date'],
                    'IP' => $_SERVER['REMOTE_ADDR']
                ]);
                
                header('Location: /dashboard?view=admin');
                exit();
            } else {
                $licenseMessage = 'Lisensi valid, tetapi gagal disimpan di database lokal.';
                $licenseMessageType = 'error';
            }
        } else {
            $licenseMessage = $verificationResult['message'] ?? 'Respons tidak valid dari server lisensi.';
            $licenseMessageType = 'error';
        }
    }
} else {
    $currentAppHost = preg_replace('/:\d+$/', '', ($_SERVER['HTTP_HOST'] ?? ''));
    $licenseManager = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
    $storedLicenseVerification = $licenseManager->getAndVerifyStoredLicense();
    if ($storedLicenseVerification['status']) {
        header('Location: /dashboard?view=admin');
        exit();
    } elseif (empty($licenseMessage)) {
        $licenseMessage = $storedLicenseVerification['message'];
        $licenseMessageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Lisensi - CTRX</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --color-primary: 79 70 229; }
        body { font-family: 'Inter', sans-serif; }
        .focus\:ring-primary:focus { --tw-ring-color: rgb(var(--color-primary) / 0.5); }
    </style>
</head>
<body class="h-full">
<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-12 w-auto" src="/assets/logo.png" alt="CTRX Logo">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Verifikasi Lisensi Aplikasi</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Silakan masukkan kode lisensi yang valid untuk mengaktifkan aplikasi.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white px-4 py-8 shadow-xl rounded-lg sm:px-10">
            
            <?php if ($licenseMessage && $licenseMessage !== 'License not found in local storage.'): ?>
            <div class="rounded-md <?php echo $licenseMessageType === 'success' ? 'bg-green-50' : 'bg-red-50'; ?> p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid <?php echo $licenseMessageType === 'success' ? 'fa-check-circle text-green-400' : 'fa-times-circle text-red-400'; ?> h-5 w-5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium <?php echo $licenseMessageType === 'success' ? 'text-green-800' : 'text-red-800'; ?>"><?php echo htmlspecialchars($licenseMessage); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <form id="licenseForm" class="space-y-6" action="license.php" method="POST">
                <div>
                    <label for="license_code" class="block text-sm font-medium leading-6 text-gray-900">Kode Lisensi</label>
                    <div class="mt-2">
                        <textarea id="license_code" name="license_code" rows="4" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6" placeholder="Tempel (paste) kode lisensi Anda di sini..."></textarea>
                    </div>
                </div>

                <div>
                    <button type="submit" id="submitButton" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500">
                        <i class="fa-solid fa-key mr-2"></i>
                        Verifikasi & Aktifkan Aplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <p class="mt-10 text-center text-sm text-gray-500">
        Belum punya lisensi?
        <a href="https://t.me/useraib" target="_blank" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Hubungi kami</a>
    </p>
</div>

<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display:none;">
    <div class="spinner border-4 border-gray-300 border-t-indigo-500 rounded-full w-12 h-12 animate-spin"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const licenseForm = document.getElementById('licenseForm');
        const submitButton = document.getElementById('submitButton');
        
        licenseForm.addEventListener('submit', function() {
            document.getElementById('loadingOverlay').style.display = 'flex';
            submitButton.disabled = true;
        });
    });
</script>
</body>
</html>
