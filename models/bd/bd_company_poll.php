<?php
/**  @author Marcos Moreno   */  
require_once "../auth/check.php"; 
if (check_session()) {
    
    require_once "../postgres.php";
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array(); 

    if ($received_data->action == 'fetchall') {
        $query = " SELECT 
                        e.*,CASE WHEN ee.id_empresa_encuesta IS NOT NULL THEN true ELSE false END AS selected
                    FROM empresa e
                    LEFT JOIN refividrio.empresa_encuesta ee ON ee.id_empresa = e.id_empresa 
                        AND ee.id_encuesta = ".$received_data->id_encuesta."
                        ORDER BY id_empresa ";  
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    if ($received_data->action == 'insert') {
        $data = array(
            ':id_empresa' => $received_data->id_empresa,
            ':id_encuesta' => $received_data->id_encuesta 
        );
        $query = "INSERT INTO empresa_encuesta (id_empresa, id_encuesta) VALUES (:id_empresa,:id_encuesta)";
        $statement = $connect->prepare($query); 
        $statement->execute($data); 
        $output = array(
            'message' => 'Data Inserted'
        ); 
        echo json_encode($output); 
    } 

    if ($received_data->action == 'delete') {
        $query = "DELETE FROM empresa_encuesta WHERE id_encuesta = '" . $received_data->id_encuesta . "' ";
        $statement = $connect->prepare($query);
        $statement->execute();
        $output = array(
            'message' => 'Data Deleted'
        );
        echo json_encode($output);
    }

}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}
