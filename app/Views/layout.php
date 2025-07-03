<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Subdomain Creator' ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

    <style>
        /* Sedikit kustomisasi font atau warna dasar */
        body { font-family: 'Inter', sans-serif; }
    </style>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <nav class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-blue-600 dark:text-blue-500">SubCreator</a>
            <div>
                <?php if (auth()->loggedIn()): ?>
                    <a href="/dashboard" class="mr-4 hover:text-blue-500">Dashboard</a>
                    <a href="/logout" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Logout</a>
                <?php else: ?>
                    <a href="/login" class="mr-4 hover:text-blue-500">Login</a>
                    <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 md:p-6">
        <?= $this->renderSection('content') ?>
    </main>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
