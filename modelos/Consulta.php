<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////       MODELO CONSULTAS      //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////    DE CONSULTAS    ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Consulta extends Conexion {

    /**
     * Metodo que valida si una autorizacion esta registrada
     * @param String $no_autorizacion
     * @return boolean
     */
    public static function getAutorizacion($no_autorizacion) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NO_AUTORIZACION FROM AUTORIZACION WITH (NOLOCK) WHERE NO_AUTORIZACION = :n_autorizacion AND ESTADO IN ('AN','CO')";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_autorizacion", $no_autorizacion);
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
     * Metodo que valida si un procedimiento existe
     * @param String $cod_procedimiento
     * @return boolean
     */
    public static function getProcedimiento($cod_procedimiento) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT EDAD_INICIO, EDAD_FINAL, COD_SEXO, DUPLICADO_UPC FROM PROCEDIMIENTOS WITH (NOLOCK) WHERE CODIGO = :c_procedimiento AND EST_PROCEDIMIENTO = '0'";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":c_procedimiento", $cod_procedimiento);
        $resultado->execute();

        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return $fila;
        } else {

            return false;
        }
    }

    /**
     * Metodo que valida si un diagnostico existe
     * @param String $cod_diagnostico
     * @return boolean
     */
    public static function getDiagnostico($cod_diagnostico) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT EDA_MININA, EDA_MAXIMA, RANGO_SEXO FROM DIAGNOSTICOS WITH (NOLOCK) WHERE COD_DIAGNOSTICO = :c_diagnostico AND EST_DIAGNOSTICO = '0'";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":c_diagnostico", $cod_diagnostico);
        $resultado->execute();

        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return $fila;
        } else {

            return false;
        }
    }

}
