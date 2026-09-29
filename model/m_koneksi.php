<?php
$server = 'localhost';
$username = 'root';
$pass = '';
$database = 'sistem_parkir';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($server, $username, $pass, $database);
if (!$conn) {
    http_response_code(500);
    die('Koneksi database gagal. Pastikan MySQL aktif dan database <b>sistem_parkir</b> sudah di-import dari file sistem_parkir.sql. Detail: ' . htmlspecialchars(mysqli_connect_error()));
}
mysqli_set_charset($conn, 'utf8mb4');
date_default_timezone_set('Asia/Jakarta');
@mysqli_query($conn, "SET time_zone = '+07:00'");
