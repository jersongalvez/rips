<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////         MODELO RIPS         //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE BUSCAN REMISIONES  //////////
////////////////////////////////////////////////////////////////////////////////

require_once '../config/Conexion.php';

class Rips extends Conexion {

    public function __construct() {
        //se deja vacio para implementar instancias hacia esta clase
        //sin enviar parametro
    }

    /**
     * Metodo que busca las remisiones asociadas a un prestador
     * @param type $nit_prestador
     * @param type $fecha_inicial
     * @param type $fecha_final
     * @param type $modalidad
     * @return boolean
     */
    public static function buscar_remisiones($nit_prestador, $fecha_inicial, $fecha_final, $modalidad) {

        $conn = Conexion::conexionPDO();

        $cadena = ($modalidad == 'T') ? "" : "AND MOD_CONTRATO = '$modalidad'";

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_PRESTADOR, NUM_REMISION, CONVERT(DATE,FEC_REMISION) AS FEC_REMISION, CONVERT(DATETIME2(0),FEC_CARGUE) AS FEC_CARGUE, MOD_CONTRATO, "
                . "COD_USUARIO FROM RECEPCIONRIPS WITH (NOLOCK) "
                . "WHERE NUM_ENTIDAD = :ni_prestador AND "
                . "CAST(FEC_CARGUE AS DATE) >= CAST(:f_inicio AS DATE) AND CAST(FEC_CARGUE AS DATE) <= CAST(:f_fin AS DATE) " . $cadena . "ORDER BY FEC_REMISION DESC";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":ni_prestador", $nit_prestador);
        $resultado->bindParam(":f_inicio", $fecha_inicial);
        $resultado->bindParam(":f_fin", $fecha_final);


        $resultado->execute();
        $filas = $resultado->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($filas != false) {

            return $filas;
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
                . "SELECT PERIODO, NIT_PRESTADOR, COUNT(NUM_CONTRATO) AS CANT_CONTRATOS, SUM(CONVERT(INT, VR_FINAL_CAPITA)) AS VF_CAP "
                . "FROM ARCH_PREFACTURA WHERE NIT_PRESTADOR = :ni_prestador AND PERIODO = :n_periodo GROUP BY PERIODO, NIT_PRESTADOR ORDER BY PERIODO DESC";

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

}
