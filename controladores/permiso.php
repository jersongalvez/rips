<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////           CONTROLADOR PERMISOS           ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: PROCESAMIENTO DE PERMISOS  ////////////////////
////////////////////////////////////////////////////////////////////////////////


require_once '../modelos/Permiso.php';

$permiso = new Permiso();

$cod_permiso = isset($_POST["cod_permiso"]) ? $permiso->limpiarCadena($_POST["cod_permiso"]) : "";
$nombre      = isset($_POST["nombre"]) ? $permiso->limpiarCadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"]) ? $permiso->limpiarCadena($_POST["descripcion"]) : "";


switch ($_GET["op"]) {


    //Inserta o edita los permisos registrados en el sistema
    case 'guardaryeditar':

        if (empty($cod_permiso)) {

            $rspta = $permiso->insertar($nombre, $descripcion);
            echo $rspta ? "Permiso registrado" : "Permiso no se pudo registrar";
        } else {

            $rspta = $permiso->editar($cod_permiso, $nombre, $descripcion);
            echo $rspta ? "Permiso actualizado" : "Permiso no se pudo actualizar";
        }
        break;


    //Lista los datos de la tabla prestadores
    case 'listar':

        $consulta = $permiso->listar();

        $data = Array();


        foreach ($consulta as $respuesta) {

            $data[] = array(
                "0" => ($respuesta["COD_ESTADO"] == 'A') ?
                "<button class='button is-info is-small' onclick= mostrar(" . $respuesta['COD_MENU'] . ")> <span class='icon is-small'> <i class='fa fa-pencil'></i> </span> </button>" .
                " <button class='button is-danger is-small' onclick= desactivar(" . $respuesta['COD_MENU'] . ")> <span class='icon is-small'> <i class='fa fa-close'></i> </span> </button>" :
                "<button class='button is-info is-small' onclick= mostrar(" . $respuesta['COD_MENU'] . ")> <span class='icon is-small'> <i class='fa fa-pencil'></i> </span> </button>" .
                " <button class='button is-success is-small' onclick= activar(" . $respuesta['COD_MENU'] . ")> <span class='icon is-small'> <i class='fa fa-check'></i> </span> </button>",
                "1" => $respuesta["NOMBRE"],
                "2" => $respuesta["DESCRIPCION"],
                "3" => ($respuesta["COD_ESTADO"] == 'A') ? '<span class="tag is-info is-size-7"> ACTIVO </span>' : '<span class="tag is-danger is-size-7"> INACTIVO </span>'
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

    //Cambia el estado a activo 'A' de un permiso
    case 'activar':

        $rspta = $permiso->activar($cod_permiso);
        echo $rspta ? "Permiso activado" : "Permiso no se puede activar";
        break;

    //Cambia el estado a inactivo 'I' de un permiso
    case 'desactivar':

        $rspta = $permiso->desactivar($cod_permiso);
        echo $rspta ? "Permiso desactivado" : "Permiso no se puede desactivar";
        break;


    //Carga en el formulario los datos de un permiso
    case 'mostrar':

        $rspta = $permiso->mostrar($cod_permiso);
        echo json_encode($rspta);
        break;
}
    