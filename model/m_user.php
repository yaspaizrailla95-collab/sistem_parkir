<?php
require_once __DIR__ . '/m_koneksi.php';
class m_user
{
    public function tampil_data(){global $conn;$q=mysqli_query($conn,'SELECT * FROM tb_user ORDER BY id_user DESC');return $q?mysqli_fetch_all($q,MYSQLI_ASSOC):[];}
    public function tampil_data_by_id($id){global $conn;$st=mysqli_prepare($conn,'SELECT * FROM tb_user WHERE id_user=?');mysqli_stmt_bind_param($st,'i',$id);mysqli_stmt_execute($st);return mysqli_stmt_get_result($st)->fetch_assoc();}
    public function tambah_data($nama,$username,$password,$role){global $conn;$hash=password_hash($password,PASSWORD_DEFAULT);$st=mysqli_prepare($conn,'INSERT INTO tb_user (nama_lengkap,username,password,role,status_aktif) VALUES (?,?,?,?,1)');mysqli_stmt_bind_param($st,'ssss',$nama,$username,$hash,$role);return mysqli_stmt_execute($st);}
    public function edit_data($id,$nama,$username,$role){global $conn;$st=mysqli_prepare($conn,'UPDATE tb_user SET nama_lengkap=?,username=?,role=? WHERE id_user=?');mysqli_stmt_bind_param($st,'sssi',$nama,$username,$role,$id);return mysqli_stmt_execute($st);}
    public function hapus_data($id)
    {
        global $conn;

        mysqli_begin_transaction($conn);
        try {
            // Ambil kendaraan milik user.
            $st = mysqli_prepare($conn, 'SELECT id_kendaraan FROM tb_kendaraan WHERE id_user=? FOR UPDATE');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $vehicles = mysqli_stmt_get_result($st)->fetch_all(MYSQLI_ASSOC);
            mysqli_stmt_close($st);

            // Transaksi aktif perlu mengurangi kembali kapasitas area.
            $st = mysqli_prepare($conn, 'SELECT t.id_parkir,t.id_area FROM tb_transaksi t LEFT JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan WHERE (t.id_user=? OR k.id_user=?) AND t.status="masuk" FOR UPDATE');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'ii', $id, $id);
            mysqli_stmt_execute($st);
            $active = mysqli_stmt_get_result($st)->fetch_all(MYSQLI_ASSOC);
            mysqli_stmt_close($st);

            foreach ($active as $row) {
                $areaId = (int)$row['id_area'];
                $up = mysqli_prepare($conn, 'UPDATE tb_area_parkir SET terisi=GREATEST(terisi-1,0) WHERE id_area=?');
                if (!$up) throw new Exception(mysqli_error($conn));
                mysqli_stmt_bind_param($up, 'i', $areaId);
                if (!mysqli_stmt_execute($up)) throw new Exception(mysqli_error($conn));
                mysqli_stmt_close($up);
            }

            // Hapus transaksi yang mereferensikan user langsung atau kendaraan miliknya.
            $st = mysqli_prepare($conn, 'DELETE t FROM tb_transaksi t LEFT JOIN tb_kendaraan k ON k.id_kendaraan=t.id_kendaraan WHERE t.id_user=? OR k.id_user=?');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'ii', $id, $id);
            if (!mysqli_stmt_execute($st)) throw new Exception(mysqli_error($conn));
            mysqli_stmt_close($st);

            // Hapus kendaraan milik user.
            $st = mysqli_prepare($conn, 'DELETE FROM tb_kendaraan WHERE id_user=?');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'i', $id);
            if (!mysqli_stmt_execute($st)) throw new Exception(mysqli_error($conn));
            mysqli_stmt_close($st);

            // Hapus log aktivitas yang masih mereferensikan user.
            $st = mysqli_prepare($conn, 'DELETE FROM tb_log_aktivitas WHERE id_user=?');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'i', $id);
            if (!mysqli_stmt_execute($st)) throw new Exception(mysqli_error($conn));
            mysqli_stmt_close($st);

            $st = mysqli_prepare($conn, 'DELETE FROM tb_user WHERE id_user=?');
            if (!$st) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($st, 'i', $id);
            if (!mysqli_stmt_execute($st)) throw new Exception(mysqli_error($conn));
            if (mysqli_stmt_affected_rows($st) === 0) throw new Exception('Data user tidak ditemukan.');
            mysqli_stmt_close($st);

            mysqli_commit($conn);
            return [true, 'User berhasil dihapus beserta data terkait.'];
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            return [false, 'User gagal dihapus: '.$e->getMessage()];
        }
    }
}
