<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////        MODELO CONTROL       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////     DE CONTROL     ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Control extends Conexion {

    /**
     * Metodo que varifica si existe una remision
     * @param int $num_remision
     * @return boolean
     */
    public static function getNumRemision($num_remision, $nit_prestador) {

        $conn = Conexion::conexionPDO();


        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT RR.NUM_REMISION, RR.COD_PRESTADOR FROM RECEPCIONRIPS RR  WITH (NOLOCK) "
                . "INNER JOIN PRESTADORES PR ON RR.NUM_ENTIDAD = PR.NIT_PRESTADOR "
                . "WHERE RR.NUM_REMISION = :remision AND PR.NIT_PRESTADOR LIKE :ni_prestador ";


        $resultado = $conn->prepare($query);

        $resultado->bindParam(":remision", $num_remision);
        $resultado->bindValue(":ni_prestador", '%' . $nit_prestador . '%');

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($fila != false) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que busca si un prestador esta registrado
     * @param int $cod_prestador
     * @param int $nit_prestador
     * @return type
     */
    public static function getPrestador($cod_prestador, $nit_prestador) {

        $conn = Conexion::conexionPDO();


        //Si el cod_prestador es igual al Nit, hago la consulta agregando una N y hago el paso por valor a la consulta
        if ($cod_prestador === $nit_prestador) {

            $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                    . "SELECT TOP 1 SUBSTRING(CAI.COD_HABILITACION, CHARINDEX('N', CAI.COD_HABILITACION) + 1, LEN(CAI.COD_HABILITACION) - CHARINDEX('N', CAI.COD_HABILITACION) -2) AS COD_PRESTADOR, "
                    . "PRE.NIT_PRESTADOR, PRE.TIP_IDENTIFICACION, PRE.NOM_PRESTADOR "
                    . "FROM PRESTADORES PRE WITH (NOLOCK) "
                    . "INNER JOIN CENTROATENCIPS CAI ON PRE.NIT_PRESTADOR = CAI.NIT_PRESTADOR "
                    . "WHERE PRE.NIT_PRESTADOR LIKE :ni_prestador AND CAI.COD_HABILITACION LIKE :c_prestador ";

            $resultado = $conn->prepare($query);

            $resultado->bindValue(":ni_prestador", '%' . $nit_prestador . '%');
            $resultado->bindValue(":c_prestador", 'N' . $cod_prestador . '%');
        } else {

            $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                    . "SELECT CAI.COD_HABILITACION AS COD_PRESTADOR, PRE.NIT_PRESTADOR, PRE.TIP_IDENTIFICACION, PRE.NOM_PRESTADOR FROM PRESTADORES PRE WITH (NOLOCK) "
                    . "INNER JOIN CENTROATENCIPS CAI ON PRE.NIT_PRESTADOR = CAI.NIT_PRESTADOR "
                    . "WHERE PRE.NIT_PRESTADOR LIKE :ni_prestador AND CAI.COD_HABILITACION = :c_prestador ";

            $resultado = $conn->prepare($query);

            $resultado->bindParam(":c_prestador", $cod_prestador);
            $resultado->bindValue(":ni_prestador", '%' . $nit_prestador . '%');
        }


        $resultado->execute();

        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $fila;
    }

}
