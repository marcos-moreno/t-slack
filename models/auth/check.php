<?php
/**  @author Marcos Moreno   */  
session_start();
function check_session(){
    if (isset($_SESSION['rol'])) {
      if ($_SESSION['rol'] == 'user' || $_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'soporte técnico' || $_SESSION['rol'] == 'SuperAdmin' || $_SESSION['rol'] == 'Administracion' )  { 
          return true;
      }else{
        session_destroy(); 
        return false; 
      } 
    }else{  
        session_destroy(); 
        return false; 
    }  
} 
?>