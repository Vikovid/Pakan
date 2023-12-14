<!--------------------------- CAJA --------------------------->
<!-- SUCURSAL: PAKAN -->

<!-- LOAD -->
<?php 
	// ARCHIVO LOAD
	require_once('../../modelo/load.php');

	//TÍTULO DE LA PÁGINA
	$page_title = 'Agregar Efectivo';

	//NIVEL DE USUARIO
	page_require_level(1);
?>

<!-- $_POST -->
<?php
	if(isset($_POST['agregarEfec'])){
		//Zona horaria
		ini_set('date.timezone','America/Mexico_City');
		//Valida los campos
		$campos = array('monto');
		validate_fields($campos);
		
		if (empty($errors)) {
			if($_POST['monto']>0){

				// Para actualizar el historial de efectivos
				$caja = buscaRegistroMaximo('histefectivo','idHistEfectivo'); //retorna el registro(fila) de la cantidad final máxima 
				$movimiento = 4; 
				$montoActual = $caja['cantFinal'];
				$montoFinal = ($_POST['monto'])+$montoActual;
				$idSucursal = 1;
				$usuario = current_user(); 
				//No hay vendedor porque no fue venta
				$fecha = date('y-m-d'); 
				$hora = date('H:i',time());

				registrarEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario['id'],"",$fecha,$hora);

				$session->msg("d","Monto agregado exitosamente.");
				redirect("caja.php",false);
			}
			else{
				$session->msg("d","El monto debe ser mayor a cero.");
				redirect("caja.php",false);
			}
		}else{
			$session->msg("d",$errors);
			redirect("caja.php",false);	
		}
	}
?>

<!-- HEADER -->
<?php include_once('../layouts/header.php')?> 

<!-- DIVS -->
<div class="row">
   <div class="col-md-7">
   	<!-- Mensaje -->
   	<?php echo display_msg($msg)?>
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar efectivo</span>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="60" alt="" align="center">
            </strong>
         </div>
         
         <div class="panel-body">
            <div class="col-md-4">
               <form method="post" action="caja.php" class="clearfix">
               	<div class="form-group">
                  	<div class="input-group">
                     	<span class="input-group-addon">
                        	<i class="glyphicon glyphicon-th-large"></i>
                     	</span>
                     	
                     	<input type="number" step="0.01" min=0.01 class="form-control" name="monto" placeholder="Monto">
                  	</div>
               	</div>
               	
               	<button type="submit" name="agregarEfec" class="btn btn-danger">Agregar</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- FOOTER -->
<?php include_once('../layouts/footer.php')?>