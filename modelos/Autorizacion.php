<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////     MODELO AUTORIZACIONES   //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE VALIDAN EL ARCHIVO //////////
//////////////////////////////  DE AUTORIZACIONES ////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////

require_once '../config/Conexion250.php';

//require_once '../config/Conexion.php';

class Autorizacion extends Conexion {

    public function __construct() {
        //se deja vacio para implementar instancias hacia esta clase
        //sin enviar parametro
    }

    /**
     * Metodo que busca a un afiliado y trae su nombre y regimen
     * @param String $tip_documento
     * @param String $num_documento
     * @return array
     */
    public static function consultar_afiliadoSub($tip_documento, $num_documento) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT CONCAT(AFS.PRI_APELLIDO,' ',AFS.SEG_APELLIDO,' ',AFS.PRI_NOMBRE,' ',AFS.NOM_NOMBRE) AS NOMBRES, SUBSTRING (AFS.CODCTROCOSTOS, 1, 1) AS REGIMEN, "
                . "IIF(SUBSTRING (AFS.CODCTROCOSTOS, 1, 1) = 'C', (SELECT NOM_TIPO_AFIL FROM TIPOS_AFILIADOS WHERE COD_TIPO_AFIL = AFS.TIP_AFILIADO), NULL) AS TIPO_AFILIADO, "
                . "ESA.NOM_ESTRATO_AFILIADO AS NIVEL FROM AFILIADOSSUB AFS WITH (NOLOCK) "
                . "INNER JOIN ESTRATOS_AFILIADOS ESA ON AFS.NIV_SISBEN = ESA.COD_ESTRATO_AFILIADO "
                . "WHERE TIP_DOCUMENTO_BEN = :t_documento AND NUM_DOCUMENTO_BEN = :num_documento ";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":t_documento", $tip_documento);
        $resultado->bindParam(":num_documento", $num_documento);

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
     * 
     * @param String $nit_prestador
     * @param date $fecha_inicial
     * @param date $fecha_final
     * @param String $tip_documento
     * @param String $num_documento
     * @return array
     */
    public static function buscar_autorizacionAfil($nit_prestador, $fecha_inicial, $fecha_final, $tip_documento, $num_documento) {

         $conn = Conexion::conexionPDO();
			if ($nit_prestador) {
			  $consulta = "AND AUT.NR_IDENT_PREST_IPS = :ni_prestador ";
			}
        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT AUT.NO_SOLICITUD, AUT.NO_AUTORIZACION, CONVERT(DATE,AUT.FEC_AUTORIZACION) AS F_INI,                     CONVERT(DATE,AUT.FEC_VENCIMIENTO) AS F_FIN, AUT.ESTADO, P.NOM_PRESTADOR, "
                . "(IIF(CONVERT(DATE,GETDATE()) > AUT.FEC_VENCIMIENTO, 'S', 'N')) AS VENCIMIENTO FROM AUTORIZACION AUT WITH (NOLOCK) "
			    ."INNER JOIN PRESTADORES P ON AUT.NR_IDENT_PREST_IPS = P.NIT_PRESTADOR "
                . "INNER JOIN AFILIADOSSUB AFS ON AUT.AUT_IDORDENITEM = AFS.IDORDENITEM WHERE AUT.ESTADO IN ('CO', 'AU', 'AN', 'NC') "
                . "AND TP_IDENT_AFILIA = :t_documento "
                . "AND NR_IDENT_AFILIA = :num_documento "
                . $consulta
                . "AND AUT.FEC_AUTORIZACION BETWEEN CONVERT(DATE, :f_inicio) AND CONVERT(DATE, :f_fin) "
                . "AND AUT.NO_AUTORIZACION NOT IN ('0') "
                . "AND CONVERT(DATE,AUT.FEC_AUTORIZACION) <= CONVERT(DATE, GETDATE()) "
                . "ORDER BY CONVERT(DATE,AUT.FEC_AUTORIZACION) DESC";

        $resultado = $conn->prepare($query);
        if ($nit_prestador) {
		  $resultado->bindParam(":ni_prestador", $nit_prestador);
		}
        $resultado->bindParam(":f_inicio", $fecha_inicial);
        $resultado->bindParam(":f_fin", $fecha_final);
        $resultado->bindParam(":t_documento", $tip_documento);
        $resultado->bindParam(":num_documento", $num_documento);


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
     * Metodo que busca todos los datos de un afiliado y su autorizacion para mostrarlo en pdf 
     * @param String $no_solicitud
     * @return array
     */
    public static function encabezado_autorizacion($no_solicitud) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT AUT.NO_AUTORIZACION, SUBSTRING (AFS.CODCTROCOSTOS, 1, 1) AS REGIMEN, CONVERT(DATE,AUT.FEC_AUTORIZACION) AS F_INI_VIGENCIA, "
                . "CONVERT(DATE,AUT.FEC_VENCIMIENTO) AS F_VENCIMIENTO, AUT.ESTADO, (IIF(GETDATE() > AUT.FEC_VENCIMIENTO, 'S', 'N')) AS VENCIMIENTO, "
                . "PRE.NOM_PRESTADOR, AUT.NR_IDENT_PREST_IPS, CAI.DIR_ATENCION, CAI.TEL_ATENCION, CONCAT(CAI.NUM_DEPARTAMENTO,' - ', DPT.NOM_DEPARTAMENTO) AS DEP_PRESTADOR, "
                . "CONCAT(CAI.NUM_CIUDAD,' - ', CIP.NOM_CIUDAD) AS CIU_PRESTADOR, CONCAT(AFS.TIP_DOCUMENTO_BEN,' - ',AFS.NUM_DOCUMENTO_BEN) AS DOCU_AFIL, "
                . "CONCAT(AFS.PRI_APELLIDO,' ',AFS.SEG_APELLIDO,' ',AFS.PRI_NOMBRE,' ',AFS.NOM_NOMBRE) AS NOMBRE_AFIL, CONVERT(DATE,AFS.FEC_NACIMIENTO) AS FEC_NAC_AFIL, "
                . "AUT.NUM_EDAD ,AFS.SEXO, TPA.NOM_TIPO_AFIL, ESA.NOM_ESTRATO_AFILIADO AS NIVEL, CONCAT(AFS.NUM_DEPARTAMENTO,' - ',AFS.NUM_CIUDAD) AS CD_CIUDAD, "
                . "CONCAT(DEP.NOM_DEPARTAMENTO,' - ',CIU.NOM_CIUDAD) AS NM_CIUDAD, LEFT(AFS.DIR_RESIDENCIA, 69) AS DIR_RESIDENCIA, AFS.TEL_MOVIL, LEFT(GRP.NOM_GRUPO, 41) AS NOM_GRUPO, "
                . "AUT.COD_DIAGNOSTICO, ESP.DES_ESPECIALIDAD, CEX.DES_CAUSAS, CLA.DES_CLASE, ATU.DES_SERVICIO, CONVERT(DATE,AUT.FEC_ORDENMEDICA) AS F_ORDENMED, AUT.OBSERVACIONES "
                . "FROM AUTORIZACION AUT WITH (NOLOCK) "
                . "INNER JOIN AFILIADOSSUB AFS ON AUT.AUT_IDORDENITEM = AFS.IDORDENITEM "
                . "INNER JOIN PRESTADORES PRE ON PRE.NIT_PRESTADOR = AUT.NR_IDENT_PREST_IPS "
                . "INNER JOIN TIPOS_AFILIADOS TPA ON AFS.TIP_AFILIADO = TPA.COD_TIPO_AFIL "
                . "INNER JOIN ESTRATOS_AFILIADOS ESA ON AFS.NIV_SISBEN = ESA.COD_ESTRATO_AFILIADO "
                . "INNER JOIN CIUDADES CIU ON AFS.NUM_CIUDAD = CIU.COD_CIUDAD AND AFS.NUM_DEPARTAMENTO = CIU.COD_DEPARTAMENTO "
                . "INNER JOIN DEPARTAMENTOS DEP ON CIU.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO "
                . "INNER JOIN CENTROATENCIPS CAI ON AUT.NR_IDENT_PREST_IPS = CAI.NIT_PRESTADOR AND AUT.NR_IDENT_PREST = CAI.PUN_ATENCION "
		. "INNER JOIN CIUDADES CIP ON CAI.NUM_CIUDAD = CIP.COD_CIUDAD AND CAI.NUM_DEPARTAMENTO = CIP.COD_DEPARTAMENTO "
		. "INNER JOIN DEPARTAMENTOS DPT ON CIP.COD_DEPARTAMENTO = DPT.COD_DEPARTAMENTO "
                . "INNER JOIN GRUPO_POBLACION GRP ON AFS.GRU_POBLACONAL = GRP.COD_GRUPO "
                . "INNER JOIN ESPECIALIDADES ESP ON AUT.TIPO_AUTORIZACION = ESP.COD_ESPECIALIDAD "
                . "INNER JOIN CAUSA_EXTERNA CEX ON CEX.COD_CAUSAS = AUT.CAUSA_EXTERNA "
                . "INNER JOIN CLASE_AUTORIZACION CLA ON CLA.COD_CLASE = AUT.CLS_AUTORIZACION "
                . "INNER JOIN AUTUBICACION_SERVICIO ATU ON AUT.COD_UBISERVICIO = ATU.COD_UBISERVICIO " 
                . "WHERE AUT.NO_SOLICITUD = :n_solicitud ";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":n_solicitud", $no_solicitud);

        $resultado->execute();
        $fila = $resultado->fetch(PDO::FETCH_ASSOC);

        if ($fila != false) {

            return $fila;
        } else {

            return false;
        }

        Conexion::cerrar_conexion($conn);
    }

    /**
     * Metodo que lista los servicios autorizados de una solicitud
     * @param String $no_solicitud
     * @return array
     */
    public static function buscar_servicioautorizado($no_solicitud) {

        $conn = Conexion::conexionPDO();

        $query = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT SAU.CD_SERVICIO, SAU.CANTIDAD, SAU.OBSERVACION FROM SERVICIOS_AUTORIZADOS SAU WITH (NOLOCK) "
                . "INNER JOIN AUTORIZACION AUT ON SAU.NO_SOLICITUD = AUT.NO_SOLICITUD WHERE AUT.NO_SOLICITUD = :n_solicitud";

        $resultado = $conn->prepare($query);

        $resultado->bindParam(":n_solicitud", $no_solicitud);

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
     * Metodo que le permite al prestador confirmar si presto los servicios asociados a la autorizacion
     * @param type $no_solicitud
     * @param type $cod_usuario
     * @return boolean
     */
    public static function aceptar_servicio($no_solicitud, $cod_usuario) {


        $conn = Conexion::conexionPDO();

        $sqlAutorizacion = "UPDATE AUTORIZACION WITH (ROWLOCK) SET ESTADO = 'NC' WHERE NO_SOLICITUD = :n_solicitud";

        $queryAutorizacion = $conn->prepare($sqlAutorizacion);
        $queryAutorizacion->bindParam(":n_solicitud", $no_solicitud);
        $queryAutorizacion->execute();

        $sw = false;

        if ($queryAutorizacion->rowCount() > 0) {


            $sqlAceptarservicio = "INSERT INTO AUTORIZACION_VALIDADA WITH (ROWLOCK) (NO_SOLICITUD, COD_USUARIO, FEC_MODIFICADO) VALUES (:nu_solicitud, :co_usuario, GETDATE())";

            $queryAceptarservicio = $conn->prepare($sqlAceptarservicio);

            $queryAceptarservicio->bindParam(":nu_solicitud", $no_solicitud);
            $queryAceptarservicio->bindParam(":co_usuario", $cod_usuario);

            $queryAceptarservicio->execute();

            if ($queryAceptarservicio->rowCount() > 0) {

                $sw = true;
            }
        }

        Conexion::cerrar_conexion($conn);

        return $sw;
    }

}

