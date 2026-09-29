<?php
require_once __DIR__ . '/_helpers.php';
$basePath = base_url();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SiParkir</title>
    <link rel="stylesheet" href="<?=e($basePath . '/asset/auth.css')?>">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card register-card">
            <div class="auth-brand">
                <img class="brand-icon" src="<?=e($basePath . '/asset/img/logo-icon.png')?>" alt="Logo SiParkir">
                <div>
                    <h1>SiParkir</h1>
                    <p>Parking Management System</p>
                </div>
            </div>

            <div class="auth-heading">
                <span class="auth-label">REGISTRASI AKUN</span>
                <h2>Buat Akun Baru</h2>
                <p>Daftarkan akun untuk masuk ke sistem. Akun hasil register dibuat sebagai Petugas.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-error"><?=e($error)?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="auth-alert auth-alert-success"><?=e($success)?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="auth-field">
                    <label for="nama">Nama Lengkap</label>
                    <input id="nama" type="text" name="nama" value="<?=e($nama)?>" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                </div>

                <div class="auth-field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="<?=e($username)?>" placeholder="Contoh: andi_parkir" autocomplete="username" required>
                    <small>4-30 karakter: huruf, angka, titik, garis bawah, atau strip.</small>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password" required minlength="6">
                </div>

                <div class="auth-field">
                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <input id="konfirmasi_password" type="password" name="konfirmasi_password" placeholder="Ulangi password" autocomplete="new-password" required minlength="6">
                </div>

                <button type="submit" class="auth-button">Register</button>
            </form>

            <div class="auth-footer">
                <span>Sudah punya akun Admin?</span>
                <a href="<?=e($basePath . '/controller/c_login.php')?>">Kembali ke Login</a>
            </div>
        </section>

        <p class="auth-copy">© <?=date('Y')?> SiParkir · Sistem Informasi Parkir</p>
    </main>
</body>
</html>
