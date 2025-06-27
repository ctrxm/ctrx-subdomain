<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php';

use App\Database;
use App\LicenseManager;

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$currentAppHost = preg_replace('/:\d+$/', '', ($_SERVER['HTTP_HOST'] ?? ''));
$licenseManager = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
$storedLicenseVerification = $licenseManager->getAndVerifyStoredLicense();

if (!$storedLicenseVerification['status']) {
    $_SESSION['status_message'] = $storedLicenseVerification['message'];
    $_SESSION['status_type'] = 'error';
    header('Location: /license');
    exit();
}
$_SESSION['is_licensed'] = true;

if (!isset($_SESSION['user_id'])) {
    $_SESSION['status_message'] = 'Anda harus login terlebih dahulu untuk membuat subdomain.';
    $_SESSION['status_type'] = 'error'; 
    header("Location: /login-user");
    exit();
}

$managedDomainsRaw = $db->fetchAll("SELECT domain_name, zone_id FROM managed_domains WHERE is_active = TRUE ORDER BY domain_name ASC");
$managedDomains = [];
foreach ($managedDomainsRaw as $row) {
    $managedDomains[$row['domain_name']] = $row['zone_id'];
}
$isAdminMode = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$editRecord = null;
if ($isAdminMode && isset($_GET['edit_id'])) {
    $editRecord = [
        'id' => filter_input(INPUT_GET, 'edit_id', FILTER_UNSAFE_RAW),
        'name' => $_GET['edit_name'] ?? '',
        'type' => $_GET['edit_type'] ?? '',
        'content' => $_GET['edit_content'] ?? '',
        'base_domain' => $_GET['edit_base_domain'] ?? '',
    ];
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editRecord ? 'Edit' : 'Buat'; ?> Subdomain - CTRX Subdomain</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>"></script>

    <style>
        :root { --color-primary: 79 70 229; }
        body { font-family: 'Inter', sans-serif; }
        .focus\:ring-primary:focus { --tw-ring-color: rgb(var(--color-primary) / 0.5); }
        #toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; }
        .toast { min-width: 300px; transform: translateX(120%); transition: transform 0.3s ease-in-out; }
        .toast.show { transform: translateX(0); }
    </style>
</head>
<body class="h-full">
<div id="toast-container"></div>
<div class="min-h-full">
    <nav class="bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <img class="h-8 w-auto" src="/assets/logo.png" alt="CTRX Logo">
                        <span class="ml-3 text-xl font-bold text-gray-800">CTRX</span>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    <?php if ($isAdminMode): ?>
                        <a href="/dashboard?view=admin" class="text-sm font-medium text-gray-500 hover:text-gray-700">Admin Panel</a>
                    <?php endif; ?>
                    <a href="/dashboard" class="text-sm font-medium text-gray-500 hover:text-gray-700">Dashboard</a>
                    <a href="/logout" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50">
                        <i class="fa-solid fa-arrow-right-from-bracket -ml-1 mr-2 h-5 w-5 text-gray-400"></i> Logout
                    </a>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" id="hamburger-button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" aria-controls="mobile-menu" aria-expanded="false">
                        <i id="hamburger-icon-open" class="fa-solid fa-bars h-6 w-6 block"></i>
                        <i id="hamburger-icon-close" class="fa-solid fa-xmark h-6 w-6 hidden"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <a href="/dashboard" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Dashboard</a>
                 <?php if ($isAdminMode): ?>
                    <a href="/dashboard?view=admin" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Admin Panel</a>
                <?php endif; ?>
                <a href="/logout" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Logout</a>
            </div>
        </div>
    </nav>

    <div class="py-10">
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 id="formTitle" class="text-3xl font-bold leading-tight tracking-tight text-gray-900"><?php echo $editRecord ? 'Edit Subdomain' : 'Buat Subdomain Baru'; ?></h1>
                <p class="mt-1 text-md text-gray-600">Isi form di bawah untuk mendaftarkan atau memperbarui record DNS.</p>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mt-8 flex justify-start gap-x-3">
                    <a href="/dashboard.php" class="inline-flex items-center rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <i class="fa-solid fa-arrow-left -ml-1 mr-2 h-5 w-5 text-gray-400"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
                <div class="mt-4 max-w-3xl mx-auto">
                    <div id="compact-success-card" class="hidden rounded-md bg-green-50 p-4 mb-6 ring-1 ring-inset ring-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0"><i class="fa-solid fa-check-circle text-green-500 h-5 w-5"></i></div>
                                <div class="ml-3"><p class="text-sm font-medium text-green-800">Berhasil dibuat: <code id="created-domain-name" class="font-semibold"></code></p></div>
                            </div>
                            <div class="ml-4"><button id="copy-button" class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Copy</button></div>
                        </div>
                    </div>
                    <form id="subdomainForm" method="POST" action="process.php" class="bg-white shadow-md rounded-lg p-6 sm:p-8 space-y-6">
                        <input type="hidden" name="action" value="<?php echo $editRecord ? 'edit' : 'add'; ?>">
                        <input type="hidden" name="recordId" id="recordId" value="<?php echo htmlspecialchars($editRecord['id'] ?? ''); ?>">
                        
                        <div>
                            <label for="subdomainName" class="block text-sm font-medium leading-6 text-gray-900">Nama Subdomain</label>
                            <div class="mt-2 flex rounded-md shadow-sm">
                                <input type="text" name="subdomainName" id="subdomainName" required class="block w-full min-w-0 flex-1 rounded-none rounded-l-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6" placeholder="proyek-baru-saya">
                                <span class="inline-flex items-center border border-l-0 border-gray-300 px-3 text-gray-500 sm:text-sm">.</span>
                                <select id="baseDomainSelect" name="baseDomainSelect" required class="block flex-shrink-0 rounded-none rounded-r-md border-0 bg-transparent py-1.5 pl-3 pr-9 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
                                    <?php if (empty($managedDomains)): ?>
                                        <option value="">(Domain tidak tersedia)</option>
                                    <?php else: ?>
                                        <?php foreach ($managedDomains as $domain => $zoneId): ?>
                                            <option value="<?php echo htmlspecialchars($domain); ?>"><?php echo htmlspecialchars($domain); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="recordType" class="block text-sm font-medium leading-6 text-gray-900">Tipe Record</label>
                                <div class="mt-2">
                                    <select id="recordType" name="recordType" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6">
                                        <option value="A">A (Address)</option>
                                        <option value="CNAME">CNAME (Canonical Name)</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="targetValue" class="block text-sm font-medium leading-6 text-gray-900">Target (IP atau Hostname)</label>
                                <div class="mt-2">
                                    <input type="text" name="targetValue" id="targetValue" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6" placeholder="1.1.1.1 atau host.lain.com">
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                        <div class="pt-5 border-t border-gray-200">
                            <div class="flex justify-end gap-x-3">
                                <a href="/dashboard" id="cancelButton" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Batal</a>
                                <button type="submit" id="submitButton" class="inline-flex justify-center rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    <i class="fa-solid <?php echo $editRecord ? 'fa-save' : 'fa-plus'; ?> -ml-1 mr-2 h-5 w-5"></i>
                                    <?php echo $editRecord ? 'Simpan Perubahan' : 'Tambah Subdomain'; ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    // Ganti seluruh blok <script> Anda dengan yang ini.

    document.addEventListener('DOMContentLoaded', function () {
        // --- Hamburger Menu Logic ---
        const hamburgerButton = document.getElementById('hamburger-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('hamburger-icon-open');
        const closeIcon = document.getElementById('hamburger-icon-close');
        hamburgerButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        const subdomainForm = document.getElementById('subdomainForm');
        
        // --- Logika untuk menampilkan notifikasi dari session ---
        <?php if (isset($_SESSION['status_message'])): ?>
            const statusMessage = '<?php echo addslashes($_SESSION['status_message']); ?>';
            const statusType = '<?php echo addslashes($_SESSION['status_type']); ?>';
            const newDomain = '<?php echo addslashes($_SESSION['newly_created_domain'] ?? ''); ?>';
            
            // Selalu tampilkan notifikasi Toast
            showToast(statusMessage, statusType);

            // LOGIKA YANG HILANG: Menampilkan kartu sukses jika domain baru berhasil dibuat
            if (statusType === 'success' && newDomain) {
                const successCard = document.getElementById('compact-success-card');
                const domainNameEl = document.getElementById('created-domain-name');
                const copyButton = document.getElementById('copy-button');
                
                if(successCard && domainNameEl && copyButton) {
                    domainNameEl.textContent = newDomain;
                    successCard.classList.remove('hidden');

                    // Reset form agar bersih untuk input selanjutnya
                    subdomainForm.reset();

                    copyButton.addEventListener('click', () => {
                        navigator.clipboard.writeText(newDomain).then(() => {
                            const originalText = copyButton.textContent;
                            copyButton.textContent = 'Tersalin!';
                            setTimeout(() => {
                               copyButton.textContent = originalText;
                            }, 2000);
                        });
                    });
                }
            }
            <?php
                // Unset session setelah digunakan
                unset($_SESSION['status_message']);
                unset($_SESSION['status_type']);
                if(isset($_SESSION['newly_created_domain'])) {
                    unset($_SESSION['newly_created_domain']);
                }
            ?>
        <?php endif; ?>
        
        // --- Logika reCAPTCHA dan Submit Form ---
        subdomainForm.addEventListener('submit', function(event) {
             event.preventDefault();
            document.getElementById('submitButton').disabled = true;
            document.getElementById('submitButton').innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...';

            grecaptcha.ready(function() {
                const action = document.querySelector('input[name="action"]').value === 'edit' ? 'submit_edit_subdomain' : 'submit_add_subdomain';
                grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: action}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    subdomainForm.submit(); 
                });
            });
        });
    });

    // --- FUNGSI TOAST/NOTIFIKASI (YANG SEBELUMNYA KOSONG) ---
    function showToast(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = 'toast relative w-full max-w-sm p-4 bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5';
        
        let iconHtml = '';
        let title = '';
        if (type === 'success') {
            iconHtml = '<i class="fa-solid fa-check-circle text-green-500 fa-lg"></i>';
            title = 'Berhasil';
        } else { // Asumsikan selain success adalah error
            iconHtml = '<i class="fa-solid fa-times-circle text-red-500 fa-lg"></i>';
            title = 'Gagal';
        }
        
        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">${iconHtml}</div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">${title}</p>
                    <p class="mt-1 text-sm text-gray-500">${message}</p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button onclick="this.closest('.toast').remove()" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Close</span>
                        <i class="fa-solid fa-xmark h-5 w-5"></i>
                    </button>
                </div>
            </div>`;
        
        toastContainer.appendChild(toast);
        
        // Buat toast muncul
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Atur agar toast hilang setelah durasi tertentu
        setTimeout(() => {
            toast.classList.remove('show');
            // Hapus elemen dari DOM setelah transisi selesai
            toast.addEventListener('transitionend', () => toast.remove());
        }, duration);
    }
</script>

</body>
</html>
