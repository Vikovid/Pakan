<!-- VENTAS ANUALES PAKAN -->
<!-- LOAD -->
<?php
  // ARCHIVO LOAD
  require_once('../../modelo/load.php');

  // TITULO DE LA PÁGINA
  $page_title = 'Consulta de ventas mensuales';

  //NIVEL DEL USUARIO
  page_require_level(1);

   // MESES
   $meses = array('01'=>'Enero',
                  '02'=>'Febrero',
                  '03' =>'Marzo',
                  '04' =>'Abril',
                  '05'=>'Mayo' ,
                  '06'=>'Junio',
                  '07'=>'Julio',
                  '08'=>'Agosto',
                  '09'=>'Septiembre',
                  '10'=>'Octubre',
                  '11'=>'Noviembre',
                  '12'=>'Diciembre');
   
   // SUCURSALES
  $sucursales = find_all('sucursal');
  
   // AÑO Y SUCURSAL
  $anio = "";
   $idSucursal = "";
   $sucursal = "";

   if(isset($_POST['sucursal'])){
      $idSucursal = remove_junk($db->escape($_POST['sucursal']));
      $sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$idSucursal);
   }
   if(isset($_POST['anio'])){
      $anio = remove_junk($db->escape($_POST['anio']));
   }
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php');?>

<script type="text/javascript" src="../../libs/js/general.js"></script>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
  <title>Consulta de ventas mensuales</title>
</head>

<body onload="focoSucursal();">
<form name="form1" method="post" action="ventas_anuales.php">

   <?php
   if ($anio == "" and $sucursal == ""){
      $anio = date('Y');
      $fechaInicial = date('Y-m-d',strtotime($anio.'-01-01'));
      $fechaFinal = date('Y-m-d',strtotime($anio.'-12-31'));
      $totalVentasAnio = ventasPeriodo($fechaInicial,$fechaFinal);
      $totalGastosAnio = gastosPeriodo($fechaInicial,$fechaFinal);
   }
   if ($anio == "" and $sucursal != "") {
      $anio = date('Y');
      $fechaInicial = date('Y-m-d',strtotime($anio.'-01-01'));
      $fechaFinal = date('Y-m-d',strtotime($anio.'-12-31'));
      $totalVentasAnio = ventasPeriodoSuc($idSucursal,$fechaInicial,$fechaFinal);
      $totalGastosAnio = gastosPeriodoSuc($idSucursal,$fechaInicial,$fechaFinal);
   }
   if ($anio != "" and $sucursal == ""){
      $fechaInicial = date('Y-m-d',strtotime($anio.'-01-01'));
      $fechaFinal = date('Y-m-d',strtotime($anio.'-12-31'));
      $totalVentasAnio = ventasPeriodo($fechaInicial,$fechaFinal);
      $totalGastosAnio = gastosPeriodo($fechaInicial,$fechaFinal);
   }
   if ($anio != "" and $sucursal != ""){
      $fechaInicial = date('Y-m-d',strtotime($anio.'-01-01'));
      $fechaFinal = date('Y-m-d',strtotime($anio.'-12-31'));
      $totalVentasAnio = ventasPeriodoSuc($idSucursal,$fechaInicial,$fechaFinal);
      $totalGastosAnio = gastosPeriodoSuc($idSucursal,$fechaInicial,$fechaFinal);
   }

   $totalAnual = $totalVentasAnio['totalVentas']-$totalGastosAnio['total'];
   ?>

  <span>Total de ventas: <?php echo number_format($totalVentasAnio['totalVentas'],2);?></span>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <span>Total Anual: <?php echo number_format($totalAnual,2);?></span>
   <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
   <span>Año: <?php echo remove_junk($anio);?></span>   
  <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <?php if($idSucursal != ''):?>
  <span>Sucursal: <?php echo remove_junk($sucursal['nom_sucursal']);?></span> 
  <?php endif;?>

  <div class="row">
    <div class="col-md-10">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
              <div>
                <div class="form-group">
                  <!-- SUCURSAL -->
                  <div class="col-md-3">
                      <select class="form-control" name="sucursal">
                        <option value="">Selecciona una sucursal</option>
                        <?php foreach($sucursales as $sucursal):?>
                        <option value="<?php echo remove_junk($sucursal['idSucursal']);?>"><?php echo remove_junk($sucursal['nom_sucursal']);?></option>
                        <?php endforeach;?>
                        </select>
                  </div>  
                  <!-- AÑO -->
                  <div class="col-md-3">
                      <select class="form-control" name="anio">
                        <option value="">Selecciona un año</option>
                        <?php $i = 2020; while($i<=2040):?>
                        <option value="<?php echo (int)$i?>"><?php echo remove_junk($i); ?></option>
                        <?php $i+=1; endwhile;?>
                      </select>
                  </div>  
                  <!-- SUBMIT -->
                  <a href="#" onclick="ventasMensuales();" class="btn btn-primary">Buscar</a>
                  <!-- GRÁFICA -->
                  <a href="#" onclick="barMensual();" class="btn btn-info">Gráfica</a> 
                  <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
                </div>   
            </div>
          </div>
          <!-- TABLE -->
          <div class="panel-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                      <th class="text-center" style="width: 10%;"> Mes</th>
                      <th class="text-center" style="width: 10%;"> Sucursal</th>
                      <th class="text-center" style="width: 10%;"> Venta </th>
                      <th class="text-center" style="width: 10%;"> Gastos </th>
                      <th class="text-center" style="width: 10%;"> Total </th>
                  </tr>
                </thead>
                <tbody>
                  <?php while(key($meses) != NULL):

                     $inicioMes = $db->escape($anio.'-'.key($meses).'-01');
                     $finalMes = $db->escape($anio.'-'.key($meses).'-31');

                     if($idSucursal != ''){
                        $ventaMes = ventasPeriodoSuc($idSucursal,$inicioMes,$finalMes);
                        $gastoMes = gastosPeriodoSuc($idSucursal,$inicioMes,$finalMes);

                     }else{
                        $ventaMes = ventasPeriodo($inicioMes,$finalMes);
                        $gastoMes = gastosPeriodo($inicioMes,$finalMes);
                     }

                     $total = $ventaMes['totalVentas'] - $gastoMes['total'];
                  ?>
                  
                  <?php if($ventaMes['totalVentas'] != 0 or $gastoMes['total'] != 0):?>
                  <tr>
                      <td> <?php echo $meses[key($meses)]; ?> </td>
                      <td class="text-center"> <?php echo $sucursal['nom_sucursal']; ?> </td>
                      <td class="text-right"> <?php echo number_format($ventaMes['totalVentas'],2); ?> </td>
                      <td class="text-right"> <?php echo number_format($gastoMes['total'],2);?> </td>
                      <td class="text-right"> <?php echo number_format($total,2); ?> </td>
                  </tr>
                  <?php endif;?>
                  <?php next($meses); endwhile; ?>
                </tbody>
              </table>
            </div>
        </div>         
      </div>
  </div>
  
  <input type="hidden" name="idSuc" value="1">  
</form>
</body>

</html>

<!-- FOOTER -->
<?php include_once('../layouts/footer.php');?>