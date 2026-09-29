<?php
session_start();

require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../views/_helpers.php';

if (!empty($_SESSION['id_user'])) {
    header('Location: ' . dashboard_url_for_role($_SESSION['role'] ?? ''));
    exit;
}

require_once __DIR__ . '/../model/m_login.php';
require_once __DIR__ . '/../model/m_log.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $model = new M_Login();
        $user = $model->cekLogin($username, $password);

        if (!$user) {
            $error = 'Username, password, atau status akun tidak valid.';
        } elseif (!in_array($user['role'], ['admin', 'petugas', 'owner'], true)) {
            $error = 'Role akun tidak dikenali. Hubungi Admin.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_user'] = (int)$user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            M_Log::catat($user['id_user'], 'Login ke sistem sebagai ' . ucfirst($user['role']));

            // Admin masuk ke dashboard Admin, Petugas otomatis masuk ke
            // tampilan Petugas sesuai role yang tersimpan di database.
            header('Location: ' . dashboard_url_for_role($user['role']));
            exit;
        }
    }
}

require_once __DIR__ . '/../views/v_login.php';
