<?php
/**  @author Marcos Moreno   */  
require_once '../../lib/phpclient/autoload.php'; 
require_once "../auth/check.php"; 
use Jaspersoft\Client\Client; 
$received_data = json_decode(file_get_contents('php://input')); 

if (check_session()) { 
    $objReport = new generate_report($received_data);
    $objReport -> showReport(); 
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
class generate_report
{
    private $received_data = array();

    public function __construct($received_data){ 
        $this->received_data = $received_data;
    }

    public function showReport()
    {
        try {
            $parameters = (array) $this->received_data->params;
            $c = new Client("http://67.205.162.138:51541/jasperserver", "DesarrolloAdmin", "Dev_JasperSoft#20");
            $report = $c->reportService()->runReport('/reports/Encuestas/'.$this->received_data->name_report
            , 'pdf', null, null,$parameters);
            print(base64_encode($report));
        } catch (\Throwable $th) {
            echo  $th;
        }
    }
}