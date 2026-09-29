<?php

require_once __DIR__ . '/m_koneksi.php';

class M_Dashboard
{
    public function getDataDashboard()
    {
        global $conn;

        // =========================
        // TOTAL KENDARAAN TERDAFTAR
        // =========================
        $total_kendaraan = 0;
        $q1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_kendaraan");
        if ($q1 && $row = mysqli_fetch_assoc($q1)) {
            $total_kendaraan = (int) $row['total'];
        }

        // =========================
        // KENDARAAN MASUK HARI INI
        // =========================
        $kendaraan_masuk = 0;
        $q2 = mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM tb_transaksi WHERE DATE(waktu_masuk) = CURDATE()"
        );
        if ($q2 && $row = mysqli_fetch_assoc($q2)) {
            $kendaraan_masuk = (int) $row['total'];
        }

        // =========================
        // KENDARAAN KELUAR HARI INI
        // =========================
        $kendaraan_keluar = 0;
        $q3 = mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM tb_transaksi WHERE status = 'keluar' AND DATE(waktu_keluar) = CURDATE()"
        );
        if ($q3 && $row = mysqli_fetch_assoc($q3)) {
            $kendaraan_keluar = (int) $row['total'];
        }

        // =========================
        // SLOT TERSEDIA (kapasitas - terisi, semua area)
        // =========================
        $slot_tersedia = 0;
        $q4 = mysqli_query(
            $conn,
            "SELECT COALESCE(SUM(kapasitas - terisi), 0) AS sisa FROM tb_area_parkir"
        );
        if ($q4 && $row = mysqli_fetch_assoc($q4)) {
            $slot_tersedia = (int) $row['sisa'];
        }

        // =========================
        // STATUS PARKIR PER AREA
        // =========================
        $status_area = [];
        $palet = [
            ['warna' => 'blue',   'bg_icon' => 'blue-bg'],
            ['warna' => 'green',  'bg_icon' => 'green-bg'],
            ['warna' => 'yellow', 'bg_icon' => 'yellow-bg'],
        ];

        $q5 = mysqli_query(
            $conn,
            "SELECT id_area, nama_area, kapasitas, terisi FROM tb_area_parkir ORDER BY id_area ASC"
        );

        if ($q5) {
            $i = 0;
            while ($row = mysqli_fetch_assoc($q5)) {
                $kapasitas = (int) $row['kapasitas'];
                $terisi = (int) $row['terisi'];
                $persen = $kapasitas > 0 ? round(($terisi / $kapasitas) * 100) : 0;
                $warna = $palet[$i % count($palet)];

                $status_area[] = [
                    'nama'    => $row['nama_area'],
                    'terisi'  => $terisi,
                    'total'   => $kapasitas,
                    'persen'  => $persen,
                    'warna'   => $warna['warna'],
                    'bg_icon' => $warna['bg_icon'],
                ];
                $i++;
            }
        }

        return [
            'total_kendaraan'  => $total_kendaraan,
            'kendaraan_masuk'  => $kendaraan_masuk,
            'kendaraan_keluar' => $kendaraan_keluar,
            'slot_tersedia'    => $slot_tersedia,
            'status_area'      => $status_area,
        ];
    }

    /**
     * Data ringkasan untuk Dashboard Owner: fokus ke performa
     * pendapatan & okupansi, bukan ke input transaksi harian.
     */
    public function getDataOwner()
    {
        global $conn;

        // Data operasional dasar dipakai ulang dari dashboard Admin.
        $dasar = $this->getDataDashboard();

        // =========================
        // PENDAPATAN HARI INI
        // =========================
        $pendapatan_hari_ini = 0;
        $q1 = mysqli_query(
            $conn,
            "SELECT COALESCE(SUM(biaya_total),0) AS total FROM tb_transaksi WHERE status='keluar' AND DATE(waktu_keluar) = CURDATE()"
        );
        if ($q1 && $row = mysqli_fetch_assoc($q1)) {
            $pendapatan_hari_ini = (float) $row['total'];
        }

        // =========================
        // PENDAPATAN BULAN INI
        // =========================
        $pendapatan_bulan_ini = 0;
        $q2 = mysqli_query(
            $conn,
            "SELECT COALESCE(SUM(biaya_total),0) AS total FROM tb_transaksi WHERE status='keluar' AND MONTH(waktu_keluar)=MONTH(CURDATE()) AND YEAR(waktu_keluar)=YEAR(CURDATE())"
        );
        if ($q2 && $row = mysqli_fetch_assoc($q2)) {
            $pendapatan_bulan_ini = (float) $row['total'];
        }

        // =========================
        // TRANSAKSI SELESAI HARI INI
        // =========================
        $transaksi_selesai = 0;
        $q3 = mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM tb_transaksi WHERE status='keluar' AND DATE(waktu_keluar) = CURDATE()"
        );
        if ($q3 && $row = mysqli_fetch_assoc($q3)) {
            $transaksi_selesai = (int) $row['total'];
        }

        // =========================
        // TREN PENDAPATAN 7 HARI TERAKHIR
        // =========================
        $tren_pendapatan = [];
        $q4 = mysqli_query(
            $conn,
            "SELECT DATE(waktu_keluar) AS tgl, COALESCE(SUM(biaya_total),0) AS total
             FROM tb_transaksi
             WHERE status='keluar' AND waktu_keluar >= (CURDATE() - INTERVAL 6 DAY)
             GROUP BY DATE(waktu_keluar)"
        );
        $perTanggal = [];
        if ($q4) {
            while ($row = mysqli_fetch_assoc($q4)) {
                $perTanggal[$row['tgl']] = (float) $row['total'];
            }
        }
        $maxHarian = max([1, ...array_values($perTanggal)]);
        for ($i = 6; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i day"));
            $total = $perTanggal[$tgl] ?? 0;
            $tren_pendapatan[] = [
                'label'  => date('d/m', strtotime($tgl)),
                'total'  => $total,
                'persen' => $maxHarian > 0 ? round(($total / $maxHarian) * 100) : 0,
            ];
        }

        // =========================
        // PENDAPATAN PER JENIS KENDARAAN (BULAN INI)
        // =========================
        $per_jenis = [];
        $q5 = mysqli_query(
            $conn,
            "SELECT k.jenis_kendaraan, COUNT(*) AS jumlah, COALESCE(SUM(t.biaya_total),0) AS total
             FROM tb_transaksi t
             JOIN tb_kendaraan k ON k.id_kendaraan = t.id_kendaraan
             WHERE t.status='keluar' AND MONTH(t.waktu_keluar)=MONTH(CURDATE()) AND YEAR(t.waktu_keluar)=YEAR(CURDATE())
             GROUP BY k.jenis_kendaraan
             ORDER BY total DESC"
        );
        if ($q5) {
            while ($row = mysqli_fetch_assoc($q5)) {
                $per_jenis[] = [
                    'jenis'  => $row['jenis_kendaraan'],
                    'jumlah' => (int) $row['jumlah'],
                    'total'  => (float) $row['total'],
                ];
            }
        }

        return array_merge($dasar, [
            'pendapatan_hari_ini'  => $pendapatan_hari_ini,
            'pendapatan_bulan_ini' => $pendapatan_bulan_ini,
            'transaksi_selesai'    => $transaksi_selesai,
            'tren_pendapatan'      => $tren_pendapatan,
            'per_jenis'            => $per_jenis,
        ]);
    }

    /**
     * Rekap transaksi untuk rentang tanggal yang diminta Owner
     * (dari form filter di Dashboard Owner: tanggal mulai s/d selesai).
     * Dipakai untuk fitur "Rekap Transaksi" yang bisa dipilih periodenya.
     */
    public function getRekapPeriode($mulai, $selesai)
    {
        global $conn;

        // =========================
        // TOTAL PENDAPATAN & JUMLAH TRANSAKSI SELESAI PADA PERIODE
        // =========================
        $total_pendapatan = 0.0;
        $jumlah_transaksi = 0;
        $stmt = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS jumlah, COALESCE(SUM(biaya_total),0) AS total
             FROM tb_transaksi
             WHERE status='keluar' AND DATE(waktu_keluar) BETWEEN ? AND ?"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $mulai, $selesai);
        mysqli_stmt_execute($stmt);
        if ($row = mysqli_stmt_get_result($stmt)->fetch_assoc()) {
            $jumlah_transaksi = (int) $row['jumlah'];
            $total_pendapatan = (float) $row['total'];
        }
        mysqli_stmt_close($stmt);

        // =========================
        // KENDARAAN MASUK PADA PERIODE (berdasarkan waktu_masuk)
        // =========================
        $kendaraan_masuk = 0;
        $stmt = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS jumlah FROM tb_transaksi WHERE DATE(waktu_masuk) BETWEEN ? AND ?"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $mulai, $selesai);
        mysqli_stmt_execute($stmt);
        if ($row = mysqli_stmt_get_result($stmt)->fetch_assoc()) {
            $kendaraan_masuk = (int) $row['jumlah'];
        }
        mysqli_stmt_close($stmt);

        $rata_rata = $jumlah_transaksi > 0 ? $total_pendapatan / $jumlah_transaksi : 0.0;

        // =========================
        // PENDAPATAN PER JENIS KENDARAAN PADA PERIODE
        // =========================
        $per_jenis = [];
        $stmt = mysqli_prepare(
            $conn,
            "SELECT k.jenis_kendaraan, COUNT(*) AS jumlah, COALESCE(SUM(t.biaya_total),0) AS total
             FROM tb_transaksi t
             JOIN tb_kendaraan k ON k.id_kendaraan = t.id_kendaraan
             WHERE t.status='keluar' AND DATE(t.waktu_keluar) BETWEEN ? AND ?
             GROUP BY k.jenis_kendaraan
             ORDER BY total DESC"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $mulai, $selesai);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = $res->fetch_assoc()) {
            $per_jenis[] = [
                'jenis'  => $row['jenis_kendaraan'],
                'jumlah' => (int) $row['jumlah'],
                'total'  => (float) $row['total'],
            ];
        }
        mysqli_stmt_close($stmt);

        return [
            'mulai'            => $mulai,
            'selesai'          => $selesai,
            'total_pendapatan' => $total_pendapatan,
            'jumlah_transaksi' => $jumlah_transaksi,
            'kendaraan_masuk'  => $kendaraan_masuk,
            'rata_rata'        => $rata_rata,
            'per_jenis'        => $per_jenis,
        ];
    }
}
