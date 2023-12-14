<?php
  $page_title = 'Cortes de las quincenas';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $encargados = find_all('users');

  $c_idEncargado = "";
  $mes = "";
  $anio = "";

  if (isset($_POST['encargado'])){  
     $c_idEncargado =  remove_junk($db->escape($_POST['encargado']));//prueba
  }  

  if(isset($_POST['mes'])){  
    $mes =  remove_junk($db->escape($_POST['mes']));//prueba
  }

  if(isset($_POST['anio'])){  
    $anio =  remove_junk($db->escape($_POST['anio']));//prueba
  }
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<!DOCTYPE html>
<html>
<head>
<title>Cortes de las quincenas</title>
</head>

<body onload="focoEncargado();">
  <form name="form1" method="post" action="cortes_quincena.php">

<?php

  if ($mes == "" && $anio == ""){                          
     $mes = date('m');
     $anio = date('Y');
     $day = date("d", mktime(0,0,0, $mes+1, 0, $anio));
     $fechaInicial = $anio."/".$mes."/01";
  }

  if ($mes != "" && $anio == ""){
     $anio = date('Y');
     $fechaInicial = $anio."/".$mes."/01";
  }

  if ($mes == "" && $anio != ""){
     $mes = date('m');
     $fechaInicial = $anio."/".$mes."/01";
  }

  if ($mes != "" && $anio != ""){
     $fechaInicial = $anio."/".$mes."/01";
  }

  $fechaInicioPQ = date('Y/m/d', strtotime($fechaInicial));

  $fechaFinPQ = date("d-m-Y",strtotime($fechaInicioPQ."+ 14 days"));
  $fechaFinalPQ = date ('Y/m/d',strtotime($fechaFinPQ));
  $fechaIniPQ = date ('d-m-Y',strtotime($fechaInicioPQ));

  $fechaIniSQ = date("d-m-Y",strtotime($fechaInicioPQ."+ 15 days"));
  $fechaInicioSQ = date ('Y/m/d',strtotime($fechaIniSQ));
  $dia = date('t', strtotime($fechaIniSQ));
  $mes = date('m', strtotime($fechaIniSQ));
  $anio = date('Y', strtotime($fechaIniSQ));
  $fechaFinSQ = $anio."/".$mes."/".$dia;
  $fechaFinSQ = date ('d-m-Y',strtotime($fechaFinSQ));
  $fechaFinalSQ = date ('Y/m/d',strtotime($fechaFinSQ));

  if ($c_idEncargado!=""){
     $result = find_by_id("users",$c_idEncargado);
     $cortesPQ = cortePeriodoVen($result['username'],$fechaInicioPQ,$fechaFinalPQ);
     $cortesSQ = cortePeriodoVen($result['username'],$fechaInicioSQ,$fechaFinalSQ);
  }else{
     $cortesPQ = cortePeriodo($fechaInicioPQ,$fechaFinalPQ);
     $cortesSQ = cortePeriodo($fechaInicioSQ,$fechaFinalSQ);
  }
?>
<div class="row">
   <div class="col-md-7">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-7">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="form-group">
               <div class="col-md-2">
                  <select class="form-control" name="encargado">
                     <option value="">Vendedor</option>
                     <?php  foreach ($encargados as $id): ?>
                     <option value="<?php echo (int)$id['id'] ?>">
                     <?php echo $id['name'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>                 
               <div class="col-md-2">
                  <select class="form-control" name="anio">
                     <option value="">Año</option>
                     <option value="2020">2020</option>
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
               <a href="#" onclick="corteQuincena();" class="btn btn-primary">Buscar</a>
            </div>   
         </div>
         <?php if (count($cortesPQ) > 0){ ?>
         <div class="panel-body">
            <span><strong><?php echo "Quincena del: $fechaIniPQ al: $fechaFinPQ"; ?></strong></span>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="text-left" style="width: 25%;"> Vendedor </th>
                     <th class="text-center" style="width: 25%;"> Sucursal </th>
                     <th class="text-center" style="width: 25%;"> Venta </th>
                     <th class="text-center" style="width: 25%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($cortesPQ as $cortePQ): ?>
                  <tr>
                     <td><?php echo remove_junk($cortePQ['vendedor']); ?></td>
                     <td class="text-center"><?php echo remove_junk($cortePQ['nom_sucursal']); ?></td>
                     <td class="text-right"> <?php echo "$".money_format("%.2n",$cortePQ['venta']); ?></td>
                     <td class="text-right"> <?php echo "$".money_format("%.2n",$cortePQ['ganancia']); ?></td>
                  </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
         <?php } ?>
         <?php if (count($cortesSQ) > 0){ ?>
         <div class="panel-body">
            <span><strong><?php echo "Quincena del: $fechaIniSQ al: $fechaFinSQ"; ?></strong></span>
         </div>
         <div class="panel-body">
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="text-left" style="width: 25%;"> Vendedor </th>
                     <th class="text-center" style="width: 25%;"> Sucursal </th>
                     <th class="text-center" style="width: 25%;"> Venta </th>
                     <th class="text-center" style="width: 25%;"> Ganancia </th>
                  </tr>
               </thead>
               <tbody>
               <?php foreach ($cortesSQ as $corteSQ): ?>
                  <tr>
                     <td><?php echo remove_junk($corteSQ['vendedor']); ?></td>
                     <td class="text-center"> <?php echo remove_junk($corteSQ['nom_sucursal']); ?></td>
                     <td class="text-right"> <?php echo "$".money_format("%.2n",$corteSQ['venta']); ?></td>
                     <td class="text-right"> <?php echo "$".money_format("%.2n",$corteSQ['ganancia']); ?></td>
                  </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
         </div>
         <?php } ?>
      </div>
   </div>
</div>
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
