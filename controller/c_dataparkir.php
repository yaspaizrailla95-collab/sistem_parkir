<?php
require_once __DIR__ . '/_auth.php';
require_staff();

require_once __DIR__ . '/../model/m_dataparkir.php';
require_once __DIR__ . '/../model/m_log.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
$model=new M_DataParkir();$aksi=$_GET['aksi']??'tampil';
function kembali_parkir($msg=''){if($msg!=='')$_SESSION['flash']=$msg;header('Location: c_dataparkir.php?aksi=tampil');exit;}
if($aksi==='tampil'){ $parkir=$model->active($_GET['q']??'');require __DIR__.'/../views/v_dataparkir.php';}
elseif($aksi==='tambah'){ $kendaraan=$model->vehicles();$area=$model->areas();$tarif=$model->tariffs();require __DIR__.'/../views/v_masuk_parkir.php';}
elseif($aksi==='proses_masuk'&&$_SERVER['REQUEST_METHOD']==='POST'){
    $idUser=isset($_SESSION['id_user'])?(int)$_SESSION['id_user']:1;
    $idKendaraan=(int)$_POST['id_kendaraan'];
    [$ok,$msg,$idParkirBaru]=$model->enter($idKendaraan,(int)$_POST['id_tarif'],(int)$_POST['id_area'],$idUser);
    if($ok){
        M_Log::catat($idUser,'Input kendaraan masuk '.$model->platByKendaraan($idKendaraan));
    }
    // Struk tidak lagi dicetak saat kendaraan baru masuk. Petugas hanya bisa
    // mencetak struk setelah kendaraan diproses keluar (lihat aksi 'keluar').
    kembali_parkir($msg);
}
elseif($aksi==='keluar'){
    $idUser=isset($_SESSION['id_user'])?(int)$_SESSION['id_user']:1;
    $idParkir=(int)$_GET['id'];
    $plat=$model->platByParkir($idParkir);
    [$ok,$msg]=$model->checkout($idParkir);
    if($ok){
        M_Log::catat($idUser,'Proses pembayaran parkir '.$plat);
        // Langsung arahkan ke struk transaksi/pembayaran supaya petugas bisa cetak.
        header('Location: c_struk.php?id='.$idParkir.'&jenis=transaksi');
        exit;
    }
    kembali_parkir($msg);
}
else kembali_parkir();
