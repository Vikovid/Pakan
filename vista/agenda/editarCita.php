<?php
   require_once('../../modelo/load.php');
   $page_title = 'Editar cita';
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $encargados = find_all('users');
   $cita = find_by_id('cita',(int)$_GET['id']);
   $idCitaOrig = $cita['id'];
   $idCliente = $cita['idCredencial'];
   $horaCitaOrig = $cita['hora'];
   $notaOrig = $cita['nota'];

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
  
   if(isset($_POST['responsable'])){
      $id  = remove_junk($db->escape($_POST['idCita']));
      $req_fields = array('fecha','responsable','hora');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $fecha  = remove_junk($db->escape($_POST['fecha']));
         $nota  = remove_junk($db->escape($_POST['nota']));
         $hora = remove_junk($db->escape($_POST['hora']));
         $idCita = "";

         $horaCita = date("H:i:s", strtotime ($hora));

         $consCita = buscaCita($responsable,$fecha,$horaCita);

         if ($consCita != null)
            $idCita= $consCita['id'];
       
         if ($idCita == ""){
            $resultado = actCita($responsable,$fecha,$nota,$horaCita,$fecha_actual,$cita['id']);

            if($resultado){
               $session->msg('s',"Registro Exitoso.");
               redirect('citas-mensuales.php', false);
            }else{
               $session->msg('d','Lo siento, falló el registro.');
               redirect('editarCita.php?id='.$id, false);
            }
         }else{
            if ($notaOrig != $nota){
               $actCita = actRegistroPorCampo('cita','nota',$nota,'id',$cita['id']);

               if($actCita){
                  $session->msg('s',"Registro Exitoso.");
                  redirect('citas-mensuales.php', false);
               }else{
                  $session->msg('d','Lo siento, falló el registro.');
                  redirect('editarCita.php?id='.$id, false);
               }
            }else{
               $session->msg('d','Lo siento, día y Hora ya agendada.');
               redirect('editarCita.php?id='.$id, false);
            }
         }
      }else{
         $session->msg("d", $errors);
         redirect('editarCita.php?id='.$id,false);
      }
   }
   $paciente = buscaRegistroPorCampo('cliente','idcredencial',$idCliente);
   $nomPaciente = $paciente['nom_cliente'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Edición de Cita</title>
</head>

<body onload="horasEdicionCita();">
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editar cita de :</span>
               <span><?php echo $nomPaciente ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="editarCita.php?id=<?php echo (int)$cita['id'] ?>">
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Responsable:</label>
            <div class="col-sm-3">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona responsable</option>
                  <?php  foreach ($encargados as $resp): ?>
                  <option value="<?php echo $resp['username']; ?>" <?php if($cita['responsable'] === $resp['username']): echo "selected"; endif; ?> >
                  <?php echo remove_junk($resp['name']); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Fecha de cita:</label>
            <div class="col-sm-9">
               <input type="date" name="fecha" min="<?php echo $fecha_actual ?>" onchange="horasEdicionCita();" value="<?php echo remove_junk($cita['fecha_cita']); ?>">
            </div>
         </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">hora de cita:</label>
            <div class="col-md-2">
               <select class="form-control" id="horasLista" name="hora">
                  <option value="">Selecciona una hora</option>
               </select>                
            </div>
         </div>
         <div class="form-group row">
            <div class="input-group">
               <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
               </span>
               <textarea name="nota" class="form-control" placeholder="Nota" maxlength="200" rows="2" style="resize: none"><?php echo remove_junk($cita['nota']); ?></textarea>
            </div>
         </div>
         <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Día:</label>
            <div class="col-sm-10">
               <strong><?php echo $time2."  ".$time1;?></strong>
            </div>
         </div>
         <input type="hidden" value="<?php echo $idCitaOrig ?>" name="idCita">
         <input type="hidden" name="horaAux" value="<?php echo $horaCitaOrig ?>">
         <input type="hidden" name="notaOrig" value="<?php echo $notaOrig ?>">  
         <button type="submit" name="consulta" class="btn btn-danger">Actualizar</button>
      </form>
   </div>      
</div>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
