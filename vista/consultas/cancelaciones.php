<!-- CANCELACIONES -->
<!-- SUCURSAL: PAKAN -->
<?php 
  $page_title = 'Lista de Cancelaciones';
  require_once('../../modelo/load.php');
  page_require_level(1);

  //Recupera todas las sucursales para "select"
  $sucursales = find_all('sucursal'); 
  //Recupera los valores del formulario
  $cancelados = isset($_POST['sucursal']) ? $_POST['sucursal']:'';
  // Recupera los datos de la sucursal
  $sucursal =  buscaRegistroPorCampo('sucursal','idSucursal',$cancelados);
  
  //Valida si los datos del formulario están vacios o no
  if ($cancelados != '') {
    // Busca por sucursal
    $cancelaciones = cancelacionesXSuc($_POST['sucursal']); 
  }else{
    // Busca todas las cancelaciones
    $cancelaciones = cancelaciones();
  }
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php')?>

<!-- FUNCIONES JAVASCRIPT -->
<script language="Javascript">

  function cancelacion(){
    document.form1.action = "cancelaciones.php"
    document.form1.submit();
  }

</script>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
  <title>Lista de cancelaciones</title>
</head>
<!-- BODY -->

<body>
<!-- FORM -->
<form name="form1" method="post" action="cancelaciones.php">
<!-- DIVS -->
<div>
  <div class="row">
    <div class="col-md-10">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <div class="form-group">

            <select class="form-control" name="sucursal">
              <!-- OPCIÓN 1 -->
              <option value="">Selecciona una sucursal</option>
              
              <!-- OPCIÓN N -->
              <?php foreach ($sucursales as $id): ?>
              <option value="<?php echo (int)$id['idSucursal']?>">
                <?php echo $id['nom_sucursal']?>
              </option>
              <?php endforeach; ?>
            </select>

            <div class="col-md-5">
              <a href="#" onclick="cancelacion()" class="btn btn-primary">Buscar</a>              
            </div>
          </div>

          <!-- SUCURSAL GLYPHICON -->
          <div class="pull right">
            <?php if($cancelados != ''){?>
            <strong>
              <span class="glyphicon glyphicon-th"></span>
              <span>Sucursal:</span>
              <?php echo $sucursal['nom_sucursal'] ?>
            </strong>
            <?php }?>
          </div>

        </div>
        <!-- TABLA -->
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="width: 1%">#</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Sucursal</th>
                <th class="text-center">Usuario</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">Razón de la cancelación</th>
              </tr>
            </thead>
            <tbody>
              <!-- INICIO FOREACH -->
              <?php foreach ($cancelaciones as $cancelacion): ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td class="text-center"><?php echo remove_junk($cancelacion['name'])?></td>
                <td class="text-center"><?php echo remove_junk($cancelacion['nom_sucursal'])?></td>
                <td class="text-center"><?php echo remove_junk($cancelacion['usuario'])?></td>
                <td class="text-center"><?php echo remove_junk($cancelacion['date'])?></td>
                <td class="text-center"><?php echo remove_junk($cancelacion['mensaje'])?></td>
              </tr>
              <!-- FIN FOREACH -->
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</form>
</body>
</html>
<!-- FIN DEL HTML -->

<!-- FOOTER -->
<?php include_once('../layouts/footer.php')?>