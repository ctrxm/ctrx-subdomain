<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Kelola Pengguna<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Pengguna</h1>
    </div>

    <?= $this->include('partials/flash_messages') ?>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Username</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Tanggal Bergabung</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <?= esc($user->username) ?>
                    </th>
                    <td class="px-6 py-4"><?= esc($user->email) ?></td>
                    <td class="px-6 py-4">
                        <?php if ($user->inGroup('admin')): ?>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">Admin</span>
                        <?php else: ?>
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">User</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4"><?= date('d M Y', strtotime($user->created_at)) ?></td>
                    <td class="px-6 py-4">
                        <?php if (auth()->id() !== $user->id): // Tombol hanya muncul jika bukan diri sendiri ?>
                            <form action="<?= site_url('admin/users/toggle-admin/' . $user->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <?php if ($user->inGroup('admin')): ?>
                                    <button type="submit" class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline">Cabut Admin</button>
                                <?php else: ?>
                                    <button type="submit" class="font-medium text-green-600 dark:text-green-500 hover:underline">Jadikan Admin</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
