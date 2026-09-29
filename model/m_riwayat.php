<?php
require_once __DIR__ . '/m_koneksi.php';
class M_Riwayat
{
    public function all($keyword='',$tanggal='')
    {
        global $conn; $where="t.status='keluar'";
        if(trim($keyword)!==''){ $safe=mysqli_real_escape_string($conn,trim($keyword)); $where.=" AND (k.plat_nomor LIKE '%$safe%' OR CAST(t.id_parkir AS CHAR) LIKE '%$safe%')"; }
        if($tanggal!==''){ $safe=mysqli_real_escape_string($conn,$tanggal); $where.=" AND DATE(t.waktu_keluar)='$safe'"; }
        $sql="SELECT t.*,k.plat_nomor,k.jenis_kendaraan,a.nama_area,tr.tarif_per_jam FROM tb_transaksi t JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan JOIN tb_area_parkir a ON a.id_area=t.id_area JOIN tb_tarif tr ON tr.id_tarif=t.id_tarif WHERE $where ORDER BY t.waktu_keluar DESC";
        $q=mysqli_query($conn,$sql); return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];
    }
}
