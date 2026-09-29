<?php
// ==========================================
// LOGIKA PHP (Ditaruh di bagian paling atas)
// ==========================================

// Variabel pesan notifikasi simpan data (simulasi)
$pesan_sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan_sukses = 'Pengaturan berhasil diperbarui!';
}

// Data dummy konfigurasi awal
$config = [
    'nama_sistem'   => 'SiParkir Management',
    'nama_admin'    => 'Admin',
    'email_admin'   => 'admin@siparkir.com',
    'slot_area_a'   => 20,
    'slot_area_b'   => 20,
    'slot_area_c'   => 20,
    'toleransi_menit'=> 10,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiParkir - Pengaturan</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-100 p-6 flex flex-col justify-between shrink-0">
        <div>
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">P</div>
                <div>
                    <h1 class="font-bold text-slate-800 leading-tight">SiParkir</h1>
                    <p class="text-xs text-slate-400">Parking System</p>
                </div>
            </div>

            <!-- Menu Navigation -->
            <nav class="space-y-1">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="kendaraan.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Kendaraan
                </a>
                <a href="data-parkir.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Data Parkir
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </a>
                <!-- Menu Aktif -->
                <a href="pengaturan.php" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-600 rounded-xl font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
            </nav>
        </div>

        <!-- Status Card Bottom -->
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="font-bold text-xs text-emerald-800">Sistem Aktif</span>
            </div>
            <p class="text-[11px] text-emerald-600 leading-snug">Parkir berjalan normal</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Header -->
        <header class="flex justify-between items-start mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-500 mb-1 block">PARKING MANAGEMENT</span>
                <h2 class="text-2xl font-bold text-slate-800">Pengaturan System</h2>
                <p class="text-xs text-slate-400 mt-1">Konfigurasi profile, kapasitas area, dan opsi keamanan.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center font-semibold text-sm">A</div>
                <div class="text-right">
                    <div class="text-sm font-bold text-slate-700">Admin</div>
                    <div class="text-xs text-slate-400">Administrator</div>
                </div>
            </div>
        </header>

        <?php if (!empty($pesan_sukses)): ?>
        <!-- Alert Banner -->
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-bold">✓</span>
                <span><?php echo htmlspecialchars($pesan_sukses); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Wrapper -->
        <form action="" method="POST" class="space-y-6 max-w-4xl">
            
            <!-- Section 1: Profil Akun -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-800 text-base mb-1">Profil Pengguna</h3>
                <p class="text-xs text-slate-400 mb-6">Informasi akun utama administrator sistem.</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_admin" value="<?php echo htmlspecialchars($config['nama_admin']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Email Admin</label>
                        <input type="email" name="email_admin" value="<?php echo htmlspecialchars($config['email_admin']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Kapasitas Area Parkir -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-800 text-base mb-1">Kapasitas Slot Area</h3>
                <p class="text-xs text-slate-400 mb-6">Batas maksimum kendaraan untuk masing-masing zona parkir.</p>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Area A (Maksimal Slot)</label>
                        <input type="number" name="slot_area_a" value="<?php echo htmlspecialchars($config['slot_area_a']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Area B (Maksimal Slot)</label>
                        <input type="number" name="slot_area_b" value="<?php echo htmlspecialchars($config['slot_area_b']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Area C (Maksimal Slot)</label>
                        <input type="number" name="slot_area_c" value="<?php echo htmlspecialchars($config['slot_area_c']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors" required>
                    </div>
                </div>
            </div>

            <!-- Section 3: Keamanan & Kunci Kata Sandi -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="font-bold text-slate-800 text-base mb-1">Ubah Kata Sandi</h3>
                <p class="text-xs text-slate-400 mb-6">Biarkan kosong jika tidak ingin mengganti kata sandi.</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Kata Sandi Baru</label>
                        <input type="password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="reset" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl transition-colors shadow-sm shadow-blue-200">Simpan Perubahan</button>
            </div>

        </form>

        <!-- Footer -->
        <footer class="mt-12 flex justify-between items-center text-xs text-slate-400 border-t border-slate-100 pt-4">
            <p>© 2026 SiParkir</p>
            <p>Sistem Informasi Parkir</p>
        </footer>
    </main>

</body>
</html>