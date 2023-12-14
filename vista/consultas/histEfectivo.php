<!-- HISTÓRICO EFECTIVO -->
<!-- SUCURSAL: PAKAN -->

<!-- LOAD -->
<?php 
	// ARCHIVO LOAD
	require_once('../../modelo/load.php');

	//TÍTULO DE LA PÁGINA
	$page_title = 'Lista de Sucursales';

	//TABLAS
	$usuarios = find_all('users');
	$sucursales = find_all('sucursal');

	//NIVEL DEL USUARIO
  	page_require_level(1);
?>

<!-- FUNCIONES JAVASCRIPT -->
<script language="Javascript">
	function historialEfectivo(){
		document.form1.action = "histEfectivo.php";
		document.form1.submit();
	}
</script>

<!-- $_POST -->
<?php 
	$usu = isset($_POST['usuario']) ? $_POST['usuario']:'';
	$suc = isset($_POST['sucursal']) ? $_POST['sucursal']:'';

	$nom_sucursal = buscaRegistroPorCampo('sucursal','idSucursal',$suc);

	//Si ambos campos están llenos
	if(($usu!='') and ($suc !='')){
		$histEfectivo = histEfecUsuSuc($usu,$suc);
		// printSucursal();
	}
	//Si alguno de los campos está vacío
	elseif(($usu!='')or($suc!='')){
		//Si usuario está vacío
		if($usu == ''){
			$histEfectivo = histEfecSuc($suc);
			// printSucursal();
		}	
		//Sí sucursal está vacío
		elseif($suc == ''){
			$histEfectivo = histEfecUsu($usu);
		}
	}
	//Si ambos campos están vacíos
	else{
		$histEfectivo = histEfectivo();
	}
	
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php');?>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<body>
<!-- FORMULARIO -->
<form name="form1" method="post" action="histEfectivo.php">

<!-- DIVS -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				
				<!-- SELECCIONAR USUARIO -->
				<select class="form-control" name="usuario">
					<!-- OPCIÓN 1 -->
					<option value="">Selecciona usuario</option>
					<!-- OPCIÓN N -->
					<?php foreach ($usuarios as $usuario): ?>
					<option value="<?php echo (int)$usuario['id'] ?>"><?php echo remove_junk($usuario['username'])?></option>
					<?php endforeach; ?>
				</select>
				<!-- SELECCIONAR SUCURSAL -->
				<select class="form-control" name="sucursal">
					<!-- OPCIÓN 1 -->
					<option value="">Selecciona sucursal</option>
					<!-- OPCIÓN N -->
					<?php foreach ($sucursales as $sucursal): ?>
					<option value="<?php echo (int)$sucursal['idSucursal']?>"><?php echo remove_junk($sucursal['nom_sucursal']) ?></option>
					<?php endforeach;?>
				</select>

				<div class="pull right">
					<!-- SUBMIT -->
					<a href="#" onclick="historialEfectivo();" class="btn btn-primary">Buscar</a>
					<!-- LOGO -->
					<img src="../../libs/imagenes/Logo.png" height="50" width="70" alt="" align="center">
					<div class="form-group">
						<!-- <p>HOLA6 xD</p> -->
						<div class="col-md-3">
							<!-- <p>HOLA7 xD</p> -->
						</div>
					</div>
				</div>

				<!-- SUCURSAL GLYPHICON -->
          	<div class="pull right">
            	<?php if($suc != ''){?>
            	<strong>
              		<span class="glyphicon glyphicon-th"></span>
              		<span>Sucursal:</span>
              		<?php echo $nom_sucursal['nom_sucursal'] ?>
            	</strong>
            	<?php }?>
          	</div>

			</div>
			<!-- TABLA -->
				<div class="panel-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center" style="width: 11%;"> Movimiento </th>
               			<th class="text-center" style="width: 10%;"> Cantidad Inicial</th>
               			<th class="text-center" style="width: 10%;"> Cantidad Final</th>
               			<th class="text-center" style="width: 8%;"> Cantidad Movimiento</th>
               			<th class="text-center" style="width: 8%;"> Sucursal </th>
               			<th class="text-center" style="width: 11%;"> Usuario </th>
               			<th class="text-center" style="width: 11%;"> Vendedor </th>
               			<th class="text-center" style="width: 7%;"> Fecha </th>
               			<th class="text-center" style="width: 7%;"> Hora </th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($histEfectivo as $efectivo): ?>
							<tr>
								<td class="text-center"><?php echo remove_junk($efectivo['movimiento'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['cantIni'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['cantFinal'])?></td>	
								<td class="text-center"><?php echo abs($efectivo['cantIni']-$efectivo['cantFinal'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['nom_sucursal'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['username'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['vendedor'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['fechaMov'])?></td>	
								<td class="text-center"><?php echo remove_junk($efectivo['horaMov'])?></td>	
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
<?php include_once('../layouts/footer.php');?>