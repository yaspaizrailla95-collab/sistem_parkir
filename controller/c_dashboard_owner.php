<?php
require_once __DIR__ . '/_auth.php';
require_owner();

require_once __DIR__ . '/../model/m_dashboard.php';
$model = new M_Dashboard();
$data = $model->getDataOwner();

// =========================================================
// REKAP TRANSAKSI SESUAI WAKTU YANG DIMINTA (Dashboard Owner)
// Owner bisa pilih preset cepat (Hari Ini/Minggu Ini/Bulan Ini/
// Tahun Ini) atau isi sendiri tanggal mulai & selesai.
// =========================================================
$preset = $_GET['preset'] ?? '';

switch ($preset) {
    case 'hari_ini':
        $mulai = $selesai = date('Y-m-d');
        break;

    case 'minggu_ini':
        $hariKe = (int) date('N'); // 1 (Senin) .. 7 (Minggu)
        $mulai = date('Y-m-d', strtotime('-' . ($hariKe - 1) . ' days'));
        $selesai = date('Y-m-d', strtotime('+' . (7 - $hariKe) . ' days'));
        break;

    case 'tahun_ini':
        $mulai = date('Y') . '-01-01';
        $selesai = date('Y') . '-12-31';
        break;

    case 'bulan_ini':
        $mulai = date('Y-m-01');
        $selesai = date('Y-m-t');
        break;

    default:
        // Tidak ada preset yang dipilih: pakai tanggal dari form (jika ada),
        // kalau tidak ada sama sekali defaultnya bulan berjalan.
        $mulai = trim($_GET['dari'] ?? '');
        $selesai = trim($_GET['sampai'] ?? '');
        if ($mulai === '' && $selesai === '') {
            $mulai = date('Y-m-01');
            $selesai = date('Y-m-d');
        }
}

// Validasi format tanggal (YYYY-MM-DD), fallback ke bulan berjalan kalau tidak valid.
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai)) {
    $mulai = date('Y-m-01');
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai)) {
    $selesai = date('Y-m-d');
}
// Kalau user membalik urutan tanggal, tukar otomatis biar tetap benar.
if ($mulai > $selesai) {
    [$mulai, $selesai] = [$selesai, $mulai];
}

$data['rekap'] = $model->getRekapPeriode($mulai, $selesai);

require_once __DIR__ . '/../views/v_dashboard_owner.php';
