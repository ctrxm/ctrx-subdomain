<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/session_manager.php'; // Memulai sesi

use App\Database;
use App\CloudflareManager;
use App\LicenseManager;

if (!isset($_SESSION['user_id'])) {
    header('Location: /login-user');
    exit();
}


$isAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);

$view = ($_GET['view'] ?? 'user');
if ($view === 'admin' && !$isAdmin) {
    $view = 'user'; 
}


$currentAppHost = preg_replace('/:\d+$/', '', ($_SERVER['HTTP_HOST'] ?? ''));

$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$licenseManagerCheck = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
$storedLicenseVerification = $licenseManagerCheck->getAndVerifyStoredLicense();

if (!$storedLicenseVerification['status']) {
    $_SESSION['status_message'] = $storedLicenseVerification['message'];
    $_SESSION['status_type'] = 'error';
    header('Location: /license');
    exit();
}

$subdomains = [];
$subdomainCount = 0;
$managedDomainsInDb = [];
$allFilteredRecords = [];
$message = $_SESSION['status_message'] ?? '';
$messageType = $_SESSION['status_type'] ?? '';
unset($_SESSION['status_message'], $_SESSION['status_type']);


if ($view === 'admin' && $isAdmin) {
    
    $currentAppHost = preg_replace('/:\d+$/', '', ($_SERVER['HTTP_HOST'] ?? ''));
    $licenseManager = new LicenseManager(LICENSE_SERVER_API_URL, $currentAppHost, $db);
    $storedLicenseVerification = $licenseManager->getAndVerifyStoredLicense();
    if (!$storedLicenseVerification['status']) {
        $_SESSION['status_message'] = $storedLicenseVerification['message'];
        $_SESSION['status_type'] = 'error';
        header('Location: /license');
        exit();
    }
    
    $managedDomainsInDb = $db->fetchAll("SELECT * FROM managed_domains ORDER BY domain_name ASC");
    
    if (!empty($managedDomainsInDb)) {
        foreach ($managedDomainsInDb as $baseDomain) {
            $baseDomainKey = $baseDomain['domain_name'];
            $zoneId = $baseDomain['zone_id'];
            try {
                $currentCloudflareManager = new CloudflareManager(CLOUDFLARE_API_TOKEN, $zoneId, CLOUDFLARE_EMAIL);
                $dnsRecords = $currentCloudflareManager->getDnsRecords();
                if ($dnsRecords !== false) {
                    foreach ($dnsRecords as $record) {
                        if (in_array($record['type'], ['A', 'CNAME']) && str_ends_with($record['name'], '.' . $baseDomainKey)) {
                            $record['base_domain'] = $baseDomainKey;
                            $allFilteredRecords[] = $record;
                        }
                    }
                }
            } catch (Exception $e) {
                if(empty($message)) { 
                    $message = "Gagal mengambil data dari Cloudflare untuk domain {$baseDomainKey}.";
                    $messageType = 'error';
                }
            }
        }
    }
} else {
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT id, cloudflare_record_id, record_name, record_type, zone_id, created_at FROM user_subdomain_records WHERE user_id = ? ORDER BY created_at DESC";
    $subdomains = $db->fetchAll($sql, [$user_id]);
    $subdomainCount = count($subdomains);
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($view === 'admin' && $isAdmin) ? 'Admin Dashboard' : 'User Dashboard'; ?> - CTRX Subdomain</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --color-primary: 79 70 229; }
        body { font-family: 'Inter', sans-serif; }
        .focus\:ring-primary:focus { --tw-ring-color: rgb(var(--color-primary) / 0.5); }
        .modal-overlay { transition: opacity 0.3s ease; }
        .modal-container { transition: transform 0.3s ease; }
        #toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; }
        .toast { min-width: 300px; transform: translateX(120%); transition: transform 0.3s ease-in-out; }
        .toast.show { transform: translateX(0); }
    </style>
</head>
<body class="h-full">
<div id="toast-container"></div>

<?php // ================================================================= ?>
<?php // === BAGIAN KONTEN HTML (BERDASARKAN TAMPILAN) ==================== ?>
<?php // ================================================================= ?>

<div class="min-h-full">
    <nav class="bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center">
                <img class="h-8 w-auto" src="/assets/logo.png" alt="CTRX Logo">
                <span class="ml-3 text-xl font-bold text-gray-800">CTRX</span>
            </div>
          
            <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                 <?php if ($isAdmin): ?>
                    <a href="?view=user" class="inline-flex items-center px-1 pt-1 text-sm font-medium <?php echo ($view !== 'admin') ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-700'; ?>">Dashboard User</a>
                    <a href="?view=admin" class="inline-flex items-center px-1 pt-1 text-sm font-medium <?php echo ($view === 'admin') ? 'text-indigo-600 border-b-2 border-indigo-500' : 'text-gray-500 hover:text-gray-700'; ?>">Panel Admin</a>
                    <a href="/license_details" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">Detail Lisensi</a>
                 <?php endif; ?>
                 
                 <a href="/create" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">Buat Subdomain</a>
                 
                 <span class="text-sm text-gray-400">|</span>
                 <span class="text-sm font-medium text-gray-800">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                 <a href="/logout" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                    <i class="fa-solid fa-arrow-right-from-bracket -ml-1 mr-2 h-5 w-5"></i> Logout
                 </a>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" id="hamburger-button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary">
                    <i id="hamburger-icon-open" class="fa-solid fa-bars h-6 w-6 block"></i><i id="hamburger-icon-close" class="fa-solid fa-xmark h-6 w-6 hidden"></i>
                </button>
            </div>
        </div>
      </div>

      <div class="sm:hidden hidden" id="mobile-menu">
            <div class="space-y-1 pb-3 pt-2">
                 <?php if ($isAdmin): ?>
                    <a href="?view=user" class="block border-l-4 <?php echo ($view !== 'admin') ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50'; ?> py-2 pl-3 pr-4 text-base font-medium">Dashboard User</a>
                    <a href="?view=admin" class="block border-l-4 <?php echo ($view === 'admin') ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50'; ?> py-2 pl-3 pr-4 text-base font-medium">Panel Admin</a>
                    <a href="/license_details" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:bg-gray-50">Detail Lisensi</a>
                 <?php endif; ?>
                <a href="/create" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:bg-gray-50">Buat Subdomain</a>
            </div>
            <div class="border-t border-gray-200 pb-3 pt-4">
                <div class="mt-3 space-y-1"><a href="/logout.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100">Logout</a></div>
            </div>
        </div>
    </nav>

    <?php if ($view === 'admin' && $isAdmin): ?>
        <?php // ================================================================= ?>
        <?php // === TAMPILAN ADMIN ============================================== ?>
        <?php // ================================================================= ?>
        <div class="py-10">
            <header>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900">Admin Dashboard</h1>
                    <p class="mt-1 text-md text-gray-600">Kelola semua subdomain yang terdaftar dan domain dasar yang tersedia.</p>
                </div>
            </header>
            <main>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mt-8 space-y-8">
                        <?php if ($message): ?>
                        <div class="rounded-md <?php echo $messageType === 'success' ? 'bg-green-50' : 'bg-red-50'; ?> p-4">
                            <div class="flex">
                                <div class="flex-shrink-0"><i class="fa-solid <?php echo $messageType === 'success' ? 'fa-check-circle text-green-400' : 'fa-times-circle text-red-400'; ?> h-5 w-5"></i></div>
                                <div class="ml-3"><p class="text-sm font-medium <?php echo $messageType === 'success' ? 'text-green-800' : 'text-red-800'; ?>"><?php echo htmlspecialchars($message); ?></p></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="bg-white shadow-md rounded-lg"><div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900"><i class="fa-solid fa-sitemap mr-2 text-indigo-600"></i>Kelola Domain Dasar</h3>
                           <form method="POST" action="process.php" class="mt-5 sm:flex sm:items-end sm:gap-4 space-y-4 sm:space-y-0">
                                <input type="hidden" name="action" value="domain_action">
                                <input type="hidden" name="domain_action_type" value="add_domain">
                                <div class="w-full">
                                    <label for="domainName" class="block text-sm font-medium leading-6 text-gray-900">Nama Domain</label>
                                    <input type="text" name="domain_name" id="domainName" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm" placeholder="contoh.com">
                                </div>
                                <div class="w-full">
                                    <label for="zoneId" class="block text-sm font-medium leading-6 text-gray-900">Cloudflare Zone ID</label>
                                    <input type="text" name="zone_id" id="zoneId" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm" placeholder="ID 32 karakter dari Cloudflare">
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tambah Domain</button>
                            </form>
                        </div></div>

                        <div class="mt-8">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4"><i class="fa-solid fa-layer-group mr-2 text-gray-500"></i>Daftar Domain Dasar</h3>
                            <div class="space-y-4">
                                <?php if (empty($managedDomainsInDb)): ?>
                                    <div class="text-center py-12 px-6 bg-white rounded-lg shadow-md"><i class="fa-solid fa-database fa-3x text-gray-400"></i><h3 class="mt-4 text-lg font-semibold text-gray-800">Belum Ada Domain Dasar</h3><p class="mt-1 text-sm text-gray-500">Silakan tambahkan domain dasar menggunakan form di atas.</p></div>
                                <?php else: ?>
                                    <?php foreach ($managedDomainsInDb as $domain): ?>
                                    <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                        <div class="flex-grow">
                                            <p class="text-lg font-bold text-indigo-600"><?php echo htmlspecialchars($domain['domain_name']); ?></p>
                                            <p class="mt-1 text-sm text-gray-600 break-all"><span class="font-medium">Zone ID:</span> <?php echo htmlspecialchars($domain['zone_id']); ?></p>
                                        </div>
                                        <div class="flex flex-shrink-0 gap-2 w-full sm:w-auto">
                                            <form method="POST" action="process.php" onsubmit="return confirm('Yakin ingin menghapus domain dasar <?php echo htmlspecialchars($domain['domain_name']); ?>?')" class="w-full sm:w-auto flex-grow">
                                                <input type="hidden" name="action" value="domain_action"><input type="hidden" name="domain_action_type" value="delete_domain"><input type="hidden" name="domain_id" value="<?php echo htmlspecialchars($domain['id']); ?>">
                                                <button type="submit" class="w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4"><i class="fa-solid fa-list-ul mr-2 text-gray-500"></i>Daftar Semua Subdomain</h3>
                            <div id="subdomain-list-container" class="space-y-4">
                                <?php foreach ($allFilteredRecords as $record): ?>
                                <div class="bg-white shadow-md rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-record-id="<?php echo htmlspecialchars($record['id']); ?>">
                                    <div class="flex-grow">
                                        <p class="text-lg font-bold text-indigo-600 subdomain-name"><?php echo htmlspecialchars($record['name']); ?></p>
                                        <div class="mt-1 flex flex-col sm:flex-row sm:gap-4 text-sm text-gray-600">
                                            <span><span class="font-medium">Tipe:</span> <span class="record-type"><?php echo htmlspecialchars($record['type']); ?></span></span>
                                            <span class="break-all"><span class="font-medium">Target:</span> <span class="record-content"><?php echo htmlspecialchars($record['content']); ?></span></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-shrink-0 gap-2 w-full sm:w-auto">
                                        <button type="button" class="edit-button w-1/2 sm:w-auto flex-grow justify-center text-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                            data-id="<?php echo htmlspecialchars($record['id']); ?>" data-name="<?php echo htmlspecialchars($record['name']); ?>"
                                            data-type="<?php echo htmlspecialchars($record['type']); ?>" data-content="<?php echo htmlspecialchars($record['content']); ?>"
                                            data-base-domain="<?php echo htmlspecialchars($record['base_domain']); ?>">
                                            Edit
                                        </button>
                                        <form method="POST" action="process.php" onsubmit="return confirm('Yakin ingin menghapus subdomain <?php echo htmlspecialchars($record['name']); ?>?');" class="w-1/2 sm:w-auto flex-grow">
                                            <input type="hidden" name="action" value="delete"><input type="hidden" name="recordId" value="<?php echo htmlspecialchars($record['id']); ?>"><input type="hidden" name="recordName" value="<?php echo htmlspecialchars($record['name']); ?>"><input type="hidden" name="recordBaseDomain" value="<?php echo htmlspecialchars($record['base_domain']); ?>">
                                            <button type="submit" class="w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
    <?php else: ?>
        <?php // ================================================================= ?>
        <?php // === TAMPILAN USER BIASA ========================================= ?>
        <?php // ================================================================= ?>
        <div class="py-10">
          <header class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900">Selamat Datang, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h1>
                <p class="mt-1 text-md text-gray-600">Selamat datang di dashboard Anda. Kelola semua subdomain dengan mudah.</p>
          </header>
          <main>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0"><i class="fa-solid fa-layer-group fa-2x text-indigo-500"></i></div>
                                <div class="ml-5 w-0 flex-1"><dl><dt class="text-sm font-medium text-gray-500 truncate">Total Subdomain</dt><dd class="text-3xl font-semibold text-gray-900"><?php echo $subdomainCount; ?></dd></dl></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-indigo-600 hover:bg-indigo-700 overflow-hidden shadow rounded-lg transition-colors">
                        <a href="/create" class="block h-full"><div class="p-5 h-full flex items-center"><div class="flex items-center"><div class="flex-shrink-0"><i class="fa-solid fa-plus-circle fa-2x text-white"></i></div><div class="ml-5 w-0 flex-1"><p class="text-xl font-semibold text-white">Buat Subdomain Baru</p></div></div></div></a>
                    </div>
                </div>
                <div class="mt-10"><h2 class="text-xl font-semibold leading-tight text-gray-800">Daftar Subdomain Anda</h2></div>
                <div class="mt-4 flow-root">
                    <?php if (empty($subdomains)): ?>
                        <div class="text-center py-16 px-6 border-2 border-dashed border-gray-300 rounded-lg bg-white">
                            <i class="fa-solid fa-cloud-moon text-6xl text-gray-400"></i>
                            <h3 class="mt-5 text-xl font-semibold text-gray-900">Anda Belum Punya Subdomain</h3>
                            <p class="mt-1 text-base text-gray-500">Ayo buat subdomain pertama Anda sekarang juga!</p>
                            <div class="mt-6"><a href="/create.php" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"><i class="fa-solid fa-plus -ml-1 mr-2 h-5 w-5"></i> Buat Subdomain</a></div>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($subdomains as $sub): ?>
                            <div class="bg-white shadow rounded-lg p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $sub['record_type'] === 'A' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>"><?php echo htmlspecialchars($sub['record_type']); ?></span>
                                        <code class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($sub['record_name']); ?></code>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Dibuat pada: <?php echo date('d M Y, H:i', strtotime($sub['created_at'])); ?></p>
                                </div>
                                <div class="flex flex-shrink-0 gap-3 w-full sm:w-auto">
                                    <button type="button" class="delete-button w-full sm:w-auto justify-center text-center rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-red-100" data-record-id="<?php echo htmlspecialchars($sub['cloudflare_record_id']); ?>" data-record-name="<?php echo htmlspecialchars($sub['record_name']); ?>"><i class="fa-solid fa-trash-can mr-2"></i>Hapus</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
          </main>
        </div>
    <?php endif; ?>
</div>

<?php // ================================================================= ?>
<?php // === BAGIAN MODAL (DIMUAT SESUAI TAMPILAN) ======================== ?>
<?php // ================================================================= ?>

<?php if ($view === 'admin' && $isAdmin): ?>
<div id="edit-modal" class="relative z-30 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div id="modal-backdrop" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto"><div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div id="modal-panel" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form id="edit-modal-form" method="POST" action="ajax_edit_handler.php">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10"><i class="fa-solid fa-pencil text-indigo-600"></i></div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Edit Subdomain</h3>
                            <div class="mt-4 space-y-4">
                                <input type="hidden" name="action" value="edit"><input type="hidden" name="recordId" id="modal-record-id">
                                <div>
                                    <label for="modal-subdomain-name" class="block text-sm font-medium text-gray-700">Nama Subdomain</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="text" name="subdomainName" id="modal-subdomain-name" class="block w-full min-w-0 flex-1 rounded-none rounded-l-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm">
                                        <span id="modal-base-domain-text" class="inline-flex items-center border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm"></span>
                                        <input type="hidden" name="baseDomainSelect" id="modal-base-domain-select">
                                    </div>
                                </div>
                                <div>
                                    <label for="modal-record-type" class="block text-sm font-medium text-gray-700">Tipe Record</label>
                                    <select id="modal-record-type" name="recordType" class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm"><option value="A">A</option><option value="CNAME">CNAME</option></select>
                                </div>
                                <div>
                                    <label for="modal-target-value" class="block text-sm font-medium text-gray-700">Target</label>
                                    <input type="text" name="targetValue" id="modal-target-value" class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm">
                                </div>
                                <div id="modal-error-message" class="hidden rounded-md bg-red-50 p-4 text-sm text-red-700"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" id="modal-save-button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white sm:ml-3 sm:w-auto">Simpan</button>
                    <button type="button" id="modal-cancel-button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </form>
        </div>
    </div></div>
</div>
<?php else: ?>
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="modal-container transform -translate-y-10 bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"><i class="fa-solid fa-triangle-exclamation text-red-600"></i></div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Subdomain</h3>
                <div class="mt-2"><p class="text-sm text-gray-500">Anda yakin ingin menghapus subdomain <strong id="domain-to-delete" class="font-bold"></strong>? Tindakan ini tidak dapat dibatalkan.</p></div>
            </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <form id="delete-form" action="process.php" method="POST">
                <input type="hidden" name="action" value="delete_subdomain">
                <input type="hidden" id="record-id-to-delete" name="cloudflare_record_id">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Ya, Hapus</button>
            </form>
            <button type="button" id="cancel-delete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
        </div>
    </div>
</div>
<?php endif; ?>


<script>
function showToast(message, type = 'info', duration = 4000) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast relative w-full max-w-sm p-4 bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5';
    let iconHtml = type === 'success' ? '<i class="fa-solid fa-check-circle text-green-500 fa-lg"></i>' : '<i class="fa-solid fa-times-circle text-red-500 fa-lg"></i>';
    toast.innerHTML = `<div class="flex items-start"><div class="flex-shrink-0">${iconHtml}</div><div class="ml-3 w-0 flex-1 pt-0.5"><p class="text-sm font-medium text-gray-900">${type === 'success' ? 'Berhasil' : 'Gagal'}</p><p class="mt-1 text-sm text-gray-500">${message}</p></div><div class="ml-4 flex flex-shrink-0"><button onclick="this.parentElement.parentElement.remove()" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"><span class="sr-only">Close</span><i class="fa-solid fa-xmark h-5 w-5"></i></button></div></div>`;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => toast.remove());
    }, duration);
}

document.addEventListener('DOMContentLoaded', function () {

    const hamburgerButton = document.getElementById('hamburger-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('hamburger-icon-open');
    const iconClose = document.getElementById('hamburger-icon-close');

    if (hamburgerButton) {
        hamburgerButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    }

    <?php if ($view === 'admin' && $isAdmin): ?>
    
    const modal = document.getElementById('edit-modal');
    if(modal) {
        const modalForm = document.getElementById('edit-modal-form');
        const cancelButton = document.getElementById('modal-cancel-button');
        const editButtons = document.querySelectorAll('.edit-button');

        const openModal = (data) => {
            document.getElementById('modal-record-id').value = data.id;
            document.getElementById('modal-record-type').value = data.type;
            document.getElementById('modal-target-value').value = data.content;
            const subdomain = data.name.replace('.' + data.baseDomain, '');
            document.getElementById('modal-subdomain-name').value = subdomain;
            document.getElementById('modal-base-domain-text').innerText = '.' + data.baseDomain;
            document.getElementById('modal-base-domain-select').value = data.baseDomain;
            modal.classList.remove('hidden');
        };
        const closeModal = () => {
            modal.classList.add('hidden');
            document.getElementById('modal-error-message').classList.add('hidden');
        };
        editButtons.forEach(button => { button.addEventListener('click', () => openModal(button.dataset)); });
        cancelButton.addEventListener('click', closeModal);
        document.getElementById('modal-backdrop').addEventListener('click', closeModal);

        modalForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const saveButton = document.getElementById('modal-save-button');
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Menyimpan...';
            const params = new URLSearchParams(new FormData(modalForm));
            fetch('ajax_edit_handler.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const recordId = params.get('recordId');
                    const row = document.querySelector(`div[data-record-id="${recordId}"]`);
                    if(row) {
                        row.querySelector('.subdomain-name').textContent = data.data.updated_record.name;
                        row.querySelector('.record-type').textContent = data.data.updated_record.type;
                        row.querySelector('.record-content').textContent = data.data.updated_record.content;
                    }
                    closeModal();
                    showToast(data.message, 'success');
                } else {
                    const errorDiv = document.getElementById('modal-error-message');
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                document.getElementById('modal-error-message').textContent = 'Terjadi kesalahan komunikasi server.';
                document.getElementById('modal-error-message').classList.remove('hidden');
            })
            .finally(() => {
                saveButton.disabled = false;
                saveButton.innerHTML = 'Simpan';
            });
        });
    }

    <?php else: ?>
    
    const modal = document.getElementById('delete-modal');
    if (modal) {
        const cancelBtn = document.getElementById('cancel-delete');
        const domainToDeleteEl = document.getElementById('domain-to-delete');
        const recordIdInput = document.getElementById('record-id-to-delete');
        
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                domainToDeleteEl.textContent = this.dataset.recordName;
                recordIdInput.value = this.dataset.recordId;
                modal.classList.remove('hidden');
            });
        });

        const closeModal = () => modal.classList.add('hidden');
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(event) { if (event.target === modal) { closeModal(); } });
    }
    <?php endif; ?>

    <?php
    $sessionMessage = $_SESSION['status_message_toast'] ?? null;
    $sessionType = $_SESSION['status_type_toast'] ?? 'info';
    if ($sessionMessage):
    ?>
        showToast('<?php echo addslashes($sessionMessage); ?>', '<?php echo addslashes($sessionType); ?>');
    <?php
        unset($_SESSION['status_message_toast'], $_SESSION['status_type_toast']);
    endif;
    ?>
});
</script>

</body>
</html>
