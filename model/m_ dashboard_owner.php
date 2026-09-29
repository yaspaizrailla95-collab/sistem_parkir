<?php
// MODEL: kumpulan fungsi untuk ambil data dashboard owner
// Sesuaikan nama tabel/kolom kalau berbeda dengan project Anda

function getPendapatanHariIni($koneksi)
{
    return $koneksi->query(
        "SELECT COALESCE(SUM(tarif),0) FROM transaksi_parkir WHERE DATE(jam_keluar) = CURDATE()"
    )->fetchColumn();
}

function getPendapatanBulanIni($koneksi)
{
    return $koneksi->query(
        "SELECT COALESCE(SUM(tarif),0) FROM transaksi_parkir
         WHERE MONTH(jam_keluar) = MONTH(CURDATE()) AND YEAR(jam_keluar) = YEAR(CURDATE())"
    )->fetchColumn();
}

function getSedangParkir($koneksi)
{
    return $koneksi->query(
        "SELECT COUNT(*) FROM transaksi_parkir WHERE status = 'masuk' AND jam_keluar IS NULL"
    )->fetchColumn();
}

function getTransaksiHariIni($koneksi)
{
    return $koneksi->query(
        "SELECT COUNT(*) FROM transaksi_parkir WHERE DATE(jam_keluar) = CURDATE()"
    )->fetchColumn();
}

function getTransaksiTerbaru($koneksi, $limit = 10)
{
    $stmt = $koneksi->prepare("SELECT * FROM transaksi_parkir ORDER BY jam_masuk DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}