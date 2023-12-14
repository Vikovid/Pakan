<?php
  require_once('../../modelo/load.php');
  $page_title = 'Consulta de citas por día';
  // Checkin What level user has permission to view this page
  page_require_level(1);
  //$products = join_product_table();
  $all_sucursal = find_all('sucursal');

  $vm_scu = "";
  $mes = "";
  $anio = "";
  $dia = "";
  
  if(isset($_POST['sucursal'])){  
    $vm_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
  }

  if(isset($_POST['dia'])){  
    $dia =  remove_junk($db->escape($_POST['dia']));//prueba
  }

  if(isset($_POST['mes'])){  
    $mes =  remove_junk($db->escape($_POST['mes']));//prueba
  }

  if(isset($_POST['anio'])){  
    $anio =  remove_junk($db->escape($_POST['anio']));//prueba
  }

  ini_set('date.timezone','America/Mexico_City');
  $fecha_actual=date('Y-m-d',time());
  $hora_actual=date('H:i',time());

  if ($mes == "" && $anio == "" && $dia == ""){               
     $year = date('Y');           
     $fechaInicial = $year."/01/01";
     $fechaFinal = date('Y/m/d',time());
  }

  if ($mes == "" && $anio == "" && $dia != ""){
     $year = date('Y');
     $month = date('m');
     $fechaInicial = $year."/".$month."/".$dia;
     $fechaFinal = $year."/".$month."/".$dia;
  }

  if ($mes == "" && $anio != "" && $dia == ""){
     $month = date('m');
     $day = date('d');
     $fechaInicial = $anio."/01/01";
     $fechaFinal = $anio."/".$month."/".$day;
  }

  if ($mes == "" && $anio != "" && $dia != ""){
     $month = date('m');
     $fechaInicial = $anio."/".$month."/".$dia;
     $fechaFinal = $anio."/".$month."/".$dia;
  }

  if ($mes != "" && $anio == "" && $dia == ""){
     $year = date('Y');
     $day = date('d');
     $fechaInicial = $year."/".$mes."/01/";
     $numDias = date('t', strtotime($fechaInicial));
     $fechaFinal = $year."/".$mes."/".$numDias;
  }

  if ($mes != "" && $anio == "" && $dia != ""){
     $year = date('Y');
     $fechaInicial = $year."/".$mes."/".$dia;
     $fechaFinal = $year."/".$mes."/".$dia;
  }

  if ($mes != "" && $anio != "" && $dia == ""){
     $fechaInicial = $anio."/".$mes."/01";
     $numDias = date('t', strtotime($fechaInicial));
     $fechaFinal = $anio."/".$mes."/".$numDias;
  }

  if ($mes != "" && $anio != "" && $dia != ""){
     $fechaInicial = $anio."/".$mes."/".$dia;
     $fechaFinal = $anio."/".$mes."/".$dia;
  }

  $fechaIni = date('Y/m/d', strtotime($fechaInicial));
  $fechaFin = date("Y/m/d", strtotime($fechaFinal));
  $fechIni = date ('d-m-Y', strtotime($fechaInicial));

  if ($vm_scu!=""){
     $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
     $nomSucursal = $sucursal['nom_sucursal'];

     $citas = citasSucFecha($vm_scu,$fechaIni,$fechaFin);
  }else{
     $citas = citasFecha($fechaIni,$fechaFin);
  }

?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Citas por día</title>
</head>
<script language="Javascript">

function citasDiarias(){
  document.form1.action = "citas-diarias.php";
  document.form1.submit();
}

function foco(){
  document.form1.sucursal.focus();
}

function diasMes() {
  var anio = "";
  var mes = "";
  var hoy = new Date();
  var dia = "";
  var array = [];

  anio = document.form1.anio.value;
  mes = document.form1.mes.value;

  if (anio == "")
     anio = hoy.getFullYear();
  
  if (mes == ""){
     mes = hoy.getMonth() + 1;
     if (mes < 10)
        mes = "0" + mes;
  }

  var numDias = new Date(anio, mes, 0).getDate();

  for (var d = 1;d <= numDias; d++){
     if (d < 10)
       dia = "0" + d;
     else
       dia = d;

     array.push(dia);
  }
  addOptions("dia", array);
}

function addOptions(domElement, array) {
  var select = document.getElementsByName(domElement)[0];
  var option;

  for (value in array) {
     option = document.createElement("option");
     option.text = array[value];
     select.add(option);
  }
}

</script>
<body onload="foco();diasMes();">
  <form name="form1" method="post" action="citas-diarias.php">

  <span>Citas del día:</span>
  <?php echo "$fechIni"; ?>
  <?php if($vm_scu!=""){ ?>
          <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
          <span>Sucursal:</span>
          <?php echo $nomSucursal; ?>
  <?php } ?>

<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div>
               <div class="form-group">
               <div class="col-md-3">
                  <select class="form-control" name="sucursal">
                     <option value="">Selecciona una sucursal</option>
                     <?php  foreach ($all_sucursal as $id): ?>
                     <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="anio" onchange="diasMes();">
                     <option value="">Selecciona un año</option>
                     <option value="2021">2021</option>
                     <option value="2022">2022</option>
                     <option value="2023">2023</option>
                     <option value="2024">2024</option>
                     <option value="2025">2025</option>
                     <option value="2026">2026</option>
                     <option value="2027">2027</option>
                     <option value="2028">2028</option>
                     <option value="2029">2029</option>
                     <option value="2030">2030</option>
                     <option value="2031">2031</option>
                     <option value="2032">2032</option>
                     <option value="2033">2033</option>
                     <option value="2034">2034</option>
                     <option value="2035">2035</option>
                     <option value="2036">2036</option>
                     <option value="2037">2037</option>
                     <option value="2038">2038</option>
                     <option value="2039">2039</option>
                     <option value="2040">2040</option>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="mes" onchange="diasMes();">
                     <option value="">Selecciona un mes</option>
                     <option value="01">Enero</option>
                     <option value="02">Febrero</option>
                     <option value="03">Marzo</option>
                     <option value="04">Abril</option>
                     <option value="05">Mayo</option>
                     <option value="06">Junio</option>
                     <option value="07">Julio</option>
                     <option value="08">Agosto</option>
                     <option value="09">Septiembre</option>
                     <option value="10">Octubre</option>
                     <option value="11">Noviembre</option>
                     <option value="12">Diciembre</option>
                  </select>
               </div>  
               <div class="col-md-2">
                  <select class="form-control" name="dia">
                     <option value="">Selecciona un día</option>
                  </select>                
               </div>
               <a href="#" onclick="citasDiarias();" class="btn btn-primary">Buscar</a>
               <img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 20%;"> Paciente</th>
                  <th class="text-center" style="width: 20%;"> Responsable</th>
                  <th class="text-center" style="width: 9%;"> Fecha cita </th>
                  <th class="text-center" style="width: 7%;"> Hora </th>
                  <th class="text-center" style="width: 44%;"> Nota </th>
                  <th class="text-center" style="width: 44%;"> Acciones </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($citas as $cita):?>
                  <tr>
                     <td><?php echo remove_junk($cita['nom_cliente']); ?></td>
                     <td><?php echo remove_junk($cita['responsable']); ?></td>
                     <td class="text-center"><?php echo date("d-m-Y", strtotime ($cita['fecha_cita'])); ?></td>
                     <td class="text-center"><?php echo date("H:i", strtotime ($cita['hora'])); ?></td>
                     <td><textarea name="nota" class="form-control" maxlength="200" rows="2" style="resize: none" readonly><?php echo remove_junk($cita['nota']); ?></textarea></td>
                     <td class="text-center">
                        <div class="btn-group">
                           <?php if ((($fecha_actual == date("Y-m-d", strtotime ($cita['fecha_cita']))) && ($hora_actual < date("H:i", strtotime ($cita['hora'])))) || ($fecha_actual < date("Y-m-d", strtotime ($cita['fecha_cita'])))){ ?>
                                    <a href="editarCita.php?id=<?php echo (int)$cita['id'];?>" class="btn btn-info btn-xs" title="Editar" data-toggle="tooltip">
                                    <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="deleteCita.php?id=<?php echo (int)$cita['id'];?>" class="btn btn-danger btn-xs" title="Eliminar" data-toggle="tooltip">
                                   <span class="glyphicon glyphicon-trash"></span>
                                   </a>
                           <?php } ?>
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