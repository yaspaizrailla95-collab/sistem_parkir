<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_area_parkir.php';
require_once __DIR__ . '/../model/m_log.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$model = new M_AreaParkir(); $aksi = $_GET['aksi'] ?? 'tampil';
function kembali_area($msg = '') { if ($msg !== '') $_SESSION['flash'] = $msg; header('Location: c_area_parkir.php?aksi=tampil'); exit; }

if ($aksi === 'tampil') {
    $area = $model->all($_GET['q'] ?? '');
    require __DIR__ . '/../views/v_area_parkir.php';
} elseif ($aksi === 'tambah') {
    $kategori = $model->kategoriList();
    require __DIR__ . '/../views/v_tambah_area_parkir.php';
} elseif ($aksi === 'proses_tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $msg] = $model->add($_POST['nama_area'] ?? '', $_POST['kapasitas'] ?? '', $_POST['id_kategori'] ?? '');
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Menambahkan area parkir ' . ($_POST['nama_area'] ?? ''));
    kembali_area($msg);
} elseif ($aksi === 'edit') {
    $data = $model->find((int) $_GET['id']);
    if (!$data) kembali_area('Data area tidak ditemukan.');
    $kategori = $model->kategoriList();
    require __DIR__ . '/../views/v_edit_area_parkir.php';
} elseif ($aksi === 'proses_edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $msg] = $model->update((int) $_POST['id_area'], $_POST['nama_area'] ?? '', $_POST['kapasitas'] ?? '', $_POST['id_kategori'] ?? '');
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Mengedit area parkir ' . ($_POST['nama_area'] ?? ''));
    kembali_area($msg);
} elseif ($aksi === 'hapus') {
    $data = $model->find((int) $_GET['id']);
    $nama = $data['nama_area'] ?? ('#' . (int) $_GET['id']);
    [$ok, $msg] = $model->delete((int) $_GET['id']);
    if ($ok) M_Log::catat($_SESSION['id_user'] ?? 0, 'Menghapus area parkir ' . $nama);
    kembali_area($msg);
} else {
    kembali_area();
}
