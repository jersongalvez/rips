<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     ARCHIVO DE USUARIOS     //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///   ES LA ESTRUCTURA DE DATOS QUE CONTIENE LOS REGISTROS DE LOS USUARIOS   ///
/////    ATENDIDOS EN EL PERÍODO, CORRESPONDIENTES A SU IDENTIFICACIÓN,    /////
//////////        ARACTERÍSTICAS BÁSICAS, LUGAR DE RESIDENCIA.        //////////
////////////////////////////////////////////////////////////////////////////////


//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require '../../modelos/Usuario.php';

class Usuario_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerus = array('', '----- Errores encontrados en el archivo de usuarios: -----');
    //array que guarda los numeros de identificacion para luego cruzarlos
    //con los demas archivos 
    private $Nidentificacion = array();

    /**
     * valida todo el archivo de usuarios
     * @param String $ruta
     * @return int
     */
    function val_usuarios($ruta) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));


        //Inicio validacion de estructura
        titulo_valEst();


        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                //recorre el archivo US txt
                foreach ($datos as $posicion => $linea) {
                    $linea = trim($linea);
                    $valor = explode(',', $linea);

                    //validacion de la estructura de 14 campos del archivo AF
                    if (count($valor) < 14 || count($valor) > 14) {

                        $this->estructuraLinea($posicion);
                    } else {

                        //asignacion de los campos del txt a variables
                        $tip_doc          = $valor[0];
                        $num_documento    = $valor[1];
                        $cod_ent_admin    = $valor[2];
                        $tip_usuario      = $valor[3];
                        $pri_apellido     = $valor[4];
                        $seg_apellido     = $valor[5];
                        $pri_nombre       = $valor[6];
                        $seg_nombre       = $valor[7];
                        $edad             = $valor[8];
                        $uni_med_edad     = $valor[9];
                        $sexo             = $valor[10];
                        $cod_departamento = $valor[11];
                        $cod_municipio    = $valor[12];
                        $zona_residencia  = $valor[13];


                        //1 validacion tipo identificacion
                        $this->tipoIdentificacion($tip_doc, $posicion);

                        //2 validacion numero documento
                        $this->numeroIdentificacion($num_documento, $posicion);

                        //Asigno a un arreglo asociativo todas los numeros y tipos de documentos
                        $datosUsu = array('n_documento' => $num_documento, 't_documento' => $tip_doc, 'edad' => $edad, 'sexo' => $sexo, 'rango_medida_ed' => $uni_med_edad);
                        array_push($this->Nidentificacion, $datosUsu);

                        //3 validacion codigo entidad administradora, permite los valores EPSI06 - EPSIC6
                        $this->codigoEntadministradora($cod_ent_admin, $posicion);

                        //4 validacion tipo usuario
                        $this->tipoUsuario($tip_usuario, $posicion);

                        //5 Validacion primer apellido
                        $this->primerApellido($pri_apellido, $posicion);

                        //6 Validacion segundo apellido
                        $this->segundoApellido($seg_apellido, $posicion);

                        //7 Validacion primer nombre
                        $this->primerNombre($pri_nombre, $posicion);

                        //8 Validacion segundo nombre
                        $this->segundoNombre($seg_nombre, $posicion);

                        //9 validacion edad
                        $this->edad($edad, $posicion);

                        //10 validacion unidad de medida de la edad
                        $this->unidadMededad($uni_med_edad, $posicion, $edad);

                        //11 validacion del sexo permitido M - F
                        $this->sexo($sexo, $posicion);

                        //12 validacion codigo departamento
                        $this->codigoDepartamento($cod_departamento, $posicion);

                        //13 validacion codigo municipio
                        $this->codigoMunicipio($cod_municipio, $posicion);

                        //14 validacion de la zona de residencia
                        $this->zonaResidencia($zona_residencia, $posicion);
                    }
                }

            echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de usuarios. </p>';
        //Fin validacion estructura
        
        
        //Inicio validacion cruzada
        titulo_valCru();

        echo '<div class="columns wrap">';
            echo '<div class="column is-12 scroll bordeD">';

                if ($this->contador_val_Estructura > 0) {
                    echo ' - No se puede continuar con la validación cruzada del archivo de usuarios. Corrija los errores e intente de nuevo. <br>';
                } else {

                    //recorre el archivo US txt cruzandolo contra la tabla AFILIADOSSUB - CIUDADES
                    foreach ($datos as $posicion => $linea) {
                        $linea = trim($linea);
                        $cruce = explode(',', $linea);


                        $nu_documento   = $cruce[1];
                        $c_departamento = $cruce[11];
                        $c_municipio    = $cruce[12];

                        //valido si el usuario esta registrado en el sistema
                        $this->buscarUsuario($nu_documento, $posicion);

                        //valido el codigo de departamento y municipio existen
                        $this->buscarCiudad($c_departamento, $c_municipio, $posicion);
                    }
                }

             echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de usuarios. </p>';
        //Fin validacion cruzada

        $_SESSION["identificacion"] = $this->Nidentificacion;
    
        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {

            echo msg_val($total_err, 'usuarios');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerus);
        }


        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el US
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $eus = msg_estructura($posicion, 14);
        array_push($this->logerus, $eus);
        echo "<p class='has-text-danger'>" . $eus . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el tipo de documento en el US
     * @param String $tip_doc
     * @param int $posicion
     */
    private function tipoIdentificacion($tip_doc, $posicion) {

        if (t_documento($tip_doc) === false) {

            $eus = msg_ertiden($posicion, $tip_doc);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida el numero de documento en el US
     * @param int $num_documento
     * @param int $posicion
     */
    private function numeroIdentificacion($num_documento, $posicion) {

        if (validar_entero($num_documento) === false || $num_documento === '') {

            $eus = msg_ernuid($posicion, $num_documento);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($num_documento) >= 21) {

            $eus = msg_cadena3('El número de identificación', $posicion, $num_documento, 20);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el codigo de la entidad administradora en el US
     * @param string $cod_ent_admin
     * @param int $posicion
     */
    private function codigoEntadministradora($cod_ent_admin, $posicion) {

        if (val_cod_ent_admin($cod_ent_admin) === false) {

            $eus = msg_ercea($posicion, $cod_ent_admin);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el tipo de usuario en el US
     * @param int $tip_usuario
     * @param int $posicion
     */
    private function tipoUsuario($tip_usuario, $posicion) {

        if ($tip_usuario === '1' || $tip_usuario === '2') {
            //mensaje de error
        } else {

            $eus = msg_cadena4('El tipo de usuario', $posicion, $tip_usuario) . ' Debe tener como valor 1 = Contributivo ó 2 = Subsidiado';
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 5.- Metodo que valida el primer apellido del usuario en el US
     * @param String $pri_apellido
     * @param int $posicion
     */
    private function primerApellido($pri_apellido, $posicion) {

        if (cespeciales($pri_apellido) === true || espacio($pri_apellido) === false) {

            $eus = msg_cadena2('El primer apellido', $posicion, utf8_encode($pri_apellido));
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($pri_apellido) >= 31) {

            $eus = msg_cadena3('El primer apellido', $posicion, $pri_apellido, 30);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 6.- Metodo que valida el segundo apellido del usuario en el US
     * @param String $seg_apellido
     * @param int $posicion
     */
    private function segundoApellido($seg_apellido, $posicion) {

        if (cespeciales($seg_apellido) === true) {

            $eus = msg_cadena2('El segundo apellido', $posicion, utf8_encode($seg_apellido));
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($seg_apellido) >= 31) {

            $eus = msg_cadena3('El segundo apellido', $posicion, $seg_apellido, 30);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 7.- Metodo que valida el primer nombre del usuario en el US
     * @param string $pri_nombre
     * @param int $posicion
     */
    private function primerNombre($pri_nombre, $posicion) {

        if (cespeciales($pri_nombre) === true || espacio($pri_nombre) === false) {

            $eus = msg_cadena2('El primer nombre', $posicion, utf8_encode($pri_nombre));
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($pri_nombre) >= 21) {

            $eus = msg_cadena3('El primer nombre', $posicion, $pri_nombre, 20);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 8.- Metodo que valida el segundo nombre del usuario en el US
     * @param String $seg_nombre
     * @param int $posicion
     */
    private function segundoNombre($seg_nombre, $posicion) {

        if (cespeciales($seg_nombre) === true) {

            $eus = msg_cadena2('El segundo nombre', $posicion, utf8_encode($seg_nombre));
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($seg_nombre) >= 21) {

            $eus = msg_cadena3('El segundo nombre', $posicion, $seg_nombre, 20);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 9.- Metodo que valida la edad del usuario en el US
     * @param int $edad
     * @param int $posicion
     */
    private function edad($edad, $posicion) {

        if (validar_entero($edad) === false || $edad === '') {

            $eus = msg_numero1('La edad', $posicion, $edad);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 10.- Metodo que valida la unidad de medida de la edad. Adicional verifica
     * que la unidad corresponda con la edad segun la norma 
     * @param int $uni_med_edad
     * @param int $posicion
     * @param int $edad
     */
    private function unidadMededad($uni_med_edad, $posicion, $edad) {

        if (validar_entero($uni_med_edad) === false || $uni_med_edad === '') {

            $eus = msg_numero1('La unidad de medida de la edad', $posicion, $uni_med_edad);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif ($this->uni_med_edad($uni_med_edad, $edad) === false || strlen($uni_med_edad) >= 2) {

            $eus = msg_generico('El rango de unidad de medida de la edad', $posicion, $uni_med_edad, 'La edad y/o unidad de medida no son correctos.');
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 11.- Metodo que valida el sexo de un usuario en el US
     * @param String $sexo
     * @param int $posicion
     */
    private function sexo($sexo, $posicion) {

        if (sexo($sexo) === false) {

            $eus = msg_generico('El sexo', $posicion, $sexo, 'Debe tener como valor M o F.');
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 12.- Metodo que valida el codigo del departamento en el US
     * @param int $cod_departamento
     * @param int $posicion
     */
    private function codigoDepartamento($cod_departamento, $posicion) {

        if (validar_entero($cod_departamento) === false || $cod_departamento === '') {

            $eus = msg_numero1('El código de departamento', $posicion, $cod_departamento);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_departamento) > 2) {

            $eus = msg_cadena3('El código de departamento', $posicion, $cod_departamento, 2);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 13.- Metodo que valida el codigo del municipio en el US
     * @param int $cod_municipio
     * @param int $posicion
     */
    private function codigoMunicipio($cod_municipio, $posicion) {

        if (validar_entero($cod_municipio) === false || $cod_municipio === '') {

            $eus = msg_numero1('El código de municipio', $posicion, $cod_municipio);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_municipio) >= 5) {

            $eus = msg_cadena3('El código de municipio', $posicion, $cod_municipio, 4);
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 14.- Metodo que valida la zona de residencia del usuario en el US
     * @param String $zona_residencia
     * @param int $posicion
     */
    private function zonaResidencia($zona_residencia, $posicion) {

        if ($zona_residencia === 'U' || $zona_residencia === 'R') {
            //validacion zona residencia
        } else {

            $eus = msg_generico('La zona de residencia', $posicion, $zona_residencia, 'Debe tener como valor U o R.');
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * Metodo que valida si el usuario esta registrado en el sistema
     * @param int $nu_documento
     * @param String $ti_documento
     * @param int $posicion
     */
    private function buscarUsuario($nu_documento, $posicion) {

        if (Usuario::getUsuario($nu_documento) === false) {

            $eus = '- El número de documento '. $nu_documento . ' de la '
                    . 'línea ' . ($posicion + 1) . ' no esta registrado en el sistema.';
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que busca la ciudad y departamento en el US
     * @param int $c_departamento
     * @param int $c_municipio
     * @param int $posicion
     */
    private function buscarCiudad($c_departamento, $c_municipio, $posicion) {


        if (Usuario::getCiudad($c_departamento, $c_municipio) === false) {

            $eus = '- El código de departamento ' . $c_departamento . ' o de municipio ' . $c_municipio . ' línea ' . ($posicion + 1) . ' no estan registrados en el sistema.';
            array_push($this->logerus, $eus);
            echo "<p>" . $eus . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metododo que valida la unidad de medida de la edad
     * @param int $unidad
     * @param int $edad
     * @return boolean
     */
    private function uni_med_edad($unidad, $edad) {

        $estado = false;
        switch ($unidad) {
            case 1:
                if ($edad >= 1 && $edad <= 120) {
                    $estado = true;
                } else {
                    $estado = false;
                }
                break;

            case 2:
                if ($edad >= 1 && $edad <= 11) {
                    $estado = true;
                } else {
                    $estado = false;
                }
                break;

            case 3:
                if ($edad >= 1 && $edad <= 29) {
                    $estado = true;
                } else {
                    $estado = false;
                }
                break;

            default:
                $estado = false;
                break;
        }

        return $estado;
    }

}
