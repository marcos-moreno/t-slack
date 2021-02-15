<?php
/**  @author Marcos Moreno   */  
require_once '../lib/phpclient/autoload.php'; 
require_once "auth/check.php"; 
use Jaspersoft\Client\Client; 

$objReport = null;
if (check_session()) { 
    $objReport = new generate_report();

    if(isset($_GET['uniformes'])){
        $objReport -> showReport_informe(); 
    }else{
        $objReport -> showReport(); 
    }
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
class generate_report
{ 
    public function showReport()
    {     
        try {
            $name_report = (isset($_GET['name_report'])?$_GET['name_report']:'');
            $id_empleado = (isset($_GET['id_empleado'])?$_GET['id_empleado']:0);
            $id_encuesta = (isset($_GET['id_encuesta'])?$_GET['id_encuesta']:0);
            $id_segmento = (isset($_GET['id_segmento'])?$_GET['id_segmento']:0);
            $id_empresa = (isset($_GET['id_empresa'])?$_GET['id_empresa']:0); 
            $realizadas = (isset($_GET['realizadas'])?$_GET['realizadas']:0);
            $num_mes = (isset($_GET['num_mes'])?$_GET['num_mes']:"1");
            $nivel = (isset($_GET['nivel'])?$_GET['nivel']:0);
            $tipo_encuesta = (isset($_GET['tipo_encuesta'])?$_GET['tipo_encuesta']:0);//0 = Todos los tipos de encuestas; 1 : Concluidas; 2 : En captura;
            $params = array(
                    'id_empleado' => $id_empleado,
                    'id_encuesta' => $id_encuesta,  
                    'id_segmento' => $id_segmento,
                    'id_empresa' => $id_empresa, 
                    'tipo_encuesta' => $tipo_encuesta,
                    'realizadas' => $realizadas, 
                    'nivel' => $nivel,
                    'num_mes' => $num_mes 
                );
            $c = new Client("http://67.205.162.138:51541/jasperserver", "DesarrolloAdmin", "Dev_JasperSoft#20");
            $report = $c->reportService()->runReport('/reports/Encuestas/' . $name_report, 'pdf', null, null, $params);
            header('Content-Type: application/pdf'); 
            echo $report;
        } catch (\Throwable $th) { 
            echo  $th;
        }
      
    }


    public function showReport_informe()
    {     
        try {
            $name_report = (isset($_GET['name_report'])?$_GET['name_report']:''); 
            $id_segmento = (isset($_GET['id_segmento'])?$_GET['id_segmento']:0);
            $id_empresa = (isset($_GET['id_empresa'])?$_GET['id_empresa']:0);    
            $id_almacen = (isset($_GET['id_almacen'])?$_GET['id_almacen']:0);    
            $codigo = (isset($_GET['codigo'])?$_GET['codigo']:0);    
            $tomar_stock = (isset($_GET['tomar_stock'])?$_GET['tomar_stock']:false);    
            $params = array(
                    'id_empleado' => $id_empleado,
                    'codigo' => $codigo,  
                    'id_almacen' => $id_almacen,  
                    'id_segmento' => $id_segmento,
                    'id_empresa' => $id_empresa,  
                    'tomar_stock' => $tomar_stock,  
                );
            $c = new Client("http://67.205.162.138:51541/jasperserver", "DesarrolloAdmin", "Dev_JasperSoft#20");
            $report = $c->reportService()->runReport('/reports/Encuestas/' . $name_report, 'pdf', null, null, $params);
            header('Content-Type: application/pdf'); 
            echo $report;
        } catch (\Throwable $th) { 
            echo  $th;
        }
    }

}