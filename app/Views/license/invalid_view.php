<?= $this->extend('layout') ?>
<?= $this->section('title') ?>Lisensi Tidak Valid<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
  @keyframes fadeSlideUp {
    from {
      opacity: 0;
      transform: translateY(40px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .fade-in-up {
    animation: fadeSlideUp 0.6s ease-out both;
  }
</style>

<div class="flex items-center justify-center min-h-screen px-6 py-10 bg-gradient-to-br from-gray-900 via-slate-800 to-gray-900">
  <div class="fade-in-up bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl max-w-md w-full p-8 text-center text-white">

    <!-- SVG Gembok Besar + Silang -->
    <div class="flex justify-center mb-6">
      <svg class="w-24 h-24 text-red-400 drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M12 17v-1m0-3.5a1 1 0 110-2 1 1 0 010 2zm6 0V9a6 6 0 00-12 0v3.5M4 17h16a1 1 0 001-1v-4a1 1 0 00-1-1H4a1 1 0 00-1 1v4a1 1 0 001 1z" />
        <line x1="6" y1="6" x2="18" y2="18" stroke="red" stroke-width="1.5" />
      </svg>
    </div>

    <h1 class="text-2xl font-bold mb-3 text-white drop-shadow">Lisensi Tidak Valid</h1>

    <p class="text-base text-gray-300 mb-3">
      <?= esc($reason) ?>
    </p>

    <p class="text-sm text-gray-400 mb-6">
      Kunci lisensi Anda tidak valid atau sudah digunakan. Silakan coba aktivasi ulang atau hubungi admin.
    </p>

    <div class="flex flex-col gap-4">
      <a href="<?= site_url('activate') ?>" class="block w-full py-3 px-6 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white font-semibold shadow-lg transition-all duration-200">
        🚀 Aktivasi Ulang
      </a>

      <a href="https://t.me/useraib" target="_blank" class="block w-full py-3 px-6 rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold shadow-md transition-all duration-200">
        💬 Hubungi Admin
      </a>
    </div>

    <p class="text-center text-xs text-gray-500 mt-6">
      &copy; <?= date('Y') ?> CTRXL.ID • Keamanan Lisensi Terjamin
    </p>
  </div>
</div>
<?= $this->endSection() ?>
