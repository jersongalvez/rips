<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////         MODELO CAPITA       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
///////////////////////////     DE PRE FACTURAS     ////////////////////////////
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Capita extends Conexion {

    /**
     * Metodo que varifica si un perido ya esta registrado.
     * @param int $periodo
     * @return boolean
     */
    public static function getPeriodo($periodo) {

        $conn = Conexion::conexionPDO();


        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT CONVERT(DATE, FEC_CARGUE) AS FC FROM RECEPCIONCAPITA WITH (NOLOCK) WHERE PERIODO = :n_periodo ";


        $resultado = $conn->prepare($query);

        $resultado->bindParam(":n_periodo", $periodo);

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return $fila;
        }

        return false;
    }

    /**
     * Metodo que valida si un prestador esta registrado
     * @param int $nit_prestador
     * @return boolean
     */
    public static function getNitPrestador($nit_prestador) {

        $conn = Conexion::conexionPDO();


        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NIT_PRESTADOR FROM PRESTADORES WITH (NOLOCK) WHERE NIT_PRESTADOR = :ni_prestador ";


        $resultado = $conn->prepare($query);

        $resultado->bindParam(":ni_prestador", $nit_prestador);

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que busca si un contrato esta registrado para un prestador en especifico.
     * @param String $nit_prestador
     * @param String $num_contrato
     * @param String $periodo
     * @return boolean
     */
    public static function getNumContrato($nit_prestador, $num_contrato, $periodo) {

        $fecha     = explode('/', $periodo);
        //Calculo el ultimo dia del mes a partir del periodo 
        $dia_final = cal_days_in_month(CAL_GREGORIAN, intval($fecha[0]), intval($fecha[1]));
        $f_inicial = '01/' . $periodo;
        $f_final   = $dia_final . '/' . $periodo;

        $conn = Conexion::conexionPDO();


        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_CONTRATO FROM CONTRATOSIPS WITH (NOLOCK) "
                . "WHERE TIP_CONTRATACION = 'C' "
                . "AND TERMINADO = '0' "
                . "AND LIQUIDADO = '0' "
                . "AND SUSPENDIDO = '0' "
                . "AND NIT_PRESTADOR = :ni_prestador "
                . "AND NUM_CONTRATO = :nu_contrato "
                . "AND :per_inicial >= FEC_INICIOCONTRAIPS "
                . "AND :per_final   <= FEC_FINALCONTRAIPS ";


        $resultado = $conn->prepare($query);

        $resultado->bindParam(":ni_prestador", $nit_prestador);
        $resultado->bindParam(":nu_contrato", $num_contrato);
        $resultado->bindParam(":per_inicial", $f_inicial);
        $resultado->bindParam(":per_final", $f_final);

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        }

        return false;
    }

}
