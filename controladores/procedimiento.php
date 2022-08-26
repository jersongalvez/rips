<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////  ARCHIVO DE PROCEDIMIENTOS  //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////    ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LOS    //////
///////    PROCEDIMIENTOS DIAGNÓSTICOS, TERAPÉUTICOS QUIRÚRGICOS Y NO    ///////
/////    QUIRÚRGICOS, DE PROTECCIÓN ESPECÍFICA Y DE DETECCIÓN TEMPRANA    //////
//////////     DE ENFERMEDAD GENERAL O DE ENFERMEDAD PROFESIONAL.     //////////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Procedimiento_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que almacena los procedimientos que no se pueden duplicar en un mismo dia
    private $duplicado_dia = array();
    //array que almacena los procedimientos que no se pueden duplicar en un mismo año
    private $duplicado_ano = array();
    //array que contiene los erres encontrados en la validacion
    private $logerap = array('', '----- Errores encontrados en el archivo de procedimientos: -----');

    /**
     * valida todo el archivo de procedimientos
     * @param String $ruta
     * @return int
     */
    function val_procedimientos($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));


        //Inicio validacion de estructura
        titulo_valEst();


        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AP txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 15 campos del archivo AC
                    if (count($valor) < 15 || count($valor) > 15) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $fecha_consulta     = $valor[4];
                        $num_autorizacion   = $valor[5];
                        $cod_procedimiento  = $valor[6];
                        $ambito_proc        = $valor[7];
                        $fin_procedimiento  = $valor[8];
                        $per_atiende        = $valor[9];
                        $diag_principal     = $valor[10];
                        $diag_relacionado   = $valor[11];
                        $complicacion       = $valor[12];
                        $acto_quirurgico    = $valor[13];
                        $val_procedimiento  = $valor[14];


                        //1 validacion numero de la factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 Validacion codigo prestador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoIdentificacion($tip_identificacion, $posicion);

                        //4 validacion numero documento
                        $this->numeroDocumento($num_documento, $posicion);

                        //5 validacion fecha del procedimiento
                        $this->fechaProcedimiento($fecha_consulta, $posicion);

                        //6 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //7 Validacion codigo procedimiento
                        $this->codigoProcedimiento($cod_procedimiento, $posicion);

                        //8 validacion ambito del procedimiento
                        $this->ambitoProcedimiento($ambito_proc, $posicion);

                        //9 validacion finalidad del procedimiento
                        $this->finalidadProcedimiento($fin_procedimiento, $posicion);

                        //10 validacion personal que atiende
                        $this->personalAtiende($per_atiende, $posicion);

                        //11 validacion diagnostico principal
                        $this->diagnosticoPrincipal($diag_principal, $posicion);

                        //12 validacion diagnostico relacionado
                        $this->diagnosticorelacionado($diag_relacionado, $posicion);

                        //13 validacion de la complicacion
                        $this->complicacion($complicacion, $posicion);

                        //14 validacion acto quirurgico
                        $this->actoQuirurgico($acto_quirurgico, $posicion);

                        //15 validacion valor del procedimiento
                        $this->valorProcedimiento($val_procedimiento, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de procedimientos. </p>';
        //Fin validacion estructura
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de procedimientos. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo AP txt cruzandolo contra la tabla PROCEDIMIENTOS - AUTORIZACIONES
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura       = $cruce[0];
                        $co_prestador     = $cruce[1];
                        $ti_documento     = $cruce[2];
                        $nu_documento     = $cruce[3];
                        $fec_consulta     = $cruce[4];
                        $nu_autorizacion  = $cruce[5];
                        $co_procedimiento = $cruce[6];
                        $diag_prin        = $cruce[10];
                        $diag_rel         = $cruce[11];
                        $complic          = $cruce[12];
                        $usuario          = 1;


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

                        //Si el usuario es el mismo en el us busco el procedimiento y diagnostico asociado
                        if ($usuario === 1) {

                            //valido si el codigo de consulta exiSte en la tabla PROCEDIMIENTOS
                            $this->buscarProcedimiento($nu_documento, $fec_consulta, $co_procedimiento, $posicion, $edadUsuario, $sexoUsuario);
                            
                            //valido si el diagnostico principal existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagprincipal($diag_prin, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico relacionado existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagrelacionado($diag_rel, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si la complicacion existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagcomplicacion($complic, $posicion, $edadUsuario, $sexoUsuario);
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

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de procedimientos. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'procedimientos');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerap);
        }


        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el US
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eap = msg_estructura($posicion, 15);
        array_push($this->logerap, $eap);
        echo "<p class='has-text-danger'>" . $eap . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de factura en el AP
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eap = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eap = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AP
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eap = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $eap = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de identificacion del usuario en el AP
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoIdentificacion($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $eap = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento del usuario en el AP
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eap = msg_ernuid($posicion, $num_documento);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $eap = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la fecha del procedimiento en el AP
     * @param date $fecha_consulta
     * @param int $posicion
     */
    private function fechaProcedimiento($fecha_consulta, $posicion) {

        if (validar_fecha($fecha_consulta) === false) {

            $eap = msg_fec1('La fecha del procedimiento', $posicion, $fecha_consulta);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_consulta, date("j/n/Y")) === true) {

            $eap = msg_fec2('La fecha del procedimiento', $posicion, $fecha_consulta);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el numero de autrorizacion en el AP
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eap = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eap = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el codigo del procedimiento en el AP
     * @param int $cod_procedimiento
     * @param int $posicion
     */
    private function codigoProcedimiento($cod_procedimiento, $posicion) {

        if (cespeciales1($cod_procedimiento) === true || $cod_procedimiento === '') {

            $eap = msg_cadena1('El código del procedimiento', $posicion, $cod_procedimiento);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_procedimiento) >= 9) {

            $eap = msg_cadena3('El código del prestador', $posicion, $cod_procedimiento, 8);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el ambito del procedimiento en el AP
     * @param type $ambito_proc
     * @param type $posicion
     */
    private function ambitoProcedimiento($ambito_proc, $posicion) {

        if (uno_tres($ambito_proc) === false) {

            $eap = msg_generico('El ámbito del procedimiento', $posicion, $ambito_proc, 'Debe estar entre los valores del 1 al 3.');
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la finalidad del procedimiento en el AP
     * @param type $fin_procedimiento
     * @param type $posicion
     */
    private function finalidadProcedimiento($fin_procedimiento, $posicion) {

        if (uno_cinco($fin_procedimiento) === false) {

            $eap = msg_generico('El fin del procedimiento', $posicion, $fin_procedimiento, 'Debe estar entre los valores del 1 al 5.');
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el personal que atiende en el AP
     * @param int $per_atiende
     * @param int $posicion
     */
    function personalAtiende($per_atiende, $posicion) {

        $permitido = array('', '1', '2', '3', '4', '5');

        for ($i = 0; $i < count($permitido); $i++) {
            if ($per_atiende === $permitido[$i]) {
                $estado = true;
                break;
            } else {
                $estado = false;
            }
        }

        if ($estado === false) {

            $eap = msg_generico('El personal que atiende', $posicion, $per_atiende, 'Debe estar entre los valores del 1 al 5.');
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el diagnostico principal en el AP
     * @param String $diag_principal
     * @param int $posicion
     */
    private function diagnosticoPrincipal($diag_principal, $posicion) {

        if (cespeciales1($diag_principal) === true) {

            $eap = msg_cadena1('El código del diagnóstico principal', $posicion, $diag_principal);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_principal != '') {

            if (strlen($diag_principal) != 4) {

                $eap = msg_cadena3('El código del diagnóstico principal', $posicion, $diag_principal, 4);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 12.- Metodo que valida el diagnostico relacionado en el AP
     * @param String $diag_relacionado
     * @param int1 $posicion
     */
    private function diagnosticorelacionado($diag_relacionado, $posicion) {

        if (cespeciales1($diag_relacionado) === true) {

            $eap = msg_cadena1('El diagnostico relacionado', $posicion, $diag_relacionado);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diag_relacionado != '') {

            if (strlen($diag_relacionado) != 4) {

                $eap = msg_cadena3('El diagnostico relacionado', $posicion, $diag_relacionado, 4);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 13.- Metodo que valida la complicacion en el AP
     * @param type $complicacion
     * @param type $posicion
     */
    private function complicacion($complicacion, $posicion) {


        if (cespeciales1($complicacion) === true) {

            $eap = msg_cadena1('La complicación', $posicion, $complicacion);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($complicacion != '') {

            if (strlen($complicacion) != 4) {

                $eap = msg_cadena3('La complicación', $posicion, $complicacion, 4);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 14.- Metodo que valida el acto quirurgico en el AP
     * @param int $acto_quirurgico
     * @param int $posicion
     */
    private function actoQuirurgico($acto_quirurgico, $posicion) {

        if ($this->acto_quirurgico($acto_quirurgico) === false) {

            $eap = msg_generico('La forma de realización del acto quirúrgico', $posicion, $acto_quirurgico, 'Debe estar entre los valores del 1 al 5.');
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 15.- Metodo que valida el valor del procedimiento en el AP
     * @param type $val_procedimiento
     * @param type $posicion
     */
    private function valorProcedimiento($val_procedimiento, $posicion) {

        if (valor_dec($val_procedimiento) === false) {

            $eap = msg_numero2('El valor del procedimiento', $posicion, $val_procedimiento);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * Metodo que valida si una factura esta declarada en el AF
     * @param String $nu_factura
     * @param int $posicion
     */
    private function buscarFacturaaf($nu_factura, $posicion) {

        if (b_factura($nu_factura) === false) {

            $eap = msg_errfac($posicion, $nu_factura);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
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

            $eap = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
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

        $eap = msg_errusu($posicion, $ti_documento, $nu_documento);
        array_push($this->logerap, $eap);
        echo "<p>" . $eap . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que busca el numero de autorizacion en el AP
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($num_autorizacion, $posicion) {

        if ($num_autorizacion != '' || $num_autorizacion === '0') {
            if (Consulta::getAutorizacion($num_autorizacion)) {

                $eap = msg_erraut($posicion, $num_autorizacion);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

    /**
     * Metodo que busca un procedimiento en el AP
     * @param string $co_procedimiento
     * @param int $posicion
     * @param int $edadUsuario
     * @param string $sexoUsuario
     */
    private function buscarProcedimiento($nu_documento, $fec_consulta, $co_procedimiento, $posicion, $edadUsuario, $sexoUsuario) {

        if (($datoProc = Consulta::getProcedimiento($co_procedimiento)) == false) {

            $eap = msg_errpro('El código del procedimiento', $posicion, $co_procedimiento);
            array_push($this->logerap, $eap);
            echo "<p>" . $eap . "</p>";
            $this->contador_val_cruzada++;
        } else {

            if ($edadUsuario < (int) $datoProc["EDAD_INICIO"] || $edadUsuario > (int) $datoProc["EDAD_FINAL"]) {

                $eap = msg_errproe('código del procedimiento', $posicion, $co_procedimiento);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_cruzada++;
            }

            if ($datoProc["COD_SEXO"] != 2) {

                if ($datoProc["COD_SEXO"] != $sexoUsuario) {

                    $eap = msg_errpros('código del procedimiento', $posicion, $co_procedimiento);
                    array_push($this->logerap, $eap);
                    echo "<p>" . $eap . "</p>";
                    $this->contador_val_cruzada++;
                }
            }
            
            
            if($datoProc["DUPLICADO_UPC"] === 'D'){
                
                array_push($this->duplicado_dia, array('n_documento' => $nu_documento, 'f_consulta' => $fec_consulta, 'cod_procedimiento' => $co_procedimiento, 'linea' => $posicion));
            }elseif ($datoProc["DUPLICADO_UPC"] === 'A') {
               
                $fec_anual = explode('/', $fec_consulta);
                array_push($this->duplicado_ano, array('n_documento' => $nu_documento, 'f_consulta' => $fec_anual[2], 'cod_procedimiento' => $co_procedimiento, 'linea' => $posicion));
            } 
        }
    }

    /**
     * Metodo que busca el diagnostico principal en el AP
     * @param type $diag_prin
     * @param type $posicion
     * @param type $edadUsuario
     * @param type $sexoUsuario
     */
    private function buscarDiagprincipal($diag_prin, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_prin != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_prin)) == false) {

                $eap = msg_errdia('principal', $posicion, $diag_prin);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eap = msg_errdiae('código de diagnóstico principal', $posicion, $diag_prin);
                    array_push($this->logerap, $eap);
                    echo "<p>" . $eap . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eap = msg_errdias('código de diagnóstico principal', $posicion, $diag_prin);
                        array_push($this->logerap, $eap);
                        echo "<p>" . $eap . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico relacionado en el AP
     * @param String $diag_rel
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagrelacionado($diag_rel, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diag_rel != '') {
            if (($datoDiag = Consulta::getDiagnostico($diag_rel)) == false) {

                $eap = msg_errdia('relacionado', $posicion, $diag_rel);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eap = msg_errdiae('código de diagnóstico relacionado', $posicion, $diag_rel);
                    array_push($this->logerap, $eap);
                    echo "<p>" . $eap . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eap = msg_errdias('código de diagnóstico relacionado', $posicion, $diag_rel);
                        array_push($this->logerap, $eap);
                        echo "<p>" . $eap . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca el diagnostico de la complicacion en el AP
     * @param String $complic
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagcomplicacion($complic, $posicion, $edadUsuario, $sexoUsuario) {

        if ($complic != '') {
            if (($datoDiag = Consulta::getDiagnostico($complic)) == false) {

                $eap = msg_errdia('de complicación', $posicion, $complic);
                array_push($this->logerap, $eap);
                echo "<p>" . $eap . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eap = msg_errdiae('código de diagnóstico de complicación', $posicion, $complic);
                    array_push($this->logerap, $eap);
                    echo "<p>" . $eap . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eap = msg_errdias('código de diagnóstico de complicación', $posicion, $complic);
                        array_push($this->logerap, $eap);
                        echo "<p>" . $eap . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que informa que el usuario no se encontro en el UP
     * @param int $posicion
     */
    private function usuarioNoencontrado($posicion) {

        $eap = '- No se puede validar el código del procedimiento y diagnósticos de la linea ' . ($posicion + 1) . ' '
                . 'porque el tipo y número de documento no están contenidos en el archivo de usuarios.';
        array_push($this->logerap, $eap);
        echo "<p>" . $eap . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que valida el acto quirurgico
     * @param int $valor
     * @return boolean
     */
    private function acto_quirurgico($valor) {

        $estado = false;

        if ($valor != '') {

            if (uno_cinco($valor) === false) {
                $estado = false;
            } else {
                $estado = true;
            }
        } else {
            $estado = true;
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
            array_push($this->logerap, $eac);
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
            array_push($this->logerap, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_cruzada++;
        }
    }

}
