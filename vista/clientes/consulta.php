<?php
   $page_title = 'Consulta';
   require_once('../../modelo/load.php');
   include ('../../libs/fpdf/pdf.php');
   // Checkin What level user has permission to view this page
   page_require_level(2);
   $user = current_user(); 
   $usuario = $user['name'];

   $idPaciente = "";

   ini_set('date.timezone','America/Mexico_City');
   $fecha_actual=date('Y-m-d',time());
   $idCliente = isset($_GET['idCliente']) ? $_GET['idCliente']:'';
  
   if(isset($_POST['consulta'])){
      $idPaciente  = remove_junk($db->escape($_POST['idCliente']));
      $req_fields = array('receta','problema','diagnostico');
      validate_fields($req_fields);
      if(empty($errors)){
         $receta  = remove_junk($db->escape($_POST['receta']));
         $problema  = remove_junk($db->escape($_POST['problema']));
         $temp  = remove_junk($db->escape($_POST['temp']));
         $peso  = remove_junk($db->escape($_POST['peso']));
         $diagnostico  = remove_junk($db->escape($_POST['diagnostico']));
         $Nota  = remove_junk($db->escape($_POST['Nota']));
         $saturacion  = remove_junk($db->escape($_POST['saturacion']));
         $talla  = remove_junk($db->escape($_POST['talla']));
         $fc  = remove_junk($db->escape($_POST['fc']));
         $fr  = remove_junk($db->escape($_POST['fr']));
         $pa  = remove_junk($db->escape($_POST['pa']));

         $resultado = altaConsulta($receta,$diagnostico,$problema,$peso,$temp,$idPaciente,$fecha_actual,'0',$usuario,$Nota,$saturacion,$talla,$fc,$fr,$pa);

         if($resultado){
            $session->msg('s',"Registro Exitoso. ");
         
            $consPaciente = buscaRegistroPorCampo('cliente','idcredencial',$idPaciente);

            $nombre = $consPaciente['nom_cliente'];
            $alergias = $consPaciente['alergias'];
            $edad = $consPaciente['fechaNac'];
            $padecimientos = $consPaciente['padecimientos'];
            $sexo = $consPaciente['sexo'];

            $Recomendaciones = "RECOMENDACIONES:";
            $Tratamiento = "TRATAMIENTO:";
            $Diagnostico = "Diagnóstico: ";

            $fecha=date('d-m-Y',time());

            if ($edad != '0000-00-00'){
               $fecha_nacimiento = new DateTime($edad);
               $hoy = new DateTime();
               $edadMas = $hoy->diff($fecha_nacimiento);

               $anios = $edadMas->y;
               $meses = $edadMas->m;
               $dias = $edadMas->d;
            }

            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->AliasNBPages();
            //$pdf->Cell(190,10,utf8_decode('Receta Medica:'),0,1,'C');
            // $pdf->Cell(190,7,$nombre,0, 1 ,'C');

            $pdf->Cell(90,6,'  ',0,1,'C');
            $pdf->Cell(90,10,'  ',0,1,'C');
            //$pdf->Cell(190,7,utf8_decode('Información del Propetario'),1, 1 ,'C',0);
            $pdf-> SetFillColor(135,207,235);
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(20,6,utf8_decode('Paciente:'),0,0,'C',0);
            $pdf->Cell(35,6,utf8_decode($nombre),0,0,'L',0);
            //$pdf->Cell(10,6,'  ',0,1,'C');

            $pdf->Cell(100,6,utf8_decode('Fecha:'),0,0,'R',0);
            $pdf->Cell(35,6,($fecha),0,0,'L',0);
            $pdf->Cell(90,8,'  ',0,1,'C');
            //$pdf->Cell(190,7,utf8_decode('Información del Paciente'),1, 1 ,'C',0);

            $pdf->Cell(13,6,'Edad:',0,0,'C',0);
            $pdf->Cell(33,6,utf8_decode($anios." Años ".$meses." meses "),0,0,'L',0);
            $pdf->Cell(40,6,utf8_decode('Sexo:'),0,0,'R',0);
            $pdf->Cell(35,6,($sexo),0,0,'L',0);
            $pdf->Cell(26,6,utf8_decode('ID:'),0,0,'R',0);
            $pdf->Cell(7,6,utf8_decode($idPaciente),0,0,'L',0);
            $pdf->Cell(90,8,'  ',0,1,'C');
            $pdf->Cell(12,6,utf8_decode('Peso:'),0,0,'C',0);
            $pdf->Cell(14,6,$peso." Kg",0,0,'L',0);
            $pdf->Cell(24,6,'',0,0,'L',0);
            $pdf->Cell(52,6,utf8_decode('Temperatura:'),0,0,'R',0);
            $pdf->Cell(12,6,$temp." ".utf8_decode('°C'),0,0,'L',0); 
            $pdf->Cell(38,6,utf8_decode('Talla:'),0,0,'R',0);
            $pdf->Cell(12,6,$talla." mts",0,0,'L',0); 
            $pdf->Cell(90,8,'  ',0,1,'C');
            $pdf->Cell(8,6,utf8_decode('FC:'),0,0,'C',0);
            $pdf->Cell(9,6,$fc,0,0,'L',0);
            $pdf->Cell(30,6,'',0,0,'L',0);
            $pdf->Cell(35,6,utf8_decode('FR:'),0,0,'R',0);
            $pdf->Cell(12,6,$fr,0,0,'L',0); 
            $pdf->Cell(55,6,utf8_decode('PA:'),0,0,'R',0);
            $pdf->Cell(12,6,$pa,0,0,'L',0); 
            $pdf->Cell(90,8,'  ',0,1,'C');
            $pdf->Cell(19,6,utf8_decode('Alergias:'),0,0,'C',0);
            $pdf->Cell(35,6,utf8_decode($alergias),0,0,'L',0);
            $pdf->Cell(190,10,'  ',0,1,'C');
            $pdf->Cell(32,6,utf8_decode('Padecimientos:'),0,0,'C',0);
            $pdf->Cell(58,6,utf8_decode($padecimientos),0,0,'L',0);
            $pdf->Cell(190,10,'  ',0,1,'C');

            $pdf->SetFont('Arial','',8);
            //$pdf->Cell(25,6,utf8_decode('Diagnóstico:         '),0,0,'L',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            //$pdf->MultiCell(170,6,utf8_decode($diagnostico),0,'L',0);
            $strText = str_replace('\r','',$diagnostico);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Diagnostico.$strText;

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(9,100);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(9,100);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }

            $pdf->Cell(190,6,'  ',0,1,'C');
            //$pdf->Cell(32,6,utf8_decode('TRATAMIENTO:'),0,0,'C',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            $strText = str_replace('\r','',$receta);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Tratamiento."\\n".$strText;

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(9,116);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(9,116);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }

            $pdf->Cell(190,6,'  ',0,1,'C');

            //$pdf->Cell(35,6,utf8_decode('Recomendaciones:'),0,0,'C',0);
            //$pdf->Cell(90,6,'  ',0,1,'C');
            $strText = str_replace('\r','',$Nota);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n\n','\n',$strText);
            $strText = str_replace('\n\n\n','\n',$strText);
            $strText = str_replace('\n\n','\n',$strText);

            $strText = $Recomendaciones."\\n".$strText;
            //$strText = strtoupper($strText);

            $pos = strpos($strText,'\n');

            if ($pos == 0 && $pos != ""){
               $strText=substr($strText,2,strlen($strText));
            }

            $cont = 0;

            if (strpos($strText,'\n') == false){
               $strText = $strText."\\n";
            }

            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(9,190);
            //$pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);

            while (strlen($strText) > 0){
               $strPos = strpos($strText,'\n');
               if ($strPos == false){
                  $pdf->MultiCell(188,5,utf8_decode($strText),"0","J",false);
                  $strText = "";
               }else{
                  $strTextAux = substr($strText,0,$strPos);
                  if ($cont == 0){
                     $pdf->SetXY(9,190);
                     $cont++;
                  }
                  $pdf->MultiCell(188,5,utf8_decode($strTextAux),"0","J",false);
                  $strText = substr($strText,$strPos+2,strlen($strText));
               }
            }
            $pdf->Output('ticket.pdf', 'I');
         }else{
            $session->msg('d',' Lo siento, falló el registro.');
            redirect('consulta.php?idCliente='.$idPaciente, false);
         }
      }else{
         $session->msg("d", $errors);
         redirect('consulta.php?idCliente='.$idPaciente,false);
      }
   }

   $paciente = buscaRegistroPorCampo('cliente','idcredencial',$idCliente);
   $nomPaciente = $paciente['nom_cliente'];
?>
<?php include_once('../layouts/header.php'); ?>
<script type="text/javascript" src="../../libs/js/general.js"></script>

<body>
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
               <span>Consulta de:</span>
               <span><?php echo $nomPaciente ?></span>
            </strong>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
            <form name="form1" method="post" action="consulta.php" class="clearfix">
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-scale"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="peso" placeholder="Peso">Kg
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-info-sign"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="temp" placeholder="Temperatura">C
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-resize-vertical"></i>
                     </span>
                     <input type="number" step="0.01" class="form-control" name="talla" placeholder="Talla">mts
                  </div>
               </div>
               <br>
               <br>
               <br>
               <div class="col-md-3">
                  <div class="input-group">
                     <input type="number" step="0.01" class="form-control" name="saturacion" placeholder="Saturación">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="input-group">
                     <input type="number" step="0.01" class="form-control" name="fc" placeholder="FC">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="input-group">
                     <input type="number" step="0.01" class="form-control" name="fr" placeholder="FR">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="input-group">
                     <input type="text" class="form-control" name="pa" placeholder="PA">
                  </div>
               </div>
               <br>
               <br>
               <br>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="problema" class="form-control" placeholder="Historial clínico" maxlength="300" rows="3" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="diagnostico" class="form-control" placeholder="Diagnóstico (2 Renglones)" maxlength="150" rows="2" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="receta" class="form-control" placeholder="Receta (12 Renglones)" maxlength="1416" rows="12" style="resize: none"></textarea></p>
                  </div>
               </div>
               <div class="form-group">
                  <div class="input-group">
                     <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th-large"></i>
                     </span>
                     <p><textarea name="Nota" class="form-control" placeholder="Recomendaciones (4 Renglones)" maxlength="590" rows="5" style="resize: none" oninput="mayusculas(event)"></textarea></p>
                  </div>
               </div>
               <input type="hidden" value="<?php echo $idCliente ?>" name="idCliente">
               <div class="form-group" align="center">
                  <input type="button" name="button" onclick="regresaHistory();" class="btn btn-primary" value="Regresar">
                  <button type="submit" name="consulta" class="btn btn-danger">Imprimir y Guardar</button>
               </div>
            </form>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
<?php include_once('../layouts/footer.php'); ?>
