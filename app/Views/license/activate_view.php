<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aktivasi Lisensi - CTRX</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(145deg, #0f172a, #1e293b);
    }

    .glass-card {
      backdrop-filter: blur(14px);
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 22px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
      animation: fadeInUp 0.5s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 4px rgb(34 211 238 / 0.5);
      border-color: #22d3ee;
    }

    button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen px-6 py-12 text-white">

  <div class="glass-card w-full max-w-md p-8 relative">
    <!-- SVG Animasi -->
    <div class="flex justify-center mb-6">
      <svg class="w-24 h-24 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="cyan">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <h1 class="text-3xl font-extrabold text-center mb-6 text-white/90 drop-shadow">Aktivasi Lisensi</h1>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 mb-5 rounded-lg text-sm font-medium shadow">
        <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
      <div class="bg-green-500/20 border border-green-500 text-green-200 px-4 py-3 mb-5 rounded-lg text-sm font-medium shadow">
        <?= esc(session()->getFlashdata('message')) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/activate/process" id="activateForm" class="space-y-6">
      <?= csrf_field() ?>
      <div class="flex flex-col space-y-2">
        <label for="license_key" class="text-base font-semibold">Kunci Lisensi</label>
        <input
          type="text"
          name="license_key"
          id="license_key"
          class="bg-white/10 border border-white/30 placeholder-gray-400 text-white rounded-xl px-5 py-4 text-lg focus:ring-4 focus:ring-cyan-400 transition"
          placeholder="XXXX-XXXX-XXXX-XXXX"
          required
          autocomplete="off"
        />
      </div>

      <button
        type="submit"
        id="submitBtn"
        class="w-full bg-cyan-500 hover:bg-cyan-400 text-white font-bold py-4 text-lg rounded-xl shadow-lg transition-all duration-300"
      >
        🚀 Aktifkan Sekarang
      </button>
    </form>

    <p class="text-center text-xs text-gray-400 mt-8 tracking-wide">
      &copy; <?= date('Y') ?> CTRXL.ID — Powered by Keamanan.
    </p>
  </div>

  <script>
    document.getElementById('activateForm').addEventListener('submit', function () {
      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.innerText = 'Memproses... ⏳';
    });
  </script>
</body>
</html>
