<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Kelola Domain Dasar<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Domain Dasar</h1>
        <a href="<?= site_url('admin/domains/create') ?>" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            + Tambah Domain Baru
        </a>
    </div>

    <?= $this->include('partials/flash_messages') ?>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Domain</th>
                    <th scope="col" class="px-6 py-3">Zone ID</th>
                    <th scope="col" class="px-6 py-3">Tanggal Dibuat</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($domains)): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="4" class="px-6 py-4 text-center">Belum ada domain dasar yang ditambahkan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($domains as $domain): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <?= esc($domain['domain_name']) ?>
                        </th>
                        <td class="px-6 py-4"><?= esc($domain['zone_id']) ?></td>
                        <td class="px-6 py-4"><?= date('d M Y, H:i', strtotime($domain['created_at'])) ?></td>
                        <td class="px-6 py-4">
                            <form action="<?= site_url('admin/domains/delete/' . $domain['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus domain ini? Semua subdomain di bawahnya juga akan terpengaruh.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Hapus</button>
                             </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
