<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Installer CTRX CORP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes background-pan {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        body {
            background-color: #020617;
            background-image: radial-gradient(circle at top left, #1e1b4b, #020617 50%);
            background-size: 200% 200%;
            animation: background-pan 15s ease infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes toast-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .toast {
            animation: toast-in 0.5s ease-out forwards;
        }
    </style>
    <script>
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        const baseUrl = '<?= base_url() ?>';
    </script>
</head>
<body class="min-h-screen flex items-center justify-center text-slate-300 font-sans p-4 antialiased" style="text-shadow: 0 1px 3px rgba(0,0,0,0.5);">

    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3 w-full max-w-xs sm:max-w-sm"></div>

    <form id="installerForm" enctype="multipart/form-data" 
          class="w-full max-w-lg space-y-6 rounded-2xl p-6 sm:p-8 
                 bg-slate-900/70 backdrop-blur-xl border border-slate-800 border-t-slate-700 shadow-2xl shadow-black/50">
        
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-bold text-white">🚀 Instalasi Aplikasi</h2>
            <p class="text-slate-400 text-sm">Selamat datang! Ikuti langkah-langkah berikut.</p>
        </div>

        <div class="form-step step-1 active space-y-5">
            <div>
                <label for="db_host" class="block text-sm font-medium mb-2 text-slate-400">Host Database</label>
                <input type="text" name="db_host" id="db_host" value="localhost" required class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>
            <div>
                <label for="db_name" class="block text-sm font-medium mb-2 text-slate-400">Nama Database</label>
                <input type="text" name="db_name" id="db_name" required class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>
            <div>
                <label for="db_user" class="block text-sm font-medium mb-2 text-slate-400">User Database</label>
                <input type="text" name="db_user" id="db_user" required class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>
            <div>
                <label for="db_pass" class="block text-sm font-medium mb-2 text-slate-400">Password Database <span class="text-slate-500"></span></label>
                <input type="password" name="db_pass" id="db_pass" class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="testConnection()" class="w-full rounded-lg px-4 py-2.5 font-semibold bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 hover:border-slate-600 hover:text-white transition-all duration-300 transform hover:-translate-y-0.5">Tes Koneksi</button>
                <button type="button" onclick="nextStep()" class="w-full rounded-lg px-4 py-2.5 font-bold text-white bg-gradient-to-r from-indigo-600 to-violet-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-[0_0_20px_#6d28d990]">Lanjut</button>
            </div>
        </div>

        <div class="form-step step-2 hidden space-y-5">
            <div>
                <label for="license_key" class="block text-sm font-medium mb-2 text-slate-400">Kunci Produk</label>
                <input type="text" name="license_key" id="license_key" required placeholder="prod_xxxxxxxxx" class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>
            <div>
                <label for="base_url" class="block text-sm font-medium mb-2 text-slate-400">Base URL</label>
                <input type="text" name="base_url" id="base_url" placeholder="https://domain-anda.com/" class="w-full p-3 rounded-lg bg-slate-800/50 border border-slate-700 text-slate-200 transition-all duration-300 focus:outline-none focus:border-indigo-500 focus:shadow-[0_0_0_2px_#1e293b,0_0_15px_2px_#4338ca] " />
            </div>
            <div>
                <label for="sql_file" class="block text-sm font-medium mb-2 text-slate-400">Upload File .SQL <span class="text-slate-500"></span></label>
                <input type="file" name="sql_file" id="sql_file" accept=".sql" class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border file:border-slate-700 file:text-sm file:font-semibold file:bg-slate-800 file:text-slate-300 hover:file:bg-slate-700 hover:file:border-slate-600 transition-colors cursor-pointer" />
            </div>
             <div id="upload_progress_container" class="pt-2 hidden">
                <div class="w-full bg-slate-700 rounded-full h-2">
                    <div id="upload_progress_bar" class="bg-gradient-to-r from-indigo-500 to-violet-500 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="prevStep()" class="w-full rounded-lg px-4 py-2.5 font-semibold bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 hover:border-slate-600 hover:text-white transition-all duration-300 transform hover:-translate-y-0.5">Kembali</button>
                <button type="button" onclick="submitForm()" class="w-full rounded-lg px-4 py-2.5 font-bold text-white bg-gradient-to-r from-green-600 to-teal-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-[0_0_20px_#0d948890] flex items-center justify-center">
                    <span id="install-text">Install Sekarang</span>
                    <span id="install-spinner" class="hidden spinner ml-2"></span>
                </button>
            </div>
        </div>

        <div class="form-step step-3 hidden text-center space-y-6 py-8">
            <h3 class="text-3xl font-bold text-green-400">✅ Instalasi Berhasil</h3>
            <p class="text-slate-300 max-w-sm mx-auto">Aplikasi Anda telah siap. Aktifkan lisensi untuk mulai menggunakan semua fitur.</p>
            <a href="/activate" class="inline-block mt-4 bg-gradient-to-r from-purple-600 to-fuchsia-700 px-8 py-3 rounded-lg text-white font-bold transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-[0_0_20px_#a21caf90]">🔐 Aktivasi Lisensi</a>
        </div>
    </form>

    <script>
        let currentStep = 1;
        
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: 'bg-green-600/80 border-green-500',
                error: 'bg-red-600/80 border-red-500',
                info: 'bg-sky-600/80 border-sky-500'
            };
            const toast = document.createElement('div');
            toast.className = `toast text-white p-4 rounded-lg shadow-lg border-l-4 ${colors[type]} flex items-center backdrop-blur-sm`;
            toast.innerHTML = `<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(el => el.classList.add('hidden'));
            document.querySelector(`.step-${step}`).classList.remove('hidden');
            currentStep = step;
        }

        function nextStep() {
            if (currentStep === 1) {
                if (!document.getElementById('db_host').value || !document.getElementById('db_name').value || !document.getElementById('db_user').value) {
                    showToast('Harap isi detail database terlebih dahulu.', 'error');
                    return;
                }
            }
            showStep(currentStep + 1);
        }

        function prevStep() { showStep(currentStep - 1); }

        function testConnection() {
            const host = document.getElementById('db_host').value;
            const name = document.getElementById('db_name').value;
            const user = document.getElementById('db_user').value;
            const pass = document.getElementById('db_pass').value;
            showToast('Menguji koneksi...', 'info');
            fetch(`${baseUrl}/install/testdb`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfHash },
                body: JSON.stringify({ host, name, user, pass })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(`✅ Koneksi ke <strong>${name}</strong> berhasil!`, 'success');
                } else {
                    showToast(`❌ Gagal: ${data.message}`, 'error');
                }
            })
            .catch(() => showToast(`❌ Gagal menghubungi server.`, 'error'));
        }

        function submitForm() {
            const form = document.getElementById('installerForm');
            const progressContainer = document.getElementById('upload_progress_container');
            const progressBar = document.getElementById('upload_progress_bar');
            const fileInput = document.getElementById('sql_file');
            const installBtnText = document.getElementById('install-text');
            const installBtnSpinner = document.getElementById('install-spinner');
            installBtnText.classList.add('hidden');
            installBtnSpinner.classList.remove('hidden');
            
            if (fileInput.files.length > 0) {
                 progressContainer.classList.remove('hidden');
            }

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `${baseUrl}/install/process`, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
            xhr.upload.onprogress = e => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                }
            };
            xhr.onload = function () {
                installBtnText.classList.remove('hidden');
                installBtnSpinner.classList.add('hidden');
                progressContainer.classList.add('hidden');
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast('✅ Instalasi berhasil diproses!', 'success');
                        setTimeout(() => showStep(3), 1000);
                    } else {
                        showToast('❌ Gagal: ' + (response.message || 'Terjadi kesalahan'), 'error');
                    }
                } catch(e) {
                     showToast('❌ Respon dari server tidak valid.', 'error');
                }
            };
            xhr.onerror = function () {
                installBtnText.classList.remove('hidden');
                installBtnSpinner.classList.add('hidden');
                progressContainer.classList.add('hidden');
                showToast('❌ Gagal menghubungi server.', 'error');
            };
            xhr.send(formData);
        }
    </script>
</body>
</html>
