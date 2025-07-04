<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - CTRX</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
  <link href="https://rsms.me/inter/inter.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8 space-y-6">
    <div class="text-center">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Login</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">Masuk ke akun CTRX Anda</p>
    </div>

    <?php if (session('error')): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm"><?= session('error') ?></div>
    <?php elseif (session('errors')): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm">
        <?php foreach ((array)session('errors') as $error): ?>
          <?= $error ?><br>
        <?php endforeach ?>
      </div>
    <?php endif ?>

    <?php if (session('message')): ?>
      <div class="bg-green-100 text-green-700 px-4 py-2 rounded text-sm"><?= session('message') ?></div>
    <?php endif ?>

    <form action="<?= url_to('login') ?>" method="post" class="space-y-4" onsubmit="return holoLogin(this)">
      <?= csrf_field() ?>

      <div>
        <label for="email" class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" id="email" class="w-full px-4 py-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white" required value="<?= old('email') ?>" />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white" required />
      </div>

      <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
        <div class="flex items-center">
          <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" <?php if (old('remember')): ?> checked<?php endif ?> />
          <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Ingat Saya</label>
        </div>
      <?php endif ?>

      <button type="submit" id="loginBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition duration-200">
        Login
      </button>

      <?php if (setting('Auth.allowRegistration')): ?>
        <p class="text-sm text-center text-gray-600 dark:text-gray-400">
          Belum punya akun? <a href="<?= url_to('register') ?>" class="text-blue-600 hover:underline dark:text-blue-400">Daftar</a>
        </p>
      <?php endif ?>

      <?php if (setting('Auth.allowMagicLinkLogins')): ?>
        <p class="text-sm text-center text-gray-600 dark:text-gray-400">
          <a href="<?= url_to('magic-link') ?>" class="text-blue-600 hover:underline dark:text-blue-400">Lupa Password / Gunakan Magic Link</a>
        </p>
      <?php endif ?>
    </form>
  </div>

  <script>
    function holoLogin(form) {
      const btn = document.getElementById('loginBtn');
      btn.disabled = true;
      btn.innerText = 'Memproses...';
      return true;
    }
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
