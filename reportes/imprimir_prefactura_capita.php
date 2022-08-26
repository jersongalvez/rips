<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////        VISTA REPORTE PRE FACTURA         ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
//////////////////        AMBITO: PROCESAMIENTO CAPITA    //////////////////////
////////  VISTA PRINCIPAL PARA LA IMPRESION DE REPORTES DE PRE FACTURAS   //////
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

    //Metodos para conectarse con el modelo Autorizacion
    require '../modelos/Reporte.php';

    //Invocar las funciones generales en el proyecto.
    require '../controladores/funciones_generales.php';

    $nit_prestador = $_GET["n_prestador"];
    $periodo       = $_GET["n_periodo"];

    //Nombre del prestador 
    $nom_prestador = Reporte::getNomPrestador($nit_prestador);

    //Prefacturas
    $prefacturas = Reporte::buscar_contratoCapitacion($nit_prestador, $periodo);

    //////////////////////////////////////////////////////////////
    class PDF extends FPDF {

        // Cabecera de página
        function Header() {
            // Logo
            $this->Image('../public/img/banner_fcapita.png', 6, 0, 200, 0);

            // Salto de línea
            $this->Ln(30);

            $this->SetFont('Arial', 'B', 12);
            $this->SetXY(15, 30);
        }

        // Pie de página
        function Footer() {
            // Posición: a 3.7 cm del final
            $this->SetY(-34);
            // Arial italic 8
            $this->SetFont('Arial', '', 8);

            // Número de página
            $this->Cell(0, 10, utf8_decode('Impreso por: ' . $_SESSION["COD_USUARIO"] . '  ' . getRealIP() . ' - ' . date("d/m/Y H:i:s")), 0, 0, 'L');
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
        }

        // Datos archivo de control
        function mostrarValoresContrato($header, $dataCapita) {

            // Colores, ancho de línea y fuente en negrita
            $this->SetFillColor(41, 118, 64);
            $this->SetTextColor(255);
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(.3);
            $this->SetFont('', 'B', 7);

            // Cabecera
            $w = array(34, 21, 33, 34, 30, 32);

            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $this->Ln();


            // Restauración de colores y fuentes
            $this->SetFillColor(235, 235, 235);
            $this->SetFillColor(235, 235, 235);
            $this->SetTextColor(0);
            $this->SetFont('', '', 7);

            // Datos
            $fill = false;
            foreach ($dataCapita as $tablaDatos) {
                $this->Cell($w[0], 6, $tablaDatos['NUM_CONTRATO'], 'LR', 0, 'C', $fill);
                $this->Cell($w[1], 6, $tablaDatos['NUM_AFILIADOS'], 'LR', 0, 'C', $fill);
                $this->Cell($w[2], 6, '$' . $this->formatearNumero($tablaDatos['VR_MES_ANTICIPADO']), 'LR', 0, 'C', $fill);
                $this->Cell($w[3], 6, '$' . $this->formatearNumero($tablaDatos['RECONOCIMIENTOS']), 'LR', 0, 'C', $fill);
                $this->Cell($w[4], 6, '$' . $this->formatearNumero($tablaDatos['RESTITUCIONES']), 'LR', 0, 'C', $fill);
                $this->Cell($w[5], 6, '$' . $this->formatearNumero($tablaDatos['VR_FINAL_CAPITA']), 'LR', 0, 'C', $fill);
                $this->Ln();
                $fill = !$fill;
            }

            // Línea de cierre
            $this->Cell(array_sum($w), 0, '', 'T');
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

    //Encabezado de la tabla de datos
    $columasSAU = array('CONTRATO', 'AFILIADOS', 'VR_MES_ANTICIPADO', 'RECONOCIMIENTOS', 'RESTITUCIONES', 'VR_FINAL_CAPITA');

    //Meses del año
    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");


    ################## Fecha de expedicion del documento #########################
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 36);
    $pdf->Cell(15, 6, utf8_decode('Ibagué, '), 0, 1);

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(25, 36);
    //$pdf->Cell(15, 6, date("d") . ' de ' . strftime(date('F')) . ' de ' . date('Y'), 0, 1);
    $pdf->Cell(15, 6, date("d") . ' de ' . $meses[date("n") - 1] . ' de ' . date("Y"), 0, 1);
    //--------------------------------------------------------------
    //Ciudad
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 50);
    $pdf->Cell(15, 6, utf8_decode('Señor (es) '), 0, 1);
    //--------------------------------------------------------------
    //Prestador
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetXY(10, 55);
    $pdf->Cell(15, 6, utf8_decode($nom_prestador["NOM_PRESTADOR"]), 0, 1);
    //-------------------------------------------------------------
    //Remitente
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 60);
    $pdf->Cell(15, 6, utf8_decode('Att: Gerencia '), 0, 1);
    //--------------------------------------------------------------
    //Ciudad y departamento del prestador
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 65);
    $pdf->Cell(15, 6, utf8_decode(' '), 0, 1);

    ############################ Referencia del oficio ####################################
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetXY(94, 75);
    $pdf->Cell(15, 6, utf8_decode('Ref.: '), 0, 1);

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(105, 75);
    $pdf->Cell(15, 6, utf8_decode('Prefactura contratos capitados - periodo ' . $periodo), 0, 1);


    ############################ Encabezado inicial del oficio ####################################
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 85);
    $pdf->Cell(15, 6, utf8_decode('Respetado (s) Señor (es): '), 0, 1);


    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(10, 95);
    $pdf->MultiCell(0, 6, utf8_decode('En atención a la referencia y de acuerdo a los contratos de prestación de servicios de baja complejidad suscritos bajo la modalidad '
                    . 'de capitación nos permitimos informar el valor que por parte de la EPSI se calculó para cada uno de los acuerdos de voluntades:'), 0, 'J');

    ############################ Grilla con los datos de facturacion ####################################


    $pdf->Ln(6);

    //Tabla que lista los servicios autorizados
    if ($prefacturas) {
        $pdf->mostrarValoresContrato($columasSAU, $prefacturas);
    }

    ############################ Segunda parte del encabezado ####################################

    $pdf->Ln(3.5);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, utf8_decode('La presente pre-factura y liquidación corresponde exclusivamente a los afiliados asegurados por la EPSI y pagados a través '
                    . 'de la Liquidación Mensual de Afiliados (LMA) entregada por el MSPS y en ningún momento incluye la prestación efectiva de los servicios '
                    . '(frecuencias de uso y estimación de actividades) las cuales serán evaluadas por la supervisión y/o interventoría del contrato.'), 0, 'J');


    $pdf->Ln(3.5);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, utf8_decode('Se deja constancia que los valores reportados no eximen a la IPS de las revisiones, calculo y liquidación de la capita '
                    . 'al interior de su institución y las diferencias que se llegaren a presentar se revisaran y conciliaran en la vigencia del contrato y/o en el '
                    . 'proceso de liquidación del mismo.'), 0, 'J');


    $pdf->Ln(3.5);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, utf8_decode('La presente se expide a los ' . date("d") . ' días del mes de ' . $meses[date("n") - 1] . ' de ' . date("Y") . ' '
                    . 'a solicitud del usuario ' . $_SESSION["COD_USUARIO"] . ' bajo dirección IP ' . getRealIP()
                    . ' a través de la plataforma web de la EPSI para efectos de la presentación '
                    . 'y legalización de la factura de prestación de servicios.'), 0, 'J');


    $pdf->Ln(3.5);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, utf8_decode('Cordialmente, '), 0, 'J');


    ############################ Firma Aseguramiento ####################################
    $pdf->Image('../public/img/firma_aseguramiento.png', 60, $pdf->GetY(), 90);



    //salida del pdf
    $pdf->Output('D', $periodo . '.pdf');
}


