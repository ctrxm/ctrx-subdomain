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
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A11.953 11.953 0 0112 16.5c-2.998 0-5.74-1.1-7.843-2.918m15.686-5.418A8.959 8.959 0 002.284 12c0 .778.099 1.533.284 2.253m18.148-2.253A11.953 11.953 0 0012 10.5c2.998 0 5.74 1.1 7.843 2.918" /></svg>
                            </div>
                            Integrasi Cloudflare API
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">Terhubung langsung dengan API resmi Cloudflare untuk kecepatan dan keandalan maksimal.</dd>
                    </div>
                    <div class="relative pl-16">
                        <dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            </div>
                            Aman & Terlindungi
                        </dt>
                        <dd class="mt-2 text-base leading-7 text-gray-600 dark:text-gray-300">Dibangun dengan praktik keamanan modern untuk melindungi akun dan domain Anda.</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
