<?php
session_start();

if (!empty($_SESSION['id_user'])) {
    require_once __DIR__ . '/../model/m_log.php';
    M_Log::catat($_SESSION['id_user'], 'Logout dari sistem');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();
header('Location: c_login.php');
exit;
