<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);

  if(isset($_GET['id'])) {
     $id = $_GET['id'];
     $resultado = borraRegistroPorCampo('cita','id',$id);
     if(!$resultado){
        die("Falló la eliminación.");
     }
     $session->msg("s","Cita eliminada correctamente");
     redirect('citas-mensuales.php');
  } 
?>
