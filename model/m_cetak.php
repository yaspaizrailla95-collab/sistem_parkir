<?php
class m_cetak {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function get_detail_parkir($id_parkir) {
    // Query disesuaikan dengan struktur tabel tb_dataparkir
    $query = "SELECT 
                p.*, 
                k.nama_kategori, 
                k.tarif AS tarif_per_jam, 
                u.nama_user AS nama_petugas
              FROM tb_dataparkir p
              LEFT JOIN tb_kendaraan ken ON p.id_kendaraan = ken.id_kendaraan
              LEFT JOIN tb_kategori k ON ken.id_kategori = k.id_kategori
              LEFT JOIN tb_user u ON p.id_user = u.id_user
              WHERE p.id = ?";
    
    $stmt = mysqli_prepare($this->db, $query);
    if (!$stmt) {
        die("Error Prepare Query: " . mysqli_error($this->db));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $id_parkir);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
    }
}
?>