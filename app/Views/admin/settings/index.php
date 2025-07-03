<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Pengaturan Sistem<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Pengaturan Sistem</h1>

    <?= $this->include('partials/flash_messages') ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <form action="<?= site_url('admin/settings/update') ?>" method="post">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div>
                    <label for="max_subdomains" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Maksimal Subdomain per Pengguna</label>
                    <input type="number" name="max_subdomains_per_user" id="max_subdomains" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" value="<?= esc($max_subdomains) ?>" required>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Atur batas jumlah subdomain yang bisa dibuat oleh satu akun pengguna.</p>
                </div>

                <div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
