<?php
   require_once('../../modelo/load.php');
   $page_title = 'Cita';
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $encargados = find_all('users');

   $idCliente = isset($_GET['idCliente']) ? $_GET['idCliente']:'';

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());

   $user = current_user(); 
   $idSucursal = $user['idSucursal'];

   if(isset($_POST['responsable'])){
      $idPaciente  = remove_junk($db->escape($_POST['id']));
      $req_fields = array('fecha','responsable','hora');
      validate_fields($req_fields);
      if(empty($errors)){
         $responsable  = remove_junk($db->escape($_POST['responsable']));
         $fecha  = remove_junk($db->escape($_POST['fecha']));
         $nota  = remove_junk($db->escape($_POST['nota']));
         $hora = remove_junk($db->escape($_POST['hora']));
         $id  = remove_junk($db->escape($_POST['id']));
         $idCita = "";

         $horaCita = date("H:i:s", strtotime ($hora));

         $consCita = buscaCita($responsable,$fecha,$horaCita);

         if ($consCita != null)
            $idCita= $consCita['id'];
       
         if ($idCita == ""){
            $resultado = altaCita($id,$responsable,$fecha,$hora,$nota,$fecha_actual,$idSucursal);

            if($resultado){
               $session->msg('s',"Registro Exitoso.");
               redirect('cliente.php', false);
            }else{
               $session->msg('d','Lo siento, falló el registro.');
               redirect('cita.php?idCliente='.$idPaciente, false);
            }
         }else{
            $session->msg('d','Lo siento, Día y Hora ya agendada.');
            redirect('cita.php?idCliente='.$idPaciente, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('cita.php?idCliente='.$idPaciente,false);
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
<title>Registro de Cita</title>
</head>

<body onload="horaInicialCita();">
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
               <span>Agendar cita de :</span>
               <span><?php echo $nomPaciente ?></span>
            </strong>     
         </div>
         <br>
         <br>
      </div>
      <form name="form1" method="post" action="cita.php">
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Responsable:</label>
            <div class="col-sm-3">
               <select class="form-control" name="responsable">
                  <option value="">Selecciona responsable</option>
                  <?php  foreach ($encargados as $id): ?>
                  <option value="<?php echo $id['username'] ?>">
                  <?php echo $id['name'] ?>
                  </option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>  
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Fecha de cita:</label>
            <div class="col-sm-9">
               <input type="date" name="fecha" min="<?php echo $fecha_actual; ?>" onchange="horasCita();">
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
               <textarea name="nota" class="form-control" placeholder="Nota" maxlength="200" rows="2" style="resize: none"></textarea>
            </div>
         </div>
         <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Día:</label>
            <div class="col-sm-10">
               <strong><?php echo $time2."  ".$time1;?></strong>
            </div>
         </div>
         <input type="hidden" value="<?php echo $idCliente ?>" name="id">
         <input type="hidden" value="<?php echo $idCliente ?>" name="idCliente">
         <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
         <button type="submit" name="consulta" class="btn btn-danger">Guardar</button>
      </form>
   </div>
</div>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
