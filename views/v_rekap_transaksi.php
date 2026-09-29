<?php
require_once __DIR__ . '/_helpers.php';
require_once __DIR__ . '/../controller/_auth.php';
require_owner();

$current_page_override = 'v_rekap_transaksi.php';
$basePath = base_url();
$cssPath = $basePath . '/asset/dashboard_admin.css';

// Jaga-jaga jika view dibuka langsung.
if (!isset($data)) {
    require_once __DIR__ . '/../model/m_dashboard.php';
    $model = new M_Dashboard();
    $mulai = date('Y-m-01');
    $selesai = date('Y-m-d');
    $data = $model->getRekapPeriode($mulai, $selesai);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi - SiParkir</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <style>
        .rekap-page { margin-top: 20px; }
        .rekap-page .stats { grid-template-columns: repeat(4, 1fr); margin-bottom: 18px; }
        .preset-group { display:flex; gap:8px; flex-wrap:wrap; }
        .preset-group a.active { background:#3978c7; color:#fff; }
        .rekap-header-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .btn-print-rekap { display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
        .rekap-jenis { display:flex; flex-direction:column; gap:12px; }
        .rekap-jenis-row { display:flex; justify-content:space-between; align-items:center; padding:12px; background:#f9fbfd; border-radius:11px; }
        .rekap-jenis-row strong { font-size:12px; color:#53677d; display:block; text-transform:capitalize; }
        .rekap-jenis-row small { font-size:10px; color:#9ba8b6; margin-top:3px; display:block; }
        .rekap-jenis-total { font-size:13px; font-weight:bold; color:#3978c7; white-space:nowrap; }
        @media (min-width:651px) {
            .rekap-page .toolbar { flex-wrap:wrap; align-items:center; }
            .rekap-page .inline-form { flex-wrap:nowrap; }
            .rekap-page .inline-form label { white-space:nowrap; }
            .rekap-page .inline-form .form-input.date-input { width:auto; flex:0 0 auto; }
        }
        @media (max-width:900px) { .rekap-page .stats { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:650px) { .rekap-header-actions { width:100%; justify-content:flex-start; } }
        @media print {
            body * { visibility:hidden; }
            #rekapTransaksi, #rekapTransaksi * { visibility:visible; }
            #rekapTransaksi { position:absolute; top:0; left:0; width:100%; margin:0; padding:0; border:0; box-shadow:none; }
            .no-print { display:none !important; }
        }
    </style>
</head>
<body>
<div class="layout">
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="main">
        <header class="header">
            <div>
                <p class="small-title">PARKING MANAGEMENT</p>
                <h1>Rekap Transaksi <span class="badge" style="vertical-align:middle;margin-left:8px;background:#fff8e7;color:#b38a3d;">OWNER</span></h1>
                <p class="subtitle">Ringkasan transaksi berdasarkan periode yang dipilih.</p>
            </div>

            <div class="profile">
                <div class="profile-icon">O</div>
                <div>
                    <strong><?= e($_SESSION['nama_lengkap'] ?? 'Owner') ?></strong>
                    <small><?= e(ucfirst($_SESSION['role'] ?? 'owner')) ?></small>
                </div>
            </div>
        </header>

        <section class="page-panel rekap-page" id="rekapTransaksi">
            <div class="panel-header">
                <div>
                    <h3>Rekap Transaksi</h3>
                    <p>Ringkasan transaksi sesuai periode yang dipilih.</p>
                </div>
                <div class="rekap-header-actions">
                    <span class="badge">
                        <?= date('d/m/Y', strtotime($data['mulai'])) ?> &ndash; <?= date('d/m/Y', strtotime($data['selesai'])) ?>
                    </span>
                    <button type="button" class="btn-soft btn-small btn-print-rekap no-print" onclick="window.print()">🖨 Cetak Rekap</button>
                </div>
            </div>

            <form class="toolbar no-print" method="get" action="<?= e($basePath . '/controller/c_rekap_transaksi.php') ?>">
                <div class="inline-form">
                    <label class="muted" style="margin:0;">Dari</label>
                    <input class="form-input date-input" type="date" name="dari" value="<?= e($data['mulai']) ?>">
                    <label class="muted" style="margin:0;">Sampai</label>
                    <input class="form-input date-input" type="date" name="sampai" value="<?= e($data['selesai']) ?>">
                    <button class="btn-secondary">Tampilkan</button>
                </div>

                <div class="preset-group">
                    <?php $presetAktif = $_GET['preset'] ?? ''; ?>
                    <a class="btn-soft <?= $presetAktif === 'hari_ini' ? 'active' : ''; ?>" href="<?= e($basePath . '/controller/c_rekap_transaksi.php?preset=hari_ini') ?>">Hari Ini</a>
                    <a class="btn-soft <?= $presetAktif === 'minggu_ini' ? 'active' : ''; ?>" href="<?= e($basePath . '/controller/c_rekap_transaksi.php?preset=minggu_ini') ?>">Minggu Ini</a>
                    <a class="btn-soft <?= $presetAktif === 'bulan_ini' ? 'active' : ''; ?>" href="<?= e($basePath . '/controller/c_rekap_transaksi.php?preset=bulan_ini') ?>">Bulan Ini</a>
                    <a class="btn-soft <?= $presetAktif === 'tahun_ini' ? 'active' : ''; ?>" href="<?= e($basePath . '/controller/c_rekap_transaksi.php?preset=tahun_ini') ?>">Tahun Ini</a>
                </div>
            </form>

            <div class="stats">
                <div class="card blue">
                    <div class="card-content">
                        <span>Total Pendapatan</span>
                        <h2><?= rupiah($data['total_pendapatan']) ?></h2>
                        <small>Periode dipilih</small>
                    </div>
                    <div class="card-icon">Rp</div>
                </div>

                <div class="card green">
                    <div class="card-content">
                        <span>Transaksi Selesai</span>
                        <h2><?= $data['jumlah_transaksi'] ?></h2>
                        <small>Kendaraan sudah keluar</small>
                    </div>
                    <div class="card-icon">✓</div>
                </div>

                <div class="card yellow">
                    <div class="card-content">
                        <span>Kendaraan Masuk</span>
                        <h2><?= $data['kendaraan_masuk'] ?></h2>
                        <small>Tercatat pada periode</small>
                    </div>
                    <div class="card-icon">🚙</div>
                </div>

                <div class="card red">
                    <div class="card-content">
                        <span>Rata-rata / Transaksi</span>
                        <h2><?= rupiah($data['rata_rata']) ?></h2>
                        <small>Dari transaksi selesai</small>
                    </div>
                    <div class="card-icon">Ø</div>
                </div>
            </div>

            <div class="rekap-jenis">
                <?php if (empty($data['per_jenis'])): ?>
                    <p class="subtitle">Belum ada transaksi selesai pada periode ini.</p>
                <?php else: ?>
                    <?php foreach ($data['per_jenis'] as $jenis): ?>
                        <div class="rekap-jenis-row">
                            <div>
                                <strong><?= e($jenis['jenis']) ?></strong>
                                <small><?= $jenis['jumlah'] ?> transaksi</small>
                            </div>
                            <span class="rekap-jenis-total"><?= rupiah($jenis['total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <footer>
            <span>© 2026 SiParkir</span>
            <span>Sistem Informasi Parkir</span>
        </footer>
    </main>
</div>
</body>
</html>
