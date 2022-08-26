<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////        MODELO REPORTE       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE GENERA LOS REPORTES /////////
//////////////////////////////  DE LAS REMISIONES  /////////////////////////////
////////////////////////////////////////////////////////////////////////////////

require_once '../config/Conexion.php';

class Reporte extends Conexion {

    /**
     * Metodo que busca y lista los datos de una remision
     * @param int $num_remision
     * @param int $cod_prestador
     * @return boolean
     */
    public static function buscar_remision($num_remision, $cod_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_PRESTADOR, NUM_ENTIDAD, NUM_REMISION, MOD_CONTRATO, CONVERT(DATE, FEC_REMISION) AS F_REMISION, NOM_PRESTADOR, "
                . "CONVERT(DATETIME2(0), FEC_CARGUE) AS F_CARGUE FROM RECEPCIONRIPS WITH (NOLOCK) "
                . "WHERE COD_PRESTADOR = :c_prestador AND NUM_REMISION = :remision";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":remision", $num_remision);
        $resultado->bindParam(":c_prestador", $cod_prestador);

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
     * Metodo que busca los archivos relacionados en el CT
     * @param int $num_remision
     * @param int $cod_prestador
     * @return boolean
     */
    public static function info_control($num_remision, $cod_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT SUBSTRING(COD_ARCHIVO, 1, 2) AS ID_ARCHIVO, COD_ARCHIVO, CONVERT(DATE, FECHA_REMISION) AS F_REMISION, TOTAL_ARCHIVOS "
                . "FROM ARC_CONTROL WITH (NOLOCK) "
                . "WHERE COD_PRESTADOR = :c_prestador AND NUM_REMISION = :remision ORDER BY ID_ARCHIVO ASC";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":remision", $num_remision);
        $resultado->bindParam(":c_prestador", $cod_prestador);

        $resultado->execute();
        $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);


        if ($filas != false) {

            return $filas;
        } else {

            return false;
        }
    }

    /**
     * Metodo que busca y lista las facturas de una remision
     * @param int $num_remision
     * @param int $cod_prestador
     * @return boolean
     */
    public static function buscar_transaccion($num_remision, $cod_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_FACTURA, CONVERT(DATE, FECHA_FACTURA) AS FECHA, VAL_COPAGO, VAL_COMISION, VAL_DESCUENTO, VAL_PAGO_ENTIDAD "
                . "FROM TRANSACCION_SERV WITH (NOLOCK) "
                . "WHERE COD_PRESTADOR = :c_prestador AND NUM_REMISION = :remision ORDER BY NUM_FACTURA ASC";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":remision", $num_remision);
        $resultado->bindParam(":c_prestador", $cod_prestador);

        $resultado->execute();
        $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($filas != false) {

            return $filas;
        } else {

            return false;
        }
    }

    /**
     * Metodo que busca una remison del archivo AF y retorna la suma de los valores del copago, descuentos, comisiones y total a pagar segun ips
     * @param int $num_remision
     * @param int $cod_prestador
     * @return boolean
     */
    public static function valores_neto($num_remision, $cod_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT SUM(CAST(VAL_COPAGO AS FLOAT)) AS COPAGO,  SUM(CAST(VAL_COMISION AS FLOAT)) AS COMISION, SUM(CAST(VAL_DESCUENTO AS FLOAT)) AS DESCUENTO, "
                . "SUM(CAST(VAL_PAGO_ENTIDAD AS FLOAT)) AS TOTAL_NETO "
                . "FROM TRANSACCION_SERV WITH (NOLOCK) WHERE COD_PRESTADOR = :c_prestador AND NUM_REMISION = :remision";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":remision", $num_remision);
        $resultado->bindParam(":c_prestador", $cod_prestador);

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return $fila;
        } else {

            return false;
        }
    }

    ############################################################################
    ####################    CONSULTAS PREFACTURA CAPITA     ####################
    ############################################################################

    /**
     * 
     * @param String $nit_prestador
     * @param String $periodo
     * @return boolean
     */
    public static function buscar_contratoCapitacion($nit_prestador, $periodo) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_CONTRATO, NUM_AFILIADOS, VR_MES_ANTICIPADO, RECONOCIMIENTOS, RESTITUCIONES, VR_FINAL_CAPITA "
                . "FROM ARCH_PREFACTURA WHERE NIT_PRESTADOR = :ni_prestador AND PERIODO = :n_periodo ";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":ni_prestador", $nit_prestador);
        $resultado->bindParam(":n_periodo", $periodo);


        $resultado->execute();
        $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($filas != false) {

            return $filas;
        } else {

            return false;
        }
    }

    /**
     * Metodo que obtiene el nombre de un prestador
     * @param String $nit_prestador
     * @return boolean
     */
    public static function getNomPrestador($nit_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NOM_PRESTADOR FROM PRESTADORES WHERE NIT_PRESTADOR = :ni_prestador ";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":ni_prestador", $nit_prestador);

        $resultado->execute();
        $filas = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($filas != false) {

            return $filas;
        } else {

            return false;
        }
    }

}
