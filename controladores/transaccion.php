<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////  ARCHIVO DE TRANSACCIONES   //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
//////////   ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE   /////////
/////////   INFORMACIÓN DE TODAS LAS FACTURAS, CADA REGISTRO INCLUYE   /////////
////////    EL ENCABEZADO DE CADA FACTURA DE COMPRA/VENTA DE SERVICIOS /////////
//////////     MÁS EL VALOR TOTAL CON SUS DESCUENTOS, COMISIONES Y    //////////    
/////   BONIFICACIONES U OTRAS PACTADAS ENTRE EL PRESTADOR Y EL PAGADOR   //////
////////////////////////////////////////////////////////////////////////////////


//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require '../../modelos/Transaccion.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

class Transaccion_validador {

    //contador de errores
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logeraf = array('', '----- Errores encontrados en el archivo de transacciones: -----');
    //array que guarda los numeros de factura para luego cruzarlos
    //con los demas archivos
    private $Nfactura = array();
    //array que guarda los numeros de contratos
    private $Ncontrato = array();
    //sumatoria copagos
    private $copagos = 0;
    //sumatoria comisiones
    private $comisiones = 0;
    //sumatoria descuentos
    private $descuentos = 0;
    //valor neto
    private $valor_neto = 0;
    
  
    /**
     * valida todo el archivo de transacciones
     * @param String $ruta
     * @return int
     */
    public function val_transacciones($ruta, $mod_contrato) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));


        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo AF txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 17 campos del archivo AF
                    if (count($valor) < 17 || count($valor) > 17) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $codigo_prestador    = $valor[0];
                        $razon_social        = $valor[1];
                        $tipo_identificacion = $valor[2];
                        $num_identificacion  = $valor[3];
                        $num_factura         = $valor[4];
                        $fecha_expedicion    = $valor[5];
                        $fecha_inicio        = $valor[6];
                        $fecha_final         = $valor[7];
                        $cod_ent_admin       = $valor[8];
                        $nom_ent_admin       = $valor[9];
                        $numero_contrato     = $valor[10];
                        $plan_beneficios     = $valor[11];
                        $numero_poliza       = $valor[12];
                        $valor_copago        = $valor[13];
                        $valor_comision      = $valor[14];
                        $val_tot_descuentos  = $valor[15];
                        $val_total_pagar     = $valor[16];


                        //1 validacion codigo del prestador
                        $this->codigoPrestador($codigo_prestador, $posicion);

                        //2 Validacion razon social
                        $this->razonSocial($razon_social, $posicion);

                        //3 validacion tipo identificacion
                        $this->tipoIdentificacion($tipo_identificacion, $posicion);

                        //4 validacion numero identificacion
                        $this->numIdentificacion($num_identificacion, $posicion);

                        //5 validacion numero de la factura
                        $this->numFactura($num_factura, $posicion);

                        //Asigno a un arreglo todas las facturas
                        array_push($this->Nfactura, $num_factura);
 
                        //6 validacion fecha expedicion factura
                        $this->fechaExpfactura($fecha_expedicion, $posicion);

                        //7 validacion fecha inicio de la factura
                        $this->fechaInifactura($fecha_inicio, $posicion);

                        //8 validacion fecha final de la factura
                        $this->fechaFinfactura($fecha_final, $posicion, $fecha_inicio);

                        //9 validacion codigo entidad administradora, permite los valores EPSI06 - EPSIC6
                        $this->codigoEntadministrativa($cod_ent_admin, $posicion);

                        //10 validacion nombre entidad administradora (pijaos)
                        $this->nombreEntadministradora($nom_ent_admin, $posicion);

                        //11 validacion numero de contrato
                        $this->numeroContrato($numero_contrato, $posicion);
                        
                        //Guardo los numeros de contrato
                        (trim($numero_contrato) === '') ? "" : array_push($this->Ncontrato, trim($numero_contrato));

                        //12 validacion plan de beneficios
                        $this->planBeneficios($plan_beneficios, $posicion);

                        //13 validacion numero de poliza
                        $this->numeroPoliza($numero_poliza, $posicion);

                        //14 validacion del copago
                        $this->copago($valor_copago, $posicion);
                        
                        //Guardo el valor de los copagos
                        $this->copagos = is_numeric($valor_copago) ? $this->copagos + $valor_copago : $this->copagos + 0;

                        //15 validacion de la comision
                        $this->comision($valor_comision, $posicion);
                        
                        //Guardo el valor de las comisiones
                        $this->comisiones = is_numeric($valor_comision) ? $this->comisiones + $valor_comision : $this->comisiones + 0;

                        //16 Validacion valor total de los descuentos
                        $this->totalDescuentos($val_tot_descuentos, $posicion);
                        
                        //Guardo el valor de los descuentos
                        $this->descuentos = is_numeric($val_tot_descuentos) ? $this->descuentos + $val_tot_descuentos : $this->descuentos + 0;

                        //17 validacion valor neto a pagar por el contratante (pijaos)
                        $this->valorNeto($val_total_pagar, $posicion);
                        
                        //Guardo el valor total
                        $this->valor_neto = is_numeric($val_total_pagar) ? $this->valor_neto + $val_total_pagar : $this->valor_neto + 0;
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de transacciones. </p>';
        //Fin validacion estructura
        
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo ' - No se puede continuar con la validación cruzada del archivo de transacciones. Corrija los errores e intente de nuevo. <br>';
                } else {

                    //recorre el archivo AF txt cruzandolo contra la tabla TRANSACCION_SERV
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);

                        $cod_prestador      = $cruce[0];
                        $nu_identificacion  = $cruce[3];
                        $nu_factura         = $cruce[4];


                        //valido que el codigo del prestador corresponda al declarado en
                        //el archivo de control
                        $this->validarCodprestador($cod_prestador, $posicion);
                        
                        //valido si el nit del prestador es el mismo que se extrae de la BD
                        $this->validarNitprestador($nu_identificacion, $posicion);

                        //valido si la factura de ese prestador ya se ha registrado
                        $this->buscarFactura($cod_prestador, $nu_factura, $posicion);
                        
                    }


                    //valido si hay facturas declaradas dos o mas veces en el archivo
                    if (count($x = $this->buscar_duplicado($this->Nfactura))) {

                        $this->mostrarFacDuplicada($x);
                    }
                    
                    
                    //Busco los numeros de contrato agrupados
                    if(count($this->Ncontrato) > 0) {
                        
                        $this->Ncontrato = array_unique($this->Ncontrato);
                        
                        foreach ($this->Ncontrato as $contrato) {
                            
                            $this->obtener_num_contrato($contrato, $_SESSION ["ni_prestador"], $mod_contrato);
                        }          
                    }
                    
                }    

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de transacciones. </p>';
        //Fin validacion cruzada

        $_SESSION["facturas"] = $this->Nfactura;
       
        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'transacciones');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logeraf);
        }

        //Retorno un array con los valores del af
        $valores_Af[0] = $total_err;
        $valores_Af[1] = $this->copagos;
        $valores_Af[2] = $this->comisiones;
        $valores_Af[3] = $this->descuentos;
        $valores_Af[4] = $this->valor_neto;
        
        
        return $valores_Af;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el AF
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eaf = msg_estructura($posicion, 17);
        array_push($this->logeraf, $eaf);
        echo "<p class='has-text-danger'>" . $eaf . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el tipo de dato y su longitud en el campo
     * codigo de prestador en el AF
     * @param int $codigo_prestador
     * @param int $posicion
     */
    private function codigoPrestador($codigo_prestador, $posicion) {

        if (validar_entero($codigo_prestador) === false || $codigo_prestador === '') {

            $eaf = msg_errCp1($posicion, $codigo_prestador);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($codigo_prestador) >= 13) {

            $eaf = msg_errCp2($posicion, $codigo_prestador);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida la razon social en el AF
     * @param type $razon_social
     * @param type $posicion
     */
    private function razonSocial($razon_social, $posicion) {

        if (cespeciales2($razon_social) === true || espacio($razon_social) === false) {

            $eaf = msg_cadena2('La razón social', $posicion, $razon_social);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($razon_social) >= 61) {

            $eaf = msg_cadena3('La razón social', $posicion, $razon_social, 60);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el tipo de identificacion del prestador en el AF
     * @param int $tipo_identificacion
     * @param int $posicion
     */
    private function tipoIdentificacion($tipo_identificacion, $posicion) {

        if ($this->validar_tip_iden($tipo_identificacion) === false) {

            $eaf = msg_ertiden($posicion, $tipo_identificacion);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el numero de identificacion en el AF
     * @param int $num_identificacion
     * @param int $posicion
     */
    private function numIdentificacion($num_identificacion, $posicion) {

        if (validar_entero($num_identificacion) === false || $num_identificacion === '') {

            $eaf = msg_ernuid($posicion, $num_identificacion);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_identificacion) >= 21) {

            $eaf = msg_cadena3('El número de identificación', $posicion, $num_identificacion, 20);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida el numero de factura en el AF
     * @param String $num_factura
     * @param int $posicion
     */
    private function numFactura($num_factura, $posicion) {

        if (cespeciales1($num_factura) === true || $num_factura === '') {

            $eaf = msg_cadena1('El número de factura', $posicion, $num_factura);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_factura) >= 21) {

            $eaf = msg_cadena3('El número de factura', $posicion, $num_factura, 20);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida la fecha de expedicion de una factura en el AF
     * @param date $fecha_expedicion
     * @param int $posicion
     */
    private function fechaExpfactura($fecha_expedicion, $posicion) {

        if (validar_fecha($fecha_expedicion) === false) {

            $eaf = msg_fec1('La fecha de expedición de la factura', $posicion, $fecha_expedicion);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_expedicion, date("j/n/Y")) === true) {

            $eaf = msg_fec2('La fecha de expedición de la factura', $posicion, $fecha_expedicion);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida la fecha de inicio de una factura en el AF
     * @param type $fecha_inicio
     * @param int $posicion
     */
    private function fechaInifactura($fecha_inicio, $posicion) {

        if (validar_fecha($fecha_inicio) === false) {

            $eaf = msg_fec1('La fecha de inicio de la factura', $posicion, $fecha_inicio);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_inicio, date("j/n/Y")) === true) {

            $eaf = msg_fec2('La fecha de inicio de la factura', $posicion, $fecha_inicio);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida la fecha final de una factura y la comprara contra la 
     * de inicio en el AF
     * @param date $fecha_final
     * @param String $posicion
     * @param date $fecha_inicio
     */
    private function fechaFinfactura($fecha_final, $posicion, $fecha_inicio) {

        if (validar_fecha($fecha_final) === false) {

            $eaf = msg_fec1('La fecha final de la factura', $posicion, $fecha_final);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_final, date("j/n/Y")) === true) {

            $eaf = msg_fec2('La fecha final de la factura', $posicion, $fecha_final);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } else if (comparar_fechas($fecha_inicio, $fecha_final) === true) {

            $eaf = msg_generico('La fecha de inicio de la factura', $posicion, $fecha_inicio, 'No debe ser mayor a la fecha final.');
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la entidad administrativa en el AF
     * @param String $cod_ent_admin
     * @param int $posicion
     */
    private function codigoEntadministrativa($cod_ent_admin, $posicion) {

        if (val_cod_ent_admin($cod_ent_admin) === false) {

            $eaf = msg_ercea($posicion, $cod_ent_admin);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida el nombre de la ent administradora en el AF
     * @param String $nom_ent_admin
     * @param int $posicion
     */
    private function nombreEntadministradora($nom_ent_admin, $posicion) {

        if (cespeciales2($nom_ent_admin) === true || espacio($nom_ent_admin) === false) {

            $eaf = msg_cadena2('El nombre de la entidad administradora', $posicion, $nom_ent_admin);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($nom_ent_admin) >= 31) {

            $eaf = msg_cadena3('El nombre de la entidad administradora', $posicion, $nom_ent_admin, 30);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el numero de contrato en el AF
     * @param String $numero_contrato
     * @param int $posicion
     */
    private function numeroContrato($numero_contrato, $posicion) {

        if (cespeciales($numero_contrato) === true) {

            $eaf = msg_cadena2('El número de contrato', $posicion, $numero_contrato);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($numero_contrato) >= 31) {

            $eaf = msg_cadena3('El número de contrato', $posicion, $numero_contrato, 30);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 12.- Metodo que valida el plan de beneficios en el AF
     * @param String $plan_beneficios
     * @param int $posicion
     */
    private function planBeneficios($plan_beneficios, $posicion) {

        if (cespeciales($plan_beneficios) === true) {

            $eaf = msg_cadena2('El plan de beneficios', $posicion, $plan_beneficios);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($plan_beneficios) >= 31) {

            $eaf = msg_cadena3('El plan de beneficios', $posicion, $plan_beneficios, 30);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 13.- Metodo que valida el numero de poliza en el AF
     * @param String $numero_poliza
     * @param int $posicion
     */
    private function numeroPoliza($numero_poliza, $posicion) {

        if (cespeciales($numero_poliza) === true) {

            $eaf = msg_cadena2('El número de la póliza', $posicion, $numero_poliza);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($numero_poliza) >= 11) {

            $eaf = msg_cadena3('El número de la póliza', $posicion, $numero_poliza, 10);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 14.- Metodo que valida el copago en el AF
     * @param float $valor_copago
     * @param int $posicion
     */
    private function copago($valor_copago, $posicion) {

        if (valor_dec($valor_copago) === false) {

            $eaf = msg_numero2('El valor del copago', $posicion, $valor_copago);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 15.- Metodo que valida las comisiones en el AF
     * @param float $valor_comision
     * @param int $posicion
     */
    private function comision($valor_comision, $posicion) {

        if (valor_dec($valor_comision) === false) {

            $eaf = msg_numero2('El valor de la comisión', $posicion, $valor_comision);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 16.- Metodo que valida el total de descuentos en el AF
     * @param float $val_tot_descuentos
     * @param int $posicion
     */
    private function totalDescuentos($val_tot_descuentos, $posicion) {

        if (valor_dec($val_tot_descuentos) === false) {

            $eaf = msg_numero2('El valor total de descuentos', $posicion, $val_tot_descuentos);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 17.- Metodo que valida el valor neto a pagar en el AF
     * @param float $val_total_pagar
     * @param int $posicion
     */
    private function valorNeto($val_total_pagar, $posicion) {

        if (valor_dec($val_total_pagar) === false) {

            $eaf = msg_numero2('El valor neto a pagar por la entidad', $posicion, $val_total_pagar);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * Metodo que valida si el prestador declarado es igual al encontrado
     * en el CT
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function validarCodprestador($cod_prestador, $posicion) {

        if ($cod_prestador !== $_SESSION ["cprestador"]) {

            $eaf = msg_errCp3($posicion, $cod_prestador);
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_cruzada++;
        }
    }
    
    /**
     * Metodo que valida si el Nit de prestador declarado es igual al encontrado
     * en la BD
     * @param int $nu_identificacion
     * @param int $posicion
     */
    private function validarNitprestador($nu_identificacion, $posicion) {

        if ($nu_identificacion != $_SESSION ["ni_prestador"]) {

            $eaf = '- El NIT de prestador de la línea ' . ($posicion + 1) . ' es incorrecto(a). '
                    . 'El valor ' . $nu_identificacion . ' no es igual al registrado en la entidad.';
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que busca si una factura se ha enviado con otra remision
     * @param int $cod_prestador
     * @param String $nu_factura
     * @param int $posicion
     */
    private function buscarFactura($cod_prestador, $nu_factura, $posicion) {

        if (($fila = Transaccion::getFactura($cod_prestador, $nu_factura))) {

            $eaf = '- La factura ' . $nu_factura . ' de la línea ' . ($posicion + 1) . ' se registró el '
                    . '' . $fila["F_FACTURA"] . ' con la remisión número ' . $fila["N_REMISION"] . '.';
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Lista las facturas dublicadas en el AF
     * @param array $x
     */
    private function mostrarFacDuplicada($x) {

        foreach ($x as $valor) {

            $eaf = '- La factura ' . $valor['n_factura'] . ' de la línea ' . $valor['linea'] . ' esta registrada dos o más veces en el archivo.';
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_cruzada++;
        }
    }
    
  /**
   * Metodo que valida si un numero de contrato esta registrado.
   * @param String $num_contrato
   * @param String $nit_prestador
   * @param String $mod_contrato
   */
    private function obtener_num_contrato($num_contrato, $nit_prestador, $mod_contrato) {

        if(Transaccion::getNumContrato($nit_prestador, $num_contrato, $mod_contrato) === false) {
            
            $eaf = '- El número de contrato ' . $num_contrato . ' no se encuentra habilitado para la fecha de prestación.';
            array_push($this->logeraf, $eaf);
            echo "<p>" . $eaf . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que valida el tipo de identificacion
     * @param String $identificacion
     * @return boolean
     */
    private function validar_tip_iden($identificacion) {

        $estado = false;

        switch ($identificacion) {
            case 'NI':
            case 'CC':
            case 'CE':
            case 'PA':
                $estado = true;
                break;

            default:
                $estado = false;
                break;
        }

        return $estado;
    }

    /**
     * Metodo que busca si una factura esta duplicada en el archivo AF
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

                $factura = array('linea' => $posicion, 'n_factura' => $valores);
                array_push($duplicado, $factura);
            } else {
                // si no existe lo añadimos al array
                $contar[$valores] = 1;
            }
        }
        return $duplicado;
    }
    

}
