<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     ARCHIVO DE CONEXION     //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////// ARCHIVO DE CONEXION CON LA BASE DE DATOS GEMAEPS_VPS    ////////////
////////////////////////////////////////////////////////////////////////////////

require 'global.php';

class Conexion {

    /**
     * Metodo que abre la conexion con la base de datos
     * @return \PDO
     */
    public function conexionPDO() {

        $servidor   = DB_HOST;
        $usuario    = DB_USERNAME;
        $contrasena = DB_PASSWORD;
        $database   = DB_NAME;
        $pdo        = null;

        try {

            $pdo = new PDO("sqlsrv:Server=$servidor; Database=$database", $usuario, $contrasena);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo 'Conectado a GEMA <br>';
            return $pdo;
        } catch (Exception $e) {

            echo 'La conexion fallo <br>';
            die($e->getMessage());
        }
    }

    /**
     * Metodo que cierra la conexion
     */
    public function cerrar_conexion(&$pdo) {

        $pdo = null;

        //echo 'Conexion cerrada <br>';
    }

    /**
     * Metodo que limpia caracteres especiales antes de consultar
     * @param type $str
     * @return type
     */
    public function limpiarCadena($str) {

        $str = trim($str);
        $str = stripcslashes($str);
        $str = htmlspecialchars($str);

        return $str;
    }

}

//Prueba de conexion a la base de datos
//$a = Conexion::conexionPDO();
//Conexion::cerrar_conexion($a);
