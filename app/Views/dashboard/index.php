<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Dashboard Pengguna<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Pengguna</h1>
        <p class="text-gray-600 dark:text-gray-400">Buat dan kelola subdomain Anda di sini.</p>
    </div>

    <?= $this->include('partials/flash_messages') ?>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold mb-4">Buat Subdomain Baru</h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
    <h2 class="text-xl font-semibold mb-2">Buat Subdomain Baru</h2>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Kuota Anda: <strong><?= $current_sub_count ?></strong> dari <strong><?= $max_subdomains ?></strong> telah digunakan.
    </div>

    <form action="<?= site_url('dashboard/subdomain/create') ?>" method="post" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <form action="<?= site_url('dashboard/subdomain/create') ?>" method="post" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <?= csrf_field() ?>
            
            <div class="md:col-span-1">
                <label for="subdomain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Subdomain</label>
                <input type="text" name="subdomain" id="subdomain" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" placeholder="proyek-saya" value="<?= old('subdomain') ?>" required>
            </div>
            
            <div class="md:col-span-1">
                <label for="domain_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Domain</label>
                <select id="domain_id" name="domain_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                    <?php foreach ($domains as $domain): ?>
                        <option value="<?= $domain['id'] ?>" <?= old('domain_id') == $domain['id'] ? 'selected' : '' ?>>.<?= $domain['domain_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="md:col-span-1">
                <label for="type_selector" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Record</label>
                <select id="type_selector" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                    <option value="A" <?= old('type', 'A') == 'A' ? 'selected' : '' ?>>A (IP Address)</option>
                    <option value="CNAME" <?= old('type') == 'CNAME' ? 'selected' : '' ?>>CNAME (Alias)</option>
                </select>
            </div>
            
            <div class="md:col-span-1">
                <label id="content_label" for="content_input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">IP Address Tujuan</label>
                <input type="text" name="content" id="content_input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" placeholder="192.168.1.1" value="<?= old('content') ?>" required>
            </div>
            
            <div class="md:col-span-1">
                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Buat!</button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold mb-4">Subdomain Anda</h2>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3">Tipe</th>
                        <th scope="col" class="px-6 py-3">Tujuan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subdomains)): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="4" class="px-6 py-4 text-center">Anda belum memiliki subdomain.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($subdomains as $sub): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <?= esc($sub['name']) ?>.<?= esc($sub['domain_name']) ?>
                            </th>
                            <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"><?= esc($sub['type']) ?></span></td>
                            <td class="px-6 py-4 break-all"><?= esc($sub['content']) ?></td>
                            <td class="px-6 py-4 flex items-center space-x-3">
    <a href="<?= site_url('dashboard/subdomain/edit/' . $sub['id']) ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
    
    <form action="<?= site_url('dashboard/subdomain/delete/' . $sub['id']) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus subdomain ini?');">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelector = document.getElementById('type_selector');
        const contentLabel = document.getElementById('content_label');
        const contentInput = document.getElementById('content_input');

        function updateForm(type) {
            if (type === 'A') {
                contentLabel.textContent = 'IP Address Tujuan';
                contentInput.placeholder = '192.168.1.1';
            } else if (type === 'CNAME') {
                contentLabel.textContent = 'Target Domain (Alias)';
                contentInput.placeholder = 'target.example.com';
            }
        }

        // Jalankan saat halaman dimuat untuk menyesuaikan dengan nilai `old()`
        updateForm(typeSelector.value);

        // Jalankan saat pilihan diubah
        typeSelector.addEventListener('change', (event) => {
            updateForm(event.target.value);
            contentInput.value = ''; // Kosongkan input saat tipe diubah
        });
    });
</script>
<?= $this->endSection() ?>
