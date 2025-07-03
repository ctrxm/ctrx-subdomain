<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Tambah Domain Dasar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg dark:bg-gray-800">
    <h1 class="text-3xl font-bold mb-6">Tambah Domain Dasar Baru</h1>
    
    <?php if (session()->has('errors')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Error</p>
            <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="/admin/domains/store" method="post">
        <?= csrf_field() ?>
        <div class="mb-4">
            <label for="domain_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Domain</label>
            <input type="text" name="domain_name" id="domain_name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" placeholder="example.com" value="<?= old('domain_name') ?>">
        </div>
        <div class="mb-6">
            <label for="zone_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cloudflare Zone ID</label>
            <input type="text" name="zone_id" id="zone_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" placeholder="e.g., a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6" value="<?= old('zone_id') ?>">
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                Simpan Domain
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
