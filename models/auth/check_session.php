<?php
/**  @author Marcos Moreno   */  
session_start();
if (isset($_SESSION['rol'])) {
  if ($_SESSION['rol'] == 'user' || $_SESSION['rol'] == 'admin' 
      || $_SESSION['rol'] == 'soporte técnico' 
      || $_SESSION['rol'] == 'SuperAdmin' || $_SESSION['rol'] == 'Administracion'
      || $_SESSION['rol'] == 'Evaluaciones' )  { 
        echo true;
  }else{
    session_destroy(); 
    echo false; 
  } 
}else{  
    session_destroy(); 
    echo false; 
}  
?>