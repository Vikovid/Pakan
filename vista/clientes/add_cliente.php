<?php
   $page_title = 'Agregar cliente';
   require_once('../../modelo/load.php');
   // Checkin What level user has permission to view this page
   page_require_level(3);

   $cliente = buscaRegistroMaximo("cliente","idcredencial");
   $idCliente = $cliente['idcredencial'] + 1;

   if(isset($_POST['add_cliente'])){
      $req_fields = array('nom_cliente','dir_cliente','tel_cliente','idcredencial' );
      validate_fields($req_fields);
      if(empty($errors)){
         $nombre  = remove_junk($db->escape($_POST['nom_cliente']));
         $p_direccion  = remove_junk($db->escape($_POST['dir_cliente']));
         $telefono  = remove_junk($db->escape($_POST['tel_cliente']));
         $p_correo  = remove_junk($db->escape($_POST['correo']));
         $credencial  = remove_junk($db->escape($_POST['idcredencial']));
         $fechaNac = remove_junk($db->escape($_POST['fechaNac']));
         $alergias = remove_junk($db->escape($_POST['alergias']));
         $padecimientos = remove_junk($db->escape($_POST['padecimientos']));
         $sexo = remove_junk($db->escape($_POST['sexo']));
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
                     redirect('add_cliente.php', false);
                  }
               }else{
                  $session->msg('d','Formato de archivo no válido.');
                  redirect('add_cliente.php', false);
               }
            }
         } 

         $hayCliente = buscaRegistroPorCampo("cliente","idcredencial",$credencial);
         $nomCliente = $hayCliente['nom_cliente'];

         if ($nomCliente == ""){

            $resultado = altaCliente($nombre,$p_direccion,$telefono,$p_correo,$credencial,$fechaNac,$alergias,$padecimientos,$foto,$sexo);
            
            if($resultado){
               $session->msg('s',"Paciente se ha agregado exitosamente. ");
               redirect('cliente.php', false);
            }else{
               $session->msg('d','Lo siento, falló el registro.');
               redirect('add_cliente.php', false);
            }
         }else{
            $session->msg('d','Lo siento, Paciente ya registrado.');
            redirect('add_cliente.php', false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('add_cliente.php',false);
      }
   }
?>
<?php include_once('../layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
      <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-9">
      <div class="panel panel-default">
         <div class="panel-heading">
            <strong>
               <span class="glyphicon glyphicon-th"></span>
               <span>Agregar Paciente</span>   
            </strong>
          	<img src="../../libs/imagenes/Logo.png" height="50" width="50" alt="" align="center">
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form method="post" action="add_cliente.php" enctype="multipart/form-data">
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4z"/>
                        <path d="M13.784 14c-.497-1.27-1.988-3-5.784-3s-5.287 1.73-5.784 3h11.568z"/>
                        <path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                     </span>
                     <input type="text" class="form-control" name="nom_cliente" placeholder="Nombre paciente">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-signpost-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 1.414V4h2V1.414a1 1 0 0 0-2 0zM1 5a1 1 0 0 1 1-1h10.532a1 1 0 0 1 .768.36l1.933 2.32a.5.5 0 0 1 0 .64L13.3 9.64a1 1 0 0 1-.768.36H2a1 1 0 0 1-1-1V5zm6 5h2v6H7v-6z"/>
                        </svg>
                     </span>
                     <input type="text" class="form-control" name="dir_cliente" placeholder="Dirección" >
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                     </span>
                     <input type="text" class="form-control" name="correo" placeholder="correo electrónico">
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
                           <input type="number" class="form-control" name="tel_cliente" placeholder="Teléfono">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="input-group">
                           <label class="col-sm-7 col-form-label">Fecha de nacimiento:</label>
                           <div class="col-md-5">
                              <input type="date" name="fechaNac">
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
                     <p><textarea name="alergias" class="form-control" placeholder="Alergias" maxlength="250" rows="4" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="padecimientos" class="form-control" placeholder="Padecimientos" maxlength="250" rows="4" style="resize: none"></textarea></p>
                  </div>
               </div>
               <label for="idcredencial"><?php echo "Siguiente id disponible $idCliente"; ?></label>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-info-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                     </span>
                     <input type="text" class="form-control" name="idcredencial" placeholder="id">
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-btn">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <label for="archivo">Seleccione el archivo:</label>
                     <input name="paciente" type="file" multiple="multiple" class="btn btn-primary btn-file">
                  </div>
               </div>    
               <div class="col text-center">
                  <button type="submit" name="add_cliente" class="btn btn-danger">Agregar paciente</button>
               </div>
           </form>
       </div>
   </div>
</div>
<?php include_once('../layouts/footer.php'); ?>
