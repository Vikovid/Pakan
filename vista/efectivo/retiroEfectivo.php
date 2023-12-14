<!---------------------- RETIRO DE EFECTIVO ---------------------->
<!-- SUCURSAL: PAKAN -->

<!-- LOAD -->
<?php 
	//ARCHIVO LOAD
	require_once("../../modelo/load.php");

	//TÍTULO DE LA PÁGINA
	$page_title='Retiro de efectivo';

	//NIVEL DE USUARIO
	page_require_level(1);
?>

<!-- $_POST -->
<?php
	if (isset($_POST['retirarEfec'])) {
		// Zona horaria
		ini_set('date.timezone','America/Mexico_City');
		//Validar campos 
		$campos = array('monto');
		validate_fields($campos);

		if(empty($errors)){
			if($_POST['monto']>0){

				$caja = buscaRegistroMaximo('histefectivo','idHistEfectivo'); //retorna el registro(fila) de la cantidad final máxima 

				$movimiento = 7; 
				$montoActual = $caja['cantFinal'];
				$montoFinal = $montoActual-$_POST['monto'];
				$idSucursal = 1;
				$usuario = current_user(); 
				//No hay vendedor porque no fue venta
				$fecha = date('Y-m-d'); 
				$hora = date('H:i',time());

				registrarEfectivo($movimiento,$montoActual,$montoFinal,$idSucursal,$usuario['id'],"",$fecha,$hora);

				$session->msg("d","Monto retirado exitosamente.");
				redirect("retiroEfectivo.php",false);

			}else{
				$session->msg("d","El monto debe ser mayor a cero");
				redirect("retiroEfectivo.php",false);
			}

		}else{
			$session->msg("d",$errors);
			redirect("retiroEfectivo.php",false);
		}
	}
?>

<!-- HEADER -->
<?php include_once("../layouts/header.php")?>

<!-- DIVS -->
<!-- DIVS -->
<div class="row">
   <div class="col-md-7">
   	<!-- Mensaje -->
   	<?php echo display_msg($msg)?>
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>retirar efectivo</span>
                  <img src="../../libs/imagenes/Logo.png" height="50" width="60" alt="" align="center">
            </strong>
         </div>
         
         <div class="panel-body">
            <div class="col-md-4">
               <form method="post" action="retiroEfectivo.php" class="clearfix">
               	<div class="form-group">
                  	<div class="input-group">
                     	<span class="input-group-addon">
                        	<i class="glyphicon glyphicon-th-large"></i>
                     	</span>
                     	
                     	<input type="number" step="0.01" min=0 class="form-control" name="monto" placeholder="Monto">
                  	</div>
               	</div>
               	
               	<button type="submit" name="retirarEfec" class="btn btn-danger">Retirar</button>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- FOOTER -->
<?php include_once("../layouts/footer.php")?>