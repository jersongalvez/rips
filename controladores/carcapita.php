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
require_once '../../modelos/Capita.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Ccapita_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //Cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //Obtiene el periodo a validar
    private $periodo_prefactura = '';
    //Sumatoria numero de facturas
    private $total_facturas;
    //Sumatoria mes anticipado
    private $mes_anticipado;
    //Sumatoria reconocimientos
    private $reconocimientos;
    //Sumatoria restituciones
    private $restituciones;
    //Sumatoria valor final capita
    private $valor_final_capita;
    //array que guarda los numeros de contrato para luego validar si estan duplicados en el archivo
    private $Ncontrato = array();
    //array que contiene los erres encontrados en la validacion
    private $logercap = array('', '----- Errores encontrados en el archivo de cargue: -----');

    /**
     * valida todo el archivo del cargue de capita
     * @param String $datos
     * @param int $nom_periodo
     * @return int
     */
    function val_cargacapita($datos, $nom_periodo) {

        //Asigno el nombre del periodo para su posterior validacion.
        $this->periodo_prefactura = $nom_periodo;

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AT txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo AT
                    if (count($valor) < 8 || count($valor) > 8) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $periodo           = $valor[0];
                        $nit_prestador     = $valor[1];
                        $cod_contrato      = $valor[2];
                        $num_afiliados     = $valor[3];
                        $valor_Manticipado = $valor[4];
                        $reconocimientos   = $valor[5];
                        $restituciones     = $valor[6];
                        $valor_Fcapita     = $valor[7];


                        //1 validacion del periodo de la cuenta
                        $this->periodoFactura($periodo, $posicion);

                        //2 validacion numero documento
                        $this->numeroDocumento($nit_prestador, $posicion);

                        //3 validacion numero de contrato
                        $this->numeroContrato($cod_contrato, $posicion);
                        
                        //Asigno a un arreglo todos los numeros de contrato
                        array_push($this->Ncontrato, $cod_contrato);

                        //Cuento la cantidad de facturas
                        $this->total_facturas++;

                        //4 validacion numero de afiliados
                        $this->numeroAfiliados($num_afiliados, $posicion);

                        //5 validacion mes anticipado
                        $this->valorMesAnticipado($valor_Manticipado, $posicion);

                        //Sumatoria del mes anticipado
                        $this->mes_anticipado = is_numeric($valor_Manticipado) ? $this->mes_anticipado + $valor_Manticipado : $this->mes_anticipado + 0;

                        //6 validacion reconocimientos
                        $this->valorReconocimientos($reconocimientos, $posicion);

                        //Sumatoria de los reconocimientos
                        $this->reconocimientos = is_numeric($reconocimientos) ? $this->reconocimientos + $reconocimientos : $this->reconocimientos + 0;

                        //7 validacion restituciones
                        $this->valorRestituciones($restituciones, $posicion);

                        //Sumatoria de las restituciones
                        $this->restituciones = is_numeric($restituciones) ? $this->restituciones + $restituciones : $this->restituciones + 0;

                        //11 validacion valor total
                        $this->valorTotal($valor_Fcapita, $valor_Manticipado, $reconocimientos, $restituciones, $posicion);

                        //Sumatoria del valor total
                        $this->valor_final_capita = is_numeric($valor_Fcapita) ? $this->valor_final_capita + $valor_Fcapita : $this->valor_final_capita + 0;
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo contrato de capitación. </p>';
        //Fin validacion estructura
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo '- No se puede continuar con la validación cruzada del archivo contrato de capitación. Corrija los errores e intente de nuevo. <br>';
                } else {


                    //recorre el archivo cruzandolo contra la base de datos
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $v_periodo = $cruce[0];
                        $ni_prestador = $cruce[1];
                        $num_contrato = $cruce[2];

                        //cruce del numero del periodo contra el declarado en el nombre del archivo
                        $this->validarPeriodo($v_periodo, $posicion);

                        //Validar que el nit de prestador este registrado en la base de datos
                        $this->validarNit($ni_prestador, $num_contrato, $v_periodo, $posicion);
                    }
                    
                    
                    //valido si hay num_contratos declaradaos dos o mas veces en el archivo
                    if (count($x = $this->buscar_duplicado($this->Ncontrato))) {

                        $this->mostrarConDuplicado($x);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo contrato de capitación. </p>';
        //Fin validacion cruzada

        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            
            $_SESSION ["logErroresCap"] = $this->logercap;
        }


        $valores_cap[0] = $total_err;
        $valores_cap[1] = $this->total_facturas;
        $valores_cap[2] = $this->mes_anticipado;
        $valores_cap[3] = $this->reconocimientos;
        $valores_cap[4] = $this->restituciones;
        $valores_cap[5] = $this->valor_final_capita;
        
        return $valores_cap;
    }

    ///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de procesarla
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eat = msg_estructura($posicion, 11);
        array_push($this->logercap, $eat);
        echo "<p class='has-text-danger'>" . $eat . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el periodo de la factura
     * @param String $fecha
     * @param int $posicion
     */
    private function periodoFactura($fecha, $posicion) {

        $estado = 1;

        if (strlen($fecha) < 8) {

            $valores = explode('/', '01/' . $fecha);
            if (count($valores) < 3 || count($valores) > 3) {

                $estado = 1;
            } else {

                if (is_numeric($valores[0]) && is_numeric($valores[1]) && is_numeric($valores[2])) {

                    if (count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2]) && !empty($fecha)) {

                        $estado = 0;
                    }
                }
            }
        }


        //Si el estado es igual a 1, envio el error.
        if ($estado == 1) {

            $epref = msg_fec3('El periodo de la cuenta', $posicion, $fecha);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el numero de documento 
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroDocumento($num_documento, $posicion) {

        if ((!preg_match('/^[0-9]*$/', $num_documento)) || $num_documento === '') {

            $epref = msg_ernuid($posicion, $num_documento);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $epref = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el numero de contrato
     * @param int $num_contrato
     * @param int $posicion
     */
    private function numeroContrato($num_contrato, $posicion) {

        if ((preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\+=\{\}\[\]\|;:"\<\>,\.\?\\\]|[[:lower:]]/', $num_contrato)) || $num_contrato === '') {

            $epref = msg_cadena1('El número de contrato', $posicion, $num_contrato);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_contrato) >= 31) {

            $epref = msg_cadena3('El número de contrato', $posicion, $num_contrato, 30);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de documento 
     * @param int $num_afiliados
     * @param int $posicion
     */
    private function numeroAfiliados($num_afiliados, $posicion) {

        if ((!preg_match('/^[0-9]*$/', $num_afiliados)) || $num_afiliados === '') {

            $epref = msg_numero1('El número de afiliados', $posicion, $num_afiliados);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_afiliados) >= 16) {

            $epref = msg_cadena3('El número de afiliados', $posicion, $num_afiliados, 15);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida el valor del mes anticipado
     * @param float $v_mesA
     * @param int $posicion
     */
    private function valorMesAnticipado($v_mesA, $posicion) {

        if ($this->valor_dec($v_mesA) === false) {

            $epref = msg_numero2('El valor del mes anticipado', $posicion, $v_mesA);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el valor de los reconocimientos
     * @param float $v_reconocimiento
     * @param int $posicion
     */
    private function valorReconocimientos($v_reconocimiento, $posicion) {

        if ($this->valor_dec($v_reconocimiento) === false) {

            $epref = msg_numero2('El valor de los reconocimientos', $posicion, $v_reconocimiento);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el valor de las restituciones
     * @param float $v_restitucion
     * @param int $posicion
     */
    private function valorRestituciones($v_restitucion, $posicion) {

        if ($this->valor_dec($v_restitucion) === false) {

            $epref = msg_numero2('El valor de las restituciones', $posicion, $v_restitucion);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el valor final de la capita
     * @param int $v_total
     * @param int $v_mesAnt
     * @param int $reconocimientos
     * @param int $restituciones
     * @param int $posicion
     */
    private function valorTotal($v_total, $v_mesAnt, $reconocimientos, $restituciones, $posicion) {

        if ($this->valor_dec($v_total) === false) {

            $epref = msg_numero2('El valor final de la cápita', $posicion, $v_total);
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_Estructura++;
        } elseif (is_numeric($v_mesAnt) && is_numeric($reconocimientos) && is_numeric($restituciones)) {
            if ((intval(($v_mesAnt + $reconocimientos) - $restituciones)) != intval($v_total)) {

                $epref = msg_generico('El valor final de la cápita', $posicion, $v_total, 'Debe ser igual al resultado de la suma entre el valor del mes anterior y '
                        . 'los reconocimientos, menos el valor de las restituciones.');
                array_push($this->logercap, $epref);
                echo "<p>" . $epref . "</p>";
                $this->contador_val_Estructura++;
            }
        }
    }

    /**
     * Metodo que valida un valor con dos posiciones decimales
     * @param float - int $valor
     * @return boolean
     */
    private function valor_dec($valor) {

        if (!preg_match('/^[0-9]{1,15}$|^[0-9]{1,15}\.[0-9]{1,2}$/', $valor)) {

            return false;
        } else {
            return true;
        }
    }

    /**
     * Metodo que valida si el prestador declarado es igual al encontrado
     * en el archivo plano
     * @param int $periodo
     * @param int $posicion
     */
    private function validarPeriodo($periodo, $posicion) {

        if ($periodo !== $this->periodo_prefactura) {

            $epref = msg_generico('El periodo de la factura', $posicion, $periodo, 'Debe ser igual al declarado en el nombre del archivo plano.');
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que en primera instancia valida el nit del prestador, si lo encuentra, valida el numero de contrato
     * @param int $nit_prestador
     * @param String $num_contrato
     * @param String $periodo
     * @param int $posicion
     */
    private function validarNit($nit_prestador, $num_contrato, $periodo, $posicion) {

        if (Capita::getNitPrestador($nit_prestador) == false) {

            $epref = '- El NIT ' . $nit_prestador . ' de la línea ' . ($posicion + 1) . ' no esta registrado en el sistema.';
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_cruzada++;

            //Indico que no se puede validar el numero de contrato
            $this->nitNoencontrado($posicion);
        } else {

            //Valido el numero de contrato
            if (Capita::getNumContrato($nit_prestador, $num_contrato, $periodo) == false) {

                $epref = '- El contrato ' . $num_contrato . ' de la línea ' . ($posicion + 1) . ' no esta registrado en el sistema. verifique el periodo y número de contrato.';
                array_push($this->logercap, $epref);
                echo "<p>" . $epref . "</p>";
                $this->contador_val_cruzada++;
            }
        }
    }

    /**
     * Metodo que informa que no se puede puscar el contrato por que el not no existe
     * @param int $posicion
     */
    private function nitNoencontrado($posicion) {

        $epref = '- No se puede validar el número de contrato de la linea ' . ($posicion + 1) . ' porque el NIT no está registrado en la base de datos.';
        array_push($this->logercap, $epref);
        echo "<p>" . $epref . "</p>";
        $this->contador_val_cruzada++;
    }
    
    
    /**
     * Metodo que busca si un contrato esta duplicado en el archivo
     * @param array $array
     * @return array
     */
    private function buscar_duplicado($array) {
        $contar = array();
        $duplicado = array();

        foreach ($array as $posicion => $valores) {
            if (isset($contar[$valores])) {
                // si ya existe, le añadimos uno
                $contar[$valores] += 1;


                $contrato = array('linea' => $posicion, 'n_contrato' => $valores);
                array_push($duplicado, $contrato);
            } else {
                // si no existe lo añadimos al array
                $contar[$valores] = 1;
            }
        }
        return $duplicado;
    }
    
    
    /**
     * Lista los contratos duplicados en el archivo
     * @param array $x
     */
    private function mostrarConDuplicado($x) {

        foreach ($x as $valor) {

            $epref = '- El número de contrato ' . $valor['n_contrato'] . ' de la línea ' . $valor['linea'] . ' esta registrado dos o más veces en el archivo.';
            array_push($this->logercap, $epref);
            echo "<p>" . $epref . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    //////////////////////// METODOS PUBLICOS DE CLASE /////////////////////////////

    /**
     * Metodo que busca si el periodo de una prefactura ya esta registrado.
     * @param int $periodo
     * @return boolean
     */
    public function buscar_periodo($periodo) {

        return Capita::getPeriodo($periodo);
    }

}
