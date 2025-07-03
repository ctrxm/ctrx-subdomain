<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Edit Subdomain<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Subdomain</h1>
        <a href="<?= site_url('dashboard') ?>" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
            &larr; Kembali ke Dashboard
        </a>
    </div>
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold mb-1 text-gray-900 dark:text-white">
            <?= esc($subdomain['name']) ?>.<?= esc($subdomain['domain_name']) ?>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Ubah tipe record atau tujuannya di bawah ini.
        </p>

        <?= $this->include('partials/flash_messages') ?>

        <form action="<?= site_url('dashboard/subdomain/update/' . $subdomain['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="space-y-4">
                <div>
                    <label for="type_selector" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Record</label>
                    <select id="type_selector" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                        <option value="A" <?= old('type', $subdomain['type']) == 'A' ? 'selected' : '' ?>>A (IP Address)</option>
                        <option value="CNAME" <?= old('type', $subdomain['type']) == 'CNAME' ? 'selected' : '' ?>>CNAME (Alias)</option>
                    </select>
                </div>
                
                <div>
                    <label id="content_label" for="content_input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">IP Address Tujuan</label>
                    <input type="text" name="content" id="content_input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600" placeholder="192.168.1.1" value="<?= old('content', $subdomain['content']) ?>" required>
                </div>

                <div>
                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
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

        updateForm(typeSelector.value);
        typeSelector.addEventListener('change', (event) => {
            updateForm(event.target.value);
        });
    });
</script>
<?= $this->endSection() ?>
