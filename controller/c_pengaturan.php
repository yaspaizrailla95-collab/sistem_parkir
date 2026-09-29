<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_pengaturan.php';
require_once __DIR__ . '/../model/m_log.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
$model=new M_Pengaturan();$aksi=$_GET['aksi']??'tampil';
if($aksi==='simpan'&&$_SERVER['REQUEST_METHOD']==='POST'){
 $d=$_POST;$d['area_ids']=$_POST['area_ids']??[];$d['kapasitas']=$_POST['kapasitas']??[];$d['tarif_ids']=$_POST['tarif_ids']??[];$d['tarif_values']=$_POST['tarif_values']??[];$d['toleransi_menit']=(int)($_POST['toleransi_menit']??10);$d['password']=trim($_POST['password']??'');
 [$ok,$msg]=$model->save($d); if($ok)M_Log::catat($_SESSION['id_user']??0,'Memperbarui pengaturan sistem'); $_SESSION['flash']=$msg;header('Location: c_pengaturan.php?aksi=tampil');exit;
}
$config=$model->get();$area=$model->areas();$tarif=$model->tariffs();require __DIR__.'/../views/v_pengaturan.php';
