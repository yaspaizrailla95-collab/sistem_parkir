<?php
require_once __DIR__ . '/_auth.php';
require_admin();

require_once __DIR__ . '/../model/m_dashboard.php';
$model=new M_Dashboard();$data=$model->getDataDashboard();

// Aktivitas terbaru untuk widget "Log Akses Aktivitas" di Dashboard Admin.
require_once __DIR__ . '/../model/m_log.php';
$log_terbaru = M_Log::recent(8);

require_once __DIR__ . '/../views/v_dashboard.php';
