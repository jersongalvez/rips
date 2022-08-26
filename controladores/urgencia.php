<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     ARCHIVO DE URGENCIAS    //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////  ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE URGENCIAS   /////
///// CORRESPONDEN A LA ESTANCIA DEL PACIENTE EN LA UNIDAD DE OBSERVACIÓN  /////
/////////////////      DE URGENCIAS, OCUPANDO UNA CAMILLA.     ///////////////// 
////////////////////////////////////////////////////////////////////////////////


//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Urgencia_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerau = array('', '----- Errores encontrados en el archivo de urgencias: -----');

    /**
     * valida todo el archivo de urgencias con observacion
     * @param String $ruta
     * @return int
     */
    function val_urgencias($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';


                //recorre el archivo AU txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 17 campos del archivo AU
                    if (count($valor) < 17 || count($valor) > 17) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $fecha_consulta     = $valor[4];
                        $hora_ingreso       = $valor[5];
                        $num_autorizacion   = $valor[6];
                        $cau_externa        = $valor[7];
                        $diag_salida        = $valor[8];
                        $diag_rel1_sal      = $valor[9];
                        $diag_rel2_sal      = $valor[10];
                        $diag_rel3_sal      = $valor[11];
                        $destino_usuario    = $valor[12];
                        $estado_salida      = $valor[13];
                        $cau_bas_muerte     = $valor[14];
                        $fecha_salida       = $valor[15];
                        $hora_salida        = $valor[16];


                        //1 validacion numero de la factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 Validacion codigo restador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoIdentificacion($tip_identificacion, $posicion);

                        //4 validacion numero documento
                        $this->numeroDocumento($num_documento, $posicion);

                        //5 validacion fecha ingreso
                        $this->fechaIngreso($fecha_consulta, $posicion);

                        //6 validacion hora ingreso
                        $this->horaIngreso($hora_ingreso, $posicion);

                        //7 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //8 validacion causa externa
                        $this->causaExterna($cau_externa, $posicion);

                        //9 validacion diagnostico de salida
                        $this->diagnosticoSalida($diag_salida, $posicion);

                        //10 validacion diagnostico relacionado N1 de salida
                        $this->diagRelacionado1($diag_rel1_sal, $posicion);

                        //11 validacion diagnostico relacionado N2 de salida
                        $this->diagRelacionado2($diag_rel2_sal, $posicion);

                        //12 validacion diagnostico N3 de salida
                        $this->diagRelacionado3($diag_rel3_sal, $posicion);

                        //13 validacion destino del usuario a la salida
                        $this->destinoSalida($destino_usuario, $posicion);

                        //14 validacion estado del usuario a la salida
                        $this->estadoSalida($estado_salida, $posicion);

                        //15 validacion causa basica de muerte
                        $this->causaMuerte($cau_bas_muerte, $posicion, $estado_salida);

                        //16 validacion fecha salida
                        $this->fechaSalida($fecha_salida, $posicion, $fecha_consulta);

                        //17 validacion hora salida
                        $this->horaSalida($hora_salida, $posicion);

                        // 17.1 validacion de horas cuando las fechas son iguales
                        $this->comprarHEHS($fecha_consulta, $fecha_salida, $hora_ingreso, $hora_salida, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de urgencias. </p>';
        //Fin validacion estructura
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';


                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de urgencias. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo AT txt cruzandolo contra la tabla AUTORIZACIONES - DIAGNOSTICOS
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura      = $cruce[0];
                        $co_prestador    = $cruce[1];
                        $ti_documento    = $cruce[2];
                        $nu_documento    = $cruce[3];
                        $nu_autorizacion = $cruce[6];
                        $diag_sal        = $cruce[8];
                        $diag_rel1       = $cruce[9];
                        $diag_rel2       = $cruce[10];
                        $diag_rel3       = $cruce[11];
                        $cau_muerte      = $cruce[14];
                        $usuario         = 1;


                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);

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



                        //Si el usuario es el mismo en el us busco los diagnosticos asociados
                        if ($usuario === 1) {

                            //valido si el diagnostico a la salida existe en la tabla DIAGNOSTICOS
                            $this->buscarDiagsalida($diag_sal, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico relacionado nº 1 a la salida existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel1($diag_rel1, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico relacionado nº 2 a la salida existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel2($diag_rel2, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico relacionado nº 2 a la salida existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel3($diag_rel3, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnóstico de la causa basica de muerte existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarCaubasmuerte($cau_muerte, $posicion, $edadUsuario, $sexoUsuario);
                        } else {

                            $this->usuarioNoencontrado($posicion);
                        }
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de urgencias. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'urgencias');


            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerau);
        }


        return $total_err;
    }

    ///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AC
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eau = msg_estructura($posicion, 17);
        array_push($this->logerau, $eau);
        echo "<p class='has-text-danger'>" . $eau . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de la factura en el AU
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eah = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eah = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AU
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eah = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) > 12) {

            $eah = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de identificacion del usuario en el AU
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoIdentificacion($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $eah = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodod que valida el numero de documento del usuario en el AU
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eah = msg_ernuid($posicion, $num_documento);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) > 20) {

            $eah = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la fecha de ingreso en el AU
     * @param date $fecha_consulta
     * @param int $posicion
     */
    private function fechaIngreso($fecha_consulta, $posicion) {

        if (validar_fecha($fecha_consulta) === false) {

            $eah = msg_fec1('La fecha de ingreso a observación', $posicion, $fecha_consulta);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_consulta, date("j/n/Y")) === true) {

            $eah = msg_fec2('La fecha de ingreso a observación', $posicion, $fecha_consulta);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida la hora de ingreso en el AU
     * @param time $hora_ingreso
     * @param int $posicion
     */
    private function horaIngreso($hora_ingreso, $posicion) {

        if (val_hora($hora_ingreso) === false) {

            $eah = msg_hor('La hora de ingreso', $posicion, $hora_ingreso);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el numero de autorizacion en el AU
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eah = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eah = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida la causa externa de la urgencia en el AU
     * @param int $cau_externa
     * @param int $posicion
     */
    private function causaExterna($cau_externa, $posicion) {

        if (uno_quince($cau_externa) === false) {

            $eah = msg_generico('La causa externa', $posicion, $cau_externa, 'Debe estar entre los valores del 01 al 15.');
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida el diagnostico a la salida en el AU
     * @param String $diag_salida
     * @param int $posicion
     */
    private function diagnosticoSalida($diag_salida, $posicion) {

        if (cespeciales1($diag_salida) === true || $diag_salida === '') {

            $eah = msg_cadena1('El diagnóstico a la salida', $posicion, $diag_salida);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($diag_salida) != 4) {

            $eah = msg_cadena3('El diagnóstico a la salida', $posicion, $diag_salida, 4);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el diagnostico relacionado 1 en el AU
     * @param String $diag_rel1_sal
     * @param int $posicion
     */
    private function diagRelacionado1($diag_rel1_sal, $posicion) {

        if (cespeciales1($diag_rel1_sal) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 1 a la salida', $posicion, $diag_rel1_sal);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_rel1_sal != '') {

            if (strlen($diag_rel1_sal) != 4) {

                $eah = msg_cadena3('El diagnóstico relacionado nº 1 a la salida', $posicion, $diag_rel1_sal, 4);
                array_push($this->logerau, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 11.- Metodo que valida el diagnostico relacionado 2 en el AU
     * @param String $diag_rel2_sal
     * @param int $posicion
     */
    private function diagRelacionado2($diag_rel2_sal, $posicion) {

        if (cespeciales1($diag_rel2_sal) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 2 a la salida', $posicion, $diag_rel2_sal);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_rel2_sal != '') {

            if (strlen($diag_rel2_sal) != 4) {

                $eah = msg_cadena3('El diagnóstico relacionado nº 2 a la salida', $posicion, $diag_rel2_sal, 4);
                array_push($this->logerau, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 12.- Metodo que valida el diagnostico relacionado 3 en el AU
     * @param String $diag_rel3_sal
     * @param int $posicion
     */
    private function diagRelacionado3($diag_rel3_sal, $posicion) {

        if (cespeciales1($diag_rel3_sal) === true) {

            $eah = msg_cadena1('El diagnóstico relacionado nº 3 a la salida', $posicion, $diag_rel3_sal);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_rel3_sal != '') {

            if (strlen($diag_rel3_sal) != 4) {

                $eah = msg_cadena3('El diagnóstico relacionado nº 3 a la salida', $posicion, $diag_rel3_sal, 4);
                array_push($this->logerau, $eah);
                echo "<p>" . $eah . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 13.- Metodo que valida el destino a la salida de un usuario en el AU
     * @param int $destino_usuario
     * @param int $posicion
     */
    private function destinoSalida($destino_usuario, $posicion) {

        if (uno_tres($destino_usuario) === false) {

            $eah = msg_generico('El destino del usuario a la salida', $posicion, $destino_usuario, 'Debe tener como valor 1 o 3.');
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 14.- Metodo que valida el estado a la salida de un usuario en el AU
     * @param type $estado_salida
     * @param type $posicion
     */
    private function estadoSalida($estado_salida, $posicion) {

        if (uno_dos($estado_salida) === false) {

            $eah = msg_generico('El estado a la salida', $posicion, $estado_salida, 'Debe tener como valor 1 o 2.');
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 15.- Metodo que valida la causa basica de muerte en el AU
     * @param String $cau_bas_muerte
     * @param int $posicion
     * @param int $estado_salida
     */
    private function causaMuerte($cau_bas_muerte, $posicion, $estado_salida) {

        if (cespeciales1($cau_bas_muerte) === true) {

            $eah = msg_cadena1('La causa básica de muerte', $posicion, $cau_bas_muerte);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($estado_salida === '2' && $cau_bas_muerte === '') {

            $eah = msg_generico('La causa básica de muerte', $posicion, $cau_bas_muerte, 'Si el estado a la salida es igual a 2, se debe registrar la causa de muerte.');
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($estado_salida === '2' && strlen($cau_bas_muerte) != 4 || $cau_bas_muerte != '' && strlen($cau_bas_muerte) != 4) {

            $eah = msg_cadena3('La causa básica de muerte', $posicion, $cau_bas_muerte, 4);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 16.- Metodo que valida la fecha de salida del usuario en el AU
     * @param type $fecha_salida
     * @param type $posicion
     * @param type $fecha_consulta
     */
    private function fechaSalida($fecha_salida, $posicion, $fecha_consulta) {

        if (validar_fecha($fecha_salida) === false) {

            $eah = msg_fec1('La fecha de salida', $posicion, $fecha_salida);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_salida, date("j/n/Y")) === true) {

            $eah = msg_fec2('La fecha de salida', $posicion, $fecha_salida);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_consulta, $fecha_salida) === true) {

            $eah = msg_generico('La fecha de salida', $posicion, $fecha_salida, 'No debe ser menor a la fecha de consulta.');
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 17.- Metodo que valida la hora de salida en el AU
     * @param time $hora_salida
     * @param int $posicion
     */
    private function horaSalida($hora_salida, $posicion) {

        if (val_hora($hora_salida) === false) {

            $eah = msg_hor('La hora de salida', $posicion, $hora_salida);
            array_push($this->logerau, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 17.1.- Metodo que compara las horas de entrada y salida, cuando la fecha 
     * de ingreso y salida son las mismas en el AU
     * @param date $fecha_consulta
     * @param date $fecha_salida
     * @param time $hora_ingreso
     * @param time $hora_salida
     * @param int $posicion
     */
    private function comprarHEHS($fecha_consulta, $fecha_salida, $hora_ingreso, $hora_salida, $posicion) {

        if ($fecha_consulta == $fecha_salida) {
            if (comparar_hora($hora_ingreso, $hora_salida) === true) {

                $eau = msg_generico('La hora de ingreso', $posicion, $hora_ingreso, 'No debe ser mayor a la de salida.');
                array_push($this->logerau, $eau);
                echo "<p>" . $eau . "</p>";
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
            array_push($this->logerau, $ect);
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
            array_push($this->logerau, $ect);
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

        $eau = msg_errusu($posicion, $ti_documento, $nu_documento);
        array_push($this->logerau, $eau);
        echo "<p>" . $eau . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que busca el numero de autorizacion en el AU
     * @param int $nu_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($nu_autorizacion, $posicion) {

        if ($nu_autorizacion != '') {
            if (Consulta::getAutorizacion($nu_autorizacion)) {

                $ect = msg_erraut($posicion, $nu_autorizacion);
                array_push($this->logerau, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

    /**
     * Metodo que busca el diagnostico a la salida en el AU
     * @param String $diag_sal
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagsalida($diag_sal, $posicion, $edadUsuario, $sexoUsuario) {

        if (($datoDiag = Consulta::getDiagnostico($diag_sal)) == false) {

            $ect = msg_errdia('a la salida', $posicion, $diag_sal);
            array_push($this->logerau, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        } else {

            if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                $eac = msg_errdiae('código de diagnóstico a la salida', $posicion, $diag_sal);
                array_push($this->logerau, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoDiag["RANGO_SEXO"] != 2) {

                if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                    $eac = msg_errdias('código de diagnóstico a la salida', $posicion, $diag_sal);
                    array_push($this->logerau, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 1 a la salida en el AU
     * @param String $diag_rel1
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel1($diag_rel1, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel1 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel1)) == false) {

                $ect = msg_errdia('relacionado nº 1 a la salida', $posicion, $diag_rel1);
                array_push($this->logerau, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 1 a la salida', $posicion, $diag_rel1);
                    array_push($this->logerau, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 1 a la salida', $posicion, $diag_rel1);
                        array_push($this->logerau, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 2 a la salida en el AU
     * @param String $diag_rel2
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel2($diag_rel2, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel2 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel2)) == false) {

                $ect = msg_errdia('relacionado nº 2 a la salida', $posicion, $diag_rel2);
                array_push($this->logerau, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 2 a la salida', $posicion, $diag_rel2);
                    array_push($this->logerau, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 2 a la salida', $posicion, $diag_rel2);
                        array_push($this->logerau, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 3 a la salida en el AU
     * @param String $diag_rel3
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel3($diag_rel3, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel3 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel3)) == false) {

                $ect = msg_errdia('relacionado nº 3 a la salida', $posicion, $diag_rel3);
                array_push($this->logerau, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado nº 3 a la salida', $posicion, $diag_rel3);
                    array_push($this->logerau, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado nº 3 a la salida', $posicion, $diag_rel3);
                        array_push($this->logerau, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que valida la causa basica de muerte de un usuario en el AU
     * @param String $cau_muerte
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarCaubasmuerte($cau_muerte, $posicion, $edadUsuario, $sexoUsuario) {

        if ($cau_muerte != '') {
            if (($datoDiag = Consulta::getDiagnostico($cau_muerte)) == false) {

                $ect = msg_errdia('de la causa básica de muerte', $posicion, $cau_muerte);
                array_push($this->logerau, $ect);
                echo "<p>" . $ect . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de la causa básica de muerte', $posicion, $cau_muerte);
                    array_push($this->logerau, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de la causa básica de muerte', $posicion, $cau_muerte);
                        array_push($this->logerau, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que informa que el usuario no se encontro en el US
     * @param int $posicion
     */
    private function usuarioNoencontrado($posicion) {

        $eau = '- No se pueden validar los diagnósticos de la linea ' . ($posicion + 1) . ' '
                . 'porque el tipo y número de documento no están contenidos en el archivo de usuarios.';
        array_push($this->logerau, $eau);
        echo "<p>" . $eau . "</p>";
        $this->contador_val_cruzada++;
    }

}
