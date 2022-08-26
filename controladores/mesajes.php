<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     ARCHIVO DE MENSAJES     //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
//////////        MENSAJES DE ERROR EN LA VALIDACIN DE ARCHIVOS        /////////
////////////////////////////////////////////////////////////////////////////////


///////////////////////////////// CADENAS //////////////////////////////////////
//Mensaje que indica que la variable no debe tener espacios en blanco - caracteres especiales - minúsculas.
function msg_cadena1($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' No debe tener espacios en blanco - caracteres especiales - minúsculas.';
    return $a;
}

//Mensaje que indica que la variable no debe tener caracteres especiales - minúsculas.
function msg_cadena2($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' No debe tener caracteres especiales - minúsculas.';
    return $a;
}

//Mensaje que indica la extension que puede tener una cadena
function msg_cadena3($msg, $posicion, $valor, $longitud) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' Debe tener ' . $longitud . ' caracteres máximo.';
    return $a;
}

//Mensaje que indica que la variable no esta dentro de un rango de valores
function msg_cadena4($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no es válido. No está dentro de los valores permitidos.';
    return $a;
}

////////////////////////////// NUMERICOS ///////////////////////////////////////
//Mensaje que imdica que la variable no es numerica
function msg_numero1($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' Debe ser de tipo entero.';
    return $a;
}

//Muestra un mensaje indicando que la variable no tiene el formato de modena requerido
function msg_numero2($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' Debe ser un valor numérico de 15 caracteres máximo y dado el caso con dos posiciones decimales.';
    return $a;
}

////////////////////////////////// FECHA ///////////////////////////////////////
//Mensaje que indica si la fecha esta incorrecta
function msg_fec1($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. '
            . 'Debe estar en formato dd/mm/aaaa.';

    return $a;
}

//Mensaje que indica si la fecha es mayor a la actual
function msg_fec2($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. '
            . 'No debe ser mayor a la fecha actual.';

    return $a;
}

//Mensaje que indica si la fecha esta incorrecta
function msg_fec3($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. '
            . 'Debe estar en formato mm/aaaa.';

    return $a;
}

////////////////////////////////// HORA ////////////////////////////////////////
//Mensaje que indica si la hora es incorrecta
function msg_hor($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. '
            . 'Debe estar en formato Hh:mm no mayor de 24 horas ni de 60 minutos.';

    return $a;
}

/////////////////////////// POSICIONES DEL ARRAY ///////////////////////////////
//Muestra un mensaje indicando que la línea validada no tiene la estructura
//requerida
function msg_estructura($posicion, $campos) {
    $a = '- No puede se validar la línea ' . ($posicion + 1) . ' no tiene la estructura correcta. '
            . 'Debe tener ' . $campos . ' valores.';
    return $a;
}

//////////////////////////////// CODIGO PRESTADOR //////////////////////////////
//Muestra un mensaje indicando que el codigo del prestador no tiene el formato
function msg_errCp1($posicion, $valor): string {
    $a = '- El código del prestador de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. Debe ser de tipo entero.';
    return $a;
}

//Muestra un mensaje indicando que el codigo del prestador no tiene la longitud
//permitida
function msg_errCp2($posicion, $valor) {
    $a = '- El código del prestador de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido.'
            . ' Debe tener 12 caracteres máximo.';
    return $a;
}

//Muestra un mensaje indicando que el codigo del prestador no esta declarado en el CT
function msg_errCp3($posicion, $valor) {
    $a = '- El código del prestador de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no esta declarado en el archivo de control.';
    return $a;
}

///////////////////////// NUMERO DE FACTURA ////////////////////////////////////
//Muestra un mensaje indicando que el numero de factura no esta declarado en el AF
function msg_errfac($posicion, $valor) {
    $a = '- El número de factura de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no esta declarado en el archivo de transacciones.';
    return $a;
}

///////////////////////////////// IDENT. USUARIO ///////////////////////////////
//Muestra un mensaje indicando que el numero de factura no esta declarado en el US
function msg_errusu($posicion, $tip_iden, $num_iden) {
    $a = '- El tipo y número de documento ' . $tip_iden . ' - ' . $num_iden . ' de la línea '
            . '' . ($posicion + 1) . ' no esta contenido en el archivo de usuarios.';

    return $a;
}

///////////////////////////// AUTORIZACION /////////////////////////////////////
//Muestra un mensaje indicando que la autorizacion no esta registrada en el sistema
function msg_erraut($posicion, $valor) {
    $a = '- El número de autorización de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'esta registrado en el sistema.';

    return $a;
}

/////////////////////////////// PROCEDIMIENTOS /////////////////////////////////
//Muestra un mensaje indicando que el procedimiento no esta registrado en el sistema
function msg_errpro($msg, $posicion, $valor) {
    $a = '- ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no esta habilitado en la CUPS vigente para la fecha de prestación.';

    return $a;
}

//Muestra un mensaje indicando que la edad del procedimiento no concuerda con el reportado
function msg_errproe($msg, $posicion, $valor) {
    $a = '- La edad del usuario de la línea ' . ($posicion + 1) . ' no corresponde a la permitida por el ' . $msg . ' ' . $valor . '.';

    return $a;
}

//Muestra un mensaje indicando que el sexo del procedimiento no concuerda con el reportado
function msg_errpros($msg, $posicion, $valor) {
    $a = '- El sexo del usuario de la línea ' . ($posicion + 1) . ' no corresponde a lo permitido por el ' . $msg . ' ' . $valor . '.';

    return $a;
}

//Muestra un mensaje indicando que el procedimiento esta duplicado para un usuario en el mismo dia
function msg_errprod($documento, $procedimiento, $posicion) {
    $a = '- El documento ' . $documento . ' de la línea ' . ($posicion + 1) . ' tiene asociado el código de procedimiento ' . $procedimiento . ' dos '
            . 'o más veces en una misma fecha de atención';

    return $a;
}

//Muestra un mensaje indicando que el procedimiento esta duplicado para un usuario en el mismo año
function msg_errproa($documento, $procedimiento, $posicion) {
    $a = '- El documento ' . $documento . ' de la línea ' . ($posicion + 1) . ' tiene asociado el código de procedimiento ' . $procedimiento . ' dos '
            . 'o más veces en un mismo año de atención';

    return $a;
}

//////////////////////////////// DIAGNOSTICOS /////////////////////////////////
//Muestra un mensaje indicando que el diagnostico no esta registrado en el sistema
function msg_errdia($msg, $posicion, $valor) {
    $a = '- El código del diagnóstico ' . $msg . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no esta habilitado en el CIE 10.';

    return $a;
}

//Muestra un mensaje indicando que la edad del diagnostico no concuerda con la reportado
function msg_errdiae($msg, $posicion, $valor) {
    $a = '- La edad del usuario de la línea ' . ($posicion + 1) . ' no corresponde a la permitida por el ' . $msg . ' ' . $valor . '.';

    return $a;
}

//Muestra un mensaje indicando que el sexo del diagnostico no concuerda con el reportado
function msg_errdias($msg, $posicion, $valor) {
    $a = '- El sexo del usuario de la línea ' . ($posicion + 1) . ' no corresponde a lo permitido por el ' . $msg . ' ' . $valor . '.';

    return $a;
}

////////////////////////////////// IDENTIFICACION //////////////////////////////
//Muestra un mensaje indicando que el tipo de identificacion es incorrecto
function msg_ertiden($posicion, $valor) {
    $a = '- El tipo de identificación de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no es válido. No está dentro de los valores permitidos.';
    return $a;
}

//muestra un mensaje indicando que el numero de identificacion tiene el formato incorrecto
function msg_ernuid($posicion, $valor) {
    $a = '- El número de identificación de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no es válido. Debe ser de tipo entero.';
    return $a;
}

////////////////////////// CODIGO ENTIDAD ADMINISTRADORA ///////////////////////
function msg_ercea($posicion, $valor) {
    $a = '- El código de la entidad administradora de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' '
            . 'no es válido. Debe tener como valor EPSI06 o EPSIC6.';
    return $a;
}

//////////////////////////// GENERICO ////////////////////////////////////////

function print_error($mensaje, $posicion, $campo) {

    $linea = ($posicion + 1);

    $salida = '<strong>' . $mensaje . '</strong> de la <strong> línea ' . $linea . '</strong> es '
            . 'incorrecto(a). El valor <strong>' . $campo . '</strong> no es válido.';

    return $salida;
}

//muestra un mensaje personalizado
function msg_generico($msg1, $posicion, $valor, $msg2) {
    $a = '- ' . $msg1 . ' de la línea ' . ($posicion + 1) . ' es incorrecto(a). El valor ' . $valor . ' no es válido. ' . $msg2;
    return $a;
}
