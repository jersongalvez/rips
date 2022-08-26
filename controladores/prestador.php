<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          CONTROLADOR PRESTADOR           ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: PROCESAMIENTO PRESTADORES  ////////////////////
////////////////////////////////////////////////////////////////////////////////


session_start();

require_once '../modelos/Prestador.php';

$prestador = new Prestador();

$nit_prestador  = isset($_POST["nit_prestador"]) ? $prestador->limpiarCadena($_POST["nit_prestador"]) : "";
$ni_prest       = isset($_POST["ni_prest"]) ? $prestador->limpiarCadena($_POST["ni_prest"]) : "";
$cod_usuario    = isset($_POST["cod_usuario"]) ? $prestador->limpiarCadena($_POST["cod_usuario"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? $prestador->limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento  = isset($_POST["num_documento"]) ? $prestador->limpiarCadena($_POST["num_documento"]) : "";
$nom_usuario    = isset($_POST["nom_usuario"]) ? $prestador->limpiarCadena($_POST["nom_usuario"]) : "";
$password       = isset($_POST["password"]) ? $prestador->limpiarCadena($_POST["password"]) : "";


switch ($_GET["op"]) {

    //Lista los datos de la tabla prestadores
    case 'listar_prestador':

        $consulta = $prestador->listar_prestador();

        $data = Array();

        foreach ($consulta as $respuesta) {

            $data[] = array(
                "0" => ($respuesta["COD_ESTADO_WEB"] == 'A') ?
                "<button class='button is-info is-small' onclick= mostrar('" . $respuesta['NIT_PRESTADOR'] . "')> <span class='icon is-small'> <i class='fa fa-eye'></i> </span> </button>" .
                " <button class='button is-danger is-small' onclick= desactivar_prestador('" . $respuesta['NIT_PRESTADOR'] . "')> <span class='icon is-small'> <i class='fa fa-close'></i> </span> </button>" :
                "<button class='button is-info is-small' onclick= mostrar('" . $respuesta['NIT_PRESTADOR'] . "')> <span class='icon is-small'> <i class='fa fa-eye'></i> </span> </button>" .
                " <button class='button is-success is-small' onclick= activar_prestador('" . $respuesta['NIT_PRESTADOR'] . "')> <span class='icon is-small'> <i class='fa fa-check'></i> </span> </button>",
                "1" => $respuesta["COD_PRESTADOR"],
                "2" => $respuesta["NIT_PRESTADOR"],
                "3" => $respuesta["NOM_PRESTADOR"],
                "4" => $respuesta["NOM_CIUDAD"],
                "5" => $respuesta["NOM_DEPARTAMENTO"],
                "6" => $respuesta["TIP_PRESTADOR"],
                "7" => $respuesta["CLA_PRESTADOR"],
                "8" => ($respuesta["COD_ESTADO_WEB"] == 'A') ? '<span class="tag is-info is-size-7"> ACTIVO </span>' : '<span class="tag is-danger is-size-7"> INACTIVO </span>'
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

    //Cambia el estado a activo 'A' de un prestador
    case 'activar_prestador':

        $rspta = $prestador->activar_prestador($nit_prestador);
        echo $rspta ? "Prestador activado" : "Prestador no se puede activar";
        break;

    //Cambia el estado a inactivo 'I' de un prestador
    case 'desactivar_prestador':

        $rspta = $prestador->desactivar_prestador($nit_prestador);
        echo $rspta ? "Prestador desactivado" : "Prestador no se puede desactivar";
        break;


    //Carga en el formulario los datos de un permiso
    case 'mostrar':

        $rspta = $prestador->mostrar($nit_prestador);
        echo json_encode($rspta);
        break;

    //Guarda y eduta los usuarios asociados a los prestadores
    case 'guardaryeditar':

        //Valido si hay permisos por grabar
        (isset($_POST['permiso'])) ? $permiso = $_POST['permiso'] : $permiso = 0;


        if (!empty($ni_prest)) {

            $rspta = $prestador->insertar_usuario($cod_usuario, $tipo_documento, $num_documento, $nom_usuario, $ni_prest, $password, $permiso);
            echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
        } else {

            $rspta = $prestador->editar_usuario($cod_usuario, $tipo_documento, $num_documento, $nom_usuario, $password, $permiso);
            // echo $rspta;
            echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
        }
        break;


    //Lista los usuarios asociados a un prestador
    case 'listar_usuarios':

        $consulta = $prestador->listar_usuarios($nit_prestador);

        $data = Array();


        foreach ($consulta as $respuesta) {

            $data[] = array(
                "0" => ($respuesta["COD_ESTADO"] == 'A') ?
                "<button class='button is-info is-small' onclick= mostrar_usuario('" . $respuesta['COD_USUARIO'] . "')> <span class='icon is-small'> <i class='fa fa-pencil'></i> </span> </button>" .
                " <button class='button is-danger is-small' onclick= desactivar_usuario('" . $respuesta['COD_USUARIO'] . "')> <span class='icon is-small'> <i class='fa fa-close'></i> </span> </button>" :
                "<button class='button is-info is-small' onclick= mostrar_usuario('" . $respuesta['COD_USUARIO'] . "')> <span class='icon is-small'> <i class='fa fa-pencil'></i> </span> </button>" .
                " <button class='button is-success is-small' onclick= activar_usuario('" . $respuesta['COD_USUARIO'] . "')> <span class='icon is-small'> <i class='fa fa-check'></i> </span> </button>",
                "1" => $respuesta["COD_USUARIO"],
                "2" => $respuesta["TIP_DOCUMENTO"],
                "3" => $respuesta["NUM_DOCUMENTO"],
                "4" => $respuesta["NOM_USUARIO"],
                "5" => $respuesta["FECHA_REGISTRO"],
                "6" => ($respuesta["COD_ESTADO"] == 'A') ? '<span class="tag is-info is-size-7"> ACTIVO </span>' : '<span class="tag is-danger is-size-7"> INACTIVO </span>'
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


    //Cambia el estado a activo 'A' de un usuario
    case 'activar_usuario':

        $rspta = $prestador->activar_usuario($cod_usuario);
        echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
        break;


    //Cambia el estado a inactivo 'I' de un usuario
    case 'desactivar_usuario':

        $rspta = $prestador->desactivar_usuario($cod_usuario);
        echo $rspta ? "Usuario desactivado" : "Usuario no se puede desactivar";
        break;


    //Carga en el formulario los datos de un usuario
    case 'mostrar_usuario':

        $rspta = $prestador->mostrar_usuario($cod_usuario);
        echo json_encode($rspta);
        break;


    //Obtiene los permisos de un usuario
    case 'permisos':
        //Obtenemos todos los permisos de la tabla permisos
        require_once "../modelos/Permiso.php";
        $permiso = new Permiso();
        $consulta = $permiso->listarActivos();

        //obtener los permisos asignados al usuario
        $cod_usuario = $_GET['id'];
        $marcados = $prestador->listar_permisoMarcado($cod_usuario);

        //se declara un array que guardara los permisos del usuario
        $valores = array();

        //almacenar los permisos asignados en un array
        foreach ($marcados as $res) {

            array_push($valores, $res['COD_MENU']);
        }


        //Mostramos la lista de permisos en la vista y si están o no marcados
        foreach ($consulta as $respuesta) {

            //in_array, busca que un valor este dentro de un arreglo
            $sw = in_array($respuesta['COD_MENU'], $valores) ? 'checked' : '';

            echo '<input type="checkbox" ' . $sw . ' name="permiso[]" value="' . $respuesta['COD_MENU'] . '"> ' . $respuesta["NOMBRE"] . ' &nbsp;';
        }
        break;

    //Lista todos los usuarios del sistema
    case 'listar_Totalusuarios':

        $consulta = $prestador->listar_Totalusuarios();

        $data = Array();


        foreach ($consulta as $respuesta) {

            $data[] = array(
                "0" => $respuesta["COD_USUARIO"],
                "1" => $respuesta["TIP_DOCUMENTO"],
                "2" => $respuesta["NUM_DOCUMENTO"],
                "3" => $respuesta["NOM_USUARIO"],
                "4" => $respuesta["NOM_PRESTADOR"],
                "5" => $respuesta["FECHA_REGISTRO"],
                "6" => ($respuesta["COD_ESTADO"] == 'A') ? '<span class="tag is-info is-size-7"> ACTIVO </span>' : '<span class="tag is-danger is-size-7"> INACTIVO </span>'
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


    //login de usuario
    case 'verificar':

        $logina = $_POST["logina"];
        $clavea = $_POST["clavea"];

        //Hash SHA256 en la contraseña
        $clavehash = hash("SHA256", 'EPSI06' . $clavea);

        $rspta = $prestador->verificar($logina, $clavehash);


        if ($rspta) {

            #######################
            # VERSION DEL SISTEMA #
            #######################
            //$_SESSION['web_version'] = time();
            $_SESSION['web_version'] = '1.0.1.10';

            $_SESSION['COD_USUARIO'] = $rspta["COD_USUARIO"];
            //$_SESSION['COD_PRESTADOR'] = $rspta["COD_PRESTADOR"];
            $_SESSION['NIT_PRESTADOR'] = $rspta["NIT_PRESTADOR"];
            $_SESSION['NOM_PRESTADOR'] = $rspta["NOM_PRESTADOR"];
            $_SESSION['CLA_PRESTADOR'] = $rspta["CLA_PRESTADOR"];
            $_SESSION['NOM_USUARIO'] = $rspta["NOM_USUARIO"];
            $contrasena = $rspta["PWD_USUARIO"];

            //Valido si la contraseña es igual al cod_usuario, de ser asi solicito el cambio de la
            //misma por parte del usuario
            if (($hashusuario = hash("SHA256", 'EPSI06' . $rspta["COD_USUARIO"])) === $contrasena) {

                $_SESSION['PWD_USER'] = 1;
                unset($contrasena);
            } else {

                $_SESSION['PWD_USER'] = 0;
                unset($contrasena);
            }


            //obtenemos los permisos del usuario
            $marcados = $prestador->listar_permisoMarcado($rspta["COD_USUARIO"]);

            //declaramos un array para almacenar todos los permisos marcados
            $valores = array();

            //almacenar los permisos asignados en un array
            foreach ($marcados as $res) {

                array_push($valores, $res['COD_MENU']);
            }

            //determinamos los accesos del usuario, con in_array se busca que el valor
            //buscado este en los permisos obtenidos            
            in_array(1, $valores) ? $_SESSION["acceso"] = 1 : $_SESSION["acceso"] = 0;
            in_array(2, $valores) ? $_SESSION["val_rips"] = 1 : $_SESSION["val_rips"] = 0;
            in_array(3, $valores) ? $_SESSION["filtrar_remision"] = 1 : $_SESSION["filtrar_remision"] = 0;
            in_array(4, $valores) ? $_SESSION["consulta_afi"] = 1 : $_SESSION["consulta_afi"] = 0;
            in_array(5, $valores) ? $_SESSION["autorizaciones"] = 1 : $_SESSION["autorizaciones"] = 0;
            in_array(6, $valores) ? $_SESSION["cargar_capita"] = 1 : $_SESSION["cargar_capita"] = 0;
            in_array(7, $valores) ? $_SESSION["consultar_prefactura"] = 1 : $_SESSION["consultar_prefactura"] = 0;
        }


        echo (json_encode($rspta));
        break;


    //Cambiar clave de usuario por la pantalla de escritorio
    case 'actualizarclave':

        $claven = $_POST["clave1"];
        $cod_usuario = $_POST["cod_usuario"];

        $rspta = $prestador->actualizarclave($cod_usuario, $claven);
        echo $rspta ? "Clave actualizada, inicie sesión nuevamente" : "Clave no se pudo actualizar";

        break;


    //Cambiar clave de usuario por el modal del header en la session de usuario
    case 'actualizarclave_session':

        $claven = $_POST["clave1_actualizar"];
        $cod_usuario = $_POST["cod_usuario_sesion"];

        $rspta = $prestador->actualizarclave($cod_usuario, $claven);
        echo $rspta ? "Clave actualizada, inicie sesión nuevamente" : "Clave no se pudo actualizar";

        break;


    //Cerrar session de usuario
    case 'salir':

        //limpiar las variables de sessison
        session_unset();

        //vaciar las cookies
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        //destruir la session
        session_destroy();

        //redireccion al login
        header("Location: ../pages/login.php");

        break;
}







