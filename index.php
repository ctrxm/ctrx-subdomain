<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php';

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

$isAdminMode = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;


?>
<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRX Subdomain: Buat Subdomain Gratis & Cepat</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --color-primary: 79 70 229; /* Indigo */
            --color-secondary: 107 114 128; /* Gray */
        }
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; /* gray-50 */ }
        .focus\:ring-primary:focus { --tw-ring-color: rgb(var(--color-primary) / 0.5); }
        .text-gradient {
            background-image: linear-gradient(to right, rgb(var(--color-primary)), #9333ea); /* Indigo to Purple */
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="h-full">
<div class="min-h-full">
    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full z-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex items-center">
                    <img class="h-8 w-auto" src="/assets/logo.png" alt="CTRX Logo">
                    <span class="ml-3 text-xl font-bold text-gray-800">CTRX</span>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    <a href="#features" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-900">Fitur</a>
                    <a href="#faq" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-900">FAQ</a>
                    <?php if ($isAdminMode): ?>
                        <a href="/admin" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            <i class="fa-solid fa-user-shield -ml-1 mr-2 h-5 w-5"></i> Admin Panel
                        </a>
                    <?php else: ?>
                         <a href="/login" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            <i class="fa-solid fa-arrow-right-to-bracket -ml-1 mr-2 h-5 w-5"></i> Login Admin
                        </a>
                    <?php endif; ?>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" id="hamburger-button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" aria-controls="mobile-menu" aria-expanded="false">
                        <i id="hamburger-icon-open" class="fa-solid fa-bars h-6 w-6 block"></i>
                        <i id="hamburger-icon-close" class="fa-solid fa-xmark h-6 w-6 hidden"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="space-y-1 pb-3 pt-2">
                <a href="#features" class="mobile-menu-link block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Fitur</a>
                <a href="#faq" class="mobile-menu-link block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">FAQ</a>
                 <?php if ($isAdminMode): ?>
                    <a href="/admin" class="mobile-menu-link block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Admin Panel</a>
                <?php else: ?>
                    <a href="/login" class="mobile-menu-link block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Login Admin</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <div class="relative bg-gray-50 pt-32 pb-20 sm:pt-40 sm:pb-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-6xl text-gradient">Subdomain Gratis, Secepat Kilat</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600">Dapatkan subdomain pilihan Anda secara instan, aman, dan tanpa biaya. Didukung penuh oleh infrastruktur Cloudflare untuk performa terbaik.</p>
                    
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="/create" class="rounded-md bg-indigo-600 px-4 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Buat Subdomain Sekarang
                        </a>
                        <a href="https://t.me/useraib" target="_blank" class="text-base font-semibold leading-6 text-gray-900">
                            Beli Source Code <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="features" class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600">Semuanya Gratis</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Semua yang Anda Butuhkan untuk Memulai</p>
                    <p class="mt-6 text-lg leading-8 text-gray-600">Kami menyediakan fitur esensial untuk memastikan subdomain Anda berjalan lancar, aman, dan cepat.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600"><i class="fa-solid fa-bolt h-6 w-6 text-white"></i></div>
                                Aktivasi Instan
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Subdomain Anda langsung aktif dalam hitungan detik setelah dibuat, terpropagasi ke seluruh dunia melalui jaringan Cloudflare.</dd>
                        </div>
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600"><i class="fa-solid fa-shield-halved h-6 w-6 text-white"></i></div>
                                Keamanan Terjamin
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Dilindungi oleh reCAPTCHA untuk mencegah bot, dan setiap subdomain secara otomatis mendapatkan perlindungan DDoS dan WAF dasar dari Cloudflare.</dd>
                        </div>
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600"><i class="fa-solid fa-server h-6 w-6 text-white"></i></div>
                                Record A & CNAME
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Dukungan penuh untuk record A (mengarahkan ke IP) dan CNAME (mengarahkan ke domain lain), memberikan Anda fleksibilitas penuh.</dd>
                        </div>
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600"><i class="fa-solid fa-infinity h-6 w-6 text-white"></i></div>
                                Gratis Selamanya
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Tidak ada biaya tersembunyi, tidak ada masa percobaan. Layanan ini sepenuhnya gratis, didukung oleh semangat komunitas.</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        
        <div id="faq" class="bg-gray-50 py-24 sm:py-32">
             <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-4xl divide-y divide-gray-900/10">
                    <h2 class="text-2xl font-bold leading-10 tracking-tight text-gray-900">Pertanyaan Umum</h2>
                    <dl class="mt-10 space-y-6 divide-y divide-gray-900/10">
                        <details class="pt-6 group">
                            <summary class="text-base font-semibold leading-7 text-gray-900 flex w-full items-center justify-between text-left cursor-pointer">
                                <span>Apakah layanan ini benar-benar gratis?</span>
                                <span class="ml-6 flex h-7 items-center"><i class="fa-solid fa-plus group-open:hidden"></i><i class="fa-solid fa-minus hidden group-open:block"></i></span>
                            </summary>
                            <div class="mt-2 pr-12">
                                <p class="text-base leading-7 text-gray-600">Ya, 100% gratis tanpa biaya tersembunyi. Kami menyediakan layanan ini untuk mendukung komunitas developer dan pegiat teknologi.</p>
                            </div>
                        </details>
                        <details class="pt-6 group">
                            <summary class="text-base font-semibold leading-7 text-gray-900 flex w-full items-center justify-between text-left cursor-pointer">
                                <span>Berapa lama subdomain saya akan aktif?</span>
                                <div class="mt-2 pr-12">
                                    <p class="text-base leading-7 text-gray-600">Karena terintegrasi langsung dengan API Cloudflare, subdomain Anda akan aktif dan dapat diakses secara global dalam hitungan detik hingga beberapa menit.</p>
                                </div>
                            </details>
                        <details class="pt-6 group">
                            <summary class="text-base font-semibold leading-7 text-gray-900 flex w-full items-center justify-between text-left cursor-pointer">
                                <span>Bisakah saya mengarahkan subdomain ke IP Dinamis?</span>
                                <div class="mt-2 pr-12">
                                    <p class="text-base leading-7 text-gray-600">Bisa. Anda bisa menggunakan layanan DDNS (Dynamic DNS) pihak ketiga lalu mengarahkan subdomain Anda ke hostname DDNS tersebut menggunakan record CNAME di halaman pembuatan subdomain.</p>
                                </div>
                            </details>
                    </dl>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900" aria-labelledby="footer-heading">
        <div class="mx-auto max-w-7xl px-6 pb-8 pt-16 sm:pt-24 lg:px-8 lg:pt-32">
            <div class="mt-16 border-t border-white/10 pt-8 sm:mt-20 lg:mt-24">
                <p class="text-xs leading-5 text-gray-400">&copy; <?php echo date('Y'); ?> CTRX Subdomain. Dibuat dengan ❤️ di CILACAP.</p>
            </div>
        </div>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hamburger Menu Logic
        const hamburgerButton = document.getElementById('hamburger-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('hamburger-icon-open');
        const iconClose = document.getElementById('hamburger-icon-close');
        hamburgerButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
        document.querySelectorAll('.mobile-menu-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            });
        });
    });
</script>

</body>
</html>
