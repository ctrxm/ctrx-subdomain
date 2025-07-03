<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Subdomain Creator' ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <!-- Tambahan untuk halaman login -->
    <?= $this->renderSection('style') ?>
</head>

<?php $segment = service('uri')->getSegment(1); ?>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 <?= $segment === 'login' ? 'overflow-hidden' : '' ?>">
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex justify-start items-center">
                    <?php if (auth()->loggedIn() && auth()->user()->is_admin == 1 && $segment === 'admin'): ?>
                        <button data-drawer-target="admin-sidebar" data-drawer-toggle="admin-sidebar" aria-controls="admin-sidebar" class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
                    <?php endif; ?>
                    <a href="<?= site_url('/') ?>" class="flex items-center justify-between mr-4">
                        <span class="self-center text-2xl font-bold whitespace-nowrap dark:text-white">CTRX CORP</span>
                    </a>
                </div>
                <div class="flex items-center lg:order-2">
                    <?php if (auth()->loggedIn()): ?>
                        <button type="button" class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                            <span class="sr-only">Buka menu pengguna</span>
                            <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=<?= urlencode(auth()->user()->username) ?>&background=0D8ABC&color=fff" alt="user photo">
                        </button>
                        <div class="hidden z-50 my-4 w-56 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown">
                            <div class="py-3 px-4">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white"><?= esc(auth()->user()->username) ?></span>
                                <span class="block text-sm text-gray-500 truncate dark:text-gray-400"><?= esc(auth()->user()->email) ?></span>
                            </div>
                            <ul class="py-1 text-gray-500 dark:text-gray-400" aria-labelledby="user-menu-button">
                                <li><a href="<?= site_url('dashboard') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Dashboard</a></li>
                                <li><a href="<?= site_url('dashboard/account') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Akun Saya</a></li>
                                <?php if (auth()->user()->is_admin == 1): ?>
                                    <div class="my-1 border-t border-gray-200 dark:border-gray-600"></div>
                                    <li><a href="<?= site_url('admin') ?>" class="block py-2 px-4 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Admin Panel</a></li>
                                <?php endif; ?>
                            </ul>
                            <ul class="py-1 text-gray-500 dark:text-gray-400">
                                <li><a href="<?= site_url('logout') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= site_url('login') ?>" class="text-gray-800 dark:text-white hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 mr-2 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Login</a>
                        <a href="<?= site_url('register') ?>" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <?php if (auth()->loggedIn() && $segment === 'admin'): ?>
            <?= $this->include('partials/admin_sidebar') ?>
            <main class="p-4 lg:ml-64 h-auto pt-20">
                <?= $this->renderSection('content') ?>
            </main>
        <?php else: ?>
            <main class="pt-20">
                <?= $this->renderSection('main') ?: '<div class="p-4 container mx-auto">' . $this->renderSection('content') . '</div>' ?>
            </main>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <?= $this->renderSection('script') ?>
</body>
</html>
