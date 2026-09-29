<?php
require_once __DIR__ . '/m_koneksi.php';
class M_Pengaturan
{
    public function get(){ global $conn; $q=mysqli_query($conn,'SELECT * FROM tb_pengaturan WHERE id_pengaturan=1'); return mysqli_fetch_assoc($q) ?: []; }
    public function areas(){ global $conn; $q=mysqli_query($conn,'SELECT id_area,nama_area,kapasitas,terisi FROM tb_area_parkir ORDER BY id_area'); return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[]; }
    public function tariffs(){ global $conn; $q=mysqli_query($conn,"SELECT * FROM tb_tarif ORDER BY jenis_kendaraan,id_tarif"); return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[]; }
    public function save($d)
    {
        global $conn; mysqli_begin_transaction($conn);
        try {
            $stmt=mysqli_prepare($conn,'UPDATE tb_pengaturan SET nama_sistem=?,nama_admin=?,email_admin=?,telepon=?,alamat=?,toleransi_menit=? WHERE id_pengaturan=1');
            mysqli_stmt_bind_param($stmt,'sssssi',$d['nama_sistem'],$d['nama_admin'],$d['email_admin'],$d['telepon'],$d['alamat'],$d['toleransi_menit']); if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            $areaIds=$d['area_ids']; $caps=$d['kapasitas'];
            $stmt=mysqli_prepare($conn,'UPDATE tb_area_parkir SET kapasitas=? WHERE id_area=?');
            foreach($areaIds as $i=>$id){$cap=(int)$caps[$i]; if($cap<0) throw new Exception('Kapasitas tidak boleh negatif.'); $check=mysqli_prepare($conn,'SELECT terisi FROM tb_area_parkir WHERE id_area=?'); mysqli_stmt_bind_param($check,'i',$id);mysqli_stmt_execute($check);$terisi=(int)mysqli_stmt_get_result($check)->fetch_assoc()['terisi'];if($cap<$terisi) throw new Exception('Kapasitas '.$id.' tidak boleh lebih kecil dari jumlah terisi.'); mysqli_stmt_bind_param($stmt,'ii',$cap,$id);if(!mysqli_stmt_execute($stmt))throw new Exception(mysqli_error($conn));}
            foreach($d['tarif_ids'] as $i=>$id){$tarif=(float)$d['tarif_values'][$i];if($tarif<0)throw new Exception('Tarif tidak boleh negatif.');$st=mysqli_prepare($conn,'UPDATE tb_tarif SET tarif_per_jam=? WHERE id_tarif=?');mysqli_stmt_bind_param($st,'di',$tarif,$id);if(!mysqli_stmt_execute($st))throw new Exception(mysqli_error($conn));}
            if($d['password']!==''){
                $hash=password_hash($d['password'],PASSWORD_DEFAULT); $st=mysqli_prepare($conn,'UPDATE tb_user SET nama_lengkap=?,password=? WHERE id_user=1'); mysqli_stmt_bind_param($st,'ss',$d['nama_admin'],$hash);if(!mysqli_stmt_execute($st))throw new Exception(mysqli_error($conn));
            } else { $st=mysqli_prepare($conn,'UPDATE tb_user SET nama_lengkap=? WHERE id_user=1');mysqli_stmt_bind_param($st,'s',$d['nama_admin']);if(!mysqli_stmt_execute($st))throw new Exception(mysqli_error($conn)); }
            $conn->commit();return [true,'Pengaturan berhasil disimpan.'];
        }catch(Exception $e){$conn->rollback();return [false,$e->getMessage()];}
    }
}
