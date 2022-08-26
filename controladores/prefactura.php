<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////         CONTROLADOR PRE FACTURA           //////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////////  AMBITO: PROCESAMIENTO DE BUSQUEDA DE PRE FACTURAS  ///////////////
////////////////////////////////////////////////////////////////////////////////

require_once '../modelos/Rips.php';

$capita = new Rips();

//envio de operaciones por medio de peticiones ajax
switch ($_GET["op"]) {


    //Filtrar las pre facturas cargadas por periodo
    case 'buscar_contrato':

        $nit_prestador = $_REQUEST["nit_prestador"];
        $periodo       = $_REQUEST["n_periodo"];

        $consulta = $capita->buscar_contratoCapitacion($nit_prestador, $periodo);

        $resultado = ($consulta !== false) ? $consulta : array();

        $data = Array();

        foreach ($resultado as $respuesta) {

            $data[] = array(
                "0" => "<a target='_blank' href='../../reportes/imprimir_prefactura_capita.php?n_prestador=" . $respuesta['NIT_PRESTADOR'] . "&n_periodo=" . $respuesta['PERIODO'] . "'> <button class='button is-warning is-small'> <span class='icon is-small'> <i class='zmdi zmdi-download'></i> </span> </button> </a>",
                "1" => $respuesta["PERIODO"],
                "2" => $respuesta["CANT_CONTRATOS"],
                "3" => $respuesta["VF_CAP"]
            );
        }

        $results = array(
            "sEcho" => 1, //Informacion para el datatables
            "iTotalRecords" => count($data), //Enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), //Enviamos el total de registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;
}
    