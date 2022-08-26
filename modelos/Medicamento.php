<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////       MODELO MEDICAMEENTOS  //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////  DE MEDICAMENTOS   ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../../config/Conexion.php';

class Medicamento extends Conexion {

    /**
     * Metodo que valida si un medicamento exsite
     * @param String $cod_medicamento
     * @return boolean
     */
    public static function getMedicamento($cod_medicamento) {
        
        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_MEDICAMENTO FROM MEDICAMENTOS WITH (NOLOCK) WHERE COD_MEDICAMENTO LIKE :c_medicamento AND ESTADO = '1'";
        
        $resultado = $conn->prepare($query);
        $resultado->bindParam(":c_medicamento", $cod_medicamento);
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
