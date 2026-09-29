<?php
require_once __DIR__ . '/_helpers.php';
require_once __DIR__ . '/../controller/_auth.php';
require_admin();
// Jaga-jaga: kalau file ini dibuka langsung lewat browser
// (bukan lewat controller/c_dashboard.php), $data belum ada.
// Maka ambil sendiri datanya dari model di sini.
if (!isset($data)) {
    require_once __DIR__ . '/../model/m_dashboard.php';
    $dashboardModel = new M_Dashboard();
    $data = $dashboardModel->getDataDashboard();
}
// Jaga-jaga juga untuk widget aktivitas terbaru kalau view ini diakses langsung.
if (!isset($log_terbaru)) {
    require_once __DIR__ . '/../model/m_log.php';
    $log_terbaru = M_Log::recent(8);
}

$current_page_override = 'v_dashboard.php';
$basePath = base_url();
$cssPath = $basePath . '/asset/dashboard_admin.css';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - SiParkir</title>

    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <style>
        .activity-panel { margin-top: 20px; }
        .activity-list { display:flex; flex-direction:column; }
        .activity-row { display:grid; grid-template-columns:100px 160px 1fr auto; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid #eef2f7; }
        @media (max-width:900px) { .activity-row { grid-template-columns:1fr; gap:4px; padding:14px 0; } .activity-row .role-tag { justify-self:start; } }
        .activity-row:last-child { border-bottom:0; }
        .activity-time { font-size:11px; color:#8a9bad; }
        .activity-who strong { font-size:12px; color:#344c66; }
        .activity-who small { display:block; font-size:10px; color:#9ba8b6; margin-top:2px; }
        .role-tag { display:inline-block; padding:4px 9px; border-radius:8px; font-size:10px; font-weight:bold; text-transform:uppercase; }
        .role-tag.admin { background:#edf5ff; color:#3978c7; }
        .role-tag.petugas { background:#eefaf3; color:#58a875; }
        .role-tag.owner { background:#fff9e8; color:#bd9336; }
    </style>
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <?php include __DIR__ . '/_sidebar.php'; ?>




        <!-- MAIN CONTENT -->
        <main class="main">

            <!-- HEADER -->
            <header class="header">

                <div>
                    <p class="small-title">PARKING MANAGEMENT</p>
                    <h1>Dashboard <span class="badge" style="vertical-align:middle;margin-left:8px;background:#e8f2ff;color:#3978c7;">ADMIN</span></h1>
                    <p class="subtitle">
                        Selamat datang di sistem pengelolaan parkir.
                    </p>
                </div>

                <div class="profile">

                    <div class="profile-icon">
                        A
                    </div>

                    <div>
                        <strong><?= e($_SESSION['nama_lengkap'] ?? 'Admin') ?></strong>
                        <small><?= e(ucfirst($_SESSION['role'] ?? 'admin')) ?></small>
                    </div>

                </div>

            </header>


            <!-- STATISTICS -->
            <section class="stats">

                <!-- TOTAL -->
                <div class="card blue">

                    <div class="card-content">
                        <span>Total Kendaraan</span>

                        <h2>
                            <?php echo $data['total_kendaraan']; ?>
                        </h2>

                        <small>Kendaraan terdaftar</small>
                    </div>

                    <div class="card-icon">
                        🚙
                    </div>

                </div>


                <!-- MASUK -->
                <div class="card green">

                    <div class="card-content">
                        <span>Kendaraan Masuk</span>

                        <h2>
                            <?php echo $data['kendaraan_masuk']; ?>
                        </h2>

                        <small>Hari ini</small>
                    </div>

                    <div class="card-icon">
                        ↑
                    </div>

                </div>


                <!-- KELUAR -->
                <div class="card red">

                    <div class="card-content">
                        <span>Kendaraan Keluar</span>

                        <h2>
                            <?php echo $data['kendaraan_keluar']; ?>
                        </h2>

                        <small>Hari ini</small>
                    </div>

                    <div class="card-icon">
                        ↓
                    </div>

                </div>


                <!-- SLOT -->
                <div class="card yellow">

                    <div class="card-content">
                        <span>Slot Tersedia</span>

                        <h2>
                            <?php echo $data['slot_tersedia']; ?>
                        </h2>

                        <small>Slot parkir kosong</small>
                    </div>

                    <div class="card-icon">
                        P
                    </div>

                </div>

            </section>


            <!-- CONTENT -->
            <section class="content-grid">

                <!-- PARKING STATUS -->
                <div class="panel">

                    <div class="panel-header">

                        <div>
                            <h3>Status Parkir</h3>
                            <p>Kondisi area parkir saat ini</p>
                        </div>

                        <span class="badge">
                            Aktif
                        </span>

                    </div>


                    <div class="parking-area">

                        <?php if (empty($data['status_area'])): ?>
                        <p class="subtitle">Belum ada data area parkir.</p>
                        <?php else: ?>
                        <?php foreach ($data['status_area'] as $area): ?>
                        <div class="parking-card">
                            <div class="parking-number <?php echo htmlspecialchars($area['bg_icon']); ?>">
                                <?php echo htmlspecialchars(strtoupper(substr($area['nama'], 0, 1))); ?>
                            </div>

                            <div>
                                <strong><?php echo htmlspecialchars($area['nama']); ?></strong>
                                <span><?php echo $area['terisi']; ?> / <?php echo $area['total']; ?> slot terisi</span>
                            </div>

                            <div class="progress">
                                <div class="progress-<?php echo htmlspecialchars($area['warna']); ?>" style="width: <?php echo $area['persen']; ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                    </div>

                </div>


                <!-- QUICK MENU -->
                <div class="panel quick-panel">

                    <div class="panel-header">

                        <div>
                            <h3>Akses Cepat</h3>
                            <p>Menu yang sering digunakan</p>
                        </div>

                    </div>


                    <div class="quick-menu">

                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_kendaraan.php?aksi=tambah'); ?>" class="quick blue-quick">
                            <span>🚗</span>
                            <div>
                                <strong>Tambah Kendaraan</strong>
                                <small>Catat kendaraan masuk</small>
                            </div>
                        </a>


                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_dataparkir.php?aksi=tampil'); ?>" class="quick green-quick">
                            <span>✓</span>
                            <div>
                                <strong>Kendaraan Keluar</strong>
                                <small>Catat kendaraan keluar</small>
                            </div>
                        </a>


                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_dataparkir.php?aksi=tampil'); ?>" class="quick yellow-quick">
                            <span>▣</span>
                            <div>
                                <strong>Lihat Data Parkir</strong>
                                <small>Lihat kendaraan yang parkir</small>
                            </div>
                        </a>

                    </div>

                </div>

            </section>


            <!-- LOG AKSES AKTIVITAS -->
            <section class="page-panel activity-panel">

                <div class="panel-header">
                    <div>
                        <h3>Aktivitas Terbaru</h3>
                        <p>Log akses &amp; aktivitas terakhir seluruh pengguna sistem</p>
                    </div>
                    <a class="btn-secondary" href="<?php echo htmlspecialchars($basePath . '/controller/c_log.php'); ?>">Lihat Semua</a>
                </div>

                <div class="activity-list">
                    <?php if (empty($log_terbaru)): ?>
                    <p class="subtitle">Belum ada aktivitas yang tercatat.</p>
                    <?php else: ?>
                    <?php foreach ($log_terbaru as $l): ?>
                    <div class="activity-row">
                        <span class="activity-time"><?php echo htmlspecialchars(tanggal_id($l['waktu_aktivitas'])); ?></span>
                        <span class="activity-who">
                            <strong><?php echo htmlspecialchars($l['nama_lengkap']); ?></strong>
                            <small>@<?php echo htmlspecialchars($l['username']); ?></small>
                        </span>
                        <span><?php echo htmlspecialchars($l['aktivitas']); ?></span>
                        <span class="role-tag <?php echo htmlspecialchars($l['role']); ?>"><?php echo htmlspecialchars($l['role']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </section>


            <!-- FOOTER -->
            <footer>

                <span>© 2026 SiParkir</span>

                <span>
                    Sistem Informasi Parkir
                </span>

            </footer>

        </main>

    </div>

</body>

</html>