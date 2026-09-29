<?php
require_once __DIR__ . '/_auth.php';
require_petugas();

require_once __DIR__ . '/../model/m_dashboard.php';
$model = new M_Dashboard();
$data = $model->getDataDashboard();
require_once __DIR__ . '/../views/v_dashboard_petugas.php';
