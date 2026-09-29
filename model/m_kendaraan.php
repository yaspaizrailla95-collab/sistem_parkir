<?php
require_once __DIR__ . '/m_koneksi.php';

class M_Kendaraan
{
    public function all($keyword = '')
    {
        global $conn;
        $keyword = trim($keyword);
        $sql = "SELECT k.*, u.nama_lengkap,
                EXISTS(SELECT 1 FROM tb_transaksi t WHERE t.id_kendaraan=k.id_kendaraan AND t.status='masuk') AS sedang_parkir
                FROM tb_kendaraan k
                JOIN tb_user u ON u.id_user=k.id_user";
        if ($keyword !== '') {
            $safe = mysqli_real_escape_string($conn, $keyword);
            $sql .= " WHERE k.plat_nomor LIKE '%$safe%' OR k.pemilik LIKE '%$safe%' OR k.jenis_kendaraan LIKE '%$safe%'";
        }
        $sql .= ' ORDER BY k.id_kendaraan DESC';
        $q = mysqli_query($conn, $sql);
        if (!$q) return [];
        return mysqli_fetch_all($q, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT * FROM tb_kendaraan WHERE id_kendaraan=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt)->fetch_assoc();
    }

    public function users()
    {
        global $conn;
        $q = mysqli_query($conn, "SELECT id_user, nama_lengkap, role FROM tb_user WHERE status_aktif=1 ORDER BY nama_lengkap");
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    /**
     * Validasi format plat nomor kendaraan Indonesia: [huruf][angka][huruf]
     * Barisan 1 (huruf kode wilayah): maksimal 2 huruf.
     * Barisan 2 (angka): maksimal 4 angka.
     * Barisan 3 (huruf seri belakang): maksimal 3 huruf.
     * Boleh lebih pendek dari batas maksimal, tidak boleh lebih panjang.
     */
    private function platValid($plat)
    {
        return (bool) preg_match('/^[A-Za-z]{1,2}\s?[0-9]{1,4}\s?[A-Za-z]{1,3}$/', $plat);
    }

    /**
     * Validasi nama pemilik: hanya huruf & spasi, tidak boleh ada angka.
     * Boleh kosong (field pemilik memang opsional).
     */
    private function pemilikValid($pemilik)
    {
        return $pemilik === '' || (bool) preg_match('/^[A-Za-z\s]+$/u', $pemilik);
    }

    public function add($plat, $jenis, $warna, $pemilik, $idUser)
    {
        global $conn;

        $plat = strtoupper(trim($plat));
        $pemilik = trim($pemilik);

        if (!$this->platValid($plat)) {
            return [false, 'Format plat nomor tidak valid. Gunakan pola seperti "B 1234 ABC": huruf depan maksimal 2, angka maksimal 4, huruf belakang maksimal 3 (boleh lebih pendek).'];
        }
        if (!$this->pemilikValid($pemilik)) {
            return [false, 'Nama pemilik hanya boleh berisi huruf, tidak boleh ada angka.'];
        }

        $stmt = mysqli_prepare($conn, 'SELECT id_kendaraan FROM tb_kendaraan WHERE UPPER(plat_nomor)=UPPER(?)');
        mysqli_stmt_bind_param($stmt, 's', $plat);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Plat nomor sudah terdaftar.'];

        $stmt = mysqli_prepare($conn, 'INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, id_user) VALUES (?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'ssssi', $plat, $jenis, $warna, $pemilik, $idUser);
        return mysqli_stmt_execute($stmt) ? [true, 'Kendaraan berhasil ditambahkan.'] : [false, mysqli_error($conn)];
    }

    public function update($id, $plat, $jenis, $warna, $pemilik, $idUser)
    {
        global $conn;

        $plat = strtoupper(trim($plat));
        $pemilik = trim($pemilik);

        if (!$this->platValid($plat)) {
            return [false, 'Format plat nomor tidak valid. Gunakan pola seperti "B 1234 ABC": huruf depan maksimal 2, angka maksimal 4, huruf belakang maksimal 3 (boleh lebih pendek).'];
        }
        if (!$this->pemilikValid($pemilik)) {
            return [false, 'Nama pemilik hanya boleh berisi huruf, tidak boleh ada angka.'];
        }

        $stmt = mysqli_prepare($conn, 'SELECT id_kendaraan FROM tb_kendaraan WHERE UPPER(plat_nomor)=UPPER(?) AND id_kendaraan<>?');
        mysqli_stmt_bind_param($stmt, 'si', $plat, $id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Plat nomor sudah dipakai kendaraan lain.'];

        $stmt = mysqli_prepare($conn, 'UPDATE tb_kendaraan SET plat_nomor=?, jenis_kendaraan=?, warna=?, pemilik=?, id_user=? WHERE id_kendaraan=?');
        mysqli_stmt_bind_param($stmt, 'ssssii', $plat, $jenis, $warna, $pemilik, $idUser, $id);
        return mysqli_stmt_execute($stmt) ? [true, 'Data kendaraan berhasil diperbarui.'] : [false, mysqli_error($conn)];
    }

    public function delete($id)
    {
        global $conn;

        // Hapus kendaraan beserta transaksi terkait secara aman dalam satu transaksi.
        // Data area dikembalikan terlebih dahulu untuk transaksi yang masih berstatus "masuk".
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, 'SELECT id_parkir, id_area, status FROM tb_transaksi WHERE id_kendaraan=? FOR UPDATE');
            if (!$stmt) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $rows = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

            foreach ($rows as $row) {
                if ($row['status'] === 'masuk') {
                    $area = mysqli_prepare($conn, 'UPDATE tb_area_parkir SET terisi=GREATEST(terisi-1,0) WHERE id_area=?');
                    if (!$area) throw new Exception(mysqli_error($conn));
                    $areaId = (int)$row['id_area'];
                    mysqli_stmt_bind_param($area, 'i', $areaId);
                    if (!mysqli_stmt_execute($area)) throw new Exception(mysqli_error($conn));
                    mysqli_stmt_close($area);
                }
            }

            $stmt = mysqli_prepare($conn, 'DELETE FROM tb_transaksi WHERE id_kendaraan=?');
            if (!$stmt) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, 'DELETE FROM tb_kendaraan WHERE id_kendaraan=?');
            if (!$stmt) throw new Exception(mysqli_error($conn));
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_error($conn));
            if (mysqli_stmt_affected_rows($stmt) === 0) throw new Exception('Data kendaraan tidak ditemukan.');
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);
            return [true, 'Kendaraan berhasil dihapus beserta transaksi terkait.'];
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            return [false, 'Kendaraan gagal dihapus: '.$e->getMessage()];
        }
    }
}
