<?php
require_once __DIR__ . '/m_koneksi.php';

class M_Tarif
{
    private $jenisValid = ['motor', 'mobil', 'lainnya'];

    public function all($keyword = '')
    {
        global $conn;
        $where = '';
        if (trim($keyword) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($keyword));
            $where = "WHERE t.jenis_kendaraan LIKE '%$safe%'";
        }
        $sql = "SELECT t.id_tarif, t.jenis_kendaraan, t.tarif_per_jam,
                    (SELECT COUNT(*) FROM tb_transaksi x WHERE x.id_tarif = t.id_tarif) AS jumlah_dipakai
                FROM tb_tarif t
                $where
                ORDER BY t.jenis_kendaraan, t.tarif_per_jam";
        $q = mysqli_query($conn, $sql);
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    public function find($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT * FROM tb_tarif WHERE id_tarif=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt)->fetch_assoc();
    }

    private function validasi($jenis, $tarifPerJam)
    {
        if (!in_array($jenis, $this->jenisValid, true)) {
            return 'Jenis kendaraan tidak valid.';
        }
        if (!is_numeric($tarifPerJam) || (float) $tarifPerJam <= 0) {
            return 'Tarif per jam harus berupa angka lebih dari 0.';
        }
        return '';
    }

    public function add($jenis, $tarifPerJam)
    {
        global $conn;
        $error = $this->validasi($jenis, $tarifPerJam);
        if ($error !== '') return [false, $error];

        $tarifPerJam = (float) $tarifPerJam;
        $stmt = mysqli_prepare($conn, 'INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam) VALUES (?,?)');
        mysqli_stmt_bind_param($stmt, 'sd', $jenis, $tarifPerJam);
        return mysqli_stmt_execute($stmt) ? [true, 'Tarif berhasil ditambahkan.'] : [false, mysqli_error($conn)];
    }

    public function update($id, $jenis, $tarifPerJam)
    {
        global $conn;
        $error = $this->validasi($jenis, $tarifPerJam);
        if ($error !== '') return [false, $error];

        $tarifPerJam = (float) $tarifPerJam;
        $stmt = mysqli_prepare($conn, 'UPDATE tb_tarif SET jenis_kendaraan=?, tarif_per_jam=? WHERE id_tarif=?');
        mysqli_stmt_bind_param($stmt, 'sdi', $jenis, $tarifPerJam, $id);
        return mysqli_stmt_execute($stmt) ? [true, 'Tarif berhasil diperbarui.'] : [false, mysqli_error($conn)];
    }

    public function delete($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) c FROM tb_transaksi WHERE id_tarif=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        if ((int) mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] > 0) {
            return [false, 'Tarif tidak dapat dihapus karena masih dipakai pada data transaksi.'];
        }
        $stmt = mysqli_prepare($conn, 'DELETE FROM tb_tarif WHERE id_tarif=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt) ? [true, 'Tarif berhasil dihapus.'] : [false, mysqli_error($conn)];
    }
}
