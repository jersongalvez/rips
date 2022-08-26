<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////        VISTA SOPORTE VALIDACION          ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////        VISTA PRINCIPAL PARA LA IMPRESION DE SOPORTES RIPS       ///////
////////////////////////////////////////////////////////////////////////////////
//zona horaria colombia
date_default_timezone_set("America/Bogota");


//Verifico si hay una sesion creada
if (strlen(session_id()) < 1) {

    session_start();
}


if (!isset($_SESSION["COD_USUARIO"])) {

    echo 'Debe ingresar al sistema correctamente para visualizar el reporte.';
} else {

    //clase fpdf
    require('../public/fpdf/fpdf.php');
    //Metodos para conectarse con el AF
    require '../modelos/Reporte.php';

    //datos del prestador
    $prestador = $_GET["c_prestador"];
    $remision = $_GET["remision"];

    //datos del prestador
    $encabezado = Reporte::buscar_remision($remision, $prestador);

    //datos del archivo de control
    $inf_control = Reporte::info_control($remision, $prestador);

    //datos del archivo de transacciones
    $inf_af = Reporte::buscar_transaccion($remision, $prestador);

    //sumas totales de la facturacion
    $valores_af = Reporte::valores_neto($remision, $prestador);

    //////////////////////////////////////////////////////////////
    class PDF extends FPDF {

        // Cabecera de página
        function Header() {
            // Logo
            $this->Image('../public/img/bannerpdf.png', 57, 5, 95, 25);

            // Salto de línea
            $this->Ln(30);
        }

        // Pie de página
        function Footer() {
            // Posición: a 3,7 cm del final
            $this->SetY(-37);
            // Arial italic 8
            $this->SetFont('Courier', '', 8);
            // Número de página
            $this->Cell(0, 10, utf8_decode('Impreso por: ' . $_SESSION["COD_USUARIO"] . ' ' . date("d/m/Y H:i:s")), 0, 0, 'L');
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
        }

        // datos archivo de control
        function mostrarControl($header, $dataControl) {

            // Colores, ancho de línea y fuente en negrita
            $this->SetFillColor(41, 118, 64);
            $this->SetTextColor(255);
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B', 10);

            // Cabecera
            $w = array(47.5, 47.5, 47.5, 47.5);

            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $this->Ln();


            // Restauración de colores y fuentes
            $this->SetFillColor(235, 235, 235);
            $this->SetFillColor(235, 235, 235);
            $this->SetTextColor(0);
            $this->SetFont('', '', 10);

            // Datos
            $fill = false;
            foreach ($dataControl as $tablaControl) {
                $this->Cell($w[0], 6, $tablaControl['ID_ARCHIVO'], 'LR', 0, 'C', $fill);
                $this->Cell($w[1], 6, $tablaControl['COD_ARCHIVO'], 'LR', 0, 'C', $fill);
                $this->Cell($w[2], 6, $tablaControl['F_REMISION'], 'LR', 0, 'C', $fill);
                $this->Cell($w[3], 6, $tablaControl['TOTAL_ARCHIVOS'], 'LR', 0, 'C', $fill);
                $this->Ln();
                $fill = !$fill;
            }

            // Línea de cierre
            $this->Cell(array_sum($w), 0, '', 'T');
        }

        //datos del archivo de transacciones
        function mostrarTransaccion($header, $dataTransaccion) {

            // Colores, ancho de línea y fuente en negrita
            $this->SetFillColor(41, 118, 64);
            $this->SetTextColor(255);
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B', 10);

            // Cabecera
            $w = array(15, 38, 30, 30, 30, 47);
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $this->Ln();

            // Restauración de colores y fuentes
            $this->SetFillColor(235, 235, 235);
            $this->SetTextColor(0);
            $this->SetFont('', '', 10);

            // Datos
            $fill = false;
            foreach ($dataTransaccion as $pos => $tablaAF) {
                $this->Cell($w[0], 6, ($pos + 1), 'LR', 0, 'C', $fill);
                $this->Cell($w[1], 6, $tablaAF['NUM_FACTURA'], 'LR', 0, 'C', $fill);
                $this->Cell($w[2], 6, '$' . $this->formatearNumero($tablaAF['VAL_COPAGO']), 'LR', 0, 'R', $fill);
                $this->Cell($w[3], 6, '$' . $this->formatearNumero($tablaAF['VAL_COMISION']), 'LR', 0, 'R', $fill);
                $this->Cell($w[4], 6, '$' . $this->formatearNumero($tablaAF['VAL_DESCUENTO']), 'LR', 0, 'R', $fill);
                $this->Cell($w[5], 6, '$' . $this->formatearNumero($tablaAF['VAL_PAGO_ENTIDAD']), 'LR', 0, 'R', $fill);
                $this->Ln();
                $fill = !$fill;
            }


            $this->Cell(array_sum($w), 0, '', 'T');
        }

        //datos de la suma total de la operacion
        function mostrarTotalesAF($header, $dataTotales) {

            // Colores, ancho de línea y fuente en negrita
            $this->SetFillColor(235, 235, 235);
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B', 10);

            // Cabecera
            $w = array(42, 42, 42, 63);
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $this->Ln();

            // Restauración de colores y fuentes
            $this->SetFont('', '', 10);


            $this->Cell($w[0], 6, '$' . $this->formatearNumero($dataTotales['COPAGO']), 'LR', 0, 'R');
            $this->Cell($w[1], 6, '$' . $this->formatearNumero($dataTotales['COMISION']), 'LR', 0, 'R');
            $this->Cell($w[2], 6, '$' . $this->formatearNumero($dataTotales['DESCUENTO']), 'LR', 0, 'R');
            $this->Cell($w[3], 6, '$' . $this->formatearNumero($dataTotales['TOTAL_NETO']), 'LR', 0, 'R');
            $this->Ln();
        }

        /**
         * Formatea un numero separandolo en miles
         * @param type $numero
         * @return int
         */
        private function formatearNumero($numero) {

            $resultado = number_format($numero, 2, ",", ".");

            return $resultado;
        }

    }

    // Creación del objeto de la clase heredada
    $pdf = new PDF();
    $pdf->SetMargins(10, 15, 15);
    $pdf->SetAutoPageBreak(true, 35);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $columasCt = array('Tipo Archivo', 'Nombre Archivo', 'Fecha Reportada', 'Total Registros');
    $columasAf = array('#', 'Factura', 'Copagos', 'Comisiones', 'Descuentos', 'Valor Total');
    $columasSum = array('Copagos', 'Comisiones', 'Descuentos', 'Total Neto');


    ////////// datos del prestador //////////
    //nombre prestador
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'Prestador:    ');
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, utf8_decode($encabezado['NOM_PRESTADOR']));
    $pdf->Ln(6);

    //nit prestador
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'NIT:          ');
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, (int) $encabezado['NUM_ENTIDAD']);
    $pdf->Ln(6);

    //remision
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, utf8_decode('Remisión:     '));
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, $encabezado['NUM_REMISION']);
    $pdf->Ln(6);

    //modalidad
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'Modalidad:    ');
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, $encabezado['MOD_CONTRATO']);
    $pdf->Ln(6);

    //fecha de cargue
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'Fecha Cargue: ');
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, $encabezado['F_CARGUE']);
    $pdf->Ln(13);


    //datos del archivo de control
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'Archivos relacionados: ');
    $pdf->Ln(7);

    //tabla del ct
    if ($inf_control) {
        $pdf->mostrarControl($columasCt, $inf_control);
    }


    //datos del archivo de transacciones
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, utf8_decode('Datos de facturación:'));
    $pdf->Ln(7);

    //tabla del af
    if ($inf_af) {
        $pdf->mostrarTransaccion($columasAf, $inf_af);
    }


    //datos de las sumas del af
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, utf8_decode('Valores totales según RIPS:'));
    $pdf->Ln(7);


    if ($valores_af) {
        $pdf->mostrarTotalesAF($columasSum, $valores_af);
    }

    $pdf->Ln(10);

    //fechas de recepcion en la epsi
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, utf8_decode('Fecha de recepción en la EPSI: '));
    $pdf->SetFont('Courier', '', 11);
    $pdf->Write(5, utf8_decode('Día____  Mes____  Año____'));
    $pdf->Ln(20);



    //firmas
    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, 'Prestador: ');
    $pdf->Write(5, '______________________      ');

    $pdf->SetFont('Courier', 'B', 11);
    $pdf->Write(5, utf8_decode('Cuentas Médicas: '));
    $pdf->Write(5, '______________________');


    //salida del pdf
    $pdf->Output('D', $remision . '.pdf');
}



