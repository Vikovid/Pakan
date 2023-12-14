<?php
  $page_title = 'Lista de Clientes';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  $paciente = isset($_POST['idCredencial']) ? $_POST['idCredencial']:'';

  $cont = 0;

  if ($paciente != "") {
     if (is_numeric($paciente)){
        $cliente = join_cliente_table1a($paciente);
     }else{
        $cliente = join_cliente_table2a($paciente);
     }
  }else{
     $cliente = join_cliente_table();
  }
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function valorPaciente(){
  var str="";

  for (i=0;i<form1.elements.length;i++){
    if (form1.elements[i].checked){
      str = form1.elements[i].value;
      document.form1.idCliente.value = str;
      break;
    }
  }

  document.form1.idCredencial.focus();  
}

function paciente(){
   document.form1.action = "cliente.php";
   document.form1.submit();
}

function datosPaciente(){
  var str="";

  for (i=0;i<form1.elements.length;i++){
    if (form1.elements[i].checked){
      str = form1.elements[i].value;
      document.form1.idCliente.value = str;
      break;
    }
  }

  document.form1.action = "history.php";
  document.form1.submit();
}

</script>

<!DOCTYPE html>
<html>
<head>
<title>Lista de Pacientes</title>
</head>
<body onload="valorPaciente();">
   <form name="form1" method="post" action="cliente.php">
      <br>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading clearfix">
            <div class="pull-right">
               <div class="form-group">
                  <div class="col-md-4">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-barcode"></i>
                        </span>
                        <input type="text" class="form-control" name="idCredencial" long="21">
                     </div>
                  </div>  
                  <div class="col-md-4">
                  <a href="#" onclick="paciente();" class="btn btn-primary">Buscar</a> 
                  <a href="add_cliente.php" class="btn btn-primary">Agregar paciente</a>      
                  </div>
                  <a href="#" class="btn btn-primary" onClick="datosPaciente();">Abrir</a>
               </div>   
            </div>   
         </div>
      </div>
      <div class="panel-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th class="text-center" style="width: 3%;">Sel</th>
                  <th class="text-center" style="width: 20%;"> Nombre </th>
                  <th class="text-center" style="width: 50%;"> Dirección </th>
                  <th class="text-center" style="width: 10%;"> Número Telefónico </th>
                  <th class="text-center" style="width: 7%;"> Id Paciente </th>
                  <th class="text-center" style="width: 5%;"> Puntos </th>
                  <th class="text-center" style="width: 5%;"> Acciones </th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($cliente as $cliente):?>
               <tr>
                  <?php if ($cont == 0){ ?>
                     <td width="3%"><input type='radio' name='paciente' value='<?php echo $cliente["idcredencial"] ?>' checked/></td>
                  <?php }else{ ?>
                     <td width="3%"><input type='radio' name='paciente' value='<?php echo $cliente["idcredencial"] ?>'/></td>
                  <?php } ?>
                  <td> <?php echo remove_junk($cliente['nom_cliente']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cliente['dir_cliente']); ?></td>
                  <td class="text-center"> <?php echo remove_junk($cliente['tel_cliente']); ?></td> 
                  <td class="text-center"> <?php echo remove_junk($cliente['idcredencial']); ?></td>
                  <td class="text-center"><?php echo remove_junk(first_character(floor($cliente['venta']))); ?></td>
                  <td class="text-center">
                     <div class="btn-group">
                        <a href="edit_client.php?idCredencial=<?php echo (int)$cliente['idcredencial'];?>" class="btn btn-info btn-xs"  title="Editar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_cliente.php?idCredencial=<?php echo (int)$cliente['idcredencial'];?>" class="btn btn-danger btn-xs"  title="Eliminar" data-toggle="tooltip">
                           <span class="glyphicon glyphicon-trash"></span>
                        </a>
                     </div>
                  </td>
               </tr>
               <?php 
                  $cont++;    
                  endforeach; 
               ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
<input type="hidden" name="idCliente" value="">
</form>
</body>
</html>
<?php include_once('../layouts/footer.php'); ?>
