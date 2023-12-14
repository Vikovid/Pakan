<!-- VENTAS MENSUALES -->
<!-- PAKAN -->

<?php
   require_once('../../modelo/load.php');
   $page_title = 'Consulta de ventas diarias';
   // Checkin What level user has permission to view this page
   page_require_level(1);
   //$products = join_product_table();
   $sucursales = find_all('sucursal');

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
   // Para consulta
   $fechaIni = date('Y/m/d', strtotime($fechaInicial));
   $fechaFin = date("Y/m/d", strtotime($fechaFinal));
   // Para imprimir en pantalla
   $fechIni = date ('d-m-Y', strtotime($fechaInicial));
   $fechFin = date ('d-m-Y', strtotime($fechaFinal));

   if ($vm_scu!=""){
      $consulta = buscaRegistroPorCampo('sucursal','idSucursal',$vm_scu);
      $nomSucursal=$consulta['nom_sucursal'];
      $totalVentasMes = ventasPeriodoSuc($vm_scu,$fechaIni,$fechaFin);
      $totalGastosMes = gastosPeriodoSuc($vm_scu,$fechaIni,$fechaFin);
      
   }else{
      $totalVentasMes = ventasPeriodo($fechaIni,$fechaFin);
      $totalGastosMes = gastosPeriodo($fechaIni,$fechaFin);
      
   }
   $totalXmes = $totalVentasMes['totalVentas'] - $totalGastosMes['total'];
?>
<!-- HEADER -->
<?php include_once('../layouts/header.php');?>

<!-- JAVASCRIPT -->
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
   <title>Consulta de ventas diarias</title>
</head>

<body onload="focoSucursal();">
   <form name="form1" method="post" action="ventas-mensuales.php">
   
   <span>Total de ventas: <?php echo number_format($totalVentasMes['totalVentas'],2);?> </span>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <span>Total: <?php echo number_format($totalXmes,2);?> </span>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <span>Período: <?php echo "del $fechIni al $fechFin"; ?> </span>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <?php if($vm_scu!=""){ ?>
          <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
          <span>Sucursal:</span>
          <?php echo $nomSucursal; ?>
   <?php } ?>

   <div class="row">
      <div class="col-md-10">
         <div class="panel panel-default">
            <div class="panel-heading clearfix">
               <div>
                  <div class="form-group">
                     <!-- SUCURSAL -->
                     <div class="col-md-2">
                        <select class="form-control" name="sucursal">
                           <option value=""> Sucursal </option>

                           <?php foreach($sucursales as $sucursal):?>
                           <option value="<?php echo remove_junk($sucursal['idSucursal']);?>"><?php echo remove_junk($sucursal['nom_sucursal']);?></option>
                           <?php endforeach;?>
                        </select>
                     </div>  
                     <!-- AÑO -->
                     <div class="col-md-2">
                        <select class="form-control" name="anio">
                           <option value="">Año</option>
                           <?php $i = 2020; while($i <= 2040):?>
                           <option value="<?php echo (int)$i; ?>"> <?php echo remove_junk($i);?> </option>
                           <?php $i+=1; endwhile;?>
                        </select>
                     </div>  
                     <!-- MES -->
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
                     <a href="#" onclick="ventasDiarias();" class="btn btn-primary">Buscar</a>
                     <a href="#" onclick="barDiaria();" class="btn btn-info">Gráfica</a> 
                     <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
                  </div>   
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  
                  <thead>
                     <tr>
                        <th class="text-center" style="width: 10%;"> Dia</th>
                        <th class="text-center" style="width: 10%;"> Sucursal</th>
                        <th class="text-center" style="width: 10%;"> Venta </th>
                        <th class="text-center" style="width: 10%;"> Gasto </th>
                        <th class="text-center" style="width: 10%;"> Total </th>
                     </tr>
                  </thead>
                  
                  <tbody>
                     <?php 
                        
                        $i= 1; 
                        while($i<=$numDias):

                        $fechaHoy = $db->escape($anio.'-'.$mes.'-'.str_pad($i, 2, "0", STR_PAD_LEFT));

                        if ($vm_scu != '') {
                           $ventaXDia = ventasPeriodoSuc($vm_scu,$fechaHoy,$fechaHoy);
                           $gastoXDia = gastosPeriodoSuc($vm_scu,$fechaHoy,$fechaHoy);
                        }else{
                           $ventaXDia = ventasPeriodo($fechaHoy,$fechaHoy);
                           $gastoXDia = gastosPeriodo($fechaHoy,$fechaHoy);
                        }

                        $totalXDia = $ventaXDia['totalVentas'] - $gastoXDia['total'];
                     ?>
                     <?php if($ventaXDia['totalVentas'] != 0 or $gastoXDia['total'] != 0): ?>
                     <tr>
                        <td class="text-left"> <?php echo $fechaHoy;?> </td>
                        <td class="text-center"> <?php echo $ventaXDia['totalVentas'] == 0 ? $gastoXDia['nom_sucursal']:$ventaXDia['nom_sucursal'] ;?> </td>
                        <td class="text-right"> <?php echo number_format($ventaXDia['totalVentas'],2);?> </td>
                        <td class="text-right"> <?php echo number_format($gastoXDia['total'],2);?> </td>
                        <td class="text-right"> <?php echo number_format($totalXDia,2);?> </td>
                     </tr>
                     <?php endif;?>
                     <?php $i+=1; endwhile;?>
                  </tbody>
               </table>
            </div>         
         </div>
      </div>   
   </div>
   </form>

</body>
</html>

<!-- FOOTER -->
<?php include_once('../layouts\footer.php');?>