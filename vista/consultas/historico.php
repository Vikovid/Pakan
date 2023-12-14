<!-- HISTÓRICO -->
<!-- SUCURSAL: PAKAN -->

<!-- LOAD -->
<?php 
  require_once('../../modelo/load.php');
  $page_title = 'Lista de productos';

  // Sucursales
  $sucursales = find_all('sucursal');

  // Checkin What level user has permission to view this page
  page_require_level(1);
?>

<!-- $_POST -->
<?php 

  $codigo = isset($_POST['codigo']) ? $_POST['codigo']:'';
  $sucursal = isset($_POST['sucursal']) ? $_POST['sucursal']:'';

  $nom_sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$sucursal);

  //Si ambos campos NO son vacios
  if(($codigo != '') and ($sucursal='')){
    
    $historico = join_his_table2($codigo,$sucursal);
  
  //Si alguno de los campos está vacío
  }elseif(($codigo != '') or ($sucursal != '')){
    
    //Si cófigo es vacio
    if($codigo == ''){
      $historico = join_his_table3($sucursal);
    //Si sucursal es vacio
    }elseif($sucursal == ''){
      $historico = join_his_table2a($codigo);
    }

  //Si ambos campos están vacíos
  }else{
    $historico = join_historico_table();
  } 

?>

<!-- BUSCA USUARIo -->
<?php
  function buscaUsuario($id){
    $usuario = find_by_id('users',$id);
    return $usuario['name'];
  }
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php')?>

<!-- FUNCIONES JAVASCRIPT -->
<script language="Javascript">
  function historico(){
    document.form1.action = "historico.php";
    document.form1.submit();
  }
</script>

<!-- HTML -->
<!DOCTYPE html>
<html>

<head>
  <title>Lista de Productos</title>
</head>

<body>

<!-- FORM -->
<form name="form1" method="post" action="historico.php">
<!-- DIVS -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="pull-right">
          <div class="form-group">
            <div class="col-md-12">
              <div class="input-group">

                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-barcode"></i>
                </span>
                <!-- CODIGO -->
                <input type="text" class="form-control" name="codigo" long="21" oninput="mayusculas(event)">

              </div>
            </div>

            <div class="col-md-12">
              <!-- SUCURSAL -->
              <select class="form-control" name="sucursal">
                <!-- OPCIÓN 1 -->
                <option value="">Selecciona una sucursal</option>
                
                <!-- OPCIÓN n -->
                <?php foreach ($sucursales as $id): ?>
                  <option value="<?php echo (int)$id['idSucursal']?>"><?php echo $id['nom_sucursal']?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <a href="#" onclick="historico();" class="btn btn-primary">Buscar</a>
            <img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center"> 
          </div>

          <!-- SUCURSAL GLYPHICON -->
            <div class="pull right">
              <?php if($sucursal != ''){?>
                <strong>
                  <span>&nbsp;&nbsp;</span>
                  <span class="glyphicon glyphicon-th"></span>
                  <span>Sucursal:</span>
                  <?php echo $nom_sucursal['nom_sucursal'] ?>
                </strong>
              <?php }?>
            </div>

        </div>  
      </div>
      
      <!-- TABLE -->
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <th class="text-center" style="width: 14%;"> Producto </th>
            <th class="text-center" style="width: 5%;"> Cantidad Inicial</th>
            <th class="text-center" style="width: 5%;"> Cantidad Final</th>
            <th class="text-center" style="width: 10%;"> Sucursal </th>
            <th class="text-center" style="width: 5%;"> Movimiento </th>
            <th class="text-center" style="width: 5%;"> Comentario </th>
            <th class="text-center" style="width: 10%;"> Usuario </th>
            <th class="text-center" style="width: 10%;"> Vendedor </th>
            <th class="text-center" style="width: 11%;"> Fecha </th>
            <th class="text-center" style="width: 7%;"> Hora </th>
          </thead>
          <tbody>
            <?php foreach ($historico as $hist): ?>
              <tr>
                <td class="text-center"><?php echo remove_junk($hist['name']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['qtyin']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['qtyfinal']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['nom_sucursal']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['movimiento']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['comentario']);?></td>
                <td class="text-center"><?php echo remove_junk(buscaUsuario($hist['usuario']));?></td>
                <td class="text-center"><?php echo remove_junk($hist['vendedor']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['fechaMov']);?></td>
                <td class="text-center"><?php echo remove_junk($hist['horaMov']);?></td>
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

<!-- FOOTER -->
<?php include_once('../layouts/footer.php')?>