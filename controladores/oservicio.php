<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////  ARCHIVO DE OTROS SERVICIOS //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////    ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LAS   /////// 
////////    ESTANCIAS, INSUMOS UTILIZADOS EN LA ATENCIÓN, TRASLADO DE    /////// 
///////////////////////      PACIENTES Y HONORARIOS.     ///////////////////////
////////////////////////////////////////////////////////////////////////////////


//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Consulta.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Oservicio_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerat = array('', '----- Errores encontrados en el archivo de otros servicios: -----');

    /**
     * valida todo el archivo de recien nacidos
     * @param String $ruta
     * @return int
     */
    function val_oservicios($ruta) {


        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AT txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo AT
                    if (count($valor) < 11 || count($valor) > 11) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura        = $valor[0];
                        $cod_prestador      = $valor[1];
                        $tip_identificacion = $valor[2];
                        $num_documento      = $valor[3];
                        $num_autorizacion   = $valor[4];
                        $tip_servicio       = $valor[5];
                        $cod_servicio       = $valor[6];
                        $nombre_servicio    = $valor[7];
                        $cantidad           = $valor[8];
                        $v_unitario         = $valor[9];
                        $v_total            = $valor[10];


                        //1 validacion numero de la factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 Validacion codigo restador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoDocumento($tip_identificacion, $posicion);

                        //4 validacion numero documento
                        $this->numeroDocumento($num_documento, $posicion);

                        //5 validacion numero autorizacion
                        $this->autorizacion($num_autorizacion, $posicion);

                        //6 validacion tipo servicio
                        $this->tipoServicio($tip_servicio, $posicion);

                        //7 validacion codigo del servicio
                        $this->codigoServicio($cod_servicio, $posicion);

                        //8 validacion  nombre del servicio
                        $this->nombreServicio($nombre_servicio, $posicion);

                        //9 validacion cantidad del servicio
                        $this->cantidadServicio($cantidad, $posicion);

                        //10 validacion valor unitario
                        $this->valorUnitario($v_unitario, $posicion);

                        //11 validacion valor total
                        $this->valorTotal($v_total, $posicion, $v_unitario, $cantidad);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de otros servicios. </p>';
        //Fin validacion estructura
        
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de otros servicios. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo AT txt cruzandolo contra la tabla AUTORIZACIONES
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura      = $cruce[0];
                        $co_prestador    = $cruce[1];
                        $ti_documento    = $cruce[2];
                        $nu_documento    = $cruce[3];
                        $nu_autorizacion = $cruce[4];

                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);

                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);

                        //cruce del numero de documento contra el archivo US
                        $this->buscarUsuario($nu_documento, $ti_documento, $posicion);

                        //valido si la autorizacion ya esta registrada en la tabla AUTORIZACIONES
                        $this->buscarAutorizacion($nu_autorizacion, $posicion);
                    }
                }

             echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de otros servicios. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'otros servicios');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerat);
        }


        return $total_err;
    }

    ///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AC
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eat = msg_estructura($posicion, 11);
        array_push($this->logerat, $eat);
        echo "<p class='has-text-danger'>" . $eat . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el numero de factura en el AT
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eat = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eat = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AT
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $eat = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $eat = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de documento en el AT
     * @param String $tip_identificacion
     * @param int $posicion
     */
    private function tipoDocumento($tip_identificacion, $posicion) {

        if (t_documento($tip_identificacion) === false) {

            $eat = msg_ertiden($posicion, $tip_identificacion);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento en el AT
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eat = msg_ernuid($posicion, $num_documento);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $eat = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida la autorizacion en el AT
     * @param int $num_autorizacion
     * @param int $posicion
     */
    private function autorizacion($num_autorizacion, $posicion) {

        if (cespeciales1($num_autorizacion) === true || $num_autorizacion === '0') {

            $eat = msg_cadena1('El número de autorización', $posicion, $num_autorizacion);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_autorizacion) >= 16) {

            $eat = msg_cadena3('El número de autorización', $posicion, $num_autorizacion, 15);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el tipo de servicio en el AT
     * @param int $tip_servicio
     * @param int $posicion
     */
    private function tipoServicio($tip_servicio, $posicion) {

        if (uno_cuatro($tip_servicio) === false) {

            $eat = msg_generico('El tipo de servicio', $posicion, $tip_servicio, 'Debe estar entre los valores del 1 al 4.');
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el codigo del servicio en el AT
     * @param String $cod_servicio
     * @param int $posicion
     */
    private function codigoServicio($cod_servicio, $posicion) {

        if (cespeciales1($cod_servicio) === true) {

            $eat = msg_cadena2('El código del servicio', $posicion, $cod_servicio);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_servicio) > 20) {

            $eat = msg_cadena3('El código del servicio', $posicion, $cod_servicio, 20);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el nombre del servicio en el AT
     * @param String $nombre_servicio
     * @param int $posicion
     */
    private function nombreServicio($nombre_servicio, $posicion) {

        if (cespeciales2($nombre_servicio) === true || espacio($nombre_servicio) === false) {

            $eat = msg_cadena2('El nombre del servicio', $posicion, $nombre_servicio);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($nombre_servicio) > 60) {

            $eat = msg_cadena3('El nombre del servicio', $posicion, $nombre_servicio, 60);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la cantidad del servicio en el AT
     * @param int $cantidad
     * @param int $posicion
     */
    private function cantidadServicio($cantidad, $posicion) {

        if (validar_entero($cantidad) === false || $cantidad === '') {

            $eat = msg_numero1('La cantidad del servicio', $posicion, $cantidad);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cantidad) > 5) {

            $eat = msg_cadena3('La cantidad del servicio', $posicion, $cantidad, 5);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el valor unitario en el AT
     * @param float $v_unitario
     * @param int $posicion
     */
    private function valorUnitario($v_unitario, $posicion) {

        if (valor_dec($v_unitario) === false) {

            $eat = msg_numero2('El valor unitario del material e insumo', $posicion, $v_unitario);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el valor total en el AT
     * @param float $v_total
     * @param int $posicion
     * @param float $v_unitario
     * @param int $cantidad
     */
    private function valorTotal($v_total, $posicion, $v_unitario, $cantidad) {

        if (valor_dec($v_total) === false) {

            $eat = msg_numero2('El valor total del material e insumo', $posicion, $v_total);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_Estructura++;
        } elseif (is_numeric($cantidad) && is_numeric($v_unitario)) {
            if ((intval($cantidad * $v_unitario)) != intval($v_total)) {

                $eat = msg_generico('El valor total del medicamento', $posicion, $v_total, 'Debe ser igual al resultado de la multiplicación entre el número de unidades y valor unitario del medicamento.');
                array_push($this->logerat, $eat);
                echo "<p>" . $eat . "</p>";
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

            $eat = msg_errfac($posicion, $nu_factura);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
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

            $eat = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que busca el usuario en el us
     * @param int $nu_documento
     * @param String $ti_documento
     * @param int $posicion
     */
    private function buscarUsuario($nu_documento, $ti_documento, $posicion) {

        if (b_identificacion($nu_documento, $ti_documento) === false) {

            $eat = msg_errusu($posicion, $ti_documento, $nu_documento);
            array_push($this->logerat, $eat);
            echo "<p>" . $eat . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que busca si una autorizacion ya esta registrada
     * @param int $nu_autorizacion
     * @param int $posicion
     */
    private function buscarAutorizacion($nu_autorizacion, $posicion) {

        if ($nu_autorizacion != '') {
            if (Consulta::getAutorizacion($nu_autorizacion)) {

                $eat = msg_erraut($posicion, $nu_autorizacion);
                array_push($this->logerat, $eat);
                echo "<p>" . $eat . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

}
