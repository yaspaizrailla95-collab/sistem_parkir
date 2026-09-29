<?php
require_once __DIR__ . '/m_koneksi.php';

class M_Log
{
    /**
     * Catat satu baris aktivitas ke tb_log_aktivitas.
     * Dipanggil dari controller mana pun setiap ada aksi penting
     * (login, logout, input transaksi, kelola data master, dll).
     */
    public static function catat($idUser, $aktivitas)
    {
        global $conn;

        $idUser = (int) $idUser;
        if ($idUser <= 0 || trim($aktivitas) === '') {
            return;
        }

        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES (?, ?, NOW())'
        );
        if (!$stmt) {
            return;
        }

        mysqli_stmt_bind_param($stmt, 'is', $idUser, $aktivitas);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Ambil daftar log aktivitas beserta nama & role pelakunya.
     * Bisa difilter berdasarkan kata kunci, tanggal, dan role.
     */
    public function all($keyword = '', $tanggal = '', $role = '')
    {
        global $conn;

        $where = '1=1';

        if (trim($keyword) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($keyword));
            $where .= " AND (l.aktivitas LIKE '%$safe%' OR u.nama_lengkap LIKE '%$safe%' OR u.username LIKE '%$safe%')";
        }

        if (trim($tanggal) !== '') {
            $safe = mysqli_real_escape_string($conn, trim($tanggal));
            $where .= " AND DATE(l.waktu_aktivitas) = '$safe'";
        }

        if (in_array($role, ['admin', 'petugas', 'owner'], true)) {
            $where .= " AND u.role = '$role'";
        }

        $sql = "SELECT l.id_log, l.aktivitas, l.waktu_aktivitas, u.nama_lengkap, u.username, u.role
                FROM tb_log_aktivitas l
                JOIN tb_user u ON u.id_user = l.id_user
                WHERE $where
                ORDER BY l.waktu_aktivitas DESC
                LIMIT 300";

        $q = mysqli_query($conn, $sql);
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }

    /**
     * Ambil beberapa aktivitas terbaru saja (tanpa filter).
     * Dipakai untuk widget "Aktivitas Terbaru" di Dashboard Admin.
     */
    public static function recent($limit = 8)
    {
        global $conn;

        $limit = (int) $limit;
        if ($limit <= 0) {
            $limit = 8;
        }

        $sql = "SELECT l.id_log, l.aktivitas, l.waktu_aktivitas, u.nama_lengkap, u.username, u.role
                FROM tb_log_aktivitas l
                JOIN tb_user u ON u.id_user = l.id_user
                ORDER BY l.waktu_aktivitas DESC
                LIMIT $limit";

        $q = mysqli_query($conn, $sql);
        return $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    }
}
