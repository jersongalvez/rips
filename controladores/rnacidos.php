<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////  ARCHIVO DE RECIEN NACIDOS  //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////   ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LOS   ///////
////////     NACIMIENTOS, CONTIENE LOS DATOS BÁSICOS DE MADRE E HIJO.     //////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Rnacidos_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logeran = array('', '----- Errores encontrados en el archivo de recién nacidos: -----');

    /**
     * valida todo el archivo de recien nacidos
     * @param String $ruta
     * @return int
     */
    function val_rnacidos($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();


        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AN txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo AN
                    if (count($valor) < 14 || count($valor) > 14) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $fecha_nac          = $valor[4];
                        $hora_nac           = $valor[5];
                        $edad_gestacional   = $valor[6];
                        $cont_prenatal      = $valor[7];
                        $sexo               = $valor[8];
                        $peso               = $valor[9];
                        $diagnostico        = $valor[10];
                        $cau_bas_muerte     = $valor[11];
                        $fech_muerte        = $valor[12];
                        $hora_muerte        = $valor[13];


                        //1 validacion numero factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 Validacion codigo restador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoDocumento($tip_identificacion, $posicion);

                        //4 validacion numero documento madre
                        $this->documentoMadre($num_documento, $posicion);

                        //5 validacion fecha nacimiento
                        $this->fechaNacimiento($fecha_nac, $posicion);

                        //6 validacion hora ingreso
                        $this->horaNacimiento($hora_nac, $posicion);

                        //7 validacion edad gestacional
                        $this->edadGestacion($edad_gestacional, $posicion);

                        //8 validacion control prenatal
                        $this->controlPrenatal($cont_prenatal, $posicion);

                        //9 validacion control sexo
                        $this->sexoR($sexo, $posicion);

                        //10 validacion peso
                        $this->pesoR($peso, $posicion);

                        //11 validacion diagnostico relacionado n1 de egreso
                        $this->diagRelegreso1($diagnostico, $posicion);

                        //12 validacion causa basica de muerte
                        $this->causaMuerte($cau_bas_muerte, $posicion);

                        //13 validacion fecha de muerte
                        $this->fechaMuerte($fech_muerte, $posicion, $cau_bas_muerte);

                        //14 validacion hora de muerte
                        $this->horaMuerte($hora_muerte, $posicion, $cau_bas_muerte);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de recién nacidos. </p>';
        //Fin validacion estructura
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';


                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de recién nacidos. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo AC txt cruzandolo contra la tabla DIAGNOSTICOS
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura   = $cruce[0];
                        $co_prestador = $cruce[1];
                        $ti_documento = $cruce[2];
                        $nu_documento = $cruce[3];
                        $f_nacimiento = $cruce[4];
                        $sexo_rn      = $cruce[8];
                        $diagnostico  = $cruce[10];
                        $cau_muerte   = $cruce[11];
                        $usuario      = 1;


                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);

                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);


                        //cruce del numero de documento contra el archivo US
                        if ((b_identificacion($nu_documento, $ti_documento)) === false) {

                            //declaro y asigno cuando el usuario no existe en el us
                            $usuario = 0;
                            $this->buscarUsuario($posicion, $ti_documento, $nu_documento);
                        } else {

                            $edadUsuario = (float) convertir_edad($this->calcular_dias($f_nacimiento), 3);
                            $sexoUsuario = $sexo_rn;
                     
                        }



                        if ($usuario === 1) {

                            //valido si el diagnostico existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarDiagprincipal($diagnostico, $posicion, $edadUsuario, $sexoUsuario);

                            //valido si el diagnostico existe en la tabla DIAGNOSTICOS, si este esta diligenciado
                            $this->buscarCaubasmuerte($cau_muerte, $posicion, $diagnostico, $edadUsuario, $sexoUsuario);
                        } else {

                            $this->usuarioNoencontrado($posicion);
                        }
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de recién nacidos. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'recién nacidos');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logeran);
        }


        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AC
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eac = msg_estructura($posicion, 14);
        array_push($this->logeran, $eac);
        echo "<p class='has-text-danger'>" . $eac . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de la factura en el AN
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $ean = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $ean = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AN
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $ean = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) > 12) {

            $ean = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de documento en el AN
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoDocumento($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $ean = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento de la madre en el AN
     * @param int $num_documento
     * @param int $posicion
     */
    private function documentoMadre($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $ean = msg_ernuid($posicion, $num_documento);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $ean = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la fecha de nacimiento en el AN
     * @param date $fecha_nac
     * @param int $posicion
     */
    private function fechaNacimiento($fecha_nac, $posicion) {

        if (validar_fecha($fecha_nac) === false) {

            $ean = msg_fec1('La fecha de nacimiento', $posicion, $fecha_nac);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_nac, date("j/n/Y")) === true) {

            $ean = msg_fec2('La fecha de nacimiento', $posicion, $fecha_nac);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida la hora de nacimiento en el AN
     * @param time $hora_nac
     * @param int $posicion
     */
    private function horaNacimiento($hora_nac, $posicion) {

        if (val_hora($hora_nac) === false) {

            $ean = msg_hor('La hora de nacimiento', $posicion, $hora_nac);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida la edad gestacional en el AN
     * @param int $edad_gestacional
     * @param int $posicion
     */
    private function edadGestacion($edad_gestacional, $posicion) {

        if (validar_entero($edad_gestacional) === false || $edad_gestacional === '') {

            $ean = msg_numero1('La edad gestacional', $posicion, $edad_gestacional);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($edad_gestacional) > 2) {

            $ean = msg_cadena3('La edad gestacional', $posicion, $edad_gestacional, 2);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el control prenatal en el AN
     * @param int $cont_prenatal
     * @param int $posicion
     */
    private function controlPrenatal($cont_prenatal, $posicion) {

        if (uno_dos($cont_prenatal) === false) {

            $ean = msg_generico('El control prenatal', $posicion, $cont_prenatal, 'Debe tener como valor 1 o 2.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida el sexo del recien nacido en el AN
     * @param int $sexo
     * @param int $posicion
     */
    private function sexoR($sexo, $posicion) {

        if (sexo($sexo) === false) {

            $ean = msg_generico('El sexo', $posicion, $sexo, 'Debe tener como valor M o F.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el peso del recien nacido en el AN
     * @param type $peso
     * @param type $posicion
     */
    private function pesoR($peso, $posicion) {

        if (!preg_match('/^[0-9]{1,4}$|^[0-9]{1,4}\.[0-9]{1}$/', $peso)) {

            $ean = msg_generico('El peso', $posicion, $peso, 'Debe ser un valor numérico de 4 caracteres máximo y dado el caso con una posición decimal.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el diagnostico relacionado 1 de egreso en el AN
     * @param String $diagnostico
     * @param int $posicion
     */
    private function diagRelegreso1($diagnostico, $posicion) {

        if (cespeciales1($diagnostico) === true) {

            $ean = msg_cadena1('El diagnóstico', $posicion, $diagnostico);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($diagnostico != '') {

            if (strlen($diagnostico) != 4) {

                $ean = msg_cadena3('El diagnóstico', $posicion, $diagnostico, 4);
                array_push($this->logeran, $ean);
                echo "<p>" . $ean . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 12.- Metodo que valida la causa basiva de muerte en el AN
     * @param String $cau_bas_muerte
     * @param int $posicion
     */
    private function causaMuerte($cau_bas_muerte, $posicion) {

        if (cespeciales1($cau_bas_muerte) === true) {

            $ean = msg_cadena1('La causa básica de muerte', $posicion, $cau_bas_muerte);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cau_bas_muerte != '') {

            if (strlen($cau_bas_muerte) != 4) {

                $ean = msg_cadena3('La causa básica de muerte', $posicion, $cau_bas_muerte, 4);
                array_push($this->logeran, $ean);
                echo "<p>" . $ean . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * 13.- Metodo que valida la fecha de muerte del recien nacido en el AN
     * @param date $fech_muerte
     * @param int $posicion
     * @param String $cau_bas_muerte
     */
    private function fechaMuerte($fech_muerte, $posicion, $cau_bas_muerte) {

        if ($this->validar_fecha1($fech_muerte) === false) {

            $ean = msg_fec1('La fecha de muerte', $posicion, $fech_muerte);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cau_bas_muerte != '' && $fech_muerte === '') {

            $ean = msg_generico('La fecha de muerte', $posicion, $fech_muerte, 'Si la causa básica esta diligenciada se debe registrar la fecha de muerte.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cau_bas_muerte === '' && $fech_muerte != '') {

            $ean = msg_generico('La fecha de muerte', $posicion, $fech_muerte, 'No se puede registrar sin que haya una causa básica de muerte.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 
     * @param time $hora_muerte
     * @param int $posicion
     * @param String $cau_bas_muerte
     */
    private function horaMuerte($hora_muerte, $posicion, $cau_bas_muerte) {

        if ($this->val_hora1($hora_muerte) === false) {

            $ean = msg_hor('La hora de muerte', $posicion, $hora_muerte);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cau_bas_muerte != '' && $hora_muerte === '') {

            $ean = msg_generico('La hora de muerte', $posicion, $hora_muerte, 'Si la causa básica esta diligenciada se debe registrar la hora de muerte.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($cau_bas_muerte === '' && $hora_muerte != '') {

            $ean = msg_generico('La hora de muerte', $posicion, $hora_muerte, 'No se puede registrar sin que haya una causa básica de muerte.');
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
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

            $ean = msg_errfac($posicion, $nu_factura);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
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

            $ean = msg_errCp3($posicion, $co_prestador);
            array_push($this->logeran, $ean);
            echo "<p>" . $ean . "</p>";
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
        array_push($this->logeran, $eac);
        echo "<p>" . $eac . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que busca el diagnostico principal de ingreso en el AN
     * @param String $diagnostico
     * @param int $posicion
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarDiagprincipal($diagnostico, $posicion, $edadUsuario, $sexoUsuario) {

        if ($diagnostico != '') {
            if (($datoDiag = Consulta::getDiagnostico($diagnostico)) == false) {

                $ean = msg_errdia('principal de ingreso', $posicion, $diagnostico);
                array_push($this->logeran, $ean);
                echo "<p>" . $ean . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || (int) $edadUsuario > $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de diagnóstico principal de ingreso', $posicion, $diagnostico);
                    array_push($this->logeran, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de diagnóstico principal de ingreso', $posicion, $diagnostico);
                        array_push($this->logeran, $eac);
                        echo "<p>" . $eac . "</p>";
                        $this->contador_val_cruzada++;
                    }
                }
            }
        }
    }

    /**
     * Metodo que busca la causa basica de muerte en el AN
     * @param String $cau_muerte
     * @param int $posicion
     * @param String $diagnostico
     * @param int $edadUsuario
     * @param String $sexoUsuario
     */
    private function buscarCaubasmuerte($cau_muerte, $posicion, $diagnostico, $edadUsuario, $sexoUsuario) {

        if ($cau_muerte != '') {
            if (($datoDiag = Consulta::getDiagnostico($cau_muerte)) == false) {

                $ean = msg_errdia('de la causa básica de muerte', $posicion, $diagnostico);
                array_push($this->logeran, $ean);
                echo "<p>" . $ean . "</p>";
                $this->contador_val_cruzada++;
            } else {

                if ($edadUsuario < (int) $datoDiag["EDA_MININA"] || $edadUsuario > (int) $datoDiag["EDA_MAXIMA"]) {

                    $eac = msg_errdiae('código de la causa básica de muerte', $posicion, $diagnostico);
                    array_push($this->logeran, $eac);
                    echo "<p>" . $eac . "</p>";
                    $this->contador_val_cruzada++;
                }

                if ($datoDiag["RANGO_SEXO"] != 2) {

                    if ($datoDiag["RANGO_SEXO"] != $sexoUsuario) {

                        $eac = msg_errdias('código de la causa básica de muerte', $posicion, $diagnostico);
                        array_push($this->logeran, $eac);
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

        $ean = '- No se pueden validar los diagnósticos de la linea ' . ($posicion + 1) . ' '
                . 'porque el tipo y número de documento de la madre no están contenidos en el archivo de usuarios.';
        array_push($this->logeran, $ean);
        echo "<p>" . $ean . "</p>";
        $this->contador_val_cruzada++;
    }

    /**
     * Metodo que valida una fecha en formato español, deja pasar espacios en blanco
     * @param date $fecha
     * @return boolean
     */
    private function validar_fecha1($fecha) {

        if ($fecha === '') {
            return true;
        } else {
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
    }

    /**
     * Metodo que valida el formato de una hora en
     * hh:mm, deja pasar espacios en blanco
     * @param date $time
     * @return boolean
     */
    private function val_hora1($time) {

        if ($time === '') {
            return true;
        } else {

            if (!preg_match('/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/', $time)) {

                return false;
            } else {
                return true;
            }
        }
    }
    
    /**
     * Metodo que calcula los dias entre la fecha de nacimiento y la fecha reportada
     * @param date $fecha_nacimiento
     * @return int
     */
    private function calcular_dias($fecha_nacimiento) {

        $convertir_fecha = explode('/', $fecha_nacimiento);
        $nueva_fecha = $convertir_fecha[2] . '-' . $convertir_fecha[1] . '-' . $convertir_fecha[0];

        $dias = (strtotime(date("Y-m-d")) - strtotime($nueva_fecha)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);
        
        return $dias;
    }
 

}
