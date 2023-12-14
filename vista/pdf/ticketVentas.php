<?php
require_once('../../modelo/load.php');

$user = current_user(); 
$idSucursal = $user['idSucursal'];

$idTicket = $_GET['idTicket'];

$nom_cliente = "";
$dir_cliente = "";
$tel_cliente = "";
$efectivo = 0;
$transferencia = 0;
$deposito = 0;
$tarjeta = 0;
$totalProd = 0;
$descPuntos = 0;
$descPorc = 0;

include ('../../libs/fpdf/pdf2.php');

$consFolio = buscaRegistroPorCampo('folio','dato',$idTicket);
$folio = $consFolio['id_folio'];

$consVenta = buscaClienteTicket($idTicket);

$fechaVenta = date("d/m/Y", strtotime ($consVenta['date']));
$usuario = $consVenta['usuario'];
$idCliente = $consVenta['idCliente'];

$consCliente = buscaRegistroPorCampo('cliente','idcredencial',$idCliente);

if ($consCliente != null){
   $nom_cliente = $consCliente['nom_cliente'];
   $dir_cliente = $consCliente['dir_cliente'];
   $tel_cliente = $consCliente['tel_cliente'];
}

$productos = buscaProductosTicket($idTicket);

$consPagos = buscaPagosSucursal($idTicket,$idSucursal);

foreach ($consPagos as $pago) {
   if ($pago['id_tipo'] == "1")
      $efectivo = $pago['cantidad'];
   if ($pago['id_tipo'] == "2")
      $transferencia = $pago['cantidad'];
   if ($pago['id_tipo'] == "3")
      $deposito = $pago['cantidad'];
   if ($pago['id_tipo'] == "4")
      $tarjeta = $pago['cantidad'];
}

$pdf=new PDF2($orientation='P',$unit='mm');
$pdf->AddPage('portrait','legal');
$pdf->SetMargins(10, 10, 10,10);
$pdf->SetAutoPageBreak(true,5); 
$pdf->SetFont('Arial','B',96);
$pdf->Cell(80,15,'             GLSoftST',0,1,'C');
$pdf->Cell(80,15,'    ',0,1,'C');
$pdf->SetFont('Arial','B',50);
$pdf->Cell(85,15,utf8_decode('                         Servicios Tecnológicos  '),0,1,'C');
$pdf->Cell(80,15,'    ',0,1,'C');
$pdf->Cell(80,15,'    ',0,1,'C');
$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',15);
$pdf->Cell(80,8,'  ',0,1,'C');
$pdf->Text(40,75,utf8_decode('Av.Adolfo López Mateos R1, Mz.36 Lt.10 Col. Río de Luz'));
$pdf->Text(23,82,utf8_decode('Ecatepec de Morelos, Méx. Tel:5588715568 Mail: glsoftst@hotmail.com'));
$pdf->Cell(90,15,'',0,0,'C',0);
$pdf->Cell(30,15,utf8_decode('Remisión:'),1,0,'C',1);
$pdf->Cell(30,15,utf8_decode($folio),1,0,'C',0);
$pdf->Cell(20,15,'Fecha: ',1,0,'C',1);
$pdf->Cell(30,15,utf8_decode($fechaVenta),1,0,'C',0);
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,'Cliente',1,0,'C',1);
if ($nom_cliente != ""){
   $pdf->CellFitScale(150,15,utf8_decode($nom_cliente),1,0,'L',1);
}else{
   $pdf->Cell(150,15,utf8_decode($nom_cliente),1,0,'L',1);   
}
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,utf8_decode('Dirección'),1,0,'C',1);
if ($dir_cliente != ""){
   $pdf->CellFitScale(150,15,utf8_decode($dir_cliente),1,0,'L',1);
}else{
   $pdf->Cell(150,15,utf8_decode($dir_cliente),1,0,'L',1);
}
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,utf8_decode('Teléfono'),1,0,'C',1);
$pdf->Cell(150,15,utf8_decode($tel_cliente),1,0,'L',1);
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',27);
$pdf->Cell(10,15,utf8_decode('N'),1,0,'C',1);
$pdf->Cell(110,15,'Producto',1,0,'C',1);
$pdf->Cell(40,15,'Cantidad',1,0,'C',1);
$pdf->Cell(40,15,'Precio',1,1,'C',1);
$pdf->SetFont('Arial','',27);
$item=0;
  
//set width for each column (6 columns)
$pdf->SetWidths(Array(10,110,40,40));

//set alignment
$pdf->SetAligns(Array('C','L','C','R'));

//set line height. This is the height of each lines, not rows.
$pdf->SetLineHeight(11);

//load json data
foreach ($productos as $producto) {
   //$row['PU'] = $row['precio']/$row['qty'];
   $pdf->Row(Array(
      $producto['contador'],
        utf8_decode($producto['nomProducto']),
        //money_format('%.2n', $row['PU']),
        $producto['cantidad'],
        $producto['precio'],
      ));
   $totalProd = $totalProd + $producto['precio'];      
   $descPuntos = $producto['descPuntos'];
}

$pdf->Cell(35,15,'Subtotal',1,0,'C',1);
$pdf->Cell(125,15,'',1,0,'C',1);
$pdf->Cell(40,15,money_format('%.2n',$totalProd),1,1,'R',1);

if ($descPuntos == 0 && $tarjeta > 0) {
   $comision = $tarjeta * .05;
   $totalcondescuento = $totalProd + ($tarjeta * .05);
}

if ($descPuntos > 0 && $tarjeta > 0) {
   $comision = $tarjeta * .05;
   $totalcondescuento = $totalProd + ($tarjeta * .05) - $descPuntos;
}

if ($descPuntos > 0 && $tarjeta == 0) {
   $totalcondescuento = $totalProd - $descPuntos;
}

if ($descPuntos == 0 && $tarjeta == 0) {
   $totalcondescuento = $totalProd;
}

   //$iva = $totalcondescuento * 0.16;
   //$totIva = $totalcondescuento + $iva;

if($descPuntos > 0){
  $pdf->CellFitScale(35,15,utf8_decode('Descuento'),1,0,'L',1);
  $pdf->Cell(125,15,'',1,0,'C',1);
  $pdf->Cell(40,15,'- '.money_format('%.2n',$descPuntos),1,1,'R',1);
}

if ($tarjeta > 0){
   $pdf->CellFitScale(35,15,utf8_decode('Comisión'),1,0,'L',1);
   $pdf->Cell(125,15,'',1,0,'C',1);
   $pdf->Cell(40,15,money_format('%.2n',$comision),1,1,'R',1);
}

$pdf->Cell(35,15,'Total',1,0,'C',1);
$pdf->Cell(125,15,'',1,0,'C',1);
$pdf->Cell(40,15,money_format('%.2n',$totalcondescuento),1,1,'R',1);
$pdf->SetFont('Arial','B',25);
$pdf->Cell(160,18,'Tipo de pago',1,0,'C',1);
$pdf->Cell(40,18,'',1,1,'C',1);

$pdf->SetFont('Arial','',25);
   
if($efectivo > 0){
   $pdf->Cell(160,18,'Efectivo',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$efectivo),1,1,'R',0);
}

if($transferencia > 0){
   $pdf->Cell(160,18,'Transferencia',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$transferencia),1,1,'R',0);  
}

if($deposito > 0){
   $pdf->Cell(160,18,utf8_decode('Depósito'),1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$deposito),1,1,'R',0);
}

if($tarjeta > 0){
   $pdf->Cell(160,18,'Tarjeta',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$tarjeta),1,1,'R',0);
}

/*$pdf->Cell(160,18,'IVA',1,0,'C',1);
  $pdf->Cell(40,18,money_format('%.2n',$iva),1,1,'C',1);
  $pdf->Cell(160,18,'Total',1,0,'C',1);
  $pdf->Cell(40,18,money_format('%.2n',$totIva),1,1,'C',1);*/

$pdf->Cell(80,15,'                                          "Gracias por su Preferencia."',0,1,'C');
$pdf->Cell(80,15,'                                             "GLSoftST"',0,1,'C');

//Siguiente página
/*$pdf->AddPage('portrait','legal');
$pdf->SetMargins(10, 10, 10,10);
$pdf->SetAutoPageBreak(true,5); 
$pdf->SetFont('Arial','B',96);
$pdf->Cell(80,15,'            GLSoftST',0,1,'C');
$pdf->Cell(80,15,'    ',0,1,'C');
$pdf->SetFont('Arial','B',50);
$pdf->Cell(80,15,utf8_decode('                         Servicios Tecnológicos  '),0,1,'C');
$pdf->Cell(80,20,'    ',0,1,'C');
$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(80,8,'  ',0,1,'C');
$pdf->Text(40,75,utf8_decode('Av.Adolfo López Mateos R1, Mz.36 Lt.10 Col. Río de Luz'));
$pdf->Text(23,82,utf8_decode('Ecatepec de Morelos, Méx. Tel:5588715568 Mail: glsoftst@hotmail.com'));
$pdf->Cell(90,15,'',0,0,'C',0);
$pdf->Cell(30,15,utf8_decode('Remisión:'),1,0,'C',1);
$pdf->Cell(30,15,utf8_decode($folio),1,0,'C',0);
$pdf->Cell(20,15,'Fecha: ',1,0,'C',1);
$pdf->Cell(30,15,utf8_decode($fechaVenta),1,0,'C',0);
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,'Cliente',1,0,'C',1);
if ($nom_cliente != ""){
   $pdf->CellFitScale(150,15,utf8_decode($nom_cliente),1,0,'L',1);
}else{
   $pdf->Cell(150,15,utf8_decode($nom_cliente),1,0,'L',1);   
}
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,utf8_decode('Dirección'),1,0,'C',1);
if ($dir_cliente != ""){
   $pdf->CellFitScale(150,15,utf8_decode($dir_cliente),1,0,'L',1);
}else{
   $pdf->Cell(150,15,utf8_decode($dir_cliente),1,0,'L',1);
}
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(50,15,utf8_decode('Teléfono'),1,0,'C',1);
$pdf->Cell(150,15,utf8_decode($tel_cliente),1,0,'L',1);
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->Cell(80,15,'  ',0,1,'C');
$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',27);
$pdf->Cell(10,15,utf8_decode('N'),1,0,'C',1);
$pdf->Cell(110,15,'Producto',1,0,'C',1);
$pdf->Cell(40,15,'Cantidad',1,0,'C',1);
$pdf->Cell(40,15,'Precio',1,1,'C',1);
$pdf->SetFont('Arial','',27);
$item=0;
$pdf->SetWidths(Array(10,110,40,40));

//set alignment
$pdf->SetAligns(Array('C','L','C','R'));

//set line height. This is the height of each lines, not rows.
$pdf->SetLineHeight(11);

//load json data
foreach ($productos as $producto) {
   //$row['PU'] = $row['precio']/$row['qty'];
   $pdf->Row(Array(
      $producto['contador'],
      utf8_decode($producto['nomProducto']),
      //money_format('%.2n', $row['PU']),
      $producto['cantidad'],
      $producto['precio'],
   ));
}

$pdf->Cell(35,15,'Subtotal',1,0,'C',1);
$pdf->Cell(125,15,'',1,0,'C',1);
$pdf->Cell(40,15,money_format('%.2n',$totalProd),1,1,'R',1);

if($descPuntos > 0){
  $pdf->CellFitScale(35,15,utf8_decode('Descuento'),1,0,'L',1);
  $pdf->Cell(125,15,'',1,0,'C',1);
  $pdf->Cell(40,15,'- '.money_format('%.2n',$descPuntos),1,1,'R',1);
}

if ($tarjeta > 0){
   $pdf->CellFitScale(35,15,utf8_decode('Comisión'),1,0,'L',1);
   $pdf->Cell(125,15,'',1,0,'C',1);
   $pdf->Cell(40,15,money_format('%.2n',$comision),1,1,'R',1);
}

$pdf->Cell(35,15,'Total',1,0,'C',1);
$pdf->Cell(125,15,'',1,0,'C',1);
$pdf->Cell(40,15,money_format('%.2n',$totalcondescuento),1,1,'R',1);
$pdf->SetFont('Arial','B',25);
$pdf->Cell(160,18,'Tipo de pago',1,0,'C',1);
$pdf->Cell(40,18,'',1,1,'C',1);

$pdf->SetFont('Arial','',25);

if($efectivo > 0){
   $pdf->Cell(160,18,'Efectivo',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$efectivo),1,1,'R',0);
}

if($transferencia > 0){
   $pdf->Cell(160,18,'Transferencia',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$transferencia),1,1,'R',0);  
}

if($deposito > 0){
   $pdf->Cell(160,18,utf8_decode('Depósito'),1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$deposito),1,1,'R',0);
}

if($tarjeta > 0){
   $pdf->Cell(160,18,'Tarjeta',1,0,'C',0);
   $pdf->Cell(40,18,money_format('%.2n',$tarjeta),1,1,'R',0);
}*/
   
/*$pdf->Cell(160,18,'IVA',1,0,'C',1);
$pdf->Cell(40,18,money_format('%.2n',$iva),1,1,'C',1);
$pdf->Cell(160,18,'Total',1,0,'C',1);
$pdf->Cell(40,18,money_format('%.2n',$totIva),1,1,'C',1);*/

/*$pdf->Cell(80,15,'                                          "Gracias por su Preferencia."',0,1,'C');
$pdf->Cell(80,15,'                                             "GLSoftST"',0,1,'C');*/

$Name_PDF="ticket_".$folio.".pdf";
$pdf->Output('D',$Name_PDF);
?>