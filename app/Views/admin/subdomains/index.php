<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Kelola Semua Subdomain<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Semua Subdomain</h1>
    </div>

    <?= $this->include('partials/flash_messages') ?>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Subdomain</th>
                    <th scope="col" class="px-6 py-3">Pemilik</th>
                    <th scope="col" class="px-6 py-3">Tipe</th>
                    <th scope="col" class="px-6 py-3">Tujuan</th>
                    <th scope="col" class="px-6 py-3">Dibuat</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subdomains)): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="6" class="px-6 py-4 text-center">Belum ada subdomain yang dibuat oleh pengguna.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($subdomains as $sub): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <?= esc($sub['name']) ?>.<?= esc($sub['domain_name']) ?>
                        </th>
                        <td class="px-6 py-4"><?= esc($sub['username']) ?></td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"><?= esc($sub['type']) ?></span>
                        </td>
                        <td class="px-6 py-4 break-all"><?= esc($sub['content']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= date('d M Y', strtotime($sub['created_at'])) ?></td>
                        <td class="px-6 py-4">
                            <form action="<?= site_url('admin/subdomains/delete/' . $sub['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus subdomain ini secara permanen?');">
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
