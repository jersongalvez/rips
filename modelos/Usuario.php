<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////       MODELO USUARIOS    //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////    DE USUARIOS     ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Usuario extends Conexion {

    /**
     * Metodo que busca un usuario en AFILIADOSSUB
     * @param int $num_documento
     * @param String $tip_documento
     * @return boolean
     */
    public static function getUsuario($num_documento) {

        $conn = Conexion::conexionPDO();

        $estado = false;

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_DOCUMENTO_BEN FROM AFILIADOSSUB WITH (NOLOCK) WHERE NUM_DOCUMENTO_BEN = :n_documento";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_documento", $num_documento);
        $resultado->execute();
        
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            $estado = true;
        } else {

            if (self::getHistdocumento($num_documento)) {

                $estado = true;
            } elseif (self::getHijoDe($num_documento)) {

                $estado = true;
            }

            return $estado;
        }
    }

    /**
     * Metodo que busca los cambios de un documento en HIST_CAMBIO_DOCUMENTO
     * @param int $num_documento
     * @return boolean
     */
    private static function getHistdocumento($num_documento) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT HCD_NUM_DOCUMENTO_AN FROM HIST_CAMBIO_DOCUMENTO WITH (NOLOCK) WHERE HCD_NUM_DOCUMENTO_AN = :n_documento";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_documento", $num_documento);
        $resultado->execute();
        
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Metodo que quita los dos ultimos caracteres de un documento y lo busca en AFILIADOSSUB
     * para buscar los "Hijos De"
     * @param type $num_documento
     * @return boolean
     */
    private static function getHijoDe($num_documento) {

        $conn = Conexion::conexionPDO();

        $nu_documento = substr($num_documento, 0, -2);

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_DOCUMENTO_BEN FROM AFILIADOSSUB WITH (NOLOCK) WHERE NUM_DOCUMENTO_BEN LIKE :n_documento";


        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_documento", $nu_documento);
        $resultado->execute();
        
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Metodo que busca el codigo de departamento y municipio en la tabla
     * CIUDADES
     * @param int $cod_departamento
     * @param int $cod_municipio
     * @return boolean
     */
    public static function getCiudad($cod_departamento, $cod_municipio) {

        $conn = Conexion::conexionPDO();

        $query = "SELECT NOM_CIUDAD FROM CIUDADES WITH (NOLOCK) WHERE COD_DEPARTAMENTO = :c_departamento AND COD_CIUDAD = :c_municipio";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":c_departamento", $cod_departamento);
        $resultado->bindParam(":c_municipio", $cod_municipio);
        $resultado->execute();
        
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        } else {

            return false;
        }
    }

}
