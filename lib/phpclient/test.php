<?php

require_once __DIR__ . "/autoload.php";

use Jaspersoft\Client\Client;

$c = new Client(
        "http://localhost:8080/jasperserver", "jasperadmin", "jasperadmin"
);

$controls = array(
    'CodigoSocio' => '6739'
);


$report = $c->reportService()->runReport('/reports/Saldos', 'html', null, null, $controls);
echo $report;


//$report = $c->reportService()->runReport('/reports/Saldos', 'pdf', null, null, $controls);
//header('Cache-Control: must-revalidate');
//header('Pragma: public');
//header('Content-Description: File Transfer');
//header('Content-Disposition: attachment; filename=report.pdf');
//header('Content-Transfer-Encoding: binary');
//header('Content-Length: ' . strlen($report));
//header('Content-Type: application/pdf');
//echo $report;