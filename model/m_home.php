<?php
// Data dummy agar tampilan dinamis
$total_kendaraan = 24;
$kendaraan_masuk = 15;
$kendaraan_keluar = 9;
$slot_tersedia = 36;

$status_area = [
    ['nama' => 'Area A', 'terisi' => 12, 'total' => 20, 'warna' => 'bg-blue-500', 'bg_icon' => 'bg-blue-100 text-blue-600'],
    ['nama' => 'Area B', 'terisi' => 8,  'total' => 20, 'warna' => 'bg-emerald-500', 'bg_icon' => 'bg-emerald-100 text-emerald-600'],
    ['nama' => 'Area C', 'terisi' => 4,  'total' => 20, 'warna' => 'bg-amber-500', 'bg_icon' => 'bg-amber-100 text-amber-600'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>                              
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiParkir - Dashboard</title>
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
                <a href="v_dashboard" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-600 rounded-xl font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Kendaraan
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Data Parkir
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 rounded-xl font-medium text-sm transition-colors">
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
                <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
                <p class="text-xs text-slate-400 mt-1">Selamat datang di sistem pengelolaan parkir.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center font-semibold text-sm">A</div>
                <div class="text-right">
                    <div class="text-sm font-bold text-slate-700">Admin</div>
                    <div class="text-xs text-slate-400">Administrator</div>
                </div>
            </div>
        </header>

        <!-- Top Stats Cards -->
        <div class="grid grid-cols-4 gap-5 mb-8">
            <!-- Card 1 -->
            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">Total Kendaraan</span>
                    <div class="text-3xl font-bold text-blue-600 my-2"><?= $total_kendaraan; ?></div>
                    <span class="text-[11px] text-slate-400">Kendaraan terdaftar</span>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-500 rounded-xl flex items-center justify-center text-lg">🚙</div>
            </div>

            <!-- Card 2 -->
            <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-5 flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">Kendaraan Masuk</span>
                    <div class="text-3xl font-bold text-emerald-600 my-2"><?= $kendaraan_masuk; ?></div>
                    <span class="text-[11px] text-slate-400">Hari ini</span>
                </div>
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-lg font-bold">↑</div>
            </div>

            <!-- Card 3 -->
            <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-5 flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">Kendaraan Keluar</span>
                    <div class="text-3xl font-bold text-rose-500 my-2"><?= $kendaraan_keluar; ?></div>
                    <span class="text-[11px] text-slate-400">Hari ini</span>
                </div>
                <div class="w-10 h-10 bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center text-lg font-bold">↓</div>
            </div>

            <!-- Card 4 -->
            <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-5 flex justify-between items-start">
                <div>
                    <span class="text-xs font-medium text-slate-400">Slot Tersedia</span>
                    <div class="text-3xl font-bold text-amber-500 my-2"><?= $slot_tersedia; ?></div>
                    <span class="text-[11px] text-slate-400">Slot parkir kosong</span>
                </div>
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-lg font-bold">P</div>
            </div>
        </div>

        <!-- Section Middle -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Left Side: Status Parkir -->
            <div class="col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-bold text-slate-800">Status Parkir</h3>
                        <p class="text-xs text-slate-400">Kondisi area parkir saat ini</p>
                    </div>
                    <span class="text-[11px] bg-emerald-100 text-emerald-600 font-semibold px-2.5 py-1 rounded-full">Aktif</span>
                </div>

                <div class="space-y-4">
                    <?php foreach ($status_area as $area): ?>
                    <?php $percentage = ($area['terisi'] / $area['total']) * 100; ?>
                    <div class="bg-slate-50/80 p-4 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 <?= $area['bg_icon']; ?> font-bold rounded-xl flex items-center justify-center">
                                <?= substr($area['nama'], -1); ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-700"><?= $area['nama']; ?></h4>
                                <p class="text-xs text-slate-400"><?= $area['terisi']; ?> / <?= $area['total']; ?> slot terisi</p>
                            </div>
                        </div>
                        <div class="w-48 bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="<?= $area['warna']; ?> h-full rounded-full" style="width: <?= $percentage; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Side: Akses Cepat -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="mb-6">
                    <h3 class="font-bold text-slate-800">Akses Cepat</h3>
                    <p class="text-xs text-slate-400">Menu yang sering digunakan</p>
                </div>

                <div class="space-y-3">
                    <!-- Shortcut 1 -->
                    <a href="#" class="flex items-center gap-3 p-3.5 bg-blue-50/50 hover:bg-blue-50 rounded-xl border border-blue-50 transition-all group">
                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">🏎️</div>
                        <div>
                            <h4 class="font-bold text-xs text-blue-600 group-hover:underline">Tambah Kendaraan</h4>
                            <p class="text-[11px] text-slate-400">Catat kendaraan masuk</p>
                        </div>
                    </a>

                    <!-- Shortcut 2 -->
                    <a href="#" class="flex items-center gap-3 p-3.5 bg-emerald-50/50 hover:bg-emerald-50 rounded-xl border border-emerald-50 transition-all group">
                        <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm font-bold">✓</div>
                        <div>
                            <h4 class="font-bold text-xs text-emerald-600 group-hover:underline">Kendaraan Keluar</h4>
                            <p class="text-[11px] text-slate-400">Catat kendaraan keluar</p>
                        </div>
                    </a>

                    <!-- Shortcut 3 -->
                    <a href="#" class="flex items-center gap-3 p-3.5 bg-amber-50/50 hover:bg-amber-50 rounded-xl border border-amber-50 transition-all group">
                        <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-sm font-bold">📋</div>
                        <div>
                            <h4 class="font-bold text-xs text-amber-600 group-hover:underline">Lihat Data Parkir</h4>
                            <p class="text-[11px] text-slate-400">Lihat kendaraan yang parkir</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-12 flex justify-between items-center text-xs text-slate-400 border-t border-slate-100 pt-4">
            <p>© 2026 SiParkir</p>
            <p>Sistem Informasi Parkir</p>
        </footer>
    </main>

</body>
</html>