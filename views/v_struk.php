<?php
require_once __DIR__ . '/_helpers.php';
$basePath = base_url();

$isTransaksi = $jenis === 'transaksi';
$namaSistem = $pengaturan['nama_sistem'] ?? 'SiParkir Management';
$alamat = trim($pengaturan['alamat'] ?? '');
$telepon = trim($pengaturan['telepon'] ?? '');

$nomorStruk = $isTransaksi
    ? '#TRX-' . str_pad((string) $struk['id_parkir'], 5, '0', STR_PAD_LEFT)
    : '#PKR-' . str_pad((string) $struk['id_parkir'], 3, '0', STR_PAD_LEFT);

$judul = $isTransaksi ? 'STRUK TRANSAKSI PARKIR' : 'STRUK PARKIR MASUK';
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> <?= e($nomorStruk) ?> - SiParkir</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #eef2f7;
        color: #26364a;
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 30px 16px;
    }
    .toolbar {
        max-width: 340px;
        width: 100%;
        margin: 0 auto 14px;
        display: flex;
        gap: 8px;
    }
    .toolbar a, .toolbar button {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 11px 10px;
        border-radius: 9px;
        border: 0;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        font-family: inherit;
    }
    .toolbar .btn-print { background: #2563eb; color: #fff; }
    .toolbar .btn-print:hover { background: #1d4ed8; }
    .toolbar .btn-back { background: #e2e8f0; color: #334155; }

    .wrap { max-width: 340px; width: 100%; }

    .struk {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 12px 35px rgba(0,0,0,.08);
        padding: 22px 20px;
        font-family: 'Courier New', Courier, monospace;
    }
    .struk-header { text-align: center; margin-bottom: 10px; }
    .struk-header .logo { font-size: 15px; font-weight: 700; letter-spacing: .5px; color: #1f3550; }
    .struk-header .alamat { font-size: 11px; color: #64748b; margin-top: 3px; line-height: 1.5; }
    .garis { border-top: 1px dashed #94a3b8; margin: 12px 0; }
    .judul-struk { text-align: center; font-size: 13px; font-weight: 700; letter-spacing: 1px; margin: 4px 0 2px; }
    .nomor-struk { text-align: center; font-size: 12px; color: #475569; margin-bottom: 4px; }

    .baris { display: flex; justify-content: space-between; gap: 10px; font-size: 12.5px; padding: 3px 0; }
    .baris span:first-child { color: #64748b; }
    .baris span:last-child { text-align: right; font-weight: 700; }

    .total-box {
        margin-top: 10px;
        padding: 10px;
        border-radius: 8px;
        background: #f0f6ff;
        text-align: center;
    }
    .total-box .label { font-size: 11px; color: #2563eb; font-weight: 700; letter-spacing: .5px; }
    .total-box .nilai { font-size: 20px; font-weight: 700; color: #1d4ed8; margin-top: 2px; }

    .catatan { font-size: 11px; color: #64748b; text-align: center; margin-top: 12px; line-height: 1.5; }
    .cap-waktu { font-size: 10px; color: #94a3b8; text-align: center; margin-top: 10px; }

    @media print {
        body { background: #fff; padding: 0; display: block; }
        .toolbar { display: none; }
        .wrap { max-width: 100%; }
        .struk { box-shadow: none; border-radius: 0; padding: 0; }
    }
</style>
</head>
<body>
    <div class="wrap">
        <div class="toolbar">
            <button type="button" class="btn-print" onclick="window.print()">🖨 Cetak Struk</button>
            <a class="btn-back" href="<?= e($basePath . '/controller/c_dataparkir.php?aksi=tampil') ?>">← Kembali</a>
        </div>

        <div class="struk">
            <div class="struk-header">
                <div class="logo"><?= e($namaSistem) ?></div>
                <?php if ($alamat !== ''): ?><div class="alamat"><?= e($alamat) ?></div><?php endif; ?>
                <?php if ($telepon !== ''): ?><div class="alamat">Telp: <?= e($telepon) ?></div><?php endif; ?>
            </div>

            <div class="garis"></div>

            <div class="judul-struk"><?= e($judul) ?></div>
            <div class="nomor-struk">No. <?= e($nomorStruk) ?></div>

            <div class="garis"></div>

            <div class="baris"><span>Plat Nomor</span><span><?= e($struk['plat_nomor']) ?></span></div>
            <div class="baris"><span>Jenis</span><span><?= e(ucfirst($struk['jenis_kendaraan'])) ?></span></div>
            <?php if (!empty($struk['warna'])): ?>
            <div class="baris"><span>Warna</span><span><?= e($struk['warna']) ?></span></div>
            <?php endif; ?>
            <div class="baris"><span>Area Parkir</span><span><?= e($struk['nama_area']) ?></span></div>
            <div class="baris"><span>Waktu Masuk</span><span><?= e(tanggal_id($struk['waktu_masuk'])) ?></span></div>

            <?php if ($isTransaksi): ?>
                <div class="baris"><span>Waktu Keluar</span><span><?= e(tanggal_id($struk['waktu_keluar'])) ?></span></div>
                <div class="baris"><span>Durasi</span><span><?= e($struk['durasi_jam']) ?> jam</span></div>
                <div class="baris"><span>Tarif/Jam</span><span><?= rupiah($struk['tarif_per_jam']) ?></span></div>
            <?php else: ?>
                <div class="baris"><span>Tarif/Jam</span><span><?= rupiah($struk['tarif_per_jam']) ?></span></div>
            <?php endif; ?>

            <div class="baris"><span>Petugas</span><span><?= e($struk['nama_petugas']) ?></span></div>

            <?php if ($isTransaksi): ?>
                <div class="total-box">
                    <div class="label">TOTAL BAYAR</div>
                    <div class="nilai"><?= rupiah($struk['biaya_total']) ?></div>
                </div>
                <div class="catatan">Terima kasih atas kunjungan Anda.<br>Simpan struk ini sebagai bukti pembayaran.</div>
            <?php else: ?>
                <div class="garis"></div>
                <div class="catatan">Simpan struk ini.<br>Tunjukkan struk saat kendaraan keluar area parkir.</div>
            <?php endif; ?>

            <div class="cap-waktu">Dicetak: <?= e(tanggal_id(date('Y-m-d H:i:s'))) ?></div>
        </div>
    </div>
</body>
</html>
