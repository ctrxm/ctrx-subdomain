<?php
// register.php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php';

use App\Database;

if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['status_message'] = 'Semua field wajib diisi.';
        $_SESSION['status_type'] = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status_message'] = 'Format email tidak valid.';
        $_SESSION['status_type'] = 'error';
    } elseif (strlen($password) < 8) {
        $_SESSION['status_message'] = 'Password minimal harus 8 karakter.';
        $_SESSION['status_type'] = 'error';
    } elseif ($password !== $confirm_password) {
        $_SESSION['status_message'] = 'Konfirmasi password tidak cocok.';
        $_SESSION['status_type'] = 'error';
    } else {
        // PERBAIKAN: Menggunakan metode fetch() yang benar
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $existing_user = $db->fetch($check_sql, [$email]);

        if ($existing_user) {
            $_SESSION['status_message'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
            $_SESSION['status_type'] = 'error';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            
            // Metode execute() sudah benar
            $success = $db->execute($insert_sql, [$name, $email, $hashed_password]);

            if ($success) {
                $_SESSION['status_message'] = 'Registrasi berhasil! Silakan login.';
                $_SESSION['status_type'] = 'success';
                header('Location: /login-user');
                exit();
            } else {
                $_SESSION['status_message'] = 'Terjadi kesalahan. Silakan coba lagi.';
                $_SESSION['status_type'] = 'error';
            }
        }
    }
    header('Location: /register');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CTRX Subdomain</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="h-full">
<div id="toast-container" class="fixed top-5 right-5 z-50"></div>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-12 w-auto" src="/assets/logo.png" alt="CTRX Logo">
        <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Buat Akun Baru</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
        <form class="space-y-6" action="register.php" method="POST">
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nama Lengkap</label>
                <div class="mt-2">
                    <input id="name" name="name" type="text" autocomplete="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Alamat Email</label>
                <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                <div class="mt-2">
                    <input id="password" name="password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
             <div>
                <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Password</label>
                <div class="mt-2">
                    <input id="confirm_password" name="confirm_password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div>
                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Daftar</button>
            </div>
        </form>
        <p class="mt-10 text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="/login" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Login di sini</a>
        </p>
    </div>
</div>
<script>
    // Script notifikasi/toast sama seperti di create.php
    function showToast(message, type = 'info') {
        // ... (Anda bisa copy-paste fungsi showToast dari create.php ke sini)
    }
    <?php if (isset($_SESSION['status_message'])): ?>
        showToast('<?php echo addslashes($_SESSION['status_message']); ?>', '<?php echo addslashes($_SESSION['status_type']); ?>');
        <?php unset($_SESSION['status_message'], $_SESSION['status_type']); ?>
    <?php endif; ?>
</script>
</body>
</html>
