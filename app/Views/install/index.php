<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Installer Multi-Step - CTRX</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      background: linear-gradient(135deg, #1f1c2c, #928DAB);
      font-family: 'Segoe UI', sans-serif;
      margin: 0; padding: 0;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    .form-step {
      display: none;
    }
    .form-step.active {
      display: block;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: none;
      border-radius: 10px;
      font-size: 14px;
    }
    input {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    input::placeholder {
      color: #ccc;
    }
    button {
      background: #00c6ff;
      color: #fff;
      cursor: pointer;
    }
    .btn-group {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }
    .db-status {
      font-size: 13px;
      margin-top: 8px;
      color: #ffdd57;
    }
  </style>

  <script>
    // CSRF token dari server CodeIgniter
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
  </script>
</head>
<body>

  <form class="container" id="installerForm">
    <h2>🚀 Setup CTRX</h2>

    <!-- STEP 1 -->
    <div class="form-step step-1 active">
      <label>Host Database</label>
      <input type="text" name="db_host" id="db_host" value="localhost" required />

      <label>Nama Database</label>
      <input type="text" name="db_name" id="db_name" required />

      <label>User Database</label>
      <input type="text" name="db_user" id="db_user" required />

      <label>Password Database</label>
      <input type="password" name="db_pass" id="db_pass" />

      <div class="btn-group">
        <button type="button" onclick="testConnection()">🔌 Tes Koneksi</button>
        <button type="button" onclick="nextStep()">Lanjut ➡️</button>
      </div>
      <div id="db_status" class="db-status"></div>
    </div>

    <!-- STEP 2 -->
    <div class="form-step step-2">
      <label>Kunci Lisensi Produk</label>
      <input type="text" name="license_key" id="license_key" required placeholder="XXXX-XXXX-XXXX" />

      <label>Base URL (opsional)</label>
      <input type="text" name="base_url" id="base_url" placeholder="https://ctrxl.id/" />

      <div class="btn-group">
        <button type="button" onclick="prevStep()">⬅️ Kembali</button>
        <button type="button" onclick="submitForm()">Install & Lanjut ➡️</button>
      </div>
      <div id="install_status" class="db-status"></div>
    </div>

    <!-- STEP 3 -->
    <div class="form-step step-3">
      <h3>✅ Instalasi Selesai!</h3>
      <p>Semua konfigurasi telah disimpan ke sistem.</p>
      <p><strong>Langkah terakhir:</strong> Silakan aktifkan lisensi asli Anda agar bisa mulai menggunakan aplikasi ini.</p>
      <p>Klik tombol di bawah untuk melanjutkan ke proses aktivasi lisensi:</p>

      <div class="btn-group">
        <a href="/activate" style="flex: 1;">
          <button type="button">🔐 Aktivasi Sekarang</button>
        </a>
      </div>
    </div>
  </form>

  <script>
    let currentStep = 1;

    function nextStep() {
      if (currentStep === 1) {
        // Validasi minimal field di step 1 sebelum next
        const dbHost = document.getElementById('db_host').value.trim();
        const dbName = document.getElementById('db_name').value.trim();
        const dbUser = document.getElementById('db_user').value.trim();
        if (!dbHost || !dbName || !dbUser) {
          alert('Mohon isi semua data koneksi database.');
          return;
        }
      }
      document.querySelector(`.step-${currentStep}`).classList.remove('active');
      currentStep++;
      document.querySelector(`.step-${currentStep}`).classList.add('active');
    }

    function prevStep() {
      document.querySelector(`.step-${currentStep}`).classList.remove('active');
      currentStep--;
      document.querySelector(`.step-${currentStep}`).classList.add('active');
    }

    function testConnection() {
      const host = document.getElementById('db_host').value;
      const name = document.getElementById('db_name').value;
      const user = document.getElementById('db_user').value;
      const pass = document.getElementById('db_pass').value;
      const status = document.getElementById('db_status');
      status.innerText = '🔄 Menguji koneksi...';

      fetch('<?= base_url('/install/testdb') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfHash
        },
        body: JSON.stringify({host, name, user, pass})
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          status.innerText = '✅ Koneksi Berhasil!';
        } else {
          status.innerText = '❌ Gagal: ' + data.message;
        }
      })
      .catch(() => {
        status.innerText = '❌ Gagal menghubungi server.';
      });
    }

    function submitForm() {
      const form = document.getElementById('installerForm');
      const status = document.getElementById('install_status');

      // Ambil semua data form
      const formData = new FormData(form);

      status.innerText = '🔄 Menyimpan konfigurasi...';

      fetch('<?= base_url('/install/process') ?>', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfHash
        },
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          status.innerText = '✅ Konfigurasi tersimpan!';
          // Pindah ke step 3
          document.querySelector(`.step-${currentStep}`).classList.remove('active');
          currentStep++;
          document.querySelector(`.step-${currentStep}`).classList.add('active');
        } else {
          status.innerText = '❌ Gagal: ' + (data.message || 'Terjadi kesalahan');
        }
      })
      .catch(() => {
        status.innerText = '❌ Gagal menghubungi server.';
      });
    }
  </script>

</body>
</html>
