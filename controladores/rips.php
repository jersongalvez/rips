<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////             CONTROLADOR RIPS           /////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
//////////////  AMBITO: PROCESAMIENTO DE BUSQUEDA DE REMISIONES  ///////////////
////////////////////////////////////////////////////////////////////////////////

require_once '../modelos/Prestador.php';
require_once '../modelos/Rips.php';
require_once '../modelos/Reporte.php';

$prestador = new Prestador();
$rips = new Rips();
$transaccion = new Reporte();

$nit_prestador = isset($_POST["nit_prestador"]) ? $rips->limpiarCadena($_POST["nit_prestador"]) : "";
$num_remision = isset($_POST["num_remision"]) ? $rips->limpiarCadena($_POST["num_remision"]) : "";


//envio de operaciones por medio de peticiones ajax
switch ($_GET["op"]) {


    //Buscar prestadores de servicios
    case 'buscar_prestador':

        $nom_prestador = $_REQUEST["nomb_prestador"];

        $consulta = $prestador->buscar_prestador($nom_prestador);

        $resultado = ($consulta !== false) ? $consulta : array();

        $data = Array();

        foreach ($resultado as $respuesta) {

            $data[] = array(
                "0" => "<button class='button is-info is-small' onclick= asignar_nit('" . $respuesta['NIT_PRESTADOR'] . "')> <span class='icon is-small'> <i class='zmdi zmdi-download'></i> </span> </button>",
                "1" => $respuesta["COD_HABILITACION"],
                "2" => $respuesta["NIT_PRESTADOR"],
                "3" => $respuesta["NOM_PRESTADOR"],
                "4" => $respuesta["NOM_CIUDAD"],
                "5" => $respuesta["NOM_DEPARTAMENTO"]
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


    //Filtrar las remisiones por fecha de generacion
    case 'buscar_remisiones':

        $nit_prestador = $_REQUEST["nit_prestador"];
        $fecha_inicio = $_REQUEST["fecha_inicial"];
        $fecha_fin = $_REQUEST["fecha_final"];
        $modalidad = $_REQUEST["smodalidad"];

        $consulta = $rips->buscar_remisiones($nit_prestador, $fecha_inicio, $fecha_fin, $modalidad);

        $resultado = ($consulta !== false) ? $consulta : array();

        $data = Array();

        foreach ($resultado as $respuesta) {

            $data[] = array(
                "0" =>
                "<a target='_blank' href='../../reportes/imprimir_sopval.php?c_prestador=" . $respuesta['COD_PRESTADOR'] . "&remision=" . $respuesta['NUM_REMISION'] . "'> <button class='button is-warning is-small'> <span class='icon is-small'> <i class='zmdi zmdi-download'></i> </span> </button> </a>" .
                " <button class='button is-info is-small' onclick= mostrar_reporte('" . $respuesta['COD_PRESTADOR'] . "','" . $respuesta['NUM_REMISION'] . "')> <span class='icon is-small'> <i class='zmdi zmdi-eye'></i> </span> </button>",
                "1" => $respuesta["NUM_REMISION"],
                "2" => $respuesta["FEC_REMISION"],
                "3" => $respuesta["FEC_CARGUE"],
                "4" => ($respuesta["MOD_CONTRATO"] == '') ? "-- No Aplica --" : $respuesta["MOD_CONTRATO"],
                "5" => $respuesta["COD_USUARIO"]
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


    //Busca una remision
    case 'mostrar_encabezado':

        $rspta = $transaccion->buscar_remision($num_remision, $nit_prestador);
        echo json_encode($rspta);
        break;


    //Carga los datos del archivo de control
    case 'datos_control':

        $rspta = $transaccion->info_control($num_remision, $nit_prestador);
        echo json_encode($rspta);
        break;


    //Lista las facturas asociadas a un prestador
    case 'listar_facturas':

        $consulta = $transaccion->buscar_transaccion($num_remision, $nit_prestador);

        $data = Array();

        foreach ($consulta as $respuesta) {

            $data[] = array(
                "0" => $respuesta["NUM_FACTURA"],
                "1" => $respuesta["FECHA"],
                "2" => $respuesta["VAL_COPAGO"],
                "3" => $respuesta["VAL_COMISION"],
                "4" => $respuesta["VAL_DESCUENTO"],
                "5" => $respuesta["VAL_PAGO_ENTIDAD"]
            );
        }


        $results = array(
            "sEcho" => 0, //Informacion para el datatables
            "iTotalRecords" => count($data), //Enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), //Enviamos el total de registros a visualizar
            "aaData" => $data
        );


        echo json_encode($results);
        break;


    //Carga en el formulario los datos de un permiso
    case 'valores_neto':

        $rspta = $transaccion->valores_neto($num_remision, $nit_prestador);
        echo json_encode($rspta);
        break;
}
    