<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     MODELO TRANSACCIONES    //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////  DE TRANSACCIONES  ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Transaccion extends Conexion {

    /**
     * Metodo que busca una factura por prestador
     * @param int $cod_prestador
     * @param String $num_factura
     * @return \AF|boolean
     */
    public static function getFactura($cod_prestador, $num_factura) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT AFT.NUM_REMISION AS 'N_REMISION', CONVERT(DATE, AFT.FECHA_FACTURA) AS 'F_FACTURA' "
                . "FROM TRANSACCION_SERV AFT WITH (NOLOCK) "
                . "WHERE AFT.NUM_DOC_PRES = :c_prestador AND AFT.NUM_FACTURA = :n_factura AND COD_RADICACION IS NOT NULL";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":c_prestador", $cod_prestador);
        $resultado->bindParam(":n_factura", $num_factura);

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
     * Metodo que busca y lista los datos de una remision
     * @param int $num_remision
     * @param int $cod_prestador
     * @return boolean
     */
    public static function buscar_remision($num_remision, $cod_prestador) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_PRESTADOR, NUM_ENTIDAD, NUM_REMISION, MOD_CONTRATO, CONVERT(DATE, FEC_REMISION) AS F_REMISION, NOM_PRESTADOR, CONVERT(DATETIME2(0), FEC_CARGUE) AS F_CARGUE "
                . "FROM RECEPCIONRIPS WITH (NOLOCK) WHERE COD_PRESTADOR = :c_prestador AND NUM_REMISION = :remision;";

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
     * Metodo que busca si un contrato esta registrado para un prestador en especifico.
     * @param String $nit_prestador
     * @param String $num_contrato
     * @return boolean
     */
    public static function getNumContrato($nit_prestador, $num_contrato, $mod_contrato) {

        $conn = Conexion::conexionPDO();


        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NUM_CONTRATO FROM CONTRATOSIPS WITH (NOLOCK) WHERE NIT_PRESTADOR LIKE :ni_prestador "
                . "AND NUM_CONTRATO = :nu_contrato "
                . "AND TIP_CONTRATACION = :mo_contrato ";


        $resultado = $conn->prepare($query);

        $resultado->bindValue(":ni_prestador", '%' . $nit_prestador . '%');
        $resultado->bindParam(":nu_contrato", $num_contrato);
        $resultado->bindParam(":mo_contrato", $mod_contrato);

        
        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        }

        return false;
    }

}
