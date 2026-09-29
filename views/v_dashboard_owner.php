<?php
require_once __DIR__ . '/_helpers.php';
require_once __DIR__ . '/../controller/_auth.php';
require_owner();
// Jaga-jaga: kalau file ini dibuka langsung lewat browser
// (bukan lewat controller/c_dashboard_owner.php), $data belum ada.
if (!isset($data)) {
    require_once __DIR__ . '/../model/m_dashboard.php';
    $dashboardModel = new M_Dashboard();
    $data = $dashboardModel->getDataOwner();
}
$current_page_override = 'v_dashboard_owner.php';
$basePath = base_url();
$cssPath = $basePath . '/asset/dashboard_admin.css';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Owner - SiParkir</title>

    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <style>
        /* Elemen tambahan khusus Dashboard Owner (tren & rincian jenis kendaraan) */
        .trend-list { display:flex; flex-direction:column; gap:12px; }
        .trend-row { display:grid; grid-template-columns:46px 1fr 110px; align-items:center; gap:13px; }
        .trend-label { font-size:11px; font-weight:bold; color:#8a9bad; text-align:center; }
        .trend-amount { font-size:12px; font-weight:bold; color:#344c66; text-align:right; white-space:nowrap; }
        .jenis-list { display:flex; flex-direction:column; gap:12px; }
        .jenis-row { display:flex; justify-content:space-between; align-items:center; padding:12px; background:#f9fbfd; border-radius:11px; }
        .jenis-row strong { font-size:12px; color:#53677d; display:block; text-transform:capitalize; }
        .jenis-row small { font-size:10px; color:#9ba8b6; margin-top:3px; display:block; }
        .jenis-total { font-size:13px; font-weight:bold; color:#3978c7; white-space:nowrap; }
    </style>
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR (otomatis menampilkan menu khusus Owner) -->
        <?php include __DIR__ . '/_sidebar.php'; ?>


        <!-- MAIN CONTENT -->
        <main class="main">

            <!-- HEADER -->
            <header class="header">

                <div>
                    <p class="small-title">PARKING MANAGEMENT</p>
                    <h1>Dashboard Owner <span class="badge" style="vertical-align:middle;margin-left:8px;background:#fff8e7;color:#b38a3d;">OWNER</span></h1>
                    <p class="subtitle">
                        Ringkasan pendapatan &amp; performa sistem parkir Anda.
                    </p>
                </div>

                <div class="profile">

                    <div class="profile-icon">
                        O
                    </div>

                    <div>
                        <strong><?= e($_SESSION['nama_lengkap'] ?? 'Owner') ?></strong>
                        <small><?= e(ucfirst($_SESSION['role'] ?? 'owner')) ?></small>
                    </div>

                </div>

            </header>


            <!-- STATISTICS -->
            <section class="stats">

                <!-- PENDAPATAN HARI INI -->
                <div class="card blue">

                    <div class="card-content">
                        <span>Pendapatan Hari Ini</span>

                        <h2>
                            <?php echo rupiah($data['pendapatan_hari_ini']); ?>
                        </h2>

                        <small><?php echo $data['transaksi_selesai']; ?> transaksi selesai</small>
                    </div>

                    <div class="card-icon">
                        Rp
                    </div>

                </div>


                <!-- PENDAPATAN BULAN INI -->
                <div class="card green">

                    <div class="card-content">
                        <span>Pendapatan Bulan Ini</span>

                        <h2>
                            <?php echo rupiah($data['pendapatan_bulan_ini']); ?>
                        </h2>

                        <small>Akumulasi transaksi selesai</small>
                    </div>

                    <div class="card-icon">
                        ↑
                    </div>

                </div>


                <!-- KENDARAAN TERDAFTAR -->
                <div class="card yellow">

                    <div class="card-content">
                        <span>Kendaraan Terdaftar</span>

                        <h2>
                            <?php echo $data['total_kendaraan']; ?>
                        </h2>

                        <small>Total di sistem</small>
                    </div>

                    <div class="card-icon">
                        🚙
                    </div>

                </div>


                <!-- SLOT TERSEDIA -->
                <div class="card red">

                    <div class="card-content">
                        <span>Slot Tersedia</span>

                        <h2>
                            <?php echo $data['slot_tersedia']; ?>
                        </h2>

                        <small>Slot parkir kosong saat ini</small>
                    </div>

                    <div class="card-icon">
                        P
                    </div>

                </div>

            </section>


            <!-- CONTENT -->
            <section class="content-grid">

                <!-- TREN PENDAPATAN -->
                <div class="panel">

                    <div class="panel-header">

                        <div>
                            <h3>Tren Pendapatan 7 Hari Terakhir</h3>
                            <p>Total pendapatan dari transaksi yang sudah keluar</p>
                        </div>

                        <span class="badge">
                            7 Hari
                        </span>

                    </div>


                    <div class="trend-list">

                        <?php foreach ($data['tren_pendapatan'] as $hari): ?>
                        <div class="trend-row">
                            <span class="trend-label"><?php echo htmlspecialchars($hari['label']); ?></span>

                            <div class="progress">
                                <div class="progress-blue" style="width: <?php echo $hari['persen']; ?>%;"></div>
                            </div>

                            <span class="trend-amount"><?php echo rupiah($hari['total']); ?></span>
                        </div>
                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- PENDAPATAN PER JENIS KENDARAAN -->
                <div class="panel">

                    <div class="panel-header">

                        <div>
                            <h3>Per Jenis Kendaraan</h3>
                            <p>Pendapatan bulan ini</p>
                        </div>

                    </div>

                    <div class="jenis-list">

                        <?php if (empty($data['per_jenis'])): ?>
                        <p class="subtitle">Belum ada transaksi selesai bulan ini.</p>
                        <?php else: ?>
                        <?php foreach ($data['per_jenis'] as $jenis): ?>
                        <div class="jenis-row">
                            <div>
                                <strong><?php echo htmlspecialchars($jenis['jenis']); ?></strong>
                                <small><?php echo $jenis['jumlah']; ?> transaksi</small>
                            </div>
                            <span class="jenis-total"><?php echo rupiah($jenis['total']); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                    </div>

                    <div style="margin-top:18px;">
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <a href="<?php echo htmlspecialchars($basePath . '/controller/c_rekap_transaksi.php'); ?>" class="btn-soft" style="flex:1;text-align:center;box-sizing:border-box;">Lihat Rekap Transaksi</a>
                            <a href="<?php echo htmlspecialchars($basePath . '/controller/c_riwayat.php'); ?>" class="btn-soft" style="flex:1;text-align:center;box-sizing:border-box;">Lihat Riwayat Lengkap</a>
                        </div>
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
