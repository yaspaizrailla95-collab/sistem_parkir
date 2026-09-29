<?php
require_once __DIR__ . '/_auth.php';
require_staff();

require_once __DIR__ . '/../model/m_dataparkir.php';
require_once __DIR__ . '/../model/m_pengaturan.php';
require_once __DIR__ . '/../views/_helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$idParkir = (int) ($_GET['id'] ?? 0);
// Struk "parkir masuk" (dicetak sebelum kendaraan keluar) sudah dihilangkan
// dari alur aplikasi. Semua struk yang bisa dicetak sekarang adalah struk
// transaksi/pembayaran, dan hanya tersedia setelah kendaraan benar-benar
// diproses keluar terlebih dahulu.
$jenis = 'transaksi';

$model = new M_DataParkir();
$struk = $idParkir > 0 ? $model->detail($idParkir) : null;

if (!$struk) {
    $_SESSION['flash'] = 'Data parkir tidak ditemukan untuk dicetak.';
    header('Location: c_dataparkir.php?aksi=tampil');
    exit;
}

// Struk hanya tersedia setelah kendaraan benar-benar diproses keluar.
if ($struk['status'] !== 'keluar') {
    $_SESSION['flash'] = 'Struk hanya bisa dicetak setelah kendaraan diproses keluar.';
    header('Location: c_dataparkir.php?aksi=tampil');
    exit;
}

$pengaturanModel = new M_Pengaturan();
$pengaturan = $pengaturanModel->get();

require __DIR__ . '/../views/v_struk.php';
