<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Lisensi - CTRX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen text-white">

    <div class="glass p-8 max-w-md w-full shadow-lg">
        <h1 class="text-2xl font-bold mb-4 text-center">🔒 Aktivasi Lisensi</h1>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-300 p-3 rounded mb-4">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
            <div class="bg-green-500/10 border border-green-500 text-green-300 p-3 rounded mb-4">
                <?= esc(session()->getFlashdata('message')) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/activate/process" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm mb-1">Kunci Lisensi</label>
                <input
                    type="text"
                    name="license_key"
                    class="w-full px-4 py-2 rounded bg-white/5 border border-white/10 focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white placeholder-gray-400"
                    placeholder="contoh: XXXX-XXXX-XXXX-XXXX"
                    required>
            </div>

            <button type="submit" class="w-full py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded font-semibold transition">
                🚀 Aktifkan Sekarang
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            Copyright © <?= date('Y') ?> CTRXL.ID. All rights reserved.
        </p>
    </div>

</body>
</html>
