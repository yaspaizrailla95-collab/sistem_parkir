<?php
require_once __DIR__ . '/m_koneksi.php';

class M_AreaParkir
{
    public function all($keyword = '')
    {
        global $conn;
        $where = '';
        if (trim($keyword) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($keyword));
            $where = "WHERE a.nama_area LIKE '%$safe%' OR k.nama_kategori LIKE '%$safe%'";
        }
        $sql = "SELECT a.id_area, a.nama_area, a.kapasitas, a.terisi, a.id_kategori, k.nama_kategori
                FROM tb_area_parkir a
                LEFT JOIN tb_kategori k ON k.id_kategori = a.id_kategori
                $where
                ORDER BY a.nama_area";
        $q = mysqli_query($conn, $sql);
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    public function find($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT * FROM tb_area_parkir WHERE id_area=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt)->fetch_assoc();
    }

    public function kategoriList()
    {
        global $conn;
        $q = mysqli_query($conn, 'SELECT id_kategori, nama_kategori FROM tb_kategori ORDER BY nama_kategori');
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    public function add($namaArea, $kapasitas, $idKategori)
    {
        global $conn;
        $namaArea = trim($namaArea);
        if ($namaArea === '') return [false, 'Nama area wajib diisi.'];
        if (!is_numeric($kapasitas) || (int) $kapasitas <= 0) return [false, 'Kapasitas harus berupa angka lebih dari 0.'];

        $kapasitas = (int) $kapasitas;
        $idKategori = $idKategori ? (int) $idKategori : null;

        $stmt = mysqli_prepare($conn, 'SELECT id_area FROM tb_area_parkir WHERE LOWER(nama_area)=LOWER(?)');
        mysqli_stmt_bind_param($stmt, 's', $namaArea);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Nama area sudah ada.'];

        $stmt = mysqli_prepare($conn, 'INSERT INTO tb_area_parkir (id_kategori, nama_area, kapasitas, terisi) VALUES (?,?,?,0)');
        mysqli_stmt_bind_param($stmt, 'isi', $idKategori, $namaArea, $kapasitas);
        return mysqli_stmt_execute($stmt) ? [true, 'Area parkir berhasil ditambahkan.'] : [false, mysqli_error($conn)];
    }

    public function update($id, $namaArea, $kapasitas, $idKategori)
    {
        global $conn;
        $namaArea = trim($namaArea);
        if ($namaArea === '') return [false, 'Nama area wajib diisi.'];
        if (!is_numeric($kapasitas) || (int) $kapasitas <= 0) return [false, 'Kapasitas harus berupa angka lebih dari 0.'];

        $kapasitas = (int) $kapasitas;
        $idKategori = $idKategori ? (int) $idKategori : null;

        $current = $this->find($id);
        if (!$current) return [false, 'Data area tidak ditemukan.'];
        if ($kapasitas < (int) $current['terisi']) {
            return [false, 'Kapasitas tidak boleh lebih kecil dari jumlah kendaraan yang sedang terisi saat ini (' . (int) $current['terisi'] . ').'];
        }

        $stmt = mysqli_prepare($conn, 'SELECT id_area FROM tb_area_parkir WHERE LOWER(nama_area)=LOWER(?) AND id_area<>?');
        mysqli_stmt_bind_param($stmt, 'si', $namaArea, $id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Nama area sudah dipakai.'];

        $stmt = mysqli_prepare($conn, 'UPDATE tb_area_parkir SET nama_area=?, kapasitas=?, id_kategori=? WHERE id_area=?');
        mysqli_stmt_bind_param($stmt, 'siii', $namaArea, $kapasitas, $idKategori, $id);
        return mysqli_stmt_execute($stmt) ? [true, 'Area parkir berhasil diperbarui.'] : [false, mysqli_error($conn)];
    }

    public function delete($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) c FROM tb_transaksi WHERE id_area=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        if ((int) mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] > 0) {
            return [false, 'Area parkir tidak dapat dihapus karena masih memiliki riwayat transaksi.'];
        }
        $stmt = mysqli_prepare($conn, 'DELETE FROM tb_area_parkir WHERE id_area=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt) ? [true, 'Area parkir berhasil dihapus.'] : [false, mysqli_error($conn)];
    }
}
