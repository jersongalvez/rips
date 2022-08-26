<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////        VISTA REPORTE LOG ERRORES         ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////        VISTA PRINCIPAL PARA LA IMPRESION DE REPORTES RIPS       ///////
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

    if (isset($_POST["values_array"])) {

        //datos del prestador
        $errores = $_POST["values_array"];

        class PDF extends FPDF {

            // Cabecera de página
            function Header() {
                // Logo
                $this->Image('../public/img/bannerlog.png', 125, 5, 95, 25);

                // Salto de línea
                $this->Ln(20);
            }

            // Pie de página
            function Footer() {
                // Posición: a 2 cm del final
                $this->SetY(-20);
                // Arial italic 8
                $this->SetFont('Courier', 'B', 8);
                // Número de página
                $this->Cell(0, 10, utf8_decode('Generado el: ') . date("d/m/Y", $time = time()), 0, 0, 'L');
                $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
            }

            // Datos archivo del log de errores
            function mostrarError($log_errores) {

                $resul = explode(",", $log_errores);

                // Colores, ancho de línea y fuente en negrita
                $this->SetFillColor(254, 254, 254);
                $this->SetTextColor(255);
                $this->SetDrawColor(255, 255, 255);

                // Datos
                $fill = false;
                foreach ($resul as $tablaError) {

                    $negrilla = (preg_match("/-----/", $tablaError)) ? "B" : "";

                    $this->SetTextColor(0);
                    $this->SetFont('', $negrilla, 9.5);

                    $this->MultiCell(0, 3, utf8_decode($tablaError), 0, 'L', $fill);
                    $this->Ln();
                    $fill = !$fill;
                }
            }

        }

        // Creación del objeto de la clase heredada
        $pdf = new PDF('L', 'mm', 'Legal');
        $pdf->SetMargins(10, 15, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AliasNbPages();
        $pdf->AddPage();


        //Titulo del informe
        $pdf->SetFont('Courier', 'B', 12);
        $pdf->cell(0, 6, utf8_decode('Informe de errores encontrados en el proceso de validación'), 0, 0, 'C');
        $pdf->Ln(10);

        //Tabla de datos
        if ($errores) {
            $pdf->mostrarError($errores);
        }

        //Salida del pdf
        $pdf->Output('D', 'Informe_errores.pdf');
    }
}




