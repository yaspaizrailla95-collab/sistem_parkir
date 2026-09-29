<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_user.php';
require_once __DIR__ . '/../model/m_log.php';

$userModel = new m_user();

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';


// ==========================
// TAMPIL DATA
// ==========================
if ($aksi == 'tampil') {

    $user = $userModel->tampil_data();

    require_once __DIR__ . '/../views/v_user.php';
}


// ==========================
// FORM TAMBAH
// ==========================
elseif ($aksi == 'tambah') {

    require_once __DIR__ . '/../views/v_tambah_user.php';
}


// ==========================
// PROSES TAMBAH
// ==========================
elseif ($aksi == 'proses_tambah') {

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $userModel->tambah_data(
        $nama,
        $username,
        $password,
        $role
    );

    M_Log::catat($_SESSION['id_user'] ?? 0, 'Menambahkan user ' . $username . ' (' . ucfirst($role) . ')');

    header('Location: c_user.php?aksi=tampil');
    exit;
}


// ==========================
// FORM EDIT
// ==========================
elseif ($aksi == 'edit') {

    $id_user = $_GET['id'];

    $data = $userModel->tampil_data_by_id($id_user);

    require_once __DIR__ . '/../views/v_edit_user.php';
}


// ==========================
// PROSES EDIT
// ==========================
elseif ($aksi == 'proses_edit') {

    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $userModel->edit_data(
        $id_user,
        $nama,
        $username,
        $role
    );

    M_Log::catat($_SESSION['id_user'] ?? 0, 'Mengedit user ' . $username . ' (' . ucfirst($role) . ')');

    header('Location: c_user.php?aksi=tampil');
    exit;
}


// ==========================
// HAPUS
// ==========================
elseif ($aksi == 'hapus') {

    $id_user = (int)($_GET['id'] ?? 0);
    $target = $userModel->tampil_data_by_id($id_user);
    [$ok, $msg] = $userModel->hapus_data($id_user);

    if ($ok && $target) {
        M_Log::catat($_SESSION['id_user'] ?? 0, 'Menghapus user ' . $target['username']);
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['flash'] = $msg;

    header('Location: c_user.php?aksi=tampil');
    exit;
}

?>