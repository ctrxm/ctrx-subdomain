<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Selamat Datang di Subdomain Creator<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="relative isolate overflow-hidden">
    <div class="mx-auto max-w-4xl pt-10 pb-24 sm:pt-16 sm:pb-32 text-center">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
            Buat Subdomain Anda dalam
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-400">Hitungan Detik</span>
        </h1>
        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
            Platform manajemen subdomain yang cepat, andal, dan terintegrasi penuh dengan Cloudflare. Mulai proyek Anda berikutnya dengan mudah.
        </p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="<?= site_url('register') ?>" class="rounded-md bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                Mulai Gratis
            </a>
            <a href="#features" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                Lihat Fitur <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>

    <!-- Statistik -->
    <div class="mt-10 max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
        <div>
            <p class="text-3xl font-bold text-blue-600">+2,300</p>
            <p class="text-gray-600 dark:text-gray-300">Subdomain Dibuat</p>
        </div>
        <div>
            <p class="text-3xl font-bold text-blue-600">99.99%</p>
            <p class="text-gray-600 dark:text-gray-300">Uptime</p>
        </div>
        <div>
            <p class="text-3xl font-bold text-blue-600">500+</p>
            <p class="text-gray-600 dark:text-gray-300">Pengguna Aktif</p>
        </div>
    </div>

    <!-- Testimoni -->
    <div class="mt-24 max-w-4xl mx-auto text-center">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Apa Kata Mereka?</h3>
        <div class="mt-8 flex flex-col md:flex-row justify-center gap-6">
            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <p class="text-gray-700 dark:text-gray-300 italic">"Dalam hitungan detik subdomain saya langsung aktif. Gila sih, cepet banget!"</p>
                <div class="mt-4 flex items-center justify-center gap-2">
                    <img src="https://i.pravatar.cc/40?u=user1" class="w-8 h-8 rounded-full" />
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Dimas R.</span>
                </div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <p class="text-gray-700 dark:text-gray-300 italic">"Gak ribet, langsung jalan. Apalagi sudah terhubung ke Cloudflare!"</p>
                <div class="mt-4 flex items-center justify-center gap-2">
                    <img src="https://i.pravatar.cc/40?u=user2" class="w-8 h-8 rounded-full" />
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Sarah L.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fitur -->
    <div id="features" class="py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-blue-600">Manajemen Mudah</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Semua yang Anda Butuhkan untuk Subdomain</p>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                    Dari rekor A hingga CNAME, kelola semua kebutuhan DNS Anda dari satu dashboard yang intuitif.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="..." /></svg>
                            </div>
                            Integrasi Cloudflare API
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">Terhubung langsung dengan API resmi Cloudflare untuk kecepatan dan keandalan maksimal.</dd>
                    </div>
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="..." /></svg>
                            </div>
                            Aman & Terlindungi
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">Dibangun dengan praktik keamanan modern untuk melindungi akun dan domain Anda.</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="mt-24 max-w-3xl mx-auto">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white text-center mb-6">Pertanyaan Umum</h3>
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-800 dark:text-white">Apakah gratis?</h4>
                <p class="text-gray-600 dark:text-gray-300">Ya, Anda bisa mulai menggunakan layanan secara gratis tanpa biaya tersembunyi.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 dark:text-white">Apakah saya perlu punya akun Cloudflare?</h4>
                <p class="text-gray-600 dark:text-gray-300">Tidak perlu. Semua integrasi sudah di-handle otomatis oleh sistem kami.</p>
            </div>
        </div>
    </div>

    <!-- Partner Logos -->
    <div class="mt-24 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wide">Powered By CTRX CORP</p>
        <div class="mt-4 flex justify-center gap-6">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/cloudflare/cloudflare-original.svg" class="h-8" alt="Cloudflare">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/codeigniter/codeigniter-plain.svg" class="h-8" alt="CodeIgniter">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-plain.svg" class="h-8" alt="PHP">
        </div>
    </div>
</div>
<?= $this->endSection() ?>
