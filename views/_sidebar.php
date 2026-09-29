<?php
require_once __DIR__ . '/_helpers.php';
$current_page = $current_page_override ?? basename($_SERVER['PHP_SELF']);
$basePath = base_url();
$role = $_SESSION['role'] ?? '';

if ($role === 'petugas') {
    // Petugas hanya melihat menu operasional (tanpa kelola data master & user).
    $menus=[
     ['v_dashboard_petugas.php','⌂','Dashboard','controller/c_dashboard_petugas.php'],
     ['v_dataparkir.php','▣','Data Parkir','controller/c_dataparkir.php?aksi=tampil'],
     ['v_riwayat.php','◷','Riwayat','controller/c_riwayat.php'],
    ];
} elseif ($role === 'owner') {
    // Owner hanya melihat laporan (tanpa input transaksi / kelola data).
    $menus=[
     ['v_dashboard_owner.php','⌂','Dashboard','controller/c_dashboard_owner.php'],
     ['v_rekap_transaksi.php','▤','Rekap Transaksi','controller/c_rekap_transaksi.php'],
     ['v_riwayat.php','◷','Riwayat','controller/c_riwayat.php'],
    ];
} else {
    $menus=[
     ['v_dashboard.php','⌂','Dashboard','controller/c_dashboard.php'],
     ['v_kendaraan.php','🚗','Kendaraan','controller/c_kendaraan.php?aksi=tampil'],
     ['v_kategori.php','▤','Kategori','controller/c_kategori.php?aksi=tampil'],
     ['v_area_parkir.php','🅿','Area Parkir','controller/c_area_parkir.php?aksi=tampil'],
     ['v_tarif.php','💲','Tarif Parkir','controller/c_tarif.php?aksi=tampil'],
     ['v_dataparkir.php','▣','Data Parkir','controller/c_dataparkir.php?aksi=tampil'],
     ['v_riwayat.php','◷','Riwayat','controller/c_riwayat.php'],
     ['v_log.php','🕘','Log Aktivitas','controller/c_log.php'],
     ['v_user.php','👤','User','controller/c_user.php?aksi=tampil'],
     ['v_pengaturan.php','⚙','Pengaturan','controller/c_pengaturan.php?aksi=tampil'],
    ];
}
?>
<aside class="sidebar">
    <div>
        <div class="logo"><img class="logo-icon" src="<?=e($basePath . '/asset/img/logo-icon.png')?>" alt="Logo SiParkir"><div><h2>SiParkir</h2><span>Parking System</span></div></div>
        <nav class="menu">
        <?php foreach($menus as [$page,$icon,$label,$url]): ?><a href="<?=e($basePath.'/'.$url)?>" class="menu-item <?=$current_page===$page?'active':''?>"><span class="icon"><?=e($icon)?></span><span><?=e($label)?></span></a><?php endforeach; ?>
        </nav>
    </div>
    <div class="sidebar-bottom"><div class="status-box"><span class="status-dot"></span><div><strong>Sistem Aktif</strong><small><?=e($_SESSION['username'] ?? 'Admin')?></small></div></div><a href="<?=e($basePath . '/controller/c_logout.php')?>" style="display:block;margin-top:12px;text-align:center;text-decoration:none;padding:10px;border-radius:10px;background:#eef4fb;color:#3978c7;font-size:12px;font-weight:700;">Keluar</a></div>
</aside>
