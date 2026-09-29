<?php
require_once __DIR__ . '/_auth.php';
require_report_access();

require_once __DIR__ . '/../model/m_riwayat.php';
$model=new M_Riwayat();
$riwayat=$model->all($_GET['q']??'',$_GET['tanggal']??'');
require __DIR__.'/../views/v_riwayat.php';
