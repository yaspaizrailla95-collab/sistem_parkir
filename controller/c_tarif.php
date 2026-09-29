<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_tarif.php';
require_once __DIR__ . '/../model/m_log.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$model = new M_Tarif(); $aksi = $_GET['aksi'] ?? 'tampil';
function kembali_tarif($msg = '') { if ($msg !== '') $_SESSION['flash'] = $msg; header('Location: c_tarif.php?aksi=tampil'); exit; }

if ($aksi === 'tampil') {
    $tarif = $model->all($_GET['q'] ?? '');
    require __DIR__ . '/../views/v_tarif.php';
} elseif ($aksi === 'tambah') {
    require __DIR__ . '/../views/v_tambah_tarif.php';
} elseif ($aksi === 'proses_tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $msg] = $model->add($_POST['jenis_kendaraan'] ?? '', $_POST['tarif_per_jam'] ?? '');
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Menambahkan tarif ' . ($_POST['jenis_kendaraan'] ?? ''));
    kembali_tarif($msg);
} elseif ($aksi === 'edit') {
    $data = $model->find((int) $_GET['id']);
    if (!$data) kembali_tarif('Data tarif tidak ditemukan.');
    require __DIR__ . '/../views/v_edit_tarif.php';
} elseif ($aksi === 'proses_edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $msg] = $model->update((int) $_POST['id_tarif'], $_POST['jenis_kendaraan'] ?? '', $_POST['tarif_per_jam'] ?? '');
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Mengedit tarif ' . ($_POST['jenis_kendaraan'] ?? ''));
    kembali_tarif($msg);
} elseif ($aksi === 'hapus') {
    $data = $model->find((int) $_GET['id']);
    $label = $data ? ($data['jenis_kendaraan'] . ' Rp' . number_format((float) $data['tarif_per_jam'], 0, ',', '.')) : ('#' . (int) $_GET['id']);
    [$ok, $msg] = $model->delete((int) $_GET['id']);
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Menghapus tarif ' . $label);
    kembali_tarif($msg);
} else {
    kembali_tarif();
}
