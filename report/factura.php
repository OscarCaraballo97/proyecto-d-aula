<?php
session_start();
require './fpdf/fpdf.php';
include '../Consultas/consultasSql.php';
$id=$_GET['id'];
$sVenta=ejecutar::consultar("SELECT * FROM venta WHERE NumPedido='$id'");
$dVenta=mysqli_fetch_array($sVenta, MYSQLI_ASSOC);
$sCliente=ejecutar::consultar("SELECT * FROM cliente WHERE NIT='".$dVenta['NIT']."'");
$dCliente=mysqli_fetch_array($sCliente, MYSQLI_ASSOC);
class PDF extends FPDF{
}
ob_end_clean();
$pdf=new PDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetFont("Times","",20);
$pdf->SetMargins(25,20,25);
$pdf->SetFillColor(0,255,255);
$pdf->Cell (0,5,('E-Market'),0,1,'C');
$pdf->Ln(5);
$pdf->SetFont("Times","",14);
$pdf->Cell (0,5,('Factura de pedido numero '.$id),0,1,'C');
$pdf->Ln(20);
$pdf->SetFont("Times","b",12);
$pdf->Cell (33,5,('Fecha del pedido: '),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (37,5,($dVenta['Fecha']),0);
$pdf->Ln(12);
$pdf->SetFont("Times","b",12);
$pdf->Cell (37,5,('Nombre del cliente: '),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (100,5,($dCliente['NombreCompleto']." ".$dCliente['Apellido']),0);
$pdf->Ln(12);
$pdf->SetFont("Times","b",12);
$pdf->Cell (30,5,('Documento:'),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (25,5,($dCliente['NIT']),0);
$pdf->Ln(12);
$pdf->SetFont("Times","b",12);
$pdf->Cell (20,5,('Direccion: '),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (70,5,($dCliente['Direccion']),0);
$pdf->Ln(12);
$pdf->SetFont("Times","b",12);
$pdf->Cell (19,5,('Telefono: '),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (70,5,($dCliente['Telefono']),0);
$pdf->SetFont("Times","b",12);
$pdf->Cell (14,5,('Email: '),0);
$pdf->SetFont("Times","",12);
$pdf->Cell (40,5,($dCliente['Email']),0);
$pdf->Ln(10);
$pdf->SetFont("Times","b",12);
$pdf->Cell (76,10,('Nombre'),1,0,'C');
$pdf->Cell (30,10,('Precio'),1,0,'C');
$pdf->Cell (30,10,('Cantidad'),1,0,'C');
$pdf->Cell (30,10,('Subtotal'),1,0,'C');
$pdf->Ln(10);
$pdf->SetFont("Times","",12);
$suma=0;
$sDet=ejecutar::consultar("SELECT * FROM detalle WHERE NumPedido='".$id."'");
while($fila1 = mysqli_fetch_array($sDet, MYSQLI_ASSOC)){
    $consulta=ejecutar::consultar("SELECT * FROM producto WHERE CodigoProd='".$fila1['CodigoProd']."'");
    $fila=mysqli_fetch_array($consulta, MYSQLI_ASSOC);
    $pdf->Cell (76,10,($fila['NombreProd']),1,0,'C');
    $pdf->Cell (30,10,('$'.$fila1['PrecioProd']),1,0,'C');
    $pdf->Cell (30,10,($fila1['CantidadProductos']),1,0,'C');
    $pdf->Cell (30,10,('$'.$fila1['PrecioProd']*$fila1['CantidadProductos']),1,0,'C');
    $pdf->Ln(10);
    $suma += $fila1['PrecioProd']*$fila1['CantidadProductos'];
    mysqli_free_result($consulta);
}
$pdf->SetFont("Times","b",12);
$pdf->Cell (76,10,(''),1,0,'C');
$pdf->Cell (30,10,(''),1,0,'C');
$pdf->Cell (30,10,(''),1,0,'C');
$pdf->Cell (30,10,('$'.number_format($suma,2)),1,0,'C');
$pdf->Ln(10);
$pdf->Output('Factura-#'.$id,'I');
mysqli_free_result($sVenta);
mysqli_free_result($sCliente);
mysqli_free_result($sDet);