<!-- DELETE GASTO -->
<!-- SUCURSAL: PAKAN -->

<!-- GASTO ELIMINADO: 14 -->

<?php
	// ARCHIVO LOAD
	require_once('../../modelo/load.php');
	// NIVEL DEL USUARIO
	page_require_level(1);
	// ZONA HORARIA
   ini_set('date.timezone','America/Mexico_City');

	if(isset($_GET['id'])){
		//ID Gasto
		$id = remove_junk($db->escape($_GET['id']));
		$tabla = 'gastos';
		$campo = 'id';
		
		// Registro del gasto
		$gasto = find_by_id($tabla,$id);

		$caja = buscaRegistroMaximo('histefectivo','idHistEfectivo'); //retorna el registro(fila) 

		$movimiento = 14;
		$montoActual = $caja['cantFinal']; 
		$montoFinal = $montoActual + $gasto['total'];
		$idSucursal = $gasto['idSucursal'];
		$usuario = current_user();
		$fecha = date('Y-m-d'); 
		$hora = date('H:i',time());

		$resultado1 = registrarEfectivo($movimiento,
												  $montoActual,
												  $montoFinal,
												  $idSucursal,
												  $usuario['id'],
												  "",
												  $fecha,
												  $hora);

		$resultado2 = borraRegistrosPorCampo($tabla, 
														 $campo, 
														 $id);
		
		if($resultado1){
			if($resultado2){
				$session->msg("s","Gasto elmininado correctamente");
  				redirect("gastos.php",false);
			}else{
				$session->msg("d","Missing spent id.");
				redirect('gastos.php',false);
			}
		}else{
			$session->msg("d","Falló la eliminación de gasto.");
			redirect('gastos.php',false);
		}	
	}
?>