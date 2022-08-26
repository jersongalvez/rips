<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     ARCHIVO DE CONSULTAS    //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////    ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS REALIZADOS    /////
////    POR LOS DISTINTOS PROFESIONALES DE LA SALUD, LAS DE PRIMERA VEZ    /////
////     Y DE CONTROL, LAS REALIZADAS EN LA CONSULTA AMBULATORIA, EN EL     ////
////   SERVICIO DE URGENCIAS, LAS INTERCONSULTAS INTRAHOSPITALARIAS Y EN    ////
///////////////        URGENCIAS, LAS JUNTAS MÉDICAS, ETC…        /////////////
////////////////////////////////////////////////////////////////////////////////


//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Consulta_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que almacena los procedimientos que no se pueden duplicar en un mismo dia
    private $duplicado_dia = array();
    //array que almacena los procedimientos que no se pueden duplicar en un mismo año
    private $duplicado_ano = array();
    //array que contiene los erres encontrados en la validacion
    private $logerac = array('', '----- Errores encontrados en el archivo de consultas: -----');

    /**
     * valida todo el archivo de consultas
     * @param String $ruta
     * @return int
     */
    function val_consultas($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AC txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 17 campos del archivo AC
                    if (count($valor) < 17 || count($valor) > 17) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $fecha_consulta     = $valor[4];
                        $num_autorizacion   = $valor[5];
                        $cod_consulta       = $valor[6];
                        $fin_consulta       = $valor[7];
                        $causa_externa      = $valor[8];
                        $cod_diag_prin      = $valor[9];
                        $cod_diag_rel1      = $valor[10];
                        $cod_diag_rel2      = $valor[11];
                        $cod_diag_rel3      = $valor[12];
                        $t_diag_prin        = $valor[13];
                        $val_consulta       = $valor[14];
                        $val_cuo_mod        = $valor[15];
                        $val_neto           = $valor[16];



                        //1 validacion numero de la factura 
                        $this->numeroFactura($num_factura, $posicion);

                        //2 validacion codigo del prestador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoDocumento($tip_identificacion, $posicion);

                        //4 validacion numero documento
                        $this->numeroDocumento($num_documento, $posicion);

                        //5 validacion fecha consulta
                        $this->fechaConsulta($fecha_consulta, $posicion);

                        //6 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //7 validacion codigo de la consulta
                        $this->codigoConsulta($cod_consulta, $posicion);

                        //8 validacion finalidad de la consulta
                        $this->finalidadConsulta($fin_consulta, $posicion);

                        //9 validacion causa externa de la consulta
                        $this->causaExterna($causa_externa, $posicion);

                        //10 validacion codigo diagnostico principal
                        $this->diagnosticoPrincipal($cod_diag_prin, $posicion, $fin_consulta);

                        //11 validacion codigo diagnostico relacionado 1
                        $this->diagnosticoRelacionado1($cod_diag_rel1, $posicion);

                        //12 validacion codigo diagnostico relacionado 2
                        $this->diagnosticoRelacionado2($cod_diag_rel2, $posicion);

                        //13 validacion codigo diagnostico relacionado 3
                        $this->diagnosticoRelacionado3($cod_diag_rel3, $posicion);

                        //14 validacion tipo diagnostico principal
                        $this->tipoDiagprincipal($t_diag_prin, $posicion);

                        //15 validacion valor de la consulta
                        $this->valorConsulta($val_consulta, $posicion);

                        //16 validacion valor cuota moderadora
                        $this->valorCuotamoderadora($val_cuo_mod, $posicion);

                        //17 validacion valor neto a pagar
                        $this->valorNeto($val_neto, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de consultas. </p>';
        //Fin validacion estructura
        
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de consultas. Corrija los errores e intente de nuevo. <br>';
                } else {

                    //recorre el archivo AC txt cruzandolo contra la tabla AUTORIZACIONES - PROCEDIMIENTO - DIAGNOSTICOS
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $co_prestador    = $cruce[1];
                        $nu_factura      = $cruce[0];
                        $ti_documento    = $cruce[2];
                        $nu_documento    = $cruce[3];
                        $fec_consulta    = $cruce[4];
                        $nu_autorizacion = $cruce[5];
                        $co_consulta     = $cruce[6];
                        $diag_prin       = $cruce[9];
                        $diag_rel1       = $cruce[10];
                        $diag_rel2       = $cruce[11];
                        $diag_rel3       = $cruce[12];
                        $usuario         = 1;


                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);

                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);
                        
                       
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

                        //Si el usuario es el mismo en el us busco el procedimiento asociado
                        if ($usuario === 1) {

                            //valido si el codigo de consulta existe en la tabla PROCEDIMIENTOS - DIAGNOSTICOS
                            $this->bucarCodigoconsulta($nu_documento, $fec_consulta, $co_consulta, $posicion, $edadUsuario, $sexoUsuario);
                            
                            //valido si el diagnostico principal existe en la tabla DIAGNOSTICOS
                            $this->buscarDiagprincipal($diag_prin, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado 1 existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel1($diag_rel1, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado 2 existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel2($diag_rel2, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado 3 existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrel3($diag_rel3, $posicion, $edadUsuario, $sexoUsuario);
                        } else {

                            $this->usuarioNoencontrado($posicion);
                        }
                    }
                    
                    //Muestro los procedimientos duplicados por dia
                    if (count($x = buscar_duplicadoProc($this->duplicado_dia))) {

                        $this->mostrarProcDupDia($x);
                    }
                    
                    //Muestro los procedimientos duplicados por año
                    if (count($y = buscar_duplicadoProc($this->duplicado_ano))) {

                        $this->mostrarProcDupAno($y);
                    }
                }

            echo '</div>';
        echo '</div>';
        

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de consultas. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'consultas');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerac);
        }

        //var_dump($_SESSION["sumatoriaAF"]);
        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////
    
    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AC
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eac = msg_estructura($posicion, 17);
        array_push($this->logerac, $eac);
        echo "<p class='has-text-danger'>" . $eac . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de la factura en el AC
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eac = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eac = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AC
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eac = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $eac = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de identificacion del usuario en el AC
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoDocumento($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $eac = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento del usuario en el AC
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eac = msg_ernuid($posicion, $num_documento);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $eac = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la fecha de la consulta en el AC
     * @param type $fecha_consulta
     * @param type $posicion
     */
    private function fechaConsulta($fecha_consulta, $posicion) {

        if (validar_fecha($fecha_consulta) === false) {

            $eac = msg_fec1('La fecha de la consulta', $posicion, $fecha_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_consulta, date("j/n/Y")) === true) {

            $eac = msg_fec2('La fecha de la consulta', $posicion, $fecha_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el numero de autorizacion en el AC
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eac = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eac = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el codigo de la consulta en el AC
     * @param int $cod_consulta
     * @param int $posicion
     */
    private function codigoConsulta($cod_consulta, $posicion) {

        if (cespeciales1($cod_consulta) === true || $cod_consulta === '') {

            $eac = msg_cadena1('El código de la consulta', $posicion, $cod_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_consulta) >= 9) {

            $eac = msg_cadena3('El código de la consulta', $posicion, $cod_consulta, 8);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida la finalidad de la consulta en el AC
     * @param int $fin_consulta
     * @param int $posicion
     */
    private function finalidadConsulta($fin_consulta, $posicion) {

        if ($this->tip_finalidad($fin_consulta) === false) {

            $eac = msg_cadena4('La finalidad de la consulta', $posicion, $fin_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la causa externa en el AC
     * @param int $causa_externa
     * @param int $posicion
     */
    private function causaExterna($causa_externa, $posicion) {

        if (uno_quince($causa_externa) === false) {

            $eac = msg_cadena4('La causa externa de la consulta', $posicion, $causa_externa);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el diagnostico principal
     * @param String $cod_diag_prin
     * @param int $posicion
     * @param int $fin_consulta
     */
    private function diagnosticoPrincipal($cod_diag_prin, $posicion, $fin_consulta) {

        if (cespeciales1($cod_diag_prin) === true || $cod_diag_prin === '') {

            $eac = msg_cadena1('El código del diagnóstico principal', $posicion, $cod_diag_prin);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_diag_prin) != 4) {

            $eac = msg_cadena3('El código del diagnóstico principal', $posicion, $cod_diag_prin, 4);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($fin_consulta === '10' && substr($cod_diag_prin, 0, 1) === 'Z') {

            $eac = msg_generico('El código del diagnóstico principal', $posicion, $cod_diag_prin, 'Si la finalidad de la consulta es 10 no puede llevar un código Z.');
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el diagnostico relacionado 1 en el AC
     * @param String $cod_diag_rel1
     * @param int $posicion
     */
    private function diagnosticoRelacionado1($cod_diag_rel1, $posicion) {

        if (cespeciales1($cod_diag_rel1) === true) {

            $eac = msg_cadena1('El código del diagnóstico relacionado 1', $posicion, $cod_diag_rel1);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cod_diag_rel1 != '') {

            if (strlen($cod_diag_rel1) != 4) {
                $eac = msg_cadena3('El código del diagnóstico relacionado 1', $posicion, $cod_diag_rel1, 4);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 12.- Metodo que valida el diagnostico relacionado 2 en el AC
     * @param String $cod_diag_rel2
     * @param int $posicion
     */
    public function diagnosticoRelacionado2($cod_diag_rel2, $posicion) {

        if (cespeciales1($cod_diag_rel2) === true) {

            $eac = msg_cadena1('El código del diagnóstico relacionado 2', $posicion, $cod_diag_rel2);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cod_diag_rel2 != '') {

            if (strlen($cod_diag_rel2) != 4) {
                $eac = msg_cadena3('El código del diagnóstico relacionado 2', $posicion, $cod_diag_rel2, 4);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 13.- Metodo que valida el diagnostico relacionado 3 en el AC
     * @param Sitrng $cod_diag_rel3
     * @param int $posicion
     */
    private function diagnosticoRelacionado3($cod_diag_rel3, $posicion) {

        if (cespeciales1($cod_diag_rel3) === true) {

            $eac = msg_cadena1('El código del diagnóstico relacionado 3', $posicion, $cod_diag_rel3);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cod_diag_rel3 != '') {

            if (strlen($cod_diag_rel3) != 4) {
                $eac = msg_cadena3('El código del diagnóstico relacionado 3', $posicion, $cod_diag_rel3, 4);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 14.- Metodo que valida el tipo de diagnostico principal en el AC
     * @param int $t_diag_prin
     * @param int $posicion
     */
    private function tipoDiagprincipal($t_diag_prin, $posicion) {

        if (uno_tres($t_diag_prin) === false) {

            $eac = msg_cadena4('El tipo de diagnóstico principal', $posicion, $t_diag_prin);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 15.- Metodo que valida el valor de la consulta en el AC
     * @param float $val_consulta
     * @param int $posicion
     */
    private function valorConsulta($val_consulta, $posicion) {

        if (valor_dec($val_consulta) === false) {

            $eac = msg_numero2('El valor de la consulta', $posicion, $val_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 16.- Metodo que valida el valor de la cuota moderadora en el AC
     * @param type $val_cuo_mod
     * @param type $posicion
     */
    private function valorCuotamoderadora($val_cuo_mod, $posicion) {

        if (valor_dec($val_cuo_mod) === false) {

            $eac = msg_numero2('El valor de la cuota moderadora', $posicion, $val_cuo_mod);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 17.- Metodo que valida el valor neto en el AC
     * @param type $val_neto
     * @param type $posicion
     */
    private function valorNeto($val_neto, $posicion) {

        if (valor_dec($val_neto) === false) {

            $eac = msg_numero2('El valor neto a pagar', $posicion, $val_neto);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
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

            $eac = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que valida si una factura esta declarada en el AF
     * @param String $nu_factura
     * @param int $posicion
     */
    private function buscarFacturaaf($nu_factura, $posicion) {

        if (b_factura($nu_factura) === false) {

            $eac = msg_errfac($posicion, $nu_factura);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
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
        array_push($this->logerac, $eac);
        echo "<p>" . $eac . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que busca el numero de autorizacion en el AC
     * @param int $nu_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($nu_autorizacion, $posicion) {

        if ($nu_autorizacion != '') {
            if (Consulta::getAutorizacion($nu_autorizacion)) {

                $eac = msg_erraut($posicion, $nu_autorizacion);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }
    
    private function bucarCodigoconsulta($nu_documento, $fec_consulta, $co_consulta, $posicion, $edadUsuario, $sexoUsuario) {
             
        if (($datoProc = Consulta::getProcedimiento($co_consulta)) == false) {

            $eac = msg_errpro('El código de la consulta', $posicion, $co_consulta);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        } else {
            
            if ($edadUsuario < (int) $datoProc["EDAD_INICIO"] || $edadUsuario > (int) $datoProc["EDAD_FINAL"]) {
                
                $eac = msg_errproe('código de la consulta', $posicion, $co_consulta);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoProc["COD_SEXO"] != 2) {

                if ($datoProc["COD_SEXO"] != $sexoUsuario) {

                    $eac = msg_errpros('código de la consulta', $posicion, $co_consulta);
                    array_push($this->logerac, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
            
            if($datoProc["DUPLICADO_UPC"] === 'D'){
                
                array_push($this->duplicado_dia, array('n_documento' => $nu_documento, 'f_consulta' => $fec_consulta, 'cod_procedimiento' => $co_consulta, 'linea' => $posicion));
            }elseif ($datoProc["DUPLICADO_UPC"] === 'A') {
               
                $fec_anual = explode('/', $fec_consulta);
                array_push($this->duplicado_ano, array('n_documento' => $nu_documento, 'f_consulta' => $fec_anual[2], 'cod_procedimiento' => $co_consulta, 'linea' => $posicion));
            } 
        }
    }

    /**
     * Metodo que busca el diagnostico principal en el AC
     * @param int $diag_prin
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagprincipal($diag_prin, $posicion, $edadUsuario, $sexoUsuario) {

        if (($datoDiag = Consulta::getDiagnostico($diag_prin)) == false) {

            $eac = msg_errdia('principal', $posicion, $diag_prin);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        } else {
            
            if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                $eac = msg_errdiae('código de diagnóstico principal', $posicion, $diag_prin);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoDiag["RANGO_SEXO"] != 2) {

                if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                    $eac = msg_errdias('código de diagnóstico principal', $posicion, $diag_prin);
                    array_push($this->logerac, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 1 en el AC
     * @param String $diag_rel1
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel1($diag_rel1, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel1 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel1)) == false) {

                $eac = msg_errdia('relacionado 1', $posicion, $diag_rel1);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado 1', $posicion, $diag_rel1);
                    array_push($this->logerac, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado 1', $posicion, $diag_rel1);
                        array_push($this->logerac, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 2 en el AC
     * @param String $diag_rel2
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel2($diag_rel2, $posicion, $edadUsuario, $sexoUsuario) {


        if ($diag_rel2 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel2)) == false) {

                $eac = msg_errdia('relacionado 2', $posicion, $diag_rel2);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado 2', $posicion, $diag_rel2);
                    array_push($this->logerac, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado 2', $posicion, $diag_rel2);
                        array_push($this->logerac, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado 3 en el AC
     * @param String $diag_rel3
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrel3($diag_rel3, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel3 != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel3)) == false) {

                $eac = msg_errdia('relacionado 3', $posicion, $diag_rel3);
                array_push($this->logerac, $eac);
                echo "<p>" . $eac . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico relacionado 3', $posicion, $diag_rel3);
                    array_push($this->logerac, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico relacionado 3', $posicion, $diag_rel3);
                        array_push($this->logerac, $eac);
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

        $eac = '- No se puede validar el código de la consulta y diagnósticos de la linea ' . ($posicion + 1) . ' '
                . 'porque el tipo y número de documento no están contenidos en el archivo de usuarios.';
        array_push($this->logerac, $eac);
        echo "<p>" . $eac . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que valida la finalidad de una consulta
     * @param String $valor
     * @return boolean
     */
    private function tip_finalidad($valor) {

        $permitido = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');

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
     * Lista los procedimientos duplicados en el archivo de consultas por DIA
     * @param array $x
     */
    private function mostrarProcDupDia($x) {

        foreach ($x as $valor) {

            $eac = msg_errprod($valor["documento"], $valor["procedimiento"], $valor["linea"]);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        }
    }
    
    
    /**
     * Lista los procedimientos duplicados en el archivo de consultas por ANO
     * @param array $x
     */
    private function mostrarProcDupAno($x) {

        foreach ($x as $valor) {

            $eac = msg_errproa($valor["documento"], $valor["procedimiento"], $valor["linea"]);
            array_push($this->logerac, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        }
    }
    
}
