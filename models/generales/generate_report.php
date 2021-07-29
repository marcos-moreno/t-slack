<?php
/**  @author Marcos Moreno   */  
require_once '../../lib/phpclient/autoload.php'; 
require_once "../auth/check.php"; 
use Jaspersoft\Client\Client; 

$objReport = null;
if (check_session()) { 
    $objReport = new generate_report();
    $objReport -> showReport(); 
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
class generate_report
{ 
    public function showReport()
    {     
        try {
            $params = array('ev_puesto_id'=>14);
            $c = new Client("http://67.205.162.138:51541/jasperserver", "DesarrolloAdmin", "Dev_JasperSoft#20");
            $report = $c->reportService()->runReport('/reports/Encuestas/PerfilPuesto', 'pdf', null, null, $params);
            header('Content-Type: application/pdf'); 
            echo $report;
        } catch (\Throwable $th) { 
            echo  $th;
        }
      
    }
}