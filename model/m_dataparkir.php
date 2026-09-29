<?php
require_once __DIR__ . '/m_koneksi.php';

class M_DataParkir
{
    public function active($keyword='')
    {
        global $conn;
        $where = "t.status='masuk'";
        if (trim($keyword) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($keyword));
            $where .= " AND (k.plat_nomor LIKE '%$safe%' OR CAST(t.id_parkir AS CHAR) LIKE '%$safe%' OR a.nama_area LIKE '%$safe%')";
        }
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.pemilik, a.nama_area, tr.tarif_per_jam
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan
                JOIN tb_area_parkir a ON a.id_area=t.id_area
                JOIN tb_tarif tr ON tr.id_tarif=t.id_tarif
                WHERE $where ORDER BY t.waktu_masuk DESC";
        $q=mysqli_query($conn,$sql); return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];
    }

    public function vehicles()
    {
        global $conn;
        $q=mysqli_query($conn,"SELECT k.id_kendaraan,k.plat_nomor,k.jenis_kendaraan,k.pemilik FROM tb_kendaraan k LEFT JOIN tb_transaksi t ON t.id_kendaraan=k.id_kendaraan AND t.status='masuk' WHERE t.id_parkir IS NULL ORDER BY k.plat_nomor");
        return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];
    }

    public function areas()
    {
        global $conn;
        $q=mysqli_query($conn,"SELECT id_area,nama_area,kapasitas,terisi FROM tb_area_parkir ORDER BY nama_area");
        return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];
    }

    public function tariffs()
    {
        global $conn;
        $q=mysqli_query($conn,"SELECT id_tarif,jenis_kendaraan,tarif_per_jam FROM tb_tarif ORDER BY id_tarif");
        return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];
    }

    /** Ambil plat nomor kendaraan (dipakai untuk pencatatan log aktivitas). */
    public function platByKendaraan($idKendaraan)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT plat_nomor FROM tb_kendaraan WHERE id_kendaraan=?');
        mysqli_stmt_bind_param($stmt, 'i', $idKendaraan);
        mysqli_stmt_execute($stmt);
        $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
        return $row['plat_nomor'] ?? ('#' . $idKendaraan);
    }

    /** Ambil plat nomor dari id transaksi parkir (dipakai untuk pencatatan log aktivitas). */
    public function platByParkir($idParkir)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT k.plat_nomor FROM tb_transaksi t JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan WHERE t.id_parkir=?');
        mysqli_stmt_bind_param($stmt, 'i', $idParkir);
        mysqli_stmt_execute($stmt);
        $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
        return $row['plat_nomor'] ?? ('#' . $idParkir);
    }

    public function enter($idKendaraan,$idTarif,$idArea,$idUser)
    {
        global $conn;
        mysqli_begin_transaction($conn);
        try {
            $stmt=mysqli_prepare($conn,"SELECT id_parkir FROM tb_transaksi WHERE id_kendaraan=? AND status='masuk' LIMIT 1");
            mysqli_stmt_bind_param($stmt,'i',$idKendaraan); mysqli_stmt_execute($stmt);
            if(mysqli_stmt_get_result($stmt)->fetch_assoc()) throw new Exception('Kendaraan masih tercatat sedang parkir.');

            $stmt=mysqli_prepare($conn,'SELECT kapasitas,terisi FROM tb_area_parkir WHERE id_area=? FOR UPDATE');
            mysqli_stmt_bind_param($stmt,'i',$idArea); mysqli_stmt_execute($stmt);
            $area=mysqli_stmt_get_result($stmt)->fetch_assoc();
            if(!$area) throw new Exception('Area parkir tidak ditemukan.');
            if((int)$area['terisi'] >= (int)$area['kapasitas']) throw new Exception('Area parkir penuh.');

            $stmt=mysqli_prepare($conn,"INSERT INTO tb_transaksi (id_kendaraan,waktu_masuk,id_tarif,status,id_user,id_area,biaya_total) VALUES (?,NOW(),?,'masuk',?,?,0)");
            mysqli_stmt_bind_param($stmt,'iiii',$idKendaraan,$idTarif,$idUser,$idArea); if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            $idParkirBaru = mysqli_insert_id($conn);
            $stmt=mysqli_prepare($conn,'UPDATE tb_area_parkir SET terisi=terisi+1 WHERE id_area=?');
            mysqli_stmt_bind_param($stmt,'i',$idArea); if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            $conn->commit(); return [true,'Kendaraan berhasil dicatat masuk.',$idParkirBaru];
        } catch(Exception $e){ $conn->rollback(); return [false,$e->getMessage(),null]; }
    }

    /**
     * Ambil detail satu transaksi parkir lengkap (kendaraan, area, tarif, petugas)
     * untuk keperluan cetak struk parkir masuk maupun struk transaksi/pembayaran.
     */
    public function detail($idParkir)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, k.warna, k.pemilik,
                    a.nama_area, tr.tarif_per_jam, u.nama_lengkap AS nama_petugas
                FROM tb_transaksi t
                JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan
                JOIN tb_area_parkir a ON a.id_area=t.id_area
                JOIN tb_tarif tr ON tr.id_tarif=t.id_tarif
                JOIN tb_user u ON u.id_user=t.id_user
                WHERE t.id_parkir=?");
        mysqli_stmt_bind_param($stmt, 'i', $idParkir);
        mysqli_stmt_execute($stmt);
        $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
        return $row ?: null;
    }

    public function checkout($idParkir)
    {
        global $conn;
        mysqli_begin_transaction($conn);
        try {
            $stmt=mysqli_prepare($conn,"SELECT t.*, tr.tarif_per_jam FROM tb_transaksi t JOIN tb_tarif tr ON tr.id_tarif=t.id_tarif WHERE t.id_parkir=? AND t.status='masuk' FOR UPDATE");
            mysqli_stmt_bind_param($stmt,'i',$idParkir); mysqli_stmt_execute($stmt); $data=mysqli_stmt_get_result($stmt)->fetch_assoc();
            if(!$data) throw new Exception('Data parkir aktif tidak ditemukan.');
            $masuk=strtotime($data['waktu_masuk']); $keluar=time(); $menit=max(1,(int)ceil(($keluar-$masuk)/60)); $durasi=max(1,(int)ceil($menit/60));
            $biaya=$durasi*(float)$data['tarif_per_jam'];
            $stmt=mysqli_prepare($conn,"UPDATE tb_transaksi SET waktu_keluar=NOW(), durasi_jam=?, biaya_total=?, status='keluar' WHERE id_parkir=?");
            mysqli_stmt_bind_param($stmt,'idi',$durasi,$biaya,$idParkir); if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            $stmt=mysqli_prepare($conn,'UPDATE tb_area_parkir SET terisi=GREATEST(terisi-1,0) WHERE id_area=?'); mysqli_stmt_bind_param($stmt,'i',$data['id_area']); if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            $conn->commit(); return [true,'Kendaraan berhasil keluar. Total: Rp '.number_format($biaya,0,',','.')];
        } catch(Exception $e){ $conn->rollback(); return [false,$e->getMessage()]; }
    }
}
