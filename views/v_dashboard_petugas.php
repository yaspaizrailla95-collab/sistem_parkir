<?php
require_once __DIR__ . '/_helpers.php';
require_once __DIR__ . '/../controller/_auth.php';
require_petugas();
// Jaga-jaga: kalau file ini dibuka langsung lewat browser
// (bukan lewat controller/c_dashboard_petugas.php), $data belum ada.
// Maka ambil sendiri datanya dari model di sini.
if (!isset($data)) {
    require_once __DIR__ . '/../model/m_dashboard.php';
    $dashboardModel = new M_Dashboard();
    $data = $dashboardModel->getDataDashboard();
}

$current_page_override = 'v_dashboard_petugas.php';
$basePath = base_url();
$cssPath = $basePath . '/asset/dashboard_admin.css';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Petugas - SiParkir</title>

    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR (otomatis menampilkan menu khusus Petugas) -->
        <?php include __DIR__ . '/_sidebar.php'; ?>


        <!-- MAIN CONTENT -->
        <main class="main">

            <!-- HEADER -->
            <header class="header">

                <div>
                    <p class="small-title">PARKING MANAGEMENT</p>
                    <h1>Dashboard Petugas <span class="badge" style="vertical-align:middle;margin-left:8px;background:#e9f8ef;color:#4d9b6d;">PETUGAS</span></h1>
                    <p class="subtitle">
                        Selamat bertugas. Catat kendaraan masuk &amp; keluar di sini.
                    </p>
                </div>

                <div class="profile">

                    <div class="profile-icon">
                        P
                    </div>

                    <div>
                        <strong><?= e($_SESSION['nama_lengkap'] ?? 'Petugas') ?></strong>
                        <small><?= e(ucfirst($_SESSION['role'] ?? 'petugas')) ?></small>
                    </div>

                </div>

            </header>


            <!-- STATISTICS -->
            <section class="stats">

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
                            <p>Tugas harian Petugas</p>
                        </div>

                    </div>


                    <div class="quick-menu">

                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_dataparkir.php?aksi=tambah'); ?>" class="quick blue-quick">
                            <span>🚗</span>
                            <div>
                                <strong>Kendaraan Masuk</strong>
                                <small>Catat kendaraan yang baru masuk</small>
                            </div>
                        </a>


                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_dataparkir.php?aksi=tampil'); ?>" class="quick green-quick">
                            <span>✓</span>
                            <div>
                                <strong>Kendaraan Keluar</strong>
                                <small>Catat kendaraan keluar</small>
                            </div>
                        </a>


                        <a href="<?php echo htmlspecialchars($basePath . '/controller/c_riwayat.php'); ?>" class="quick yellow-quick">
                            <span>◷</span>
                            <div>
                                <strong>Lihat Riwayat</strong>
                                <small>Riwayat transaksi parkir</small>
                            </div>
                        </a>

                    </div>

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
