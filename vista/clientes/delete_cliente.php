<?php
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  if(isset($_GET['idCredencial'])) {
     $id = $_GET['idCredencial'];

     $resultado = borraRegistroPorCampo("cliente","idcredencial",$id);
  
     if(!$resultado) {
        die("falló la eliminación.");
     }
     $session->msg("s","Paciente eliminado correctamente.");
     redirect('cliente.php');
  }
?>
