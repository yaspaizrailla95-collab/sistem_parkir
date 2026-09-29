<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_log.php';
$model = new M_Log();
$log = $model->all($_GET['q'] ?? '', $_GET['tanggal'] ?? '', $_GET['role'] ?? '');
require __DIR__ . '/../views/v_log.php';
