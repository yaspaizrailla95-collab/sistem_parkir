<?php
require_once __DIR__ . '/m_koneksi.php';

class M_Login
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Cek username + password pada tb_user.
     * Hanya akun aktif yang boleh login.
     */
    public function cekLogin($username, $password)
    {
        $sql = "SELECT id_user, nama_lengkap, username, password, role, status_aktif
                FROM tb_user
                WHERE username = ?
                LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$user || (int)$user['status_aktif'] !== 1) {
            return null;
        }

        // Semua data password baru disimpan dengan password_hash().
        if (!password_verify($password, $user['password'])) {
            return null;
        }

        // Upgrade hash otomatis bila kebutuhan cost berubah di masa depan.
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($this->conn, "UPDATE tb_user SET password = ? WHERE id_user = ?");
            if ($update) {
                mysqli_stmt_bind_param($update, 'si', $newHash, $user['id_user']);
                mysqli_stmt_execute($update);
                mysqli_stmt_close($update);
            }
        }

        return $user;
    }

    public function usernameExists($username)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT id_user FROM tb_user WHERE username = ? LIMIT 1");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = $result && $result->num_rows > 0;
        mysqli_stmt_close($stmt);

        return $exists;
    }

    /**
     * Register publik membuat akun petugas aktif.
     * Akun admin tetap dikelola oleh admin melalui menu User.
     */
    public function register($nama, $username, $password)
    {
        if ($this->usernameExists($username)) {
            return [false, 'Username sudah digunakan. Silakan pilih username lain.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'petugas';

        $stmt = mysqli_prepare(
            $this->conn,
            "INSERT INTO tb_user (nama_lengkap, username, password, role, status_aktif)
             VALUES (?, ?, ?, ?, 1)"
        );

        if (!$stmt) {
            return [false, 'Register gagal: query database tidak dapat diproses.'];
        }

        mysqli_stmt_bind_param($stmt, 'ssss', $nama, $username, $hash, $role);
        $ok = mysqli_stmt_execute($stmt);
        $message = $ok
            ? 'Registrasi berhasil. Silakan login dengan akun baru.'
            : 'Register gagal: ' . mysqli_error($this->conn);
        mysqli_stmt_close($stmt);

        return [$ok, $message];
    }
}
