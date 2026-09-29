<?php
session_start();

if (!empty($_SESSION['id_user'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: c_dashboard.php');
    } else {
        session_unset();
        session_destroy();
        header('Location: c_register.php');
    }
    exit;
}

require_once __DIR__ . '/../model/m_login.php';
require_once __DIR__ . '/../views/_helpers.php';

$error = '';
$success = '';
$nama = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if ($nama === '' || $username === '' || $password === '' || $konfirmasi === '') {
        $error = 'Semua data wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $error = 'Nama lengkap minimal 3 karakter.';
    } elseif (!preg_match('/^[A-Za-z0-9._-]{4,30}$/', $username)) {
        $error = 'Username 4-30 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau strip.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $model = new M_Login();
        [$ok, $message] = $model->register($nama, $username, $password);

        if ($ok) {
            $success = $message;
            $nama = '';
            $username = '';
        } else {
            $error = $message;
        }
    }
}

require_once __DIR__ . '/../views/v_register.php';
