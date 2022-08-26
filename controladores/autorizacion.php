<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////        CONTROLADOR AUTORIZACION           //////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
//////////////   AMBITO: PROCESAMIENTO DE AUTORIZACIONES WEB     ///////////////
////////////////////////////////////////////////////////////////////////////////

//Verifico si hay una sesion creada
if (strlen(session_id()) < 1) {

    session_start();
}

require_once '../modelos/Autorizacion.php';

$autorizacion = new Autorizacion();

$no_solicitud = isset($_POST["n_solicitud"]) ? $autorizacion->limpiarCadena($_POST["n_solicitud"]) : "";

//envio de operaciones por medio de peticiones ajax
switch ($_GET["op"]) {

    //Carga el nombre y regimen de un afiliado
    case 'consultar_afiliadoSub':

        $tp_documento = $autorizacion->limpiarCadena($_REQUEST["tip_documento"]);
        $nu_documento = $autorizacion->limpiarCadena($_REQUEST["numd_afiliado"]);

        $rspta = $autorizacion->consultar_afiliadoSub($tp_documento, $nu_documento);
        echo json_encode($rspta);
        break;


    //Busca autorizaciones por afiliado y prestador
    case 'buscar_autafiliado':

        //zona horaria colombia
        date_default_timezone_set("America/Bogota");

        $nit_prestador = $autorizacion->limpiarCadena($_REQUEST["nit_prestador"]);
        $fecha_inicio  = date('Y-m-d');
        $fecha_fin     = date('Y-m-d', strtotime($fecha_inicio . ' - 366 days'));
        $tip_documento = $autorizacion->limpiarCadena($_REQUEST["tip_documento"]);
        $num_documento = $autorizacion->limpiarCadena($_REQUEST["numd_afiliado"]);

        $consulta = $autorizacion->buscar_autorizacionAfil($nit_prestador, $fecha_fin, $fecha_inicio, $tip_documento, $num_documento);

        $resultado = ($consulta !== false) ? $consulta : array();

        $data = Array();

        foreach ($resultado as $respuesta) {

            $boton = $_SESSION["COD_USUARIO"] == "JGALVEZ" || $_SESSION["COD_USUARIO"] == "RRAMIREZ" || $_SESSION["COD_USUARIO"] == "JCRUZ" || $nit_prestador ? "<button class='button is-primary is-small' onclick= aceptar_servicio('" . $respuesta["NO_SOLICITUD"] . "')> <span class='icon is-small'> <i class='zmdi zmdi-check-square'></i> </span> </button>" : "" ;

    $data[] = array(
        "0" => ($respuesta["ESTADO"] == 'AU' && $respuesta["VENCIMIENTO"] == 'N') ?
        "<button class='button is-link is-small details-control' id='" . $respuesta["NO_SOLICITUD"] . "'> <span class='icon is-small'> <i class='zmdi zmdi-plus'></i> </span> </button>" .
        "<a target='_blank' href='../../reportes/imprimir_autorizacion.php?n_solicitud=" . $respuesta["NO_SOLICITUD"] . "'> <button class='button is-dark is-small'> <span class='icon is-small'> <i class='zmdi zmdi-download'></i> </span> </button> </a>" .
        $boton:
        "<button class='button is-link is-small details-control' id='" . $respuesta["NO_SOLICITUD"] . "'> <span class='icon is-small'> <i class='zmdi zmdi-plus'></i> </span> </button>" .
        " <a target='_blank' href='../../reportes/imprimir_autorizacion.php?n_solicitud=" . $respuesta["NO_SOLICITUD"] . "'> <button class='button is-dark is-small'> <span class='icon is-small'> <i class='zmdi zmdi-download'></i> </span> </button> </a>",

        "1" => $respuesta["NO_AUTORIZACION"],
        "2" => $respuesta["NO_SOLICITUD"],
        "3" => $respuesta["F_INI"],
        "4" => $respuesta["F_FIN"],
        "5" => $respuesta["NOM_PRESTADOR"],
        "6" => ($respuesta["ESTADO"] == 'AU' && $respuesta["VENCIMIENTO"] == 'N') ? '<span class="tag is-primary is-size-7"> AUTORIZADA </span>' :
        ($respuesta["ESTADO"] == 'AN' ? '<span class="tag is-danger is-size-7"> ANULADA </span>'
            : ($respuesta["ESTADO"] == 'CO' ? '<span class="tag is-warning has-text-black-bis is-size-7"> COBRADA </span>'
            : ($respuesta["ESTADO"] == 'NC' ? '<span class="tag is-info is-size-7"> NO COBRADA </span>'
            : '<span class="tag is-dark is-size-7"> VENCIDA </span>')))
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

        
    //Buscar servicios autorizados
    case 'buscar_servicioautorizado':

        $no_solicitud = $autorizacion->limpiarCadena($_REQUEST["n_solicitud"]);

        $rspta = $autorizacion->buscar_servicioautorizado($no_solicitud);
        echo json_encode($rspta);

        break;


    //Comfirma que la ips presto los servicios asociados a la autorizacion
    case 'aceptar_servicio':

        $rspta = $autorizacion->aceptar_servicio($no_solicitud, $_SESSION["COD_USUARIO"]);
        echo $rspta ? "Autorización actualizada" : "Autorización no se puede actualizar";
        break;
}
    