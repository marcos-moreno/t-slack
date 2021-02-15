<?php
/**  @author Marcos Moreno   */  
require '../auth/check.php';
require '../../lib/PHPMailer/src/Exception.php';
require '../../lib/PHPMailer/src/PHPMailer.php';
require '../../lib/PHPMailer/src/SMTP.php';  
require_once '../../lib/html2pdf/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
  
$received_data = json_decode(file_get_contents("php://input"));

if (check_session()) {
    if (isset($received_data->data->correo)) { 
        $objEmail =  new Email("<h1>".$received_data->data->msg."</h1>" . "<h2>".$received_data->data->description."</h2>"
                                ,$received_data->subject
                                ,$received_data->data->correo
                                ,$received_data->data->epleado);
        if ($objEmail->sendEmail() == false) {
            echo "ERROR:" . $objEmail->get_descriptionError();
        }else {
            echo "Enviado.";
        }  
    }else{
        echo "Error, Correo Inválido.";
        var_dump($received_data);
    }  
}else{
    $output = array('message' => 'Not authorized'); 
    echo json_encode($output); 
}  
class Email 
{
    private $body;
    private $subject; 
    private $for_mail;
    private $for_name;
    private $descriptionError;
    private $mail; 

    public function __construct($body, $subject, $for_mail,$for_name){
        $this->body = $body;
        $this->subject = $subject;
        $this->for_mail = $for_mail;
        $this->for_name = $for_name;
        $this->descriptionError = "";
        $this->mail = new PHPMailer(true);
    }
    public function __construct0(){ 
        $this->descriptionError = "";
        $this->mail = new PHPMailer(true);
    }  
    public function get_body(){ 
        return $this->body;
    }
    public function set_body($body){ 
        $this->body = $body;
    } 
    public function get_subject(){ 
        return $this->subject;
    }  
    public function set_subject($subject){ 
        $this->subject = $subject;
    } 
    public function get_for_mail(){ 
        return $this->for_mail;
    }  
    public function set_for_mail($for_mail){ 
        $this->for_mail = $for_mail;
    }   
    public function get_for_name(){ 
        return $this->for_name;
    }  
    public function set_for_name($for_name){ 
        $this->for_name = $for_name;
    }  
    public function get_mail(){ 
        return $this->mail;
    }  
    public function set_mail($mail){ 
        $this->mail = $mail;
    } 
    public function get_descriptionError(){ 
        return $this->descriptionError;
    }  
    public function sendEmail(){
        try {
            require 'msg.php'; 
            $msg = new msg();
            $this->mail->CharSet = 'UTF-8';
            $this->body = $msg->get_msg($this->body); 
            $this->mail->isHTML(true);
            $this->mail->IsSMTP();
            $this->mail->Host = 'mail.refividrio.com.mx';
            $this->mail->SMTPSecure = 'ssl';
            $this->mail->Port = 465;
            $this->mail->SMTPDebug = 0;
            $this->mail->SMTPAuth = true;
            $this->mail->Username =  'desarrollo2@refividrio.com.mx';
            $this->mail->Password = 'desarrollos$123';
            $this->mail->SetFrom('desarrollo2@refividrio.com.mx' , "Refividrio");
            $this->mail->Subject = $this->subject; 
            $this->mail->MsgHTML($this->body); 
            $this->mail->AddAddress($this->for_mail,$this->for_name ); 
            $this->mail->send();
            return true;
        } catch (Exception $exc) {
            $this->descriptionError = $exc;
            return false;
        } 
    }    
}
