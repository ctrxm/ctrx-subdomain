<!DOCTYPE html>
<html>
<head>
    <title>Aktivasi Lisensi</title>
</head>
<body>
    <h1>Masukkan Kunci Lisensi</h1>
    <?php if (session()->getFlashdata('error')): ?>
        <p style="color:red"><?= esc(session()->getFlashdata('error')) ?></p>
    <?php endif; ?>

    <form method="post" action="/activate/process">
        <input type="text" name="license_key" placeholder="Kunci Lisensi" required>
        <button type="submit">Aktifkan</button>
    </form>
</body>
</html>
