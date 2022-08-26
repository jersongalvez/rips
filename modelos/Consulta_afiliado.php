<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////  MODELO CONSULTAS AFILIADOS      /////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////    CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN LAS CONSULTAS //////////
//////////////////////////////    DE CONSULTAS    ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////


//require_once '../../config/Conexion.php';
require_once '../../config/Conexion250.php';

class Consulta_afiliado extends Conexion {

    /**
     * Metodo que busca a un afiliado y muestra su estado actual
     * @param int $num_documento
     * @param String $tip_documento
     * @return boolean
     */
    public static function getAfiliado($num_documento, $tip_documento) {

        $conn = Conexion::conexionPDO();

        /*$query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT AF.TIP_DOCUMENTO_BEN, AF.NUM_DOCUMENTO_BEN, CONCAT(PRI_APELLIDO,' ',SEG_APELLIDO) AS 'APELLIDOS',"
                . " CONCAT(PRI_NOMBRE,' ',NOM_NOMBRE) AS 'NOMBRES', AF.EST_AFILIADO, SUBSTRING (AF.CODCTROCOSTOS, 1, 1) AS 'REGIMEN',"
                . " ESA.NOM_ESTRATO_AFILIADO, DEP.NOM_DEPARTAMENTO AS 'DEPARTAMENTO', CIU.NOM_CIUDAD AS 'CIUDAD' FROM AFILIADOSSUB AF WITH (NOLOCK)"
                . "INNER JOIN ESTRATOS_AFILIADOS ESA ON AF.NIV_SISBEN = ESA.COD_ESTRATO_AFILIADO "
                . "INNER JOIN CIUDADES CIU ON AF.NUM_CIUDAD = CIU.COD_CIUDAD AND AF.NUM_DEPARTAMENTO = CIU.COD_DEPARTAMENTO "
                . "INNER JOIN DEPARTAMENTOS DEP ON CIU.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO "
                . "WHERE AF.TIP_DOCUMENTO_BEN = :t_documento AND AF.NUM_DOCUMENTO_BEN = :n_documento";*/
        
        
        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT AF.TIP_DOCUMENTO_BEN, AF.NUM_DOCUMENTO_BEN, CONCAT(PRI_APELLIDO,' ',SEG_APELLIDO) AS 'APELLIDOS', CONCAT(PRI_NOMBRE,' ',NOM_NOMBRE) AS 'NOMBRES', "
                . "AF.EST_AFILIADO, SUBSTRING (AF.CODCTROCOSTOS, 1, 1) AS 'REGIMEN', ESA.NOM_ESTRATO_AFILIADO, DEP.NOM_DEPARTAMENTO AS 'DEPARTAMENTO', "
                . "CIU.NOM_CIUDAD AS 'CIUDAD', COUNT(CAR.PER_COMPENSACION) AS 'CARTERA', "
                . "IIF(SUBSTRING (AF.CODCTROCOSTOS, 1, 1) = 'C', (SELECT NOM_TIPO_AFIL FROM TIPOS_AFILIADOS WHERE COD_TIPO_AFIL = AF.TIP_AFILIADO), NULL) AS TIPO_AFILIADO "
                . "FROM AFILIADOSSUB AF WITH (NOLOCK) "
                . "INNER JOIN ESTRATOS_AFILIADOS ESA ON AF.NIV_SISBEN = ESA.COD_ESTRATO_AFILIADO "
                . "INNER JOIN CIUDADES CIU ON AF.NUM_CIUDAD = CIU.COD_CIUDAD AND AF.NUM_DEPARTAMENTO = CIU.COD_DEPARTAMENTO "
                . "INNER JOIN DEPARTAMENTOS DEP ON CIU.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO "
                . "LEFT JOIN CARTERA CAR ON AF.IDORDENITEM = CAR.DAP_IDORDENITEM "
                . "WHERE AF.TIP_DOCUMENTO_BEN = :t_documento AND AF.NUM_DOCUMENTO_BEN = :n_documento "
                . "GROUP BY AF.TIP_DOCUMENTO_BEN, AF.NUM_DOCUMENTO_BEN, CONCAT(PRI_APELLIDO,' ',SEG_APELLIDO), CONCAT(PRI_NOMBRE,' ',NOM_NOMBRE) , "
                . "AF.EST_AFILIADO, SUBSTRING (AF.CODCTROCOSTOS, 1, 1), ESA.NOM_ESTRATO_AFILIADO, DEP.NOM_DEPARTAMENTO , CIU.NOM_CIUDAD, AF.TIP_AFILIADO ";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_documento", $num_documento);
        $resultado->bindParam(":t_documento", $tip_documento);

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
     * Metodo que busca las ips prestadoras de un afiliado 
     * @param int $num_documento
     * @param String $tip_documento
     * @return boolean
     */
    public static function getIpsprimaria($num_documento, $tip_documento) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT P.NOM_PRESTADOR, "
                . "(SELECT TOP 1 DIR_ATENCION FROM CENTROATENCIPS CE WITH (NOLOCK) WHERE CE.NIT_PRESTADOR = C.NIT_PRESTADOR AND CE.NUM_DEPARTAMENTO+CE.NUM_CIUDAD = "
                . "(SELECT TOP 1 NUM_DEPARTAMENTO+NUM_CIUDAD FROM AFILIADOSSUB WITH (NOLOCK) WHERE NUM_DOCUMENTO_BEN = A.NUM_DOCUMENTO AND TIP_DOCUMENTO_BEN = A.TIP_DOCUMENTO )) DIRECCION "
                . "FROM PRESTADORES P WITH (NOLOCK), CONTRATOSIPS C WITH (NOLOCK), CONTRATOSAFILIADOS A WITH (NOLOCK) "
                . "WHERE C.NIT_PRESTADOR = P.NIT_PRESTADOR "
                . "AND C.NIT_PRESTADOR = C.NIT_PRESTADOR "
                . "AND C.NUM_CONTRATO  = A.NUM_CONTRATO "
                . "AND A.NUM_DOCUMENTO = :n_documento "
                . "AND A.TIP_DOCUMENTO = :t_documento "
                . "AND (C.FEC_INICIOCONTRAIPS <= GETDATE() AND C.FEC_FINALCONTRAIPS >= GETDATE()) "
                . "GROUP BY C.NIT_PRESTADOR, P.NOM_PRESTADOR, P.DIR_PRINCIPAL,A.NUM_DOCUMENTO,A.TIP_DOCUMENTO ORDER BY P.NOM_PRESTADOR ASC";

        $resultado = $conn->prepare($query);
        $resultado->bindParam(":n_documento", $num_documento);
        $resultado->bindParam(":t_documento", $tip_documento);

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
