<?php
   require_once('../../modelo/load.php');
   $page_title = 'Citas mensuales';
   // Checkin What level user has permission to view this page
   page_require_level(1);
   //$products = join_product_table();
   $all_sucursal = find_all('sucursal');

   $vm_scu = "";
   $mes = "";
   $anio = "";
  
   if(isset($_POST['sucursal'])){  
      $vm_scu =  remove_junk($db->escape($_POST['sucursal']));//prueba
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

   if ($mes == "" && $anio == ""){                          
      $mes = date('m');
      $anio = date('Y');
      $day = date("d", mktime(0,0,0, $mes+1, 0, $anio));
      $fechaInicial = $anio."/".$mes."/01";
      $fechaFinal = $anio."/".$mes."/".$day;
   }

   if ($mes != "" && $anio == ""){
      $anio = date('Y');
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   if ($mes == "" && $anio != ""){
      $mes = date('m');
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   if ($mes != "" && $anio != ""){
      $fechaInicial = $anio."/".$mes."/01";
      $numDias = date('t', strtotime($fechaInicial));
      $fechaFinal = $anio."/".$mes."/".$numDias;
   }

   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));

   if ($vm_scu!=""){
      $consulta = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $nomSucursal=$consulta['nom_sucursal'];

      $citas = citasSucFecha($vm_scu,$fechaIni,$fechaFin);
   }else{
      $citas = citasFecha($fechaIni,$fechaFin);
   }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Citas Mensuales</title>
</head>

<body onload="focoSucursal();">
  <form name="form1" method="post" action="citas-mensuales.php">

  <span>Período:</span>
  <?php echo "del $fechIni al $fechFin"; ?>
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
                        <option value="">Sucursal</option>
                        <?php  foreach ($all_sucursal as $id): ?>
                        <option value="<?php echo (int)$id['idSucursal'] ?>">
                        <?php echo $id['nom_sucursal'] ?></option>
                        <?php endforeach; ?>
                     </select>
                  </div>  
                  <div class="col-md-2">
                     <select class="form-control" name="mes">
                        <option value="">Mes</option>
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
                     <select class="form-control" name="anio">
                        <option value="">Año</option>
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
                  <a href="#" onclick="citasMens();" class="btn btn-primary">Buscar</a>
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