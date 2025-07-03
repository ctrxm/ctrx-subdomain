<aside id="admin-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <div class="flex items-center justify-between mb-5">
         <a href="<?= site_url('admin') ?>" class="flex items-center ps-2.5">
            <span class="self-center text-lg font-semibold whitespace-nowrap dark:text-white">Admin Panel</span>
         </a>
         <button type="button" data-drawer-hide="admin-sidebar" aria-controls="admin-sidebar" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 lg:hidden dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">...</svg>
            <span class="sr-only">Close menu</span>
         </button>
      </div>
      <ul class="space-y-2">
          <li><a href="<?= site_url('admin') ?>" class="flex ...">Dashboard</a></li>
          <li><a href="<?= site_url('admin/domains') ?>" class="flex ...">Kelola Domain</a></li>
          <li><a href="<?= site_url('admin/users') ?>" class="flex ...">Kelola Pengguna</a></li>
          <li><a href="<?= site_url('admin/subdomains') ?>" class="flex ...">Kelola Subdomain</a></li>
          <li><a href="<?= site_url('admin/settings') ?>" class="flex ...">Pengaturan</a></li>
      </ul>
   </div>
</aside>
