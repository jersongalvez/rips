<?php

require_once '../config/Conexion250.php';

class Contrareferencia1 extends Conexion {

    public static function buscarAfiliado($tpdocumento, $documento) {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT a.PRI_APELLIDO, a.SEG_APELLIDO, a.PRI_NOMBRE, a.NOM_NOMBRE, a.FEC_NACIMIENTO, SEXO, c.COD_CIUDAD, d.COD_DEPARTAMENTO"
            ."FROM AFILIADOSSUB a INNER JOIN DEPARTAMENTOS d ON a.NUM_DEPARTAMENTO = d.COD_DEPARTAMENTO"
            ."CIUDADES c ON a.NUM_CIUDAD = c.COD_CIUDAD"
            ."WHERE a.TIP_DOCUMENTO_BEN = ".$tpdocumento." AND a.NUM_DOCUMENTO_BEN ".$documento." AND a.EST_AFILIADO = 1 ";
      $resultado = $conn->prepare($sql);
      $resultado->execute();
      $fila = $resultado->fetch(PDO::FETCH_ASSOC);
      Conexion::cerrar_conexion($conn);
      if ($fila != false) {

        return true;
    } else {

        return false;
    }
    }
    //METODO ENCARGADO DE CARGAR LOS DIAGNOSTICOS
    public  function cargarDiagnosticos() {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT COD_DIAGNOSTICO, NOM_DIAGNSOTICO FROM DIAGNOSTICOS WHERE EST_DIAGNOSTICO = 0 ";
      $resultado = $conn->prepare($sql);
      $resultado->execute();
      $result = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }
    //METODO ENCARGADO DE CARGAR LOS PROCEDIMIENTOS
    public function cargarProcedimientos() {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT CODIGO, DESCRIPCION FROM PROCEDIMIENTOS";
      $resultado = $conn->prepare($sql);
      $resultado->execute();
      $result = $resultado->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }
    //METODO ENCARGADO DE CARGAR LOS MUNICIPIOS
    public function cargarMunicipios() {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT * FROM CIUDADES ORDER BY NOM_CIUDAD ASC";
      $resultado = $conn->prepare($sql);
      $resultado->execute();
      $result = $resultado->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    //METODO ENCARGADO DE CREAR LA REMISION
    public static function crearRemision($data) {
      $conn = Conexion::conexionPDO();
      $datos = [
        "TPDOCUMENTO" => $data["tpdocumento"],
        "DOCUMENTO" => $data["documento"],
        "FECHA" => $data["fecha"],
        "REGIMEN" => $data["regimen"],
        "NOMBRE" => $data["pnombre"]." ".$data["snombre"]." ".$data["papellido"]." ".$data["sapellido"],
        "NACIMIENTO" => $data["nacimiento"],
        "SEXO" => $data["sexo"],
        "DIRECCION" => $data["direccion"],
        "TELEFONO" => $data["telefono"],
        "ENCARGADO" => $data["encargado"],
        "TPTRAMITE" => $data["tptramite"],
        "CAUSA_ATENCION" => $data["causa_atencion"],
        "OBSERVACION" => $data["observacion"]
      ];
      $sql = "INSERT INTO REMISION_CONTRAREFERENCIA VALUES (:TP_DOCUMENTO, :DOCUMENTO, :FECHA, :REGIMEN, :NOMBRE, :NACIMIENTO, :SEXO, :DIRECCION, :TELEFONO, :ENCARGADO, :TPTRAMITE, :CAUSA_ATENCION, :OBSERVACION)";
      $resultado = $conn->prepare($sql);
      $resultado->execute($datos);
    }

    public function validarUsuarioCambio($documento) {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT * FROM HIST_CAMBIO_DOCUMENTO WHERE  HCD_NUM_DOCUMENTO_AN = '$documento' AND HCD_DES_CAMBIO = 'CAMBIO DOCUMENTO AFILIADO' ";
      $resultado = $conn->prepare($sql);
      $resultado->execute();
      $result = $resultado->fetchAll(PDO::FETCH_ASSOC);
       return $result;
    }
}

?>