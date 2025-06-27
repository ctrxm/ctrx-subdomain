<?php
// logout.php (letakkan di root web Anda, misal: /www/wwwroot/ctrxl.id/logout.php)

session_start(); // Wajib di awal

// Hancurkan semua data session
session_destroy();

// Redirect ke halaman utama atau halaman login
header('Location: index.php'); // Atau login.php
exit();