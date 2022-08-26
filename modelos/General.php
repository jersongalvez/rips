<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////      MODELO PRESTADOR       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////    CLASE QUE CONTIENE LAS FUNCIONES QUE USAN LOS PRESTADORES     /////// 
////////////////////////////////////////////////////////////////////////////////


require_once '../config/Conexion250.php';

class General extends Conexion {

    public function __construct() {
        //se deja vacio para implementar instancias hacia esta clase
        //sin enviar parametro
    }
	
	public function contratos() {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT NUM_CONTRATO, C.* FROM GEMAEPS..CONTRATOSIPS C WITH (NOLOCK)
      WHERE C.NUM_CONTRATO NOT IN ('0241','0272') AND C.TIP_CONTRATACION = 'C'
      AND IIF(C.TIP_PRORROGA = 0, DATEADD(MONTH,12,C.FEC_FINALCONTRAIPS), C.FEC_FINALCONTRAIPS) >= CAST(GETDATE() AS DATE)
      AND C.FEC_INICIOCONTRAIPS <= CAST(GETDATE() AS DATE) AND NIT_PRESTADOR = ".$_SESSION['NIT_PRESTADOR']."
      ORDER BY FEC_FINALCONTRAIPS DESC, FEC_INICIOCONTRAIPS DESC";
      $query = $conn->prepare($sql);
      $query->execute();
      $result = $query->fetchAll(PDO::FETCH_ASSOC);
      Conexion::cerrar_conexion($conn);
      return $result;
    }
}

/*$a = Prestador::buscar_prestador('wala');
echo '<pre>';
var_dump($a);
echo '</pre>';
*/