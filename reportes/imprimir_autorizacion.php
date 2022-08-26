<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////       VISTA REPORTE AUTORIZACION         ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
//////////////////  AMBITO: PROCESAMIENTO AUTORIZACIONES  //////////////////////
////////  VISTA PRINCIPAL PARA LA IMPRESION DE REPORTES DE AUTORIZACIONES //////
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
    require '../modelos/Autorizacion.php';

    //datos del prestador
    $no_solicitud = $_GET["n_solicitud"];


    //Encabezado 
    $encabezado = Autorizacion::encabezado_autorizacion($no_solicitud);

    //Servicios autorizados
    $servicios_autorizados = Autorizacion::buscar_servicioautorizado($no_solicitud);

    //////////////////////////////////////////////////////////////
    class PDF extends FPDF {

        private $no_solicitud;

        // Cabecera de página
        function Header() {
            // Logo
            $this->Image('../public/img/banneraut.png', 6, 0, 200, 0);

            // Salto de línea
            $this->Ln(30);

            $this->SetFont('Arial', 'B', 12);
            $this->SetXY(15, 30);
            $this->MultiCell(0, 0, utf8_decode('COPIA INFORMATIVA'), 0, 'C');
        }

        // Obtengo el numero de solicitud de la remision
        function n_solicitud($no_solicitud) {

            $this->no_solicitud = $no_solicitud;
        }

        // Pie de página
        function Footer() {
            // Posición: a 3.7 cm del final
            $this->SetY(-37);
            // Arial italic 8
            $this->SetFont('Arial', '', 8);

            // Número de página
            $this->MultiCell(0, 6, utf8_decode('NÚMERO DE SOLICITUD DE ORIGEN ' . $this->no_solicitud), 0, 'C');
            $this->Cell(0, 10, utf8_decode('Impreso por: ' . $_SESSION["COD_USUARIO"] . ' ' . date("d/m/Y H:i:s")), 0, 0, 'L');
            $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
        }

        // Datos archivo de control
        function mostrarServicioAutorizado($header, $dataSAU) {

            // Colores, ancho de línea y fuente en negrita
            $this->SetFillColor(255, 255, 255);
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(.3);
            $this->SetFont('Arial', 'B', 8);

            // Cabecera
            $w = array(20, 10, 160);

            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, utf8_decode($header[$i]), 1, 0, 'C', true);
            }
            $this->Ln();


            // Restauración de colores y fuentes
            $this->SetTextColor(0);
            $this->SetFont('Arial', '', 7);

            // Datos
            $fill = false;
            foreach ($dataSAU as $tablaSAU) {
                $this->Cell($w[0], 6, $tablaSAU['CD_SERVICIO'], 'LR', 0, 'C', $fill);
                $this->Cell($w[1], 6, $tablaSAU['CANTIDAD'], 'LR', 0, 'C', $fill);
                $this->MultiCell($w[2], 6, utf8_decode($tablaSAU['OBSERVACION']), 0, 'J', $fill);
                $fill = !$fill;
            }

            // Línea de cierre
            $this->Cell(array_sum($w), 0, '', 'T');
        }

    }

    // Creación del objeto de la clase heredada
    $pdf = new PDF();
    $pdf->SetMargins(10, 15, 15);
    $pdf->SetAutoPageBreak(true, 35);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->n_solicitud($no_solicitud);
    $columasSAU = array('CÓDIGO', 'CANT', 'DESCRIPCIÓN');


    ################## Numero de autorizacion #########################
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(59, 36);
    $pdf->Cell(15, 6, utf8_decode('NUMERO DE AUTORIZACIÓN '), 0, 1);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(112, 36);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NO_AUTORIZACION']), 0, 1);
    //--------------------------------------------------------------
    //Regimen
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 48);
    $pdf->Cell(15, 6, utf8_decode('REGIMEN '), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(25, 48);
    $pdf->Cell(15, 6, utf8_decode(($encabezado['REGIMEN'] == 'S') ? "SUBSIDIADO" : "MOVILIDAD"), 0, 1);
    //-------------------------------------------------------------
    //Fecha-hora inicio vigencia
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(52, 48);
    $pdf->Cell(15, 6, utf8_decode('FECHA INICIO VIGENCIA '), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(87, 48);
    $pdf->Cell(15, 6, utf8_decode($encabezado['F_INI_VIGENCIA']), 0, 1);

    //-------------------------------------------------------------
    //Fecha vencimiento
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(110, 48);
    $pdf->Cell(15, 6, utf8_decode('FECHA VENCIMIENTO '), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(141, 48);
    $pdf->Cell(15, 6, utf8_decode($encabezado['F_VENCIMIENTO']), 0, 1);
    //-------------------------------------------------------------
    //Estado autorizacion
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(167, 48);
    $pdf->Cell(15, 6, utf8_decode('ESTADO '), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(179, 48);
    $pdf->Cell(15, 6, ($encabezado["ESTADO"] == 'AU' && $encabezado["VENCIMIENTO"] == 'N') ? 'AUTORIZADA' : ($encabezado["ESTADO"] == 'AN' ? 'ANULADA' : ($encabezado["ESTADO"] == 'CO' ? 'COBRADA' : ($encabezado["ESTADO"] == 'NC' ? 'NO COBRADA' : 'VENCIDA'))), 0, 1);

    ################### Prestador remitente #############################
    $pdf->Line(10, 59, 210 - 10, 59);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY(70, 60);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(72, 6, utf8_decode('INFORMACIÓN DEL PRESTADOR REMITENTE'), 0, 1, '', 1);

    //-------------------------------------------------------------
    //Nombre prestador
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 67);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NOM_PRESTADOR']), 0, 1);
    //-------------------------------------------------------------
    //Nit prestador
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(161, 67);
    $pdf->Cell(15, 6, utf8_decode('NIT'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(167, 67);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NR_IDENT_PREST_IPS']), 0, 1);
    //-------------------------------------------------------------
    //Direccion prestador
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 73);
    $pdf->Cell(15, 6, utf8_decode('DIRECCIÓN'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(28, 73);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DIR_ATENCION']), 0, 1);
    //-------------------------------------------------------------
    //Telefono prestador
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(143, 73);
    $pdf->Cell(15, 6, utf8_decode('TELÉFONO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(160, 73);
    $pdf->Cell(15, 6, utf8_decode(substr($encabezado['TEL_ATENCION'], 0, 20)), 0, 1);

    //-------------------------------------------------------------
    //Departamento prestador

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 80);
    $pdf->Cell(15, 6, utf8_decode('DEPARTAMENTO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(35, 80);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DEP_PRESTADOR']), 0, 1);

    //-------------------------------------------------------------
    //Ciudad prestador

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(110, 80);
    $pdf->Cell(15, 6, utf8_decode('CIUDAD'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(122, 80);
    $pdf->Cell(15, 6, utf8_decode($encabezado['CIU_PRESTADOR']), 0, 1);

    ############################ Datos paciente ####################################

    $pdf->Line(10, 88, 210 - 10, 88);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY(85, 89);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(38, 6, utf8_decode('DATOS DEL PACIENTE'), 0, 1, '', 1);

    //-------------------------------------------------------------
    //Documento paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 95);
    $pdf->Cell(15, 6, utf8_decode('Nº DOCUMENTO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(10, 100);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DOCU_AFIL']), 0, 1);

    //-------------------------------------------------------------
    //Documento paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(45, 95);
    $pdf->Cell(15, 6, utf8_decode('NOMBRE'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(45, 100);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NOMBRE_AFIL']), 0, 1);

    //-------------------------------------------------------------
    //Fecha nacimiento paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(135, 95);
    $pdf->Cell(15, 6, utf8_decode('FEC. NACIMIENTO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(135, 100);
    $pdf->Cell(15, 6, utf8_decode($encabezado['FEC_NAC_AFIL']), 0, 1);

    //-------------------------------------------------------------
    //Edad paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(165, 95);
    $pdf->Cell(15, 6, utf8_decode('EDAD'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(165, 100);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NUM_EDAD']), 0, 1);

    //-------------------------------------------------------------
    //Sexo paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(180, 95);
    $pdf->Cell(15, 6, utf8_decode('SEXO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(180, 100);
    $pdf->Cell(15, 6, utf8_decode(($encabezado['SEXO'] == "M") ? "MASCULINO" : "FEMENINO"), 0, 1);

    //-------------------------------------------------------------
    //Tipo afiliado paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 107);
    $pdf->Cell(15, 6, utf8_decode('TIPO AFILIADO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(10, 112);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NOM_TIPO_AFIL']), 0, 1);

    //-------------------------------------------------------------
    //Nivel o estrato  paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(50, 107);
    $pdf->Cell(15, 6, utf8_decode('NIVEL O ESTRATO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(50, 112);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NIVEL']), 0, 1);

    //-------------------------------------------------------------
    //Lugar residencia paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(90, 107);
    $pdf->Cell(15, 6, utf8_decode('LUGAR RESIDENCIA'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(90, 112);
    $pdf->Cell(15, 6, utf8_decode($encabezado['CD_CIUDAD'] . ' ' . $encabezado['NM_CIUDAD']), 0, 1);

    //-------------------------------------------------------------
    //Lugar residencia paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(170, 107);
    $pdf->Cell(15, 6, utf8_decode('TELÉFONO'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(170, 112);
    $pdf->Cell(15, 6, utf8_decode($encabezado['TEL_MOVIL']), 0, 1);

    //-------------------------------------------------------------
    //Direccion paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 119);
    $pdf->Cell(15, 6, utf8_decode('DIRECCIÓN'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(10, 124);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DIR_RESIDENCIA']), 0, 1);

    //-------------------------------------------------------------
    //Grupo poblacional paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(125, 119);
    $pdf->Cell(15, 6, utf8_decode('GRUPO POBLACIONAL'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(125, 124);
    $pdf->Cell(15, 6, utf8_decode($encabezado['NOM_GRUPO']), 0, 1);

    ############################ Diagnostico paciente ####################################

    $pdf->Line(10, 132, 210 - 10, 132);

    //-------------------------------------------------------------
    //Diagnostico paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 133);
    $pdf->Cell(15, 6, utf8_decode('CÓDIGO DX'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(28, 133);
    $pdf->Cell(15, 6, utf8_decode($encabezado['COD_DIAGNOSTICO']), 0, 1);

    //-------------------------------------------------------------
    //Especialidad
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(40, 133);
    $pdf->Cell(15, 6, utf8_decode('ESPECIALIDAD'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(62, 133);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DES_ESPECIALIDAD']), 0, 1);

    //-------------------------------------------------------------
    //Origen
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 140);
    $pdf->Cell(15, 6, utf8_decode('ORIGEN'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(23, 140);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DES_CAUSAS']), 0, 1);

    //-------------------------------------------------------------
    //Clase autorizacion
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(70, 140);
    $pdf->Cell(15, 6, utf8_decode('CLASE AUTORIZACIÓN'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(103, 140);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DES_CLASE']), 0, 1);

    //-------------------------------------------------------------
    //Ubicacion del paciente
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(10, 147);
    $pdf->Cell(15, 6, utf8_decode('UBICACIÓN DEL PACIENTE'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(48, 147);
    $pdf->Cell(15, 6, utf8_decode($encabezado['DES_SERVICIO']), 0, 1);

    //-------------------------------------------------------------
    //Fecha orden medica
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(110, 147);
    $pdf->Cell(15, 6, utf8_decode('FECHA ORDEN MÉDICA'), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY(144, 147);
    $pdf->Cell(15, 6, utf8_decode($encabezado['F_ORDENMED']), 0, 1);


    ############################ Servicios autorizados ####################################

    $pdf->Line(10, 155, 210 - 10, 155);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY(83, 156);
    $pdf->SetFillColor(235, 235, 235);
    $pdf->Cell(44, 6, utf8_decode('SERVICIOS AUTORIZADOS'), 0, 1, '', 1);

    $pdf->Ln(1.5);

    //Tabla que lista los servicios autorizados
    if ($servicios_autorizados) {
        $pdf->mostrarServicioAutorizado($columasSAU, $servicios_autorizados);
    }

    $pdf->Ln(3.5);

    $pdf->SetFont('Arial', 'B', 8);
    //$pdf->SetX(83);
    $pdf->MultiCell(0, 6, utf8_decode('RECAUDO DEL PRESTADOR'), 0, 'L');

    $pdf->SetFont('Arial', '', 7);
    //$pdf->SetX(83);
    $pdf->MultiCell(0, 6, utf8_decode('SI CORRESPONDE A LA IPS EL RECAUDO DE CUOTAS MODERADORAS Y/O COPAGOS, FAVOR APLICAR LO ESTABLECIDO EN EL ACUERDO 260 DE 2004 Y DEMÁS NORMATIVIDAD DEL CASO.'), 0, 'J');

    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->MultiCell(0, 6, utf8_decode('OBSERVACIONES'), 0, 'L');

    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(0, 6, utf8_decode($encabezado['OBSERVACIONES']), 0, 'L');


    //salida del pdf
    $pdf->Output('D', $encabezado['NO_AUTORIZACION'] . '.pdf');
}


