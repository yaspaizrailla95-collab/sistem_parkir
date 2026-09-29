<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_kendaraan.php';
require_once __DIR__ . '/../model/m_log.php';
$model=new M_Kendaraan();$aksi=$_GET['aksi']??'tampil';
function kembali_kendaraan($msg=''){ if($msg!=='') $_SESSION['flash']=$msg; header('Location: c_kendaraan.php?aksi=tampil'); exit; }
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
if($aksi==='tampil'){ $kendaraan=$model->all($_GET['q']??''); require __DIR__.'/../views/v_kendaraan.php'; }
elseif($aksi==='tambah'){ $users=$model->users(); require __DIR__.'/../views/v_tambah_kendaraan.php'; }
elseif($aksi==='proses_tambah'&&$_SERVER['REQUEST_METHOD']==='POST'){$plat=strtoupper(trim($_POST['plat_nomor']));[$ok,$msg]=$model->add($plat,$_POST['jenis_kendaraan'],trim($_POST['warna']),trim($_POST['pemilik']), (int)$_POST['id_user']); if($ok)M_Log::catat($_SESSION['id_user']??0,'Menambahkan kendaraan '.$plat); kembali_kendaraan($msg);}
elseif($aksi==='edit'){ $data=$model->find((int)$_GET['id']); if(!$data) kembali_kendaraan('Data kendaraan tidak ditemukan.'); $users=$model->users(); require __DIR__.'/../views/v_edit_kendaraan.php'; }
elseif($aksi==='proses_edit'&&$_SERVER['REQUEST_METHOD']==='POST'){$plat=strtoupper(trim($_POST['plat_nomor']));[$ok,$msg]=$model->update((int)$_POST['id_kendaraan'],$plat,$_POST['jenis_kendaraan'],trim($_POST['warna']),trim($_POST['pemilik']),(int)$_POST['id_user']); if($ok)M_Log::catat($_SESSION['id_user']??0,'Mengedit kendaraan '.$plat); kembali_kendaraan($msg);}
elseif($aksi==='hapus'){ $data=$model->find((int)$_GET['id']); $plat=$data['plat_nomor']??('#'.(int)$_GET['id']); [$ok,$msg]=$model->delete((int)$_GET['id']); if($ok)M_Log::catat($_SESSION['id_user']??0,'Menghapus kendaraan '.$plat); kembali_kendaraan($msg);}
else kembali_kendaraan();
