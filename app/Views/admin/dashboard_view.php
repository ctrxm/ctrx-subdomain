<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <h1 class="text-3xl font-bold">Selamat Datang di Admin Panel</h1>
    <p class="mt-2 text-gray-600">Ini adalah halaman utama dashboard admin.</p>
    
    <div class="mt-6">
        <a href="<?= site_url('admin/domains') ?>" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
            Kelola Domain Dasar
        </a>
    </div>
<?= $this->endSection() ?>
