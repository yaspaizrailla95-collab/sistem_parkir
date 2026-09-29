<?php
require_once __DIR__ . '/_auth.php';
require_owner();

require_once __DIR__ . '/../model/m_dashboard.php';
$model = new M_Dashboard();

// =========================================================
// FILTER REKAP TRANSAKSI OWNER
// =========================================================
$preset = $_GET['preset'] ?? '';

switch ($preset) {
    case 'hari_ini':
        $mulai = $selesai = date('Y-m-d');
        break;

    case 'minggu_ini':
        $hariKe = (int) date('N');
        $mulai = date('Y-m-d', strtotime('-' . ($hariKe - 1) . ' days'));
        $selesai = date('Y-m-d', strtotime('+' . (7 - $hariKe) . ' days'));
        break;

    case 'bulan_ini':
        $mulai = date('Y-m-01');
        $selesai = date('Y-m-t');
        break;

    case 'tahun_ini':
        $mulai = date('Y') . '-01-01';
        $selesai = date('Y') . '-12-31';
        break;

    default:
        $mulai = trim($_GET['dari'] ?? '');
        $selesai = trim($_GET['sampai'] ?? '');

        if ($mulai === '' && $selesai === '') {
            $mulai = date('Y-m-01');
            $selesai = date('Y-m-d');
        }
}

// Validasi tanggal.
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai)) {
    $mulai = date('Y-m-01');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai)) {
    $selesai = date('Y-m-d');
}

// Jika tanggal terbalik, otomatis ditukar.
if ($mulai > $selesai) {
    [$mulai, $selesai] = [$selesai, $mulai];
}

$data = $model->getRekapPeriode($mulai, $selesai);

require_once __DIR__ . '/../views/v_rekap_transaksi.php';
