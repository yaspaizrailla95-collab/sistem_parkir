<?php
require_once __DIR__ . '/_helpers.php';
$basePath = base_url();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiParkir</title>
    <link rel="stylesheet" href="<?=e($basePath . '/asset/auth.css')?>">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-brand">
                <img class="brand-icon" src="<?=e($basePath . '/asset/img/logo-icon.png')?>" alt="Logo SiParkir">
                <div>
                    <h1>SiParkir</h1>
                    <p>Parking Management System</p>
                </div>
            </div>

            <div class="auth-heading">
                <span class="auth-label">SISTEM PARKIR</span>
                <h2>Masuk ke Sistem</h2>
                <p>Login dengan akun Admin atau Petugas. Tampilan akan menyesuaikan secara otomatis.</p>
            </div>

            <div class="role-pill">
                <span class="role-dot"></span>
                Admin &amp; Petugas login di halaman yang sama
            </div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-error"><?=e($error)?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="auth-field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="<?=e($username)?>" placeholder="Masukkan username admin" autocomplete="username" required autofocus>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Tampilkan password">Lihat</button>
                    </div>
                </div>

                <button type="submit" class="auth-button">Masuk</button>
            </form>

            <div class="auth-footer">
                <span>Belum memiliki akun?</span>
                <a href="<?=e($basePath . '/controller/c_register.php')?>">Register</a>
            </div>

            <p class="auth-note">Register publik dibuat sebagai <strong>Petugas</strong>. Hak Admin tetap dikelola dari menu User.</p>
        </section>

        <p class="auth-copy">© <?=date('Y')?> SiParkir · Sistem Informasi Parkir</p>
    </main>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const button = document.querySelector('.toggle-password');
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    button.textContent = visible ? 'Lihat' : 'Sembunyi';
}
</script>
</body>
</html>
