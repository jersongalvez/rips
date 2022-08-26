<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////        ARCHIVO DE FUNCIONES GENERALES        /////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
////  FUNCIONES UTILITARIAS QUE SE USAN EN TODOS LOS ARCHIVOS DEL PROYECTO  ////
////////////////////////////////////////////////////////////////////////////////
///////////////////////////// FUNCIONES HTML ///////////////////////////////////

/**
 * Metodo que muestra un mensaje indicando el numero de errores en total que tiene 
 * un archivo
 * @param int $n_errores
 * @param String $archivo
 * @return string
 */
function msg_val($n_errores, $archivo) {

    $salida = '<br> <p class="has-text-centered has-text-danger"><span class="icon is-small"><i class="fas fa-exclamation-triangle"></i></span>'
            . ' Se encontraron ' . $n_errores . ' errores en el archivo de ' . $archivo . '. </p>';

    return $salida;
}

/**
 * Imprime el titulo de la validacion de estructura
 */
function titulo_valEst() {

    echo '<p class="title is-6 has-text-grey-dark" style="margin-top: 10px;">
                <span class="icon is-small">
                    <i class="fas fa-search-plus"></i>
                </span>
                <strong> Validación de la estructura </strong>
          </p>';
}

/**
 * Imprime el titulo de la validacion cruzada
 */
function titulo_valCru() {

    echo '<p class="title is-6 has-text-grey-dark" style="margin-top: 30px;">
                <span class="icon is-small">
                    <i class="fas fa-server"></i>
                </span>
                <strong> Validación cruzada </strong>
          </p>';
}

/**
 * Funcion que muestra un mensaje de error en el archivo rips.php
 * @param String $desc_error
 */
function imp_emodal($tit_error, $desc_error) {

    echo '<div class="modal is-active">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title"><strong>' . $tit_error . '</strong></p>
                    <button class="delete" aria-label="close"></button>
                </header>
                <section class="modal-card-body">
                    <p class="has-text-justified">
                      ' . $desc_error . '
                    </p>
                </section>
                <footer class="modal-card-foot has-text-centered">
                    <button class="button is-info" id="closeModal">
                        <span class="icon is-small">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span> Aceptar </span>
                    </button>
                </footer>
            </div>
        </div>';
}

/**
 * Funcion que muestra el tipo de error al validar el archivo zip e invoca un
 * modal para mostrarlo al usuario
 * @param int $error
 */
function errorRips($error) {

    $msg1 = 'No se puede procesar el archivo';

    switch ($error) {

        //Borrar aca ---
        case '1':
            imp_emodal($msg1, 'La extensión, tamaño del archivo o número de remisión no son correctos.');
            break;

        case '2':
            imp_emodal($msg1, 'Esta remisión ya se encuentra registrada en el sistema. Verifique e intente de nuevo.');
            break;

        case '3':
            imp_emodal($msg1, 'El código Reps del prestador no esta registrado en el sistema.');
            break;

        case '4':
            imp_emodal($msg1, 'Ocurrió un error y el archivo no se pudo validar y/o formato no es ZIP.');
            break;

        case '5':
            imp_emodal($msg1, 'Este archivo intento cargarse en un envío anterior, inicie el proceso de nuevo.');
            break;

        case '6':
            imp_emodal($msg1, 'No se encuentran los datos requeridos para iniciar el proceso de validación.');
            break;
        //Borrar hasta aca ---

        case '7':
            imp_emodal('Usuario no encontrado', 'El usuario no está registrado en la base de datos única de la entidad.');
            break;

        case '8':
            imp_emodal('Error al validar la captcha', 'Hubo un error en la validación, haga la consulta de nuevo.');
            break;

        case '9':
            imp_emodal('No se puede consultar el usuario', 'No se encuentran los datos requeridos para iniciar el proceso de consulta.');
            break;

        case '10':
            imp_emodal('No se puede grabar la remisión', 'No se encuentra el directorio temporal de archivos.');
            break;

        case '11':
            imp_emodal('No encuentra la remisión', 'Esta remisión no se encuentra registrada en el sistema.');
            break;

        //Borrar aca ---
        case '12':
            imp_emodal($msg1, 'El código Reps de la remisión no corresponde con el del usuario actual.');
            break;
        //Borrar hasta aca ---
    }
}

/**
 * Funcion que muestra el tipo de error al validar el archivo txt e invoca un
 * modal para mostrarlo al usuario
 * @param int $error
 */
//function errorPrefectura($error) {
//
//    $msg1 = 'No se puede procesar el archivo';
//
//    switch ($error) {
//
//       
//        case '1':
//            imp_emodal($msg1, 'No se encuentran los datos requeridos para iniciar el proceso de inserción.');
//            break;
//
//        case '2':
//            imp_emodal($msg1, 'Esta remisión ya se encuentra registrada en el sistema. Verifique e intente de nuevo.');
//            break;
//    }
//}

/**
 * Metodo que retorna la modalidad del rips
 * @param String $valor
 * @return string
 */
function modalidad($valor) {

    $salida = '';

    switch ($valor) {
        case "C":
            $salida = "Cápita";
            break;

        case "E":
            $salida = "Evento";
            break;

        default:
            $salida = "-- No aplica --";
            break;
    }

    return $salida;
}

/**
 * Normaliza una fecha de dd/mm/aaaa a aaaammdd
 * @param type $date
 * @return type
 */
function setear_fecha($date) {

    if (!empty($date)) {
        $var = explode('/', str_replace('-', '/', $date));
        return "$var[2]$var[1]$var[0] 00:00:00 AM";
    }
}

//////////////////////// FUNCIONES ESPECIFICAS ////////////////////////////////

/**
 * Funcion que limpia y valida un campo
 * @param  input $campo Debe ser una entrada tipo POST
 * @return String
 */
function validar_campo($campo) {

    $campo = trim($campo);
    $campo = stripcslashes($campo);
    $campo = htmlspecialchars($campo);

    return $campo;
}

/**
 * Obtiene el id de cada archivo
 * @param String $nombre
 * @return String
 */
function id_archivo($nombre) {

    $salida = substr($nombre, 0, 2);

    return $salida;
}

/**
 * Metodo que busca los id de cada archivo
 * @param String $valor
 * @return boolean
 */
function comparar_idA($valor) {

    $permitido = array('CT', 'AF', 'US', 'AC', 'AD', 'AH', 'AM', 'AN', 'AP', 'AT', 'AU');

    $estado = false;

    for ($i = 0; $i < count($permitido); $i++) {
        if ($valor === $permitido[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Metodo que muestra los archivos encontrados en el archivo 
 * comprimido
 * @param String $valor
 * @param array $encontrados
 * @return boolean
 */
function mostrar_rip($valor, $encontrados) {

    $estado = false;

    for ($i = 0; $i < count($encontrados); $i++) {
        if ($valor === $encontrados[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Obtiene la remnison de cada txt
 * @param String $nombre
 * @return String
 */
function get_remision($nombre) {

    $salida = substr($nombre, 2, -4);

    return $salida;
}

/**
 * Metodo que elimina un directorio con sus subdirectorios
 * se pasa la ruta temporal de los ficheros como paranetro
 * @param String $directorio
 */
function eliminar_directorio($directorio) {
    if (is_dir($directorio)) {
        $objects = scandir($directorio);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($directorio . DIRECTORY_SEPARATOR . $object) == "dir") {
                    eliminar_directorio($directorio . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($directorio . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        rmdir($directorio);
    }
}

/**
 * Metodo que valida si un numero es entero positivo
 * @param int $valor
 * @return boolean
 */
function validar_entero($valor) {

    if (!preg_match('/^[0-9]*$/', $valor)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Metodo que valida que no tengan espacios en blanco al inicio o fin
 * de una cadena
 * @param String $cadena
 * @return boolean
 */
function espacio($cadena) {

    if (empty(trim($cadena))) {
        return false;
    } else {
        return true;
    }
}

/**
 * Metodo que valida si una cadena tiene carateres especiales, deja pasar espacios
 * @param String $cadena
 * @return boolean
 */
function cespeciales($cadena) {

    if (preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\+=\{\}\[\]\|;:"\<\>,\.\?\\\]|[[:lower:]]/', $cadena)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida si una cadena tiene carateres especiales, no deja pasar espacios ni tabulaciones
 * @param String $cadena
 * @return boolean
 */
function cespeciales1($cadena) {

    if (preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\s\s+\+=\{\}\[\]\|;:"\<\>,\.\?\ñ\\\]|[[:lower:]]/', $cadena)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida si una cadena tiene carateres especiales, deja pasar %, *, /, +, (), " y espacios
 * @param String $cadena
 * @return boolean
 */
function cespeciales2($cadena) {

    if (preg_match('/[\'\~`\!@#\$\^&\_\=\{\}\[\]\|;:\<\>,\?\\\]|[[:lower:]]/', $cadena)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida el cod de entidad administradora (Pijaos)
 * @param String $cod_ent_admin
 * @return boolean
 */
function val_cod_ent_admin($cod_ent_admin) {

    $estado = false;

    switch ($cod_ent_admin) {
        case 'EPSI06':
        case 'EPSIC6':
            $estado = true;
            break;

        default:
            $estado = false;
            break;
    }

    return $estado;
}

/**
 * Valida el tipo de documento del usuario
 * @param String $tipo
 * @return boolean
 */
function t_documento($tipo) {

    $estado = false;

    switch ($tipo) {
        case 'CC':
        case 'CE':
        case 'CD':
        case 'PA':
        case 'RC':
        case 'TI':
        case 'CN':
        case 'SC':
        case 'AS':
        case 'MS':
        case 'NU':
        case 'PE':
		case 'PT':	
            $estado = true;
            break;

        default:
            $estado = false;
            break;
    }

    return $estado;
}

/**
 * Formatea un numero separandolo en miles
 * @param type $numero
 * @return int
 */
function formatearNumero($numero) {

    $resultado = number_format($numero, 2, ",", ".");

    return $resultado;
}

/**
 * Metodo que valida una fecha en formato español
 * @param date $fecha
 * @return boolean
 */
function validar_fecha($fecha) {

    if (strlen($fecha) < 11) {

        $valores = explode('/', $fecha);

        if (count($valores) < 3 || count($valores) > 3) {

            return false;
        } else {

            if (is_numeric($valores[0]) && is_numeric($valores[1]) && is_numeric($valores[2])) {

                if (count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2]) && !empty($fecha)) {
                    return true;
                }
            }
        }
    }
    return false;
}

/**
 * Metodo que compara dos fechas y obtiene la mayor
 * @param String $fecha1
 * @param String $fecha2
 * @return boolean
 */
function comparar_fechas($fecha1, $fecha2) {
    date_default_timezone_set("America/Bogota");

    $dt1 = DateTime::createFromFormat('d/m/Y', $fecha1);
    $dt2 = DateTime::createFromFormat('d/m/Y', $fecha2);

    if ($dt1 > $dt2) {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida que los valores del campo esten del 01 al 15
 * @param String $valor
 * @return boolean
 */
function uno_quince($valor) {

    $permitido = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15');

    $estado = false;

    for ($i = 0; $i < count($permitido); $i++) {
        if ($valor === $permitido[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Metodo que valida que los valores del campo esten del 1 al 2
 * @param type $valor
 * @return boolean
 */
function uno_dos($valor) {

    if ($valor === '1' || $valor === '2') {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida que los valores del campo esten del 1 al 3
 * @param String $valor
 * @return boolean
 */
function uno_tres($valor) {

    if ($valor === '1' || $valor === '2' || $valor === '3') {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida que los valores del campo esten del 1 al 4
 * @param int $valor
 * @return boolean
 */
function uno_cuatro($valor) {

    $permitido = array('1', '2', '3', '4');

    $estado = false;

    for ($i = 0; $i < count($permitido); $i++) {
        if ($valor === $permitido[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Metodo que valida que los valores del campo esten del 1 al 5
 * @param int $valor
 * @return boolean
 */
function uno_cinco($valor) {

    $permitido = array('1', '2', '3', '4', '5');

    $estado = false;

    for ($i = 0; $i < count($permitido); $i++) {
        if ($valor === $permitido[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Metodo que valida el formato de una hora en
 * hh:mm
 * @param date $time
 * @return boolean
 */
function val_hora($time) {

    if (!preg_match('/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/', $time)) {

        return false;
    } else {
        return true;
    }
}

/**
 * Metodo que compara dos horas en formato 24
 * @param time $hora1
 * @param time $hora2
 * @return boolean
 */
function comparar_hora($hora1, $hora2) {

    $h1 = strtotime($hora1);
    $h2 = strtotime($hora2);

    if ($h1 > $h2) {
        return true;
    } else {
        return false;
    }
}

/**
 * Metodo que valida el sexo de una persona
 * @param Char $sexo
 * @return boolean
 */
function sexo($sexo) {

    $estado = false;

    switch ($sexo) {
        case 'M':
        case 'F':
            $estado = true;
            break;

        default:
            $estado = false;
            break;
    }

    return $estado;
}

/**
 * Metodo que valida un valor con dos posiciones decimales
 * @param float - int $valor
 * @return boolean
 */
function valor_dec($valor) {

    if (!preg_match('/^[0-9]{1,15}$|^[0-9]{1,15}\.[0-9]{1,2}$/', $valor)) {

        return false;
    } else {
        return true;
    }
}

/**
 * Metodo que hace la conversion del sexo 0 = M y 1 = F
 * @param String $sexo
 * @return int
 */
function c_sexo($sexo) {

    $salida = 3;

    switch ($sexo) {
        case 'M':
            $salida = 0;
            break;

        case 'F':
            $salida = 1;
            break;
    }

    return $salida;
}

/**
 * Metodo que convierte los meses y dias en años para validar contra los diagnosticos 
 * y procedimientos
 * @param int $edad
 * @param int $rango_medida
 * @return float
 */
function convertir_edad($edad, $rango_medida) {

    $edad_dias = 0;

    switch ($rango_medida) {

        //Años
        case '1':
            $edad_dias = $edad;
            break;

        //Meses
        case '2':
            $edad_dias = ($edad / 12);
            break;

        //Dias
        case '3':
            $edad_dias = ($edad / 365);
            break;
    }

    return $edad_dias;
}

/**
 * Metodo que muestra la ip del cliente
 * @return ip
 */
function getRealIP() {

    if (isset($_SERVER["HTTP_CLIENT_IP"])) {

        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {

        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {

        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {

        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {

        return $_SERVER["HTTP_FORWARDED"];
    } else {

        return $_SERVER["REMOTE_ADDR"];
    }
}

/////////////////////////// FRM CONSULTA AFILIADO //////////////////////////////
/**
 * Metodo que retorna el regimen de acuerdo al centro de costos
 * @param String $valor
 * @return type
 */
function regimen($valor) {

    $salida = '';

    switch ($valor) {
        case 'S':
            $salida = 'SUBSIDIADO';
            break;

        case 'C':
            $salida = 'CONTRIBUTIVO';
            break;

        default:
            $salida = $valor;
            break;
    }

    return $salida;
}

/**
 * Metodo que devuelve el estado de una afiliado
 * @param int $valor
 * @return string
 */
function estado($valor, $cartera) {

    $valor = intval($valor);
    $cartera = intval($cartera);

    $estado = 'INACTIVO';

    /* switch ($valor) {
      case '1':
      $estado = 'ACTIVO';
      break;

      default:
      $estado = 'INACTIVO';
      break;
      } */

    if ($valor == 1 && $cartera > 2) {

        $estado = 'SUSPENDIDO POR MORA';
    } elseif (($valor === 1 && $cartera == 0) || ($valor === 1 && $cartera <= 2)) {

        $estado = 'ACTIVO';
    }

    return $estado;
}

////////////////////////////  METODOS CRUZADOS  ////////////////////////////////

/**
 * Metodo que busca si una factura esta en el AF
 * @param String $valor
 * @return boolean
 */
function b_factura($valor) {

    $permitido = $_SESSION["facturas"];

    $estado = false;

    for ($i = 0; $i < count($permitido); $i++) {
        if ($valor === $permitido[$i]) {
            $estado = true;
            break;
        } else {
            $estado = false;
        }
    }

    return $estado;
}

/**
 * Metodo que busca si una identificacion esta en el US
 * @param int $n_doc
 * @param String $t_doc
 * @return boolean
 */
function b_identificacion($n_doc, $t_doc) {

    $estado = false;

    foreach ($_SESSION["identificacion"] as $datos) {

        if ($datos['n_documento'] === $n_doc) {

            if ($datos['t_documento'] === $t_doc) {

                $usuario[0] = $datos['edad'];
                $usuario[1] = $datos['sexo'];
                $usuario[2] = $datos['rango_medida_ed'];
                $estado = $usuario;
                break;
            }
        }
    }


    return $estado;
}

/**
 * Metodo que valida si un procedimiento asociado a un paciente esta duplicado
 * @param array $array
 * @return array
 */
function buscar_duplicadoProc($array) {
    $contar = array();
    $duplicado = array();

    foreach ($array as $valores) {

        if (isset($contar[$valores["n_documento"]]) && isset($contar[$valores["f_consulta"]]) && isset($contar[$valores["cod_procedimiento"]])) {

            $contar = array(
                $valores['n_documento']       => $contar[$valores["n_documento"]] + 1,
                $valores['f_consulta']        => $contar[$valores["f_consulta"]] + 1,
                $valores['cod_procedimiento'] => $contar[$valores["cod_procedimiento"]] + 1
            );

            array_push($duplicado, array('documento' => $valores["n_documento"], 'procedimiento' => $valores["cod_procedimiento"], 'linea' => $valores["linea"]));
        } else {

            $contar = array(
                $valores['n_documento']       => 1,
                $valores['f_consulta']        => 1,
                $valores['cod_procedimiento'] => 1
            );
        }
    }

    return $duplicado;
}

/**
 * Limpia las variables de session de las facturas, identificaciones de usuario,
 * codigo del prestador
 */
function limpiar_session() {

    unset($_SESSION["facturas"]);
    unset($_SESSION["identificacion"]);
    unset($_SESSION ["cprestador"]);
    unset($_SESSION ["logErrores"]);
    unset($_SESSION ["ni_prestador"]);
}
