<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');

echo '<h3>Cek proyek SiParkir</h3>PHP ' . PHP_VERSION . '<hr>';

$files = [
    'index.php',
    'controller/_auth.php',
    'controller/c_login.php',
    'controller/c_logout.php',
    'controller/c_dashboard.php',
    'model/m_koneksi.php',
    'model/m_login.php',
    'view/v_login.php',
    'view/_sidebar.php',
    'view/_helpers.php',
];

foreach ($files as $f) {
    $path = __DIR__ . '/' . $f;
    if (!file_exists($path)) {
        echo "<div style='color:red'>[X] $f - TIDAK ADA</div>";
        continue;
    }
    $src = file_get_contents($path);
    if (substr($src, 0, 3) === "\xEF\xBB\xBF") {
        echo "<div style='color:orange'>[!] $f - ada BOM di awal file</div>";
    }
    try {
        token_get_all($src, TOKEN_PARSE);
        echo "<div style='color:green'>[OK] $f - sintaks benar</div>";
    } catch (ParseError $e) {
        echo "<div style='color:red'>[X] $f - ERROR baris " . $e->getLine() . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

echo '<hr>';
mysqli_report(MYSQLI_REPORT_OFF);
$c = @mysqli_connect('localhost', 'root', '', 'sistem_parkir');
if (!$c) {
    echo '[X] Koneksi database gagal: ' . htmlspecialchars(mysqli_connect_error());
} else {
    $r = mysqli_query($c, 'SELECT username, role, status_aktif, LEFT(password, 7) AS awal_password FROM tb_user');
    if (!$r) {
        echo '[X] Query gagal: ' . htmlspecialchars(mysqli_error($c));
    } else {
        echo '[OK] Database terhubung. Isi tb_user:<br>';
        while ($u = mysqli_fetch_assoc($r)) {
            echo htmlspecialchars(implode(' | ', $u)) . '<br>';
        }
    }
}