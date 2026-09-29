<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_logged_in()
{
    return !empty($_SESSION['id_user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: c_login.php');
        exit;
    }
}

function get_role()
{
    return $_SESSION['role'] ?? '';
}

function is_admin()
{
    return get_role() === 'admin';
}

function is_petugas()
{
    return get_role() === 'petugas';
}

function _tolak_akses($pesanRole)
{
    http_response_code(403);
    require __DIR__ . '/../views/v_akses_ditolak.php';
    exit;
}

/**
 * Wajibkan role tertentu. $roles bisa berupa satu string atau array role.
 */
function require_role($roles)
{
    require_login();

    $roles = (array) $roles;
    if (!in_array(get_role(), $roles, true)) {
        $label = implode(' / ', array_map('ucfirst', $roles));
        _tolak_akses($label);
    }
}

function require_admin()
{
    require_role('admin');
}

function require_petugas()
{
    require_role('petugas');
}

function require_owner()
{
    require_role('owner');
}

/**
 * Halaman operasional (data parkir, riwayat) yang boleh diakses
 * baik oleh Admin maupun Petugas.
 */
function require_staff()
{
    require_role(['admin', 'petugas']);
}

/**
 * Halaman laporan/riwayat: boleh dilihat Admin, Petugas, maupun Owner
 * (Owner hanya melihat, tidak melakukan input transaksi).
 */
function require_report_access()
{
    require_role(['admin', 'petugas', 'owner']);
}
