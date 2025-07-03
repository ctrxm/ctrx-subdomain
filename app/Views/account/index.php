<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Pengaturan Akun<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Pengaturan Akun</h1>

    <?= $this->include('partials/flash_messages') ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Ubah Password</h2>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Email Anda: <strong><?= esc(auth()->user()->email) ?></strong>. <br>
            Gunakan form di bawah ini untuk mengubah password Anda.
        </p>

        <form action="<?= site_url('actions/password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div>
                    <label for="password-old" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Lama</label>
                    <input type="password" name="password-old" id="password-old" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" required>
                </div>
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password Baru</label>
                    <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" required>
                </div>
                <div>
                    <label for="password-confirm" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password Baru</label>
                    <input type="password" name="password-confirm" id="password-confirm" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" required>
                </div>

                <div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Password Baru
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
