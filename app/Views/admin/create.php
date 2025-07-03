<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Tambah Domain Dasar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg dark:bg-gray-800">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Domain Dasar Baru</h1>
        <a href="<?= site_url('admin/domains') ?>" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
            &larr; Kembali
        </a>
    </div>
    
    <?= $this->include('partials/flash_messages') ?>

    <form action="<?= site_url('admin/domains/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-4">
            <label for="domain_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Domain</label>
            <input type="text" name="domain_name" id="domain_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" placeholder="example.com" value="<?= old('domain_name') ?>" required>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pastikan domain ini sudah terdaftar di akun Cloudflare Anda.</p>
        </div>
        <div class="mb-6">
            <label for="zone_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cloudflare Zone ID</label>
            <input type="text" name="zone_id" id="zone_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" placeholder="e.g., a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6" value="<?= old('zone_id') ?>" required>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Anda bisa menemukan Zone ID di halaman overview domain di Cloudflare.</p>
        </div>
        <div>
            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Simpan Domain
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
