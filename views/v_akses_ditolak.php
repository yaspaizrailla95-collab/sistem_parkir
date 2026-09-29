<?php
require_once __DIR__ . '/_helpers.php';
// $pesanRole dikirim dari controller/_auth.php (fungsi _tolak_akses),
// berisi nama role yang berhak mengakses halaman ini (Admin/Petugas/Owner).
$basePath = base_url();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - SiParkir</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f5f7fb;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .box{max-width:500px;background:#fff;border:1px solid #e7edf5;border-radius:16px;padding:30px;box-shadow:0 12px 35px rgba(0,0,0,.06)}
        h1{margin:0 0 8px;color:#1f3550;font-size:24px}
        p{color:#697b8f;line-height:1.6}
        .back{display:inline-block;margin-top:12px;padding:10px 15px;border-radius:9px;background:#3978c7;color:#fff;text-decoration:none}
    </style>
</head>
<body>
    <div class="box">
        <h1>Akses ditolak</h1>
        <p>Halaman ini hanya dapat diakses oleh akun <strong><?=e($pesanRole)?></strong>.</p>
        <a class="back" href="<?=e($basePath . '/controller/c_login.php')?>">Kembali ke Login</a>
    </div>
</body>
</html>
