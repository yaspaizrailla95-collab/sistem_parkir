<?php
class LogModel {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getAllLogs() {
        $query = "SELECT l.id_log, u.username, l.aktivitas, l.waktu_aktivitas 
                  FROM tb_log_aktifitas l 
                  JOIN tb_user u ON l.id_user = u.id_user 
                  ORDER BY l.waktu_aktivitas DESC";
        
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}