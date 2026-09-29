<?php
session_start();

// 1. Validasi Akses (Hanya Petugas dan Admin yang diizinkan)
if (!isset($_SESSION['login']) || !in_array($_SESSION['role'], ['petugas', 'admin'])) {
    header("Location: c_login.php");
    exit();
}

require_once '../model/m_koneksi.php';
require_once '../model/m_cetak.php';
require_once '../view/_helpers.php';

$id_parkir = $_GET['id'] ?? null;

if (!$id_parkir) {
    header("Location: c_dataparkir.php");
    exit();
}

$modelCetak = new M_Cetak($koneksi);
$data = $modelCetak->get_detail_parkir($id_parkir);

if (!$data) {
    echo "Data transaksi parkir tidak ditemukan!";
    exit();
}

// Catat ke Log Aktivitas
if (function_exists('catatLog') && isset($_SESSION['id_user'])) {
    catatLog($koneksi, $_SESSION['id_user'], "Mencetak struk parkir ID: " . $id_parkir);
}

require_once '../view/v_cetak_struk.php';
?>