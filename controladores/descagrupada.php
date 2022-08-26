<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////     ARCHIVO DESCRIPCION AGRUPADA     /////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';

class Descagrupada_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerad = array('', '----- Errores encontrados en el archivo descripción agrupada: -----');

    /**
     * valida todo el archivo descripcion agrupada
     * @param String $ruta
     * @return int
     */
    function val_dagrupada($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AD txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo CT
                    if (count($valor) < 6 || count($valor) > 6) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $num_factura = $valor[0];
                        $cod_prestador = $valor[1];
                        $cod_concepto = $valor[2];
                        $cantidad = $valor[3];
                        $val_unitario = $valor[4];
                        $val_total = $valor[5];


                        //1 validacion numero de la factura
                        $this->numeroFactura($num_factura, $posicion);

                        //2 validacion codigo del prestador
                        $this->codigoPrestador($cod_prestador, $posicion);

                        //3 validacion codigo del concepto
                        $this->codigoConcepto($cod_concepto, $posicion);

                        //4 validacion cantidad
                        $this->cantidad($cantidad, $posicion);

                        //5 validacion valor unitario
                        $this->valorUnitario($val_unitario, $posicion);

                        //6 validacion valor total
                        $this->valorTotal($val_total, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo descripción agrupada. </p>';
        //Fin validacion estructura
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo de control. Corrija los errores de estructura e intente de nuevo. <br>';
                } else {

                    //recorre el archivo CT txt cruzandolo con los encontrados en el zip
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $nu_factura   = $cruce[0];
                        $co_prestador = $cruce[1];


                        //cruce del numero de factura contra el archivo AF
                        $this->buscarFacturaaf($nu_factura, $posicion);

                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($co_prestador, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo descripción agrupada. </p>';
        //Fin validacion cruzada


        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'control');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerad);
        }

        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AD
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $ead = msg_estructura($posicion, 6);
        array_push($this->logerad, $ead);
        echo "<p class='has-text-danger'>" . $ead . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida la factura en el AD
     * @param String $num_factura
     * @param int $posicion
     */
    private function numeroFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $ead = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $ead = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el codigo del prestador en el AD
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $ead = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $ead = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el codigo del concepto en el AD
     * @param int $cod_concepto
     * @param int $posicion
     */
    private function codigoConcepto($cod_concepto, $posicion) {

        $permitido = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14');

        for ($i = 0; $i < count($permitido); $i++) {
            if ($cod_concepto === $permitido[$i]) {
                $estado = true;
                break;
            } else {
                $estado = false;
            }
        }


        if ($estado === false) {

            $ead = msg_cadena4('El código del concepto', $posicion, $cod_concepto);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida la cantidad en el AD
     * @param int $cantidad
     * @param int $posicion
     */
    private function cantidad($cantidad, $posicion) {

        if (validar_entero($cantidad) === false || $cantidad === '') {

            $ead = msg_numero1('La cantidad', $posicion, $cantidad);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cantidad) >= 16) {

            $ead = msg_cadena3('La cantidad', $posicion, $cantidad, 15);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida el valor unitario en el AD
     * @param float $val_unitario
     * @param int $posicion
     */
    private function valorUnitario($val_unitario, $posicion) {

        if (valor_dec($val_unitario) === false) {

            $ead = msg_numero2('El valor unitario', $posicion, $val_unitario);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el valor total en el AD
     * @param float $val_total
     * @param int $posicion
     */
    private function valorTotal($val_total, $posicion) {

        if (valor_dec($val_total) === false) {

            $ead = msg_numero2('El valor total', $posicion, $val_total);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * Metodo que busca una factura en el AF
     * @param String $nu_factura
     * @param int $posicion
     */
    private function buscarFacturaaf($nu_factura, $posicion) {

        if (b_factura($nu_factura) === false) {

            $ead = msg_errfac($posicion, $nu_factura);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
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

            $ead = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerad, $ead);
            echo "<p>" . $ead . "</p>";
            $this->contador_val_cruzada++;
        }
    }

}
