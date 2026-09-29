<?php
require_once __DIR__ . '/m_koneksi.php';

class M_Kategori
{
    public function all($keyword='')
    {
        global $conn;
        $where = '';
        if (trim($keyword) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($keyword));
            $where = "WHERE k.nama_kategori LIKE '%$safe%'";
        }
        $sql = "SELECT k.id_kategori, k.nama_kategori, COUNT(a.id_area) AS jumlah_area
                FROM tb_kategori k LEFT JOIN tb_area_parkir a ON a.id_kategori=k.id_kategori
                $where GROUP BY k.id_kategori, k.nama_kategori ORDER BY k.id_kategori DESC";
        $q = mysqli_query($conn, $sql);
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    public function find($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT * FROM tb_kategori WHERE id_kategori=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt)->fetch_assoc();
    }

    public function add($nama)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT id_kategori FROM tb_kategori WHERE LOWER(nama_kategori)=LOWER(?)');
        mysqli_stmt_bind_param($stmt, 's', $nama);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Nama kategori sudah ada.'];
        $stmt = mysqli_prepare($conn, 'INSERT INTO tb_kategori (nama_kategori) VALUES (?)');
        mysqli_stmt_bind_param($stmt, 's', $nama);
        return mysqli_stmt_execute($stmt) ? [true,'Kategori berhasil ditambahkan.'] : [false,mysqli_error($conn)];
    }

    public function update($id, $nama)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT id_kategori FROM tb_kategori WHERE LOWER(nama_kategori)=LOWER(?) AND id_kategori<>?');
        mysqli_stmt_bind_param($stmt, 'si', $nama, $id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) return [false, 'Nama kategori sudah dipakai.'];
        $stmt = mysqli_prepare($conn, 'UPDATE tb_kategori SET nama_kategori=? WHERE id_kategori=?');
        mysqli_stmt_bind_param($stmt, 'si', $nama, $id);
        return mysqli_stmt_execute($stmt) ? [true,'Kategori berhasil diperbarui.'] : [false,mysqli_error($conn)];
    }

    public function delete($id)
    {
        global $conn;
        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) c FROM tb_area_parkir WHERE id_kategori=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        if ((int)mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] > 0) return [false,'Kategori tidak dapat dihapus karena masih digunakan area parkir.'];
        $stmt = mysqli_prepare($conn, 'DELETE FROM tb_kategori WHERE id_kategori=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt) ? [true,'Kategori berhasil dihapus.'] : [false,mysqli_error($conn)];
    }
}
