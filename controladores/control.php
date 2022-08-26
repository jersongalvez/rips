<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////      ARCHIVO DE CONTROL     //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////   ES LA ESTRUCTURA DE DATOS QUE CONTIENE Y VALIDA LOS ARCHIVOS   /////// 
/////////     CONTENIDOS EN EL FICHERO COMPRIMIDO, ESTOS SE DEBEN      /////////
/////////////////      DECLARAR EN EL ARCHIVO DE CONTROL      //////////////////
////////////////////////////////////////////////////////////////////////////////

//mensajes de error en cada validacion
require_once 'mesajes.php';
//Metodos de conexion a la base de datos
require_once '../../modelos/Control.php';
//zona horaria colombia
date_default_timezone_set("America/Bogota");

//Instancia de la clase control 



class Control_validador {

    //contador de errores encontrados en la validacion de estructura
    private $contador_val_Estructura = 0;
    //cuenta los errores encontrados en la validacion cruzada
    private $contador_val_cruzada = 0;
    //array que contiene los erres encontrados en la validacion
    private $logerr = array('----- Errores encontrados en el archivo de control: -----');

    /**
     * valida todo el archivo de control
     * @param String $ruta
     * @return int
     */
    public function val_control($ruta, $fichero) {

        //hacer que el navegador reconozca acentos y eñes
        $datos = array_map("utf8_encode", file($ruta));

        //Inicio validacion de estructura
        titulo_valEst();

        echo '<div class="columns wrap">';
        echo '<div class="column is-12 scroll bordeD">';

        //recorre el archivo CT txt
        foreach ($datos as $posicion => $linea) {
            $linea = trim($linea);
            $valor = explode(',', $linea);

            //validacion de la estructura de 14 campos del archivo CT
            if (count($valor) < 4 || count($valor) > 4) {

                $this->estructuraLinea($posicion);
            } else {

                //asignacion de los campos del txt a variables
                $cod_prestador = $valor[0];
                $fecha_remision = $valor[1];
                $cod_archivo = $valor[2];
                $total_registros = $valor[3];


                //1 validacion codigo del prestador
                $this->codigoPrestador($cod_prestador, $posicion);

                //2 validacion fecha remision
                $this->fechaRemision($fecha_remision, $posicion);

                //3 validacion codigo del archivo 
                $this->codigoArchivo($cod_archivo, $posicion);

                //4 validacion total de registros
                $this->totalRegistros($total_registros, $posicion);
            }
        }

        echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_Estructura . '</strong> errores en la estructura del archivo de control. </p>';
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

                $co_prestador = $cruce[0];
                $c_archivo = $cruce[2];
                $t_registros = $cruce[3];


                //valido que el codigo del prestador corresponda al encontrado
                //en la consulta sql
                $this->validarCodprestador($co_prestador, $posicion);

                //valido si el archivo declarado existe en el fichero comprimido
                $this->verificarArchivo($c_archivo, $fichero, $posicion, $t_registros);
            }
        }

        echo '</div>';
        echo '</div>';

        echo '<p> - Se encontraron <strong>' . $this->contador_val_cruzada . '</strong> errores en la validación cruzada del archivo de control. </p>';
        //Fin validacion cruzada


        $total_err = $this->contador_val_Estructura + $this->contador_val_cruzada;

        if ($total_err > 0) {
            echo msg_val($total_err, 'control');

            $_SESSION ["logErrores"] = array_merge($_SESSION ["logErrores"], $this->logerr);
        }

        return $total_err;
    }

///////////////////////////// METODOS PRIVADOS DE CLASE /////////////////////////

    /**
     * Metodo que valida la estructura de una linea antes de validarla en el CT
     * @param int $posicion
     */
    private function estructuraLinea($posicion) {

        $ect = msg_estructura($posicion, 4);
        array_push($this->logerr, $ect);
        echo "<p class='has-text-danger'>" . $ect . "</p>";
        $this->contador_val_Estructura++;
    }

    /**
     * 1.- Metodo que valida el tipo de dato y su longitud en el campo
     * codigo de prestador en el CT
     * @param int $cod_prestador
     * @param int $posicion
     */
    private function codigoPrestador($cod_prestador, $posicion) {

        if (validar_entero($cod_prestador) === false || $cod_prestador === '') {

            $ect = msg_errCp1($posicion, $cod_prestador);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_prestador) >= 13) {

            $ect = msg_errCp2($posicion, $cod_prestador);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 2.- Metodo que valida la fecha de una remision en el CT
     * @param date $fecha_remision
     * @param int $posicion
     */
    private function fechaRemision($fecha_remision, $posicion) {

        if (validar_fecha($fecha_remision) === false) {

            $ect = msg_fec1('La fecha de remisión', $posicion, $fecha_remision);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        } elseif (comparar_fechas($fecha_remision, date("j/n/Y")) === true) {

            $ect = msg_fec2('La fecha de remisión', $posicion, $fecha_remision);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 3.- Metodo que valida el cod de archivo de una remision en el CT
     * @param String $cod_archivo
     * @param int $posicion
     */
    private function codigoArchivo($cod_archivo, $posicion) {

        if (cespeciales1($cod_archivo) === true || $cod_archivo === '') {

            $ect = msg_cadena1('El código del archivo', $posicion, $cod_archivo);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($cod_archivo) >= 15) {

            $ect = msg_cadena3('El código del archivo', $posicion, $cod_archivo, 14);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * 4.- Metodo que valida el total de registros en el CT
     * @param int $total_registros
     * @param int $posicion
     */
    private function totalRegistros($total_registros, $posicion) {

        if (validar_entero($total_registros) === false || $total_registros === '') {

            $ect = msg_numero1('El total de registros', $posicion, $total_registros);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        } elseif (strlen($total_registros) >= 11) {

            $ect = msg_cadena3('El total de registros', $posicion, $total_registros, 10);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_Estructura++;
        }
    }

    /**
     * Metodo que valida si el prestador declarado es igual al encontrado
     * por la consulta sql
     * @param int $co_prestador
     * @param int $posicion
     */
    private function validarCodprestador($co_prestador, $posicion) {

        if ($co_prestador != $_SESSION ["cprestador"]) {

            $ect = msg_errCp3($posicion, $co_prestador);
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que valida que el archivo exista en el zip y que el # de registros
     * coincida con el declarado en el CT
     * @param String $c_archivo
     * @param String $fichero
     * @param int $posicion
     * @param int $t_registros
     */
    private function verificarArchivo($c_archivo, $fichero, $posicion, $t_registros) {

        if ($this->existeA($c_archivo, $fichero) === false) {

            $ect = '- El archivo declarado ' . $c_archivo . ' de la línea ' . ($posicion + 1) . ' no existe en el fichero comprimido.';
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        } elseif ($t_registros != count(file($fichero . '/' . $c_archivo . '.txt'))) {

            $ect = '- El total de registros de la línea ' . ($posicion + 1) . ' no corresponde con el encontrado en el archivo ' . $c_archivo . '.';
            array_push($this->logerr, $ect);
            echo "<p>" . $ect . "</p>";
            $this->contador_val_cruzada++;
        }
    }

    /**
     * Metodo que valida si un archivo existe en el fichero comprimido
     * @param String $nom_archivo
     * @param String $ruta
     * @return boolean
     */
    private function existeA($nom_archivo, $ruta) {

        if (file_exists($ruta . '/' . $nom_archivo . '.txt')) {
            return true;
        } else {

            return false;
        }
    }

//////////////////////// METODOS PUBLICOS DE CLASE /////////////////////////////

    /**
     * Metodo que busca un prestador y muestra sus datos
     * @param array $archivoCt
     * @param array $archivoAf
     * @return boolean
     */
    public function buscar_prestador($archivoCt, $archivoAf) {

        $datosCt = file($archivoCt);
        $datosAf = file($archivoAf);

        //Datos del archivo de control, se extrae el codigo de prestador y la fecha del rips
        $info_prestadorCt = explode(',', $datosCt[0]);
        $cod_prestador    = utf8_encode($info_prestadorCt[0]);
        $fecha_rips       = utf8_encode($info_prestadorCt[1]);
        
        //Datos del archivo de transacciones, se extrae el Nit
        $info_prestadorAf = explode(',', $datosAf[0]);
        $nit_prestador    = utf8_encode($info_prestadorAf[3]);

        if (($datos_prestador = Control::getPrestador($cod_prestador, $nit_prestador))) {

            $_SESSION ["cprestador"] = $cod_prestador;
            $prestador[0] = $datos_prestador['COD_PRESTADOR'];
            $prestador[1] = $datos_prestador['NIT_PRESTADOR'];
            $prestador[2] = $datos_prestador['TIP_IDENTIFICACION'];
            $prestador[3] = $datos_prestador['NOM_PRESTADOR'];
            $prestador[4] = $fecha_rips;
            $_SESSION ["ni_prestador"] = $nit_prestador;

            return $prestador;
        } else {

            //echo 'No se encontro el prestador';
            return false;
        }
    }

    /**
     * Metodo que valida en primera instancia si el usuario local pertenese a pijaos salud esto 
     * para validar remisiones de cualquier prestador, si es falso, valida que el 
     * codigo de prestador sea el mismo que se relaciono en el CT
     * @return boolean
     */
    public function validar_codprestador() {

        $estado = false;

        //Valido contra el nit de pijaos salud
        if ($_SESSION['NIT_PRESTADOR'] === '809008362') {

            $estado = true;
        } elseif ($_SESSION['NIT_PRESTADOR'] == $_SESSION ["ni_prestador"]) {

            $estado = true;
        }
        
        //echo $_SESSION['NIT_PRESTADOR'];

        return $estado;
    }

    /**
     * Metodo que busca si una remision esta registrada
     * @param int $cod_prestador
     * @param int $n_remision
     * @return boolean
     */
    public function buscar_remision($cod_prestador, $n_remision) {

        if (($resultado = Control::getNumRemision($n_remision, $cod_prestador))) {

            //echo 'Se encontro la remision';
            return true;
        } else {

            //echo 'No se encontro';
            return false;
        }
    }

}
