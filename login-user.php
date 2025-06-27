<?php
// login.php
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
$licenseManagerCheck = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
$storedLicenseVerification = $licenseManagerCheck->getAndVerifyStoredLicense();

if (!$storedLicenseVerification['status']) {
    $_SESSION['status_message'] = $storedLicenseVerification['message'];
    $_SESSION['status_type'] = 'error';
    header('Location: /license');
    exit();
}

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['status_message'] = 'Email dan password wajib diisi.';
        $_SESSION['status_type'] = 'error';
    } else {
        $sql = "SELECT id, name, password, is_admin FROM users WHERE email = ?";
        // PERBAIKAN: Menggunakan metode fetch() yang benar sesuai Database.php
        $user = $db->fetch($sql, [$email]);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                header("Location: /dashboard");
                exit();
            } else {
                $_SESSION['status_message'] = 'Password yang Anda masukkan salah.';
                $_SESSION['status_type'] = 'error';
            }
        } else {
            $_SESSION['status_message'] = 'Email tidak ditemukan.';
            $_SESSION['status_type'] = 'error';
        }
    }
    header('Location: /login-user');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CTRX Subdomain</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="h-full">
<div id="toast-container" class="fixed top-5 right-5 z-50"></div>
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-12 w-auto" src="/assets/logo.png" alt="CTRX Logo">
    <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Login ke Akun Anda</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" action="login-user.php" method="POST">
      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Alamat Email</label>
        <div class="mt-2">
          <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        </div>
        <div class="mt-2">
          <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
      Belum punya akun?
      <a href="/register" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Daftar sekarang</a>
    </p>
  </div>
</div>
<script>
    // Script notifikasi/toast sama seperti di create.php
    function showToast(message, type = 'info', duration = 5000) {
        // ... (Anda bisa copy-paste fungsi showToast dari create.php ke sini)
    }
    <?php if (isset($_SESSION['status_message'])): ?>
        showToast('<?php echo addslashes($_SESSION['status_message']); ?>', '<?php echo addslashes($_SESSION['status_type']); ?>');
        <?php unset($_SESSION['status_message'], $_SESSION['status_type']); ?>
    <?php endif; ?>
</script>
</body>
</html>
