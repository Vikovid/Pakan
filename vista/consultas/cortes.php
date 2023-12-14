<?php
  $page_title = 'Cortes';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $encargados = find_all('users');

  $c_idEncargado = "";

  if (isset($_POST['encargado'])){  
     $c_idEncargado =  remove_junk($db->escape($_POST['encargado']));//prueba
  }  
?>
<?php include_once('../layouts/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
<title>Lista de productos</title>
</head>

<script language="Javascript">
function corteEncargado(){
  document.form1.action = "cortes.php";
  document.form1.submit();
}

function cortespdf(){
  document.form1.action = "../pdf/cortespdf.php";
  document.form1.submit();
}

function excel(){
  document.form1.action = "../excel/excelcortes.php";
  document.form1.submit();
}

function foco(){
  document.form1.encargado.focus();
}
</script>
<body onload="foco();">
  <form name="form1" method="post" action="cortes.php">

  <?php
     if ($c_idEncargado!=""){
        $result = find_by_id("users",$c_idEncargado);
        $cortes = corteVendedor($result['username']);
     }else{
        $cortes = corte();
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
                 <div class="col-md-4">
                    <select class="form-control" name="encargado">
                       <option value="">Selecciona vendedor</option>
                       <?php  foreach ($encargados as $id): ?>
                       <option value="<?php echo (int)$id['id'] ?>">
                       <?php echo $id['name'] ?></option>
                       <?php endforeach; ?>
                    </select>
                 </div>                 
                 <a href="#" onclick="corteEncargado();" class="btn btn-primary">Buscar</a> 
                 <a href="#" onclick="cortespdf();" class="btn btn-danger">PDF</a>
                 <a href="#" onclick="excel();" class="btn btn-success">Excel</a>  
              </div>   
           </div>
           <div class="panel-body">
              <table class="table table-bordered">
                 <thead>
                    <tr>
                       <th class="text-center" style="width: 3%;">#</th>
                       <th class="text-center" style="width: 20%;"> Vendedor </th>
                       <th class="text-center" style="width: 20%;"> Sucursal </th>
                       <th class="text-center" style="width: 15%;"> Venta </th>
                       <th class="text-center" style="width: 15%;"> Ganancia </th>
                       <th class="text-center" style="width: 15%;"> Fecha </th>
                    </tr>
                 </thead>
                 <tbody>
                 <?php foreach ($cortes as $ventas):?>
                    <tr>
                       <td class="text-center"><?php echo count_id();?></td>
                       <td><?php echo remove_junk($ventas['vendedor']); ?></td>
                       <td class="text-center"><?php echo remove_junk($ventas['nom_sucursal']); ?></td>
                       <td class="text-right"> <?php echo money_format("%.2n",$ventas['venta']); ?></td>
                       <td class="text-right"> <?php echo money_format("%.2n",$ventas['ganancia']); ?></td>
                       <td class="text-center"> <?php echo date("d-m-Y", strtotime ($ventas['date'])); ?></td>
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
