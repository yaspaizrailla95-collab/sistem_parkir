<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_kategori.php';
require_once __DIR__ . '/../model/m_log.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
$model=new M_Kategori();$aksi=$_GET['aksi']??'tampil';
function kembali_kategori($msg=''){if($msg!=='')$_SESSION['flash']=$msg;header('Location: c_kategori.php?aksi=tampil');exit;}
if($aksi==='tampil'){ $kategori=$model->all($_GET['q']??''); require __DIR__.'/../views/v_kategori.php'; }
elseif($aksi==='tambah'){require __DIR__.'/../views/v_tambah_kategori.php';}
elseif($aksi==='proses_tambah'&&$_SERVER['REQUEST_METHOD']==='POST'){ $nama=trim($_POST['nama_kategori']); if($nama==='') kembali_kategori('Nama kategori wajib diisi.');[$ok,$msg]=$model->add($nama); if($ok)M_Log::catat($_SESSION['id_user']??0,'Menambahkan kategori '.$nama); kembali_kategori($msg);}
elseif($aksi==='edit'){ $data=$model->find((int)$_GET['id']);if(!$data)kembali_kategori('Data kategori tidak ditemukan.');require __DIR__.'/../views/v_edit_kategori.php';}
elseif($aksi==='proses_edit'&&$_SERVER['REQUEST_METHOD']==='POST'){ $nama=trim($_POST['nama_kategori']);[$ok,$msg]=$model->update((int)$_POST['id_kategori'],$nama); if($ok)M_Log::catat($_SESSION['id_user']??0,'Mengedit kategori '.$nama); kembali_kategori($msg);}
elseif($aksi==='hapus'){ $data=$model->find((int)$_GET['id']); $nama=$data['nama_kategori']??('#'.(int)$_GET['id']); [$ok,$msg]=$model->delete((int)$_GET['id']); if($ok)M_Log::catat($_SESSION['id_user']??0,'Menghapus kategori '.$nama); kembali_kategori($msg);}
else kembali_kategori();
