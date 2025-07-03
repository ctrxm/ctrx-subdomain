<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang di Admin Panel</h1>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Ringkasan data dari sistem Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" ...></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengguna</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $totalUsers ?></p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-full">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" ...></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Domain Dasar</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $totalDomains ?></p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex items-center space-x-4">
            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600 dark:text-green-300" ...></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Subdomain</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $totalSubdomains ?></p>
            </div>
        </div>
    </div>
    
    <div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Aktivitas Subdomain (7 Hari Terakhir)</h2>
        <div id="subdomainChart"></div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 2. Opsi untuk grafik
        var options = {
          series: [{
            name: "Subdomain Baru",
            data: <?= $chartData ?>  // <-- Ambil data dari controller
        }],
          chart: {
          height: 350,
          type: 'area',
          toolbar: {
            show: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        xaxis: {
          type: 'category',
          categories: <?= $chartLabels ?>, // <-- Ambil label dari controller
          labels: {
            style: {
                colors: '#6B7280' // Warna label sumbu X (abu-abu)
            }
          }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6B7280' // Warna label sumbu Y (abu-abu)
                }
            }
        },
        tooltip: {
            theme: 'dark',
            x: {
                format: 'dd MMM'
            },
        },
        grid: {
            borderColor: '#374151' // Warna garis grid
        }
        };

        // 3. Buat dan render grafik
        var chart = new ApexCharts(document.querySelector("#subdomainChart"), options);
        chart.render();
    });
</script>
<?= $this->endSection() ?>
