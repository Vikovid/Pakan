<?php
  $page_title = 'Editar cliente';
  require_once('../../modelo/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);

  $idcreden= $_GET['idCredencial'];

  if(isset($_GET['idCredencial'])){
     $cliente = buscaRegistroPorCampo("cliente","idcredencial",$idcreden);
     $foto = $cliente['foto'];
     $idCliente = $cliente['idcredencial'];
     $sexoAux = $cliente['sexo'];
  }

  if(isset($_POST['nombre'])) {
     $idcreden= $_GET['idCredencial'];
     $name = $_POST['nombre'];
     $direc = $_POST['direccion'];
     $telcliente= $_POST['telefono'];
     $email = $_POST['email'];
     $fechaNac = $_POST['fechaNac'];
     $alergias = $_POST['alergias'];
     $padecimientos = $_POST['padecimientos'];
     $foto = "";

     if(is_uploaded_file($_FILES['paciente']['tmp_name'])){
        $file_name = $_FILES['paciente']['name'];

        if ($file_name != '' || $file_name != null) {
           $file_type = $_FILES['paciente']['type'];
           list($type, $extension) = explode('/', $file_type);

           if ($extension == "gif" || $extension == "jpg" || 
              $extension == "jpeg" || $extension == "png"){

              $file_tmp_name = $_FILES['paciente']['tmp_name'];

              $fp = fopen($file_tmp_name, 'r+b');
              $data = fread($fp, filesize($file_tmp_name));
              fclose($fp);            

              $foto = $db->escape($data);

              if (empty($file_name) || empty($file_tmp_name)){
                 $session->msg('d','La ubicación del archivo no se encuenta disponible.');
                 redirect('edit_client.php?idCredencial='.$cliente['idcredencial'], false);
              }

              if ($idmasc['foto'] != ''){
                 $borrado = $db->query("UPDATE cliente SET foto = '' WHERE idcredencial = $idCliente");
                 if (!$borrado){
                    $session->msg('d','Error al borrar el archivo original.');
                    redirect('edit_client.php?idCredencial='.$cliente['idcredencial'], false);
                 }
              }
           }else{
              $session->msg('d','Formato de archivo no válido.');
              redirect('edit_client.php?idCredencial='.$cliente['idcredencial'], false);
           }
       }
       $resultado = actCliente($name,$direc,$telcliente,$email,$idcreden,$fechaNac,$alergias,$padecimientos,$foto);
    }else{
       $resultado = actCliente($name,$direc,$telcliente,$email,$idcreden,$fechaNac,$alergias,$padecimientos,'');
    } 

    if($resultado){
       $session->msg('s',"Paciente ha sido actualizado.");
       redirect('cliente.php?idCredencial='.(int)$idcreden, false);
    }else{
       $session->msg('d','Lo siento no se actualizaron los datos.');
       redirect('edit_client.php?idCredencial='.(int)$idcreden, false);
   }
 }
?>
<?php include_once('../layouts/header.php'); ?>

<script language="Javascript">

function valorAnt(){
   document.form1.sexo.value=document.form1.sexoAux.value;
}

</script>

<body onload="valorAnt();">
<div class="row">
   <div class="col-md-10">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Editar paciente</span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-9">
               <form method="post" name="form1" action="edit_client.php?idCredencial=<?php echo $_GET['idCredencial'];?>" enctype="multipart/form-data">
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                        </span>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $cliente['nom_cliente']; ?>" placeholder="Edita el nombre" required>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-signpost-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                           <path d="M7 1.414V4h2V1.414a1 1 0 0 0-2 0zM1 5a1 1 0 0 1 1-1h10.532a1 1 0 0 1 .768.36l1.933 2.32a.5.5 0 0 1 0 .64L13.3 9.64a1 1 0 0 1-.768.36H2a1 1 0 0 1-1-1V5zm6 5h2v6H7v-6z"/>
                           </svg></i>
                        </span>
                        <input type="text" class="form-control" name="direccion" value="<?php echo $cliente['dir_cliente']; ?>" placeholder="Edita la dirección" required>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                           <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                           </svg></i>
                        </span>
                        <input type="text" class="form-control" name="email" value="<?php echo $cliente['correo']; ?>" placeholder="Edita el correo">
                     </div>
                  </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-telephone-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M2.267.98a1.636 1.636 0 0 1 2.448.152l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z"/>
                              </svg>
                           </span>
                           <input type="number" class="form-control" name="tel_cliente" placeholder="Teléfono" value="<?php echo $cliente['tel_cliente']; ?>">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <label class="col-sm-7 col-form-label">Fecha de nacimiento:</label>
                           <div class="col-md-5">
                              <input type="date" name="fechaNac" value="<?php echo $cliente['fechaNac']; ?>">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <label class="col-sm-3 col-form-label">Sexo:</label>
                           <div class="col-md-9">
                              <select class="form-control" name="sexo">
                                 <option value="Masculino">Masculino</option>
                                 <option value="Femenino">Femenino</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                        </span>
                        <p><textarea name="alergias" class="form-control" placeholder="Alergias" maxlength="250" rows="4" style="resize: none"><?php echo remove_junk($cliente['alergias']);?></textarea></p>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="input-group">
                        <span class="input-group-addon">
                           <i class="glyphicon glyphicon-th-large"></i>
                        </span>
                        <p><textarea name="padecimientos" class="form-control" placeholder="Padecimientos" maxlength="250" rows="4" style="resize: none"><?php echo remove_junk($cliente['padecimientos']);?></textarea></p>
                     </div>
                  </div>
                  <div class="panel-heading">
                     <div class="panel-heading clearfix">
                        <span class="glyphicon glyphicon-camera"></span>
                        <span>Cambiar foto del paciente</span>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="panel profile">
                           <?php if ($foto != ""){ 
                              echo "<img src='data:image/jpg; base64,".base64_encode($foto)."' width='150' height='200'>";
                           } ?>
                        </div>
                     </div>
                     <div class="col-md-8">
                        <div class="form-group">
                           <input type="file" name="paciente" multiple="multiple" class="btn btn-primary btn-file"/>
                        </div>
                     </div>
                  </div>
                  <div class="text-center">
                     <input type="hidden" name="sexoAux" value="<?php echo $sexoAux ?>">
                     <button type="submit" name="update" class="btn btn-danger">Actualizar</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>
