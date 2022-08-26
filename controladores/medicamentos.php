
<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////   ARCHIVO DE MEDICAMENTOS   //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////      ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LOS     /////
////    MEDICAMENTOS ENTREGADOS A UN PACIENTE DONDE SE DESCRIBE LOS DATOS   ////
/////////////////        BÁSICOS DEL MEDICAMENTO ENTREGADO        //////////////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Medicamento.php';
require_once '../../modelos/Consulta.php';

class Medicamentos_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logeram = array('', '----- Errores encontrados en el archivo de medicamentos: -----');

    /**
     * valida todo el archivo de medicamentos
     * @param String $ruta
     * @return int
     */
    function val_medicamentos($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();


        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AM txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo AM
                    if (count($valor) < 14 || count($valor) > 14) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura               = $valor[0];
                        $cod_prestador             = $valor[1];
                        $tip_doc                   = $valor[2];
                        $num_doc                   = $valor[3];
                        $num_autorizacion          = $valor[4];
                        $cod_medicamento           = $valor[5];
                        $tip_medicamento           = $valor[6];
                        $nom_medicamento           = $valor[7];
                        $forma_farmaceutica        = $valor[8];
                        $concentracion_medicamento = $valor[9];
                        $uni_medida                = $valor[10];
                        $num_unidades              = $valor[11];
                        $val_uni_med               = $valor[12];
                        $val_tot_med               = $valor[13];


                        //1 validacion numero de la factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 Validacion codigo prestador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoDocumento($tip_doc, $posicion);

                        //4 validacion numero documento
                        $this->numeroDocumento($num_doc, $posicion);

                        //5 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //6 validacion codigo medicamento
                        $this->codigoMedicamento($cod_medicamento, $posicion);

                        //7 validacion tipo medicamento
                        $this->tipoMedicamento($tip_medicamento, $posicion);

                        //8 validacion nombre medicamento
                        $this->nombreMedicamento($nom_medicamento, $posicion);

                        //9 validacion forma farmaceutica
                        $this->formaFarmaceutica($forma_farmaceutica, $posicion);

                        //10 validacion concentracion del medicamento
                        $this->concentracionMedicamento($concentracion_medicamento, $posicion);

                        //11 validacion unidad de medida del medicamento
                        $this->unidadMedida($uni_medida, $posicion);

                        //12 validacion numero de unidades
                        $this->numeroUnidades($num_unidades, $posicion);

                        //13 validacion valor unitario del medicamento
                        $this->valorUnitario($val_uni_med, $posicion);

                        //14 validacion valor total del medicamento
                        $this->valorTotal($val_tot_med, $posicion, $num_unidades, $val_uni_med);
                    }
                }


            echo '</div>';
        echo '</div>';


        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de medicamentos. </p>';
        //Fin validacion estructura
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de medicamentos. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo AM txt cruzandolo contra la tabla AUTORIZACIONES - MEDICAMENTOS
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura      = $cruce[0];
                        $co_prestador    = $cruce[1];
                        $ti_documento    = $cruce[2];
                        $nu_documento    = $cruce[3];
                        $nu_autorizacion = $cruce[4];
                        $co_medicamento  = $cruce[5];


                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);

                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);

                        //cruce del numero de documento contra el archivo US
                        $this->buscarUsuario($posicion, $ti_documento, $nu_documento);

                        //valido si la autorizacion ya esta registrada en la tabla AUTORIZACIONES
                        $this->buscarAutorizacion($nu_autorizacion, $posicion);

                        //valido si el cod medicamento esta registrado en la tabla MEDICAMENTOS
                        //$this->buscarMedicamento($co_medicamento, $posicion);
                    }
                }


            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de medicamentos. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'medicamentos');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logeram);
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
        array_push($this->logeram, $eac);
        echo "<p class='has-text-danger'>" . $eac . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de factura en el AM
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eam = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eam = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AM
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eam = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $eam = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de documento en el AM
     * @param String $tip_doc
     * @param int $posicion
     */
    private function tipoDocumento($tip_doc, $posicion) {

        if (t_documento($tip_doc) === false) {

            $eam = msg_ertiden($posicion, $tip_doc);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento en el AM
     * @param int $num_doc
     * @param int $posicion
     */
    private function numeroDocumento($num_doc, $posicion) {

        if (validar_entero($num_doc) === false || $num_doc === '') {

            $eac = msg_ernuid($posicion, $num_doc);
            array_push($this->logeram, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_doc) >= 21) {

            $eac = msg_cadena3('El número de identificación', $posicion, $num_doc, 20);
            array_push($this->logeram, $eac);
            echo "<p>" . $eac . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida el numero de autorizacion en el AM
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eam = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eam = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el codigo del medicamento en el AM
     * @param String $cod_medicamento
     * @param int $posicion
     */
    private function codigoMedicamento($cod_medicamento, $posicion) {

        if (cespeciales1($cod_medicamento) === true || $cod_medicamento === '') {

            $eam = msg_cadena1('El código del medicamento', $posicion, $cod_medicamento);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_medicamento) >= 21) {

            $eam = msg_cadena3('El código del medicamento', $posicion, $cod_medicamento, 20);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el tipo de medicamento en el AM
     * @param int $tip_medicamento
     * @param int $posicion
     */
    private function tipoMedicamento($tip_medicamento, $posicion) {

        if (uno_dos($tip_medicamento) === false) {

            $eah = msg_generico('El tipo de medicamento', $posicion, $tip_medicamento, 'Debe tener como valor 1 o 2.');
            array_push($this->logeram, $eah);
            echo "<p>" . $eah . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el nombre del medicamento en el AM
     * @param String $nom_medicamento
     * @param int $posicion
     */
    private function nombreMedicamento($nom_medicamento, $posicion) {

        if (cespeciales2($nom_medicamento) === true || espacio($nom_medicamento) === false) {

            $eam = msg_cadena2('El nombre del medicamento', $posicion, $nom_medicamento);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($nom_medicamento) >= 31) {

            $eam = msg_cadena3('El nombre del medicamento', $posicion, $nom_medicamento, 30);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la forma farmaceutica en el AM
     * @param String $forma_farmaceutica
     * @param int $posicion
     */
    private function formaFarmaceutica($forma_farmaceutica, $posicion) {

        if (cespeciales2($forma_farmaceutica) === true || espacio($forma_farmaceutica) === false) {

            $eam = msg_cadena2('La forma farmaceutica del medicamento', $posicion, $forma_farmaceutica);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($forma_farmaceutica) >= 21) {

            $eam = msg_cadena3('La forma farmaceutica del medicamento', $posicion, $forma_farmaceutica, 20);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida la concentracion del medicamento en el AM
     * @param String $concentracion_medicamento
     * @param int $posicion
     */
    private function concentracionMedicamento($concentracion_medicamento, $posicion) {

        if (cespeciales2($concentracion_medicamento) === true || espacio($concentracion_medicamento) === false) {

            $eam = msg_cadena2('La concentración del medicamento', $posicion, $concentracion_medicamento);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($concentracion_medicamento) >= 21) {

            $eam = msg_cadena3('La concentración del medicamento', $posicion, $concentracion_medicamento, 20);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida la unidad de medida del medicamento en el AM
     * @param String $uni_medida
     * @param int $posicion
     */
    private function unidadMedida($uni_medida, $posicion) {

        if (cespeciales2($uni_medida) === true || espacio($uni_medida) === false) {

            $eam = msg_cadena2('La unidad de medida del medicamento', $posicion, $uni_medida);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($uni_medida) >= 21) {

            $eam = msg_cadena3('La unidad de medida del medicamento', $posicion, $uni_medida, 20);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 12.- Metodo que valida el numero de unidades del medicamento en el AM
     * @param int $num_unidades
     * @param int $posicion
     */
    private function numeroUnidades($num_unidades, $posicion) {

        if (validar_entero($num_unidades) === false || $num_unidades === '') {

            $eam = msg_numero1('El número de unidades', $posicion, $num_unidades);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_unidades) > 5) {

            $eam = msg_cadena3('El número de unidades', $posicion, $num_unidades, 5);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 13.- Metodo que valida el valor unitario del 
     * @param float $val_uni_med
     * @param int $posicion
     */
    private function valorUnitario($val_uni_med, $posicion) {

        if (valor_dec($val_uni_med) === false) {

            $eam = msg_numero2('El valor unitario del medicamento', $posicion, $val_uni_med);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 14.- Metodo que valida el valor total del medicamento en el AM
     * @param float $val_tot_med
     * @param int $posicion
     * @param int $num_unidades
     * @param float $val_uni_med
     */
    private function valorTotal($val_tot_med, $posicion, $num_unidades, $val_uni_med) {

        if (valor_dec($val_tot_med) === false) {

            $eam = msg_numero2('El valor total del medicamento', $posicion, $val_tot_med);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_Estructura++;
        } elseif (is_numeric($num_unidades) && is_numeric($val_uni_med)) {
            if ((intval($num_unidades * $val_uni_med)) != intval($val_tot_med)) {

                $eam = msg_generico('El valor total del medicamento', $posicion, $val_tot_med,
                        'Debe ser igual al resultado de la multiplicación entre el número de unidades y valor unitario del medicamento.');
                array_push($this->logeram, $eam);
                echo "<p>" . $eam . "</p>";
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

            $eam = msg_errfac($posicion, $nu_factura);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
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

            $eam = msg_errCp3($posicion, $co_prestador);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
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


        if (b_identificacion($nu_documento, $ti_documento) === false) {

            $eam = msg_errusu($posicion, $ti_documento, $nu_documento);
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que busca si una autorizacion esta registrada en el sistema
     * @param int $nu_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($nu_autorizacion, $posicion) {

        if ($nu_autorizacion != '') {
            if (Consulta::getAutorizacion($nu_autorizacion)) {

                $eam = msg_erraut($posicion, $nu_autorizacion);
                array_push($this->logeram, $eam);
                echo "<p>" . $eam . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

    /**
     * Metodo que busca un medicamento
     * @param String $co_medicamento
     * @param int $posicion
     */
    private function buscarMedicamento($co_medicamento, $posicion) {

        if (Medicamento::getMedicamento($co_medicamento) == false) {

            $eam = '- El código del medicamento ' . $co_medicamento . ' de la línea ' . ($posicion + 1) . ' no esta vigente en INVIMA.';
            array_push($this->logeram, $eam);
            echo "<p>" . $eam . "</p>";
            $this->contador_val_cruzada++;
        }
    }

}
