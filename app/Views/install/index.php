<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Installer CTRX</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .glass {
      backdrop-filter: blur(20px);
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .spinner {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #fff;
      border-top-color: transparent;
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      margin-right: 8px;
    }
  </style>
  <script>
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
  </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-indigo-900 text-white font-sans">

  <form id="installerForm" enctype="multipart/form-data" class="glass p-6 sm:p-10 rounded-2xl shadow-xl w-full max-w-lg space-y-6">
    <h2 class="text-2xl font-bold text-center">🚀 Setup Aplikasi CTRX</h2>

    <!-- STEP 1 -->
    <div class="form-step step-1 active space-y-4">
      <div>
        <label>Host Database</label>
        <input type="text" name="db_host" id="db_host" value="localhost" required class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>
      <div>
        <label>Nama Database</label>
        <input type="text" name="db_name" id="db_name" required class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>
      <div>
        <label>User Database</label>
        <input type="text" name="db_user" id="db_user" required class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>
      <div>
        <label>Password Database</label>
        <input type="password" name="db_pass" id="db_pass" class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>

      <div class="flex gap-4 pt-4">
        <button type="button" onclick="testConnection()" class="bg-yellow-400 text-black rounded-lg px-4 py-2 font-semibold hover:bg-yellow-300 w-full">🔌 Tes Koneksi</button>
        <button type="button" onclick="nextStep()" class="bg-blue-600 rounded-lg px-4 py-2 font-semibold hover:bg-blue-500 w-full">➡️ Lanjut</button>
      </div>

      <div id="db_status" class="mt-4 hidden text-sm rounded-lg p-3"></div>
    </div>

    <!-- STEP 2 -->
    <div class="form-step step-2 hidden space-y-4">
      <div>
        <label>Kunci Lisensi Produk</label>
        <input type="text" name="license_key" id="license_key" required placeholder="XXXX-XXXX-XXXX" class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>
      <div>
        <label>Base URL (opsional)</label>
        <input type="text" name="base_url" id="base_url" placeholder="https://ctrxl.id/" class="w-full p-3 rounded-lg bg-white/10 text-white focus:outline-none focus:ring focus:ring-blue-500" />
      </div>

      <div>
        <label>Upload SQL File</label>
        <label>Upload SQL File (opsional, max 3MB)</label>
<input type="file" name="sql_file" id="sql_file" accept=".sql"
  class="w-full p-3 rounded-lg bg-white/10 text-white file:bg-blue-600 file:text-white file:border-none file:rounded file:px-4 file:py-2 hover:file:bg-blue-700"
/>
<p class="text-sm text-gray-300 mt-1">Pastikan file .sql valid, max 3MB.</p>

        </div>
        
        <div id="sql_preview" class="bg-black/20 text-sm text-green-200 font-mono rounded-lg mt-2 p-3 hidden max-h-40 overflow-y-auto"></div>

<div id="upload_progress_container" class="mt-4 hidden">
  <div class="w-full bg-gray-700 rounded-full h-4">
    <div id="upload_progress_bar" class="bg-blue-500 h-4 rounded-full transition-all duration-300 ease-in-out" style="width: 0%;"></div>
  </div>
  <div id="upload_progress_text" class="text-sm mt-1 text-gray-300">0%</div>
</div>

      <div class="flex gap-4 pt-4">
        <button type="button" onclick="prevStep()" class="bg-gray-500 rounded-lg px-4 py-2 font-semibold hover:bg-gray-400 w-full">⬅️ Kembali</button>
        <button type="button" onclick="submitForm()" class="bg-green-600 rounded-lg px-4 py-2 font-semibold hover:bg-green-500 w-full">Install ➡️</button>
      </div>

      <div id="install_status" class="mt-4 hidden text-sm rounded-lg p-3"></div>
    </div>

    <!-- STEP 3 -->
    <div class="form-step step-3 hidden space-y-3 text-center">
      <h3 class="text-xl font-bold text-green-400">✅ Instalasi Selesai!</h3>
      <p>Semua konfigurasi telah disimpan ke sistem.</p>
      <p><strong>Langkah terakhir:</strong> Aktifkan lisensi untuk memulai menggunakan aplikasi.</p>
      <a href="/activate" class="inline-block mt-4 bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded-lg text-white font-semibold">🔐 Aktivasi Sekarang</a>
    </div>
  </form>

  <script>
    let currentStep = 1;

    function nextStep() {
      if (currentStep === 1) {
        const dbHost = document.getElementById('db_host').value.trim();
        const dbName = document.getElementById('db_name').value.trim();
        const dbUser = document.getElementById('db_user').value.trim();
        if (!dbHost || !dbName || !dbUser) {
          alert('Mohon isi semua data koneksi database.');
          return;
        }
      }
      document.querySelector(`.step-${currentStep}`).classList.add('hidden');
      currentStep++;
      document.querySelector(`.step-${currentStep}`).classList.remove('hidden');
    }

    function prevStep() {
      document.querySelector(`.step-${currentStep}`).classList.add('hidden');
      currentStep--;
      document.querySelector(`.step-${currentStep}`).classList.remove('hidden');
    }

    function testConnection() {
      const host = document.getElementById('db_host').value;
      const name = document.getElementById('db_name').value;
      const user = document.getElementById('db_user').value;
      const pass = document.getElementById('db_pass').value;
      const statusBox = document.getElementById('db_status');

      statusBox.className = "mt-4 text-sm rounded-lg p-3 bg-yellow-500 text-black flex items-center";
      statusBox.style.display = "block";
      statusBox.innerHTML = `<span class="spinner"></span> Menguji koneksi ke <strong class="ml-1">${host}</strong>...`;

      fetch('<?= base_url('/install/testdb') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfHash
        },
        body: JSON.stringify({ host, name, user, pass })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          statusBox.className = "mt-4 text-sm rounded-lg p-3 bg-green-500 text-black";
          statusBox.innerText = `✅ Koneksi berhasil ke database: ${name}`;
        } else {
          statusBox.className = "mt-4 text-sm rounded-lg p-3 bg-red-500 text-white";
          statusBox.innerText = `❌ Gagal: ${data.message}`;
        }
      })
      .catch(() => {
        statusBox.className = "mt-4 text-sm rounded-lg p-3 bg-red-600 text-white";
        statusBox.innerText = `❌ Gagal menghubungi server.`;
      });
    }

     function validateSQLFile() {
   document.getElementById('sql_file').addEventListener('change', function () {
  const file = this.files[0];
  const preview = document.getElementById('sql_preview');
  preview.innerText = '';
  preview.classList.add('hidden');

  if (file && file.name.endsWith('.sql')) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const content = e.target.result;
      const lines = content.split('\n').slice(0, 20).join('\n');
      preview.innerText = lines;
      preview.classList.remove('hidden');
    };
    reader.readAsText(file);
  }
});

  const fileInput = document.getElementById('sql_file');
  if (!fileInput || !fileInput.files.length) return true; // Tidak ada file = lanjut

  const file = fileInput.files[0];

  if (file.size > 3 * 1024 * 1024) {
    alert("❌ File SQL terlalu besar. Maksimal 3MB.");
    return false;
  }

  if (!file.name.endsWith('.sql')) {
    alert("❌ File bukan .sql!");
    return false;
  }

  return true;
}

    function submitForm() {
  if (!validateSQLFile()) return;

  const form = document.getElementById('installerForm');
  const status = document.getElementById('install_status');

  const progressContainer = document.getElementById('upload_progress_container');
  const progressBar = document.getElementById('upload_progress_bar');
  const progressText = document.getElementById('upload_progress_text');

  progressBar.style.width = '0%';
  progressText.innerText = '0%';
  progressContainer.classList.remove('hidden');

  status.className = "mt-4 text-sm rounded-lg p-3 bg-yellow-400 text-black flex items-center";
  status.style.display = "block";
  status.innerHTML = `<span class="spinner"></span> Mengirim data dan menyimpan konfigurasi...`;

  const formData = new FormData(form);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '<?= base_url('/install/process') ?>', true);
  xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);

  xhr.upload.onprogress = function (e) {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = percent + '%';
      progressText.innerText = percent + '%';
    }
  };

  xhr.onload = function () {
    const response = JSON.parse(xhr.responseText);
    if (response.success) {
      status.className = "mt-4 text-sm rounded-lg p-3 bg-green-500 text-black";
      status.innerText = '✅ Konfigurasi tersimpan!';
      document.querySelector(`.step-${currentStep}`).classList.add('hidden');
      currentStep++;
      document.querySelector(`.step-${currentStep}`).classList.remove('hidden');
    } else {
      status.className = "mt-4 text-sm rounded-lg p-3 bg-red-500 text-white";
      status.innerText = '❌ Gagal: ' + (response.message || 'Terjadi kesalahan');
    }
    progressContainer.classList.add('hidden');
  };

  xhr.onerror = function () {
    status.className = "mt-4 text-sm rounded-lg p-3 bg-red-600 text-white";
    status.innerText = '❌ Gagal menghubungi server.';
    progressContainer.classList.add('hidden');
  };

  xhr.send(formData);
}
  </script>

</body>
</html>
