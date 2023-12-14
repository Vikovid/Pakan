<?php
   $page_title = 'Información del paciente';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(2);

   $idPaciente = isset($_POST['idCliente']) ? $_POST['idCliente']:'';

   $nombre = "";
   $sexo = "";
   $edad = "";
   $nom_cliente = "";
   $foto = "";
   $idCredencial = "";
   $dir_cliente = "";
   $tel_cliente = "";
   $correo = "";

   $consultas = array();
   $vacunas = array();
   $desparasitaciones = array();
   $estudios = array();

   if ($idPaciente != ""){

      $consPaciente = buscaRegistroPorCampo('cliente','idcredencial',$idPaciente);

      $nombre = $consPaciente['nom_cliente'];
      $direccion = $consPaciente['dir_cliente'];
      $telefono = $consPaciente['tel_cliente'];
      $correo = $consPaciente['correo'];
      $alergias = $consPaciente['alergias'];
      $edad = $consPaciente['fechaNac'];
      $padecimientos = $consPaciente['padecimientos'];
      $sexo = $consPaciente['sexo'];
      $foto = $consPaciente['foto'];

      $consultas = buscaConsultas($idPaciente);
      /*$vacunas = buscaVacunas($idPaciente);
      $desparasitaciones = buscaDesparasitaciones($idPaciente);*/
      $estudios = buscaEstudios($idPaciente);
   }
?>
<script type="text/javascript">

if (window.history.replaceState) { // verificamos disponibilidad
    window.history.replaceState(null, null, window.location.href);
}

function preventBack(){window.history.forward();}

setTimeout("preventBack()", 0);

window.onunload=function(){null};

function paciente(){
  if (document.form1.idPaciente.value == ""){
    alert("Recargaste la página");
        document.form1.action = "cliente.php";
        document.form1.submit();
  }
}

</script>
<?php include_once('../layouts/header.php');?>

<!DOCTYPE html>
<html>
<head>
<title>Historial Clínico</title>
</head>

<body onload="paciente();">
<form name="form1" method="post" action="history.php">  <div class="row">
<div class="col-md-12">
   <?php echo display_msg($msg); ?>
</div>
<div class="col-md-12">
   <div class="panel panel-default">
      <div class="panel-heading clearfix">
         <div class="pull-right">
            <div class="form-group">
               <div class="col-md-2">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <h5>Información del paciente</h5>
                     </span>
                  </div>    
               </div>
               <div class="text-right">
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Estatus de <?php echo $nombre ?></span>
                  <div>
                     <!-- En el href se manda el valor del parámetro en este caso, envia-->
                     <a href="../pdf/historialpdf.php?idCliente=<?php echo $idPaciente;?>" class="btn btn-danger btn-sm ">PDF</a>  
                     <a href="consulta.php?idCliente=<?php echo $idPaciente;?>" class="btn btn-primary btn-success">Consulta</a> 
                     <a href="estudio.php?idCliente=<?php echo $idPaciente;?>" class="btn btn-warning">estudio</a> 
                     <a href="cita.php?idCliente=<?php echo $idPaciente;?>" class="btn btn-primary btn-success">cita</a>
                     <input type="hidden" name="idPaciente" value="<?php echo $idPaciente;?>">
                  </div>
               </div>   
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 30%;">
  		      <table class="table table-bordered">
  			    <span>
               <h4 class="text-center"><text style="font-weight:bold;">Información del paciente</text></h4>
            </span>
            <thead>
                 <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Id Cliente:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$idPaciente"; ?></td>    
               </tr>
                 <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Cliente:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$nombre"; ?></td>    
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Dirección:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$direccion"; ?></td>
               </tr> 
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Teléfono:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$telefono"; ?></td>
               </tr>
               <tr>     
                  <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Correo:</text></td>
                  <td class="text-center" style="width: 10%;"><?php echo "$correo"; ?></td>             
               </tr>
              <tr>     
                <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Fecha de nacimiento:</text></td>
                <td class="text-center" style="width: 10%;"><?php echo date("d-m-Y", strtotime ($edad));?></td>
              </tr>
              <tr>     
                <td class="text-center" style="width: 10%;"><text style="font-weight:bold;">Sexo:</text></td>
                <td class="text-center" style="width: 10%;"><?php echo "$sexo"; ?></td>
              </tr>
            </thead>
            </table>
         </div>    
         <div style="float:left;width: 30%;">
            <table class="table table-bordered">
  		      <span>
              <h4 class="text-center">
                <text style="font-weight:bold;">Consideraciones</text>
              </h4>
            </span>
            <thead>
       		     <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Alergias:</text></td>
                  <td class="text-center" style="width: 70%;"><?php echo "$alergias"; ?></td>    
               </tr>
       		     <tr>
                  <td class="text-center" style="width: 10%:bold;"><text style="font-weight:bold;">Padecimientos:</text></td>
                  <td class="text-center" style="width: 70%;"><?php echo "$padecimientos"; ?></td>    
               </tr>
            </thead>
            </table>   
         </div>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <!--img src='imgPaciente.php?id=<?php echo $idPaciente; ?>' width="250" height="350"-->
         <?php if ($foto != ""){ 
            echo "<img src='data:image/jpg; base64,".base64_encode($foto)."' width='250' height='350'>";
         } ?> 
      </div>
      <div class="panel-body">
         <div>

<?php function calcular_edad($fecha){

$fecha_nac = new DateTime(date('Y/m/d',strtotime($fecha))); // Creo un objeto DateTime de la fecha ingresada
$fecha_hoy =  new DateTime(date('Y/m/d',time())); // Creo un objeto DateTime de la fecha de hoy
$edad = date_diff($fecha_hoy,$fecha_nac); // La funcion ayuda a calcular la diferencia, esto seria un objeto
return $edad;
}

if ($edad != "0000-00-00" && $edad != ""){
   $edad = calcular_edad($edad);
}

if ($edad != "0000-00-00" && $edad != ""){?>
   <span>
      <h4>
         <?php echo "$nombre "; echo "Tiene {$edad->format('%Y')} años, {$edad->format('%m')} meses y {$edad->format('%d')} dias."; ?>
      </h4>
   </span>
<?php } ?>
         </div>
      </div>

<!--parte de estudio clinico-->
<div class="col-md-6">
   <div class="panel panel-default">
      <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Estudios clínicos</span>
         </strong>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 100%;">
            <table class="table table-bordered table-striped">
               <thead>
               <tbody>
                  <tr>
                     <th class="text-center" style="width: 10%;">Nombre</th>
                     <th class="text-center" style="width: 35%;">Descripción</th>
                     <th class="text-center" style="width: 5%;">Acción</th>
                  </tr>
               </tbody>
               </thead>
               <tbody>
               <?php foreach ($estudios as $estudio):?>
                  <tr>
                     <td class="text-justify"><?php echo $estudio['nombre'] ?></td>
                     <td class="text-justify"><?php echo $estudio['descripcion'] ?></td>
                     <td class="text-center">
                       <!--button onclick="openModelPDF('<?php //echo $val['url'] ?>')" class="btn btn-primary" type="button">Ver Archivo Modal</button-->
                          <a class="btn btn-primary" target="_black" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/Pakan/' . $estudio['url']; ?>" >Ver Archivo</a>
                     </td>
                  </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<div class="col-md-12">
   <div class="panel panel-default">
   	  <div class="panel-heading">
         <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Historial Clínico</span>
         </strong>
      </div>
      <div class="panel-body">
         <div style="float:left;width: 100%;">
         <table class="table table-bordered table-striped">
            <thead>
            <tbody>              
               <tr>
                  <th class="text-center" style="width: 3%;">#</th>
                  <th class="text-center" style="width: 70%;"> Diagnóstico </th>
                  <th class="text-center" style="width: 10%;"> Fecha </th>
                  <th class="text-center" style="width: 5%;"> Acciones </th>
               </tr>
            </tbody>
            </thead>
            <tbody>
               <?php foreach ($consultas as $consulta):?>
               <tr>
                  <td class="text-center" ><?php echo count_id();?></td>
                  <td class="text-justify" > <?php echo remove_junk($consulta['diagnostico']); ?></td>
                  <td class="text-center" style="width: 05%;"> <?php echo date("d-m-Y", strtotime ($consulta['fecha'])); ?></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="ver_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="btn btn-primary btn-xs" title="Consultar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-eye-open"></span>
                        </a>
                        <a href="edit_consulta.php?idconsulta=<?php echo (int)$consulta['idconsulta'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-edit"></span>
                        </a>
                     </div>
                  </td>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
         </div>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
