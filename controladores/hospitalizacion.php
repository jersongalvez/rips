<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////////  ARCHIVO DE HOSPITALIZACIONES   ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////   ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LA ESTANCIA  ////
////   DEL PACIENTE EN CUALQUIER SERVICIO HOSPITALARIO OCUPANDO UNA CAMA.   ////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Hospitalizacion_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerah = array('', '----- Errores encontrados en el archivo de hospitalizaciones: -----');

    /**
     * valida todo el archivo de hospitalizaciones
     * @param String $ruta
     * @return int
     */
    function val_hospitalizacion($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AH txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 19 campos del archivo AH
                    if (count($valor) < 19 || count($valor) > 19) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $via_ingreso        = $valor[4];
                        $fecha_ingreso      = $valor[5];
                        $hora_ingreso       = $valor[6];
                        $num_autorizacion   = $valor[7];
                        $causa_externa      = $valor[8];
                        $diag_prinIn        = $valor[9];
                        $diag_prinEg        = $valor[10];
                        $diag_egre1         = $valor[11];
                        $diag_egre2         = $valor[12];
                        $diag_egre3         = $valor[13];
                        $diag_complicacion  = $valor[14];
                        $estado_salida      = $valor[15];
                        $cau_bas_muerte     = $valor[16];
                        $fecha_salida       = $valor[17];
                        $hora_egreso        = $valor[18];



                        //1 validacion numero factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 validacion codigo del prestador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoDocumento($tip_identificacion, $posicion);

                        //4 validacion numero de identificacion
                        $this->numeroDocumento($num_documento, $posicion);

                        //5 validacion via ingreso
                        $this->viaIngreso($via_ingreso, $posicion);

                        //6 validacion fecha consulta
                        $this->fechaConsulta($fecha_ingreso, $posicion);

                        //7 validacion hora ingreso
                        $this->horaIngreso($hora_ingreso, $posicion);

                        //8 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //9 validacion causa externa
                        $this->causaExterna($causa_externa, $posicion);

                        //10 validacion diagnostico principal de ingreso
                        $this->diagnosticoPriningreso($diag_prinIn, $posicion);

                        //11 validacion diagnostico principal egreso
                        $this->diagnosticopEgreso($diag_prinEg, $posicion);

                        //12 validacion diagnostico relacionado n1 de egreso
                        $this->diagrelEgreso1($diag_egre1, $posicion);

                        //13 validacion diagnostico relacionado n2 de egreso
                        $this->diagrelEgreso2($diag_egre2, $posicion);

                        //14 validacion diagnostico relacionado n3 de egreso
                        $this->diagrelEgreso3($diag_egre3, $posicion);

                        //15 validacion diagnostico de la complicacion
                        $this->diagnosticoComplicacion($diag_complicacion, $posicion);

                        //16 validacion estado del usuario a la salida
                        $this->estadoSalida($estado_salida, $posicion);

                        //17 validacion causa basica de muerte
                        $this->causaBasmuerte($cau_bas_muerte, $posicion, $estado_salida);

                        //18 validacion fecha salida
                        $this->fechaSalida($fecha_salida, $posicion, $fecha_ingreso);

                        //19 validacion hora egreso
                        $this->horaEgreso($hora_egreso, $posicion);

                        // 19.1 validacion de horas cuando las fechas son iguales
                        $this->comparaHIHE($fecha_ingreso, $fecha_salida, $hora_ingreso, $hora_egreso, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de hospitalizaciones. </p>';
        //Fin validacion estructura
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de hospitalizaciones. Corrija los errores e intente de nuevo. <br>';
                } else {

                    //recorre el archivo AC txt cruzandolo contra la tabla AUTORIZACIONES - PROCEDIMIENTO - DIAGNOSTICOS
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura      = $cruce[0];
                        $co_prestador    = $cruce[1];
                        $ti_documento    = $cruce[2];
                        $nu_documento    = $cruce[3];
                        $nu_autorizacion = $cruce[7];
                        $diag_In         = $cruce[9];
                        $diag_Eg         = $cruce[10];
                        $diag_eg1        = $cruce[11];
                        $diag_eg2        = $cruce[12];
                        $diag_eg3        = $cruce[13];
                        $diag_comp       = $cruce[14];
                        $cau_bas_muer    = $cruce[16];
                        $usuario         = 1;


                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $usuario);

                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);

                        //cruce del numero de documento contra el archivo US
                        if (($datosUsuario = b_identificacion($nu_documento, $ti_documento)) === false) {

                            //declaro y asigno cuando el usuario no existe en el us
                            $usuario = 0;
                            $this->buscarUsuario($posicion, $ti_documento, $nu_documento);
                        } else {

                            $edadUsuario = (float) convertir_edad($datosUsuario[0], $datosUsuario[2]);
                            $sexoUsuario = c_sexo($datosUsuario[1]);
                        }

                        //valido si la autorizacion ya esta registrada en la tabla AUTORIZACIONES
                        $this->buscarAutorizacion($nu_autorizacion, $posicion);


                        if ($usuario === 1) {

                            //valido si el diagnostico principal existe en la tabla DIAGNOSTICOS
                            $this->buscarDiagpriningreso($diag_In, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico de egreso existe en la tabla DIAGNOSTICOS
                            $this->buscarDiagprinegreso($diag_Eg, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado nº 1 de egreso existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel1($diag_eg1, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado nº 2 de egreso existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel2($diag_eg2, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado nº 3 de egreso existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel3($diag_eg3, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico de la complicación existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagcomplicacion($diag_comp, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico de la causa basica de muerte existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->cauBasmuerte($cau_bas_muer, $posicion, $edadUsuario, $sexoUsuario);
                        } else {

                            $this->usuarioNoencontrado($posicion);
                        }
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de hospitalizaciones. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'hospitalizaciones');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerah);
        }


        return $total_err;
    }

    ///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AC
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eah = msg_estructura($posicion, 19);
        array_push($this->logerah, $eah);
        echo "<p class='has-text-danger'>" . $eah . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de factura en el AH
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eah = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eah = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AH
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eah = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) > 12) {

            $eah = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de identificacion en el AH
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoDocumento($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $eah = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento en el AH
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eah = msg_ernuid($posicion, $num_documento);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $eah = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la via de ingreso en el AH
     * @param int $via_ingreso
     * @param int $posicion
     */
    private function viaIngreso($via_ingreso, $posicion) {


        if (uno_cuatro($via_ingreso) === false) {

            $eah = msg_generico('La vía de ingreso a la institución', $posicion, $via_ingreso, 'Debe estar entre los valores del 1 al 4.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida la fecha de consulta en el AH
     * @param date $fecha_ingreso
     * @param int $posicion
     */
    private function fechaConsulta($fecha_ingreso, $posicion) {

        if (validar_fecha($fecha_ingreso) === false) {

            $eah = msg_fec1('La fecha de ingreso a la institución', $posicion, $fecha_ingreso);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_ingreso, date("j/n/Y")) === true) {

            $eah = msg_fec2('La fecha de ingreso a la institución', $posicion, $fecha_ingreso);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida la hora de ingreso en el AH
     * @param time $hora_ingreso
     * @param int $posicion
     */
    private function horaIngreso($hora_ingreso, $posicion) {

        if (val_hora($hora_ingreso) === false) {

            $eah = msg_hor('La hora de ingreso', $posicion, $hora_ingreso);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el numero de autorizacion en el AH
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eah = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eah = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la causa externa en el AH
     * @param String $causa_externa
     * @param int $posicion
     */
    private function causaExterna($causa_externa, $posicion) {

        if (uno_quince($causa_externa) === false) {

            $eah = msg_generico('La causa externa', $posicion, $causa_externa, 'Debe estar entre los valores del 01 al 15.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el diagnostico principal de ingreso
     * @param String $diag_prinIn
     * @param int $posicion
     */
    private function diagnosticoPriningreso($diag_prinIn, $posicion) {

        if (cespeciales1($diag_prinIn) === true || $diag_prinIn === '') {

            $eah = msg_cadena1('El diagnóstico principal de ingreso', $posicion, $diag_prinIn);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($diag_prinIn) != 4) {

            $eah = msg_cadena3('El diagnóstico principal de ingreso', $posicion, $diag_prinIn, 4);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (substr($diag_prinIn, 0, 1) === 'Z') {

            $eah = msg_generico('El diagnóstico principal de ingreso', $posicion, $diag_prinIn, 'No puede llevar un código Z.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el diagnostico principal de egreso
     * @param type $diag_prinEg
     * @param type $posicion
     */
    private function diagnosticopEgreso($diag_prinEg, $posicion) {

        if (cespeciales1($diag_prinEg) === true || $diag_prinEg === '') {

            $eah = msg_cadena1('El diagnóstico principal de egreso', $posicion, $diag_prinEg);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($diag_prinEg) != 4) {

            $eah = msg_cadena3('El diagnóstico principal de egreso', $posicion, $diag_prinEg, 4);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 12.- Metodo que valida el diagnostico principal de egreso 1 en el AH
     * @param String $diag_egre1
     * @param int $posicion
     */
    private function diagrelEgreso1($diag_egre1, $posicion) {

        if (cespeciales1($diag_egre1) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 1 de egreso', $posicion, $diag_egre1);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_egre1 != '') {

            if (strlen($diag_egre1) != 4) {
                $eah = msg_cadena3('El diagnóstico relacionado nº 1 de egreso', $posicion, $diag_egre1, 4);
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 13.- Metodo que valida el diagnostico principal de egreso 2 en el AH
     * @param String $diag_egre2
     * @param int $posicion
     */
    private function diagrelEgreso2($diag_egre2, $posicion) {

        if (cespeciales1($diag_egre2) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 2 de egreso', $posicion, $diag_egre2);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_egre2 != '') {

            if (strlen($diag_egre2) != 4) {

                $eah = msg_cadena3('El diagnóstico relacionado nº 2 de egreso', $posicion, $diag_egre2, 4);
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 14.- Metodo que valida el diagnostico principal de egreso 3 en el AH
     * @param String $diag_egre3
     * @param int $posicion
     */
    private function diagrelEgreso3($diag_egre3, $posicion) {

        if (cespeciales1($diag_egre3) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 3 de egreso', $posicion, $diag_egre3);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_egre3 != '') {

            if (strlen($diag_egre3) != 4) {

                $eah = msg_cadena3('El diagnóstico relacionado nº 3 de egreso', $posicion, $diag_egre3, 4);
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 15.- Metodo que valida el diagnostico de la complicacion en el AH
     * @param String $diag_complicacion
     * @param int $posicion
     */
    private function diagnosticoComplicacion($diag_complicacion, $posicion) {

        if (cespeciales1($diag_complicacion) === true) {

            $eah = msg_cadena1('El diagnóstico de la complicación', $posicion, $diag_complicacion);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_complicacion != '') {

            if (strlen($diag_complicacion) != 4) {
                $eah = msg_cadena3('El diagnóstico de la complicación', $posicion, $diag_complicacion, 4);
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 16.- Metodo que valida el estado a la salida en el AH
     * @param int $estado_salida
     * @param int $posicion
     */
    private function estadoSalida($estado_salida, $posicion) {

        if (uno_dos($estado_salida) === false) {

            $eah = msg_generico('El estado a la salida', $posicion, $estado_salida, 'Debe tener como valor 1 o 2.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 17.- Metodo que valida la causa basica de muerte en el AH
     * @param String $cau_bas_muerte
     * @param int $posicion
     * @param int $estado_salida
     */
    private function causaBasmuerte($cau_bas_muerte, $posicion, $estado_salida) {

        if (cespeciales1($cau_bas_muerte) === true) {

            $eah = msg_cadena1('La causa básica de muerte', $posicion, $cau_bas_muerte);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($estado_salida === '2' && $cau_bas_muerte === '') {

            $eah = msg_generico('La causa básica de muerte', $posicion, $cau_bas_muerte, 'Si el estado a la salida es igual a 2 se debe registrar la causa de muerte.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($estado_salida === '2' && strlen($cau_bas_muerte) != 4 || $cau_bas_muerte != '' && strlen($cau_bas_muerte) != 4) {

            $eah = msg_cadena3('La causa básica de muerte', $posicion, $cau_bas_muerte, 4);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 18.- Metodo que valida la fecha de salida en el AH
     * @param date $fecha_salida
     * @param int $posicion
     * @param date $fecha_ingreso
     */
    private function fechaSalida($fecha_salida, $posicion, $fecha_ingreso) {

        if (validar_fecha($fecha_salida) === false) {

            $eah = msg_fec1('La fecha de salida', $posicion, $fecha_salida);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_salida, date("j/n/Y")) === true) {

            $eah = msg_fec2('La fecha de salida', $posicion, $fecha_salida);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_ingreso, $fecha_salida) === true) {

            $eah = msg_generico('La fecha de salida', $posicion, $fecha_salida, 'No debe ser menor a la fecha de consulta.');
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 19.- Metodo que valida la hora de egreso en el AH
     * @param time $hora_egreso
     * @param int $posicion
     */
    private function horaEgreso($hora_egreso, $posicion) {

        if (val_hora($hora_egreso) === false) {

            $eah = msg_hor('La hora de salida', $posicion, $hora_egreso);
            array_push($this->logerah, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 19.1.- Metodo que compara la hora de ingreso y egreso cuando las fechas son
     * iguales en el AH
     * @param date $fecha_ingreso
     * @param date $fecha_salida
     * @param time $hora_ingreso
     * @param time $hora_egreso
     * @param int $posicion
     */
    private function comparaHIHE($fecha_ingreso, $fecha_salida, $hora_ingreso, $hora_egreso, $posicion) {

        if ($fecha_ingreso == $fecha_salida) {
            if (comparar_hora($hora_ingreso, $hora_egreso) === true) {

                $eah = msg_generico('La hora de ingreso', $posicion, $hora_ingreso, 'No debe ser mayor a la de salida.');
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * Metodo que valida si una factura esta declarada en el AF
     * @param String $nu_factura
     * @param int $posicion
     */
    private function buscarFacturaaf($nu_factura, $posicion) {

        if (b_factura($nu_factura) === false) {

            $ect = msg_errfac($posicion, $nu_factura);
            array_push($this->logerah, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que valida si el prestador declarado es igual al encontrado
     * en el CT
     * @param int $co_prestador
     * @param int $posicion
     */
    private function validarCodprestador($co_prestador, $posicion) {

        if ($co_prestador !== $_SESSION ["cprestador"]) {

            $ect = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerah, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que indica que un usuario no esta registrado en la base de datos
     * @param int $posicion
     * @param String $ti_documento
     * @param int $nu_documento
     */
    private function buscarUsuario($posicion, $ti_documento, $nu_documento) {

        $eac = msg_errusu($posicion, $ti_documento, $nu_documento);
        array_push($this->logerah, $eac);
        echo "<p>" . $eac . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que busca el numero de autorizacion en el AH
     * @param int $nu_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($nu_autorizacion, $posicion) {

        if ($nu_autorizacion != '') {
            if (Consulta::getAutorizacion($nu_autorizacion)) {

                $ect = msg_erraut($posicion, $nu_autorizacion);
                array_push($this->logerah, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

    /**
     * Metodo que busca el diagnostico principal de ingreso en el AH
     * @param String $diag_In
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagpriningreso($diag_In, $posicion, $edadUsuario, $sexoUsuario) {

        if (($datoDiag = Consulta::getDiagnostico($diag_In)) == false) {

            $ect = msg_errdia('principal de ingreso', $posicion, $diag_In);
            array_push($this->logerah, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        } else {

            if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                $eac = msg_errdiae('código de diagnóstico principal de ingreso', $posicion, $diag_In);
                array_push($this->logerah, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoDiag["RANGO_SEXO"] != 2) {

                if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                    $eac = msg_errdias('código de diagnóstico principal de ingreso', $posicion, $diag_In);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico principal de egreso en el AH
     * @param String $diag_Eg
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagprinegreso($diag_Eg, $posicion, $edadUsuario, $sexoUsuario) {

        if (($datoDiag = Consulta::getDiagnostico($diag_Eg)) == false) {

            $ect = msg_errdia('principal de egreso', $posicion, $diag_Eg);
            array_push($this->logerah, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        } else {

            if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                $eac = msg_errdiae('código de diagnóstico principal de egreso', $posicion, $diag_Eg);
                array_push($this->logerah, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoDiag["RANGO_SEXO"] != 2) {

                if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                    $eac = msg_errdias('código de diagnóstico principal de egreso', $posicion, $diag_Eg);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico principal de egreso 1 en el AH
     * @param String $diag_eg1
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel1($diag_eg1, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_eg1 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_eg1)) == false) {

                $ect = msg_errdia('relacionado nº 1 de egreso', $posicion, $diag_eg1);
                array_push($this->logerah, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 1 de egreso', $posicion, $diag_eg1);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 1 de egreso', $posicion, $diag_eg1);
                        array_push($this->logerah, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico principal de egreso 2 en el AH
     * @param String $diag_eg2
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel2($diag_eg2, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_eg2 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_eg2)) == false) {

                $ect = msg_errdia('relacionado nº 2 de egreso', $posicion, $diag_eg2);
                array_push($this->logerah, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 2 de egreso', $posicion, $diag_eg2);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 2 de egreso', $posicion, $diag_eg2);
                        array_push($this->logerah, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico principal de egreso 3 en el AH
     * @param String $diag_eg3
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel3($diag_eg3, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_eg3 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_eg3)) == false) {

                $ect = msg_errdia('relacionado nº 3 de egreso', $posicion, $diag_eg3);
                array_push($this->logerah, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 3 de egreso', $posicion, $diag_eg3);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 3 de egreso', $posicion, $diag_eg3);
                        array_push($this->logerah, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico de la complicacion en el AH
     * @param String $diag_comp
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagcomplicacion($diag_comp, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_comp != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_comp)) == false) {

                $ect = msg_errdia('de la complicación', $posicion, $diag_comp);
                array_push($this->logerah, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico de la complicación', $posicion, $diag_comp);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico de la complicación', $posicion, $diag_comp);
                        array_push($this->logerah, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca la causa basica de muerte en el AH
     * @param String $cau_bas_muer
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function cauBasmuerte($cau_bas_muer, $posicion, $edadUsuario, $sexoUsuario) {

        if ($cau_bas_muer != '') {
            if (($datoDiag = Consulta::getDiagnostico($cau_bas_muer)) == false) {

                $eah = msg_generico('La causa básica de muerte', $posicion, $cau_bas_muer, 'no esta registrada en el sistema.');
                array_push($this->logerah, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de la causa básica de muerte', $posicion, $cau_bas_muer);
                    array_push($this->logerah, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de la causa básica de muerte', $posicion, $cau_bas_muer);
                        array_push($this->logerah, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que informa que el usuario no se encontro en el AH
     * @param int $posicion
     */
    private function usuarioNoencontrado($posicion) {

        $eah = '- No se pueden validar los diagnósticos de la linea ' . ($posicion + 1) .
                ' porque el tipo y número de documento no están contenidos en el archivo de usuarios.';
        array_push($this->logerah, $eah);
        echo "<p>" . $eah . "</p>";
        $this->contador_val_cruzada++;
    }

}
