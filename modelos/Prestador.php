<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////      MODELO PRESTADOR       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////    CLASE QUE CONTIENE LAS FUNCIONES QUE USAN LOS PRESTADORES     /////// 
////////////////////////////////////////////////////////////////////////////////


require_once '../config/Conexion.php';

class Prestador extends Conexion {

    public function __construct() {
        //se deja vacio para implementar instancias hacia esta clase
        //sin enviar parametro
    }

    /**
     * Metodo que lista todos los prestadores
     * @return type
     */
    public function listar_prestador() {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NIT_PRESTADOR, COD_PRESTADOR, NOM_PRESTADOR, CIU.NOM_CIUDAD, DEP.NOM_DEPARTAMENTO, TIP_PRESTADOR, CLA_PRESTADOR, COD_ESTADO_WEB "
                . "FROM PRESTADORES PRE WITH (NOLOCK) "
                . "INNER JOIN CIUDADES CIU ON PRE.NUM_DEPARTAMENTO = CIU.COD_DEPARTAMENTO AND PRE.NUM_CIUDAD = CIU.COD_CIUDAD "
                . "INNER JOIN DEPARTAMENTOS DEP ON DEP.COD_DEPARTAMENTO = CIU.COD_DEPARTAMENTO";

        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que busca un prestador por su nombre
     * @param type $nombre
     * @return array
     */
    public function buscar_prestador($nombre) {

        $conn = Conexion::conexionPDO();

        /*$sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_PRESTADOR, NIT_PRESTADOR, NOM_PRESTADOR FROM PRESTADORES WHERE NOM_PRESTADOR LIKE :nom_prestador";*/
        
        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT CAI.COD_HABILITACION, PRE.NIT_PRESTADOR, PRE.NOM_PRESTADOR, CIU.NOM_CIUDAD, DEP.NOM_DEPARTAMENTO "
                . "FROM PRESTADORES PRE WITH (NOLOCK) "
                . "INNER JOIN CENTROATENCIPS CAI ON PRE.NIT_PRESTADOR = CAI.NIT_PRESTADOR "
                . "INNER JOIN CIUDADES CIU ON CIU.COD_DEPARTAMENTO = CAI.NUM_DEPARTAMENTO AND CIU.COD_CIUDAD = CAI.NUM_CIUDAD "
                . "INNER JOIN DEPARTAMENTOS DEP ON DEP.COD_DEPARTAMENTO = CIU.COD_DEPARTAMENTO WHERE PRE.NOM_PRESTADOR LIKE :nom_prestador ";

        $query = $conn->prepare($sql);

        $query->bindValue(":nom_prestador", '%' . $nombre . '%');

        $query->execute();
        $filas = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        if ($filas != false) {

            return $filas;
        } else {

            return false;
        }
    }

    /**
     * Metodo que cambia el estado de un prestador a activo 'A'
     * @param type $nit_prestador
     * @return boolean
     */
    public function activar_prestador($nit_prestador) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE PRESTADORES WITH (ROWLOCK) SET COD_ESTADO_WEB = 'A' WHERE NIT_PRESTADOR = :nit_prestador";

        $query = $conn->prepare($sql);
        $query->bindParam(":nit_prestador", $nit_prestador);
        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que cambia el estado de un prestador a inactivo 'I'
     * @param type $nit_prestador
     * @return boolean
     */
    public function desactivar_prestador($nit_prestador) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE PRESTADORES WITH (ROWLOCK) SET COD_ESTADO_WEB = 'I' WHERE NIT_PRESTADOR = :nit_prestador";

        $query = $conn->prepare($sql);
        $query->bindParam(":nit_prestador", $nit_prestador);
        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que lista los datos de un prestador
     * @param type $nit_prestador
     * @return array
     */
    public function mostrar($nit_prestador) {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT NIT_PRESTADOR, NOM_PRESTADOR FROM PRESTADORES WITH (NOLOCK) WHERE NIT_PRESTADOR = :nit_prestador";
        $query = $conn->prepare($sql);

        $query->bindParam(":nit_prestador", $nit_prestador);

        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que lista los usuarios asociados a un prestador
     * @param String $nit_prestador
     * @return array
     */
    public function listar_usuarios($nit_prestador) {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_USUARIO, TIP_DOCUMENTO, NUM_DOCUMENTO, NOM_USUARIO, CONVERT(DATETIME2(0),FECHA_REGISTRO) AS FECHA_REGISTRO, COD_ESTADO "
                . "FROM USUARIOS_RIPSWEB URW WITH (NOLOCK) "
                . "INNER JOIN PRESTADORES PRE ON URW.NIT_PRESTADOR = PRE.NIT_PRESTADOR WHERE URW.NIT_PRESTADOR = :nit_prestador";

        $query = $conn->prepare($sql);

        $query->bindParam(":nit_prestador", $nit_prestador);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que activa un usuario asociado aun prestador
     * @param String $cod_usuario
     * @return boolean
     */
    public function activar_usuario($cod_usuario) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE USUARIOS_RIPSWEB WITH (ROWLOCK) SET COD_ESTADO = 'A' WHERE COD_USUARIO = :co_usuario";
        $query = $conn->prepare($sql);

        $query->bindParam(":co_usuario", $cod_usuario);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que desactiva un usuario asociado aun prestador
     * @param String $cod_usuario
     * @return boolean
     */
    public function desactivar_usuario($cod_usuario) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE USUARIOS_RIPSWEB WITH (ROWLOCK) SET COD_ESTADO = 'I' WHERE COD_USUARIO = :co_usuario";
        $query = $conn->prepare($sql);

        $query->bindParam(":co_usuario", $cod_usuario);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que registra un usuario y sus permisos
     * @param String $cod_usuario
     * @param String $tip_documento
     * @param String $num_documento
     * @param String $nom_usuario
     * @param String $nit_prestador
     * @param String $clave
     * @param int $permisos
     * @return boolean
     */
    public function insertar_usuario($cod_usuario, $tip_documento, $num_documento, $nom_usuario, $nit_prestador, $clave, $permisos) {

        $conn = Conexion::conexionPDO();

        try {
            $sqlUsuario = "INSERT INTO USUARIOS_RIPSWEB WITH (ROWLOCK) (COD_USUARIO, TIP_DOCUMENTO, NUM_DOCUMENTO, NOM_USUARIO, NIT_PRESTADOR, PWD_USUARIO, COD_ESTADO, FECHA_REGISTRO) VALUES "
                    . "(:cod_usuario, :tip_documento, :num_documento, :nom_usuario, :nit_prestador, :clave, 'A', GETDATE())";

            $queryUsuario = $conn->prepare($sqlUsuario);

            //Hash SHA256 en la contraseña
            $clavehash = hash("SHA256", 'EPSI06' . $clave);

            $queryUsuario->bindParam(":cod_usuario", $cod_usuario);
            $queryUsuario->bindParam(":tip_documento", $tip_documento);
            $queryUsuario->bindParam(":num_documento", $num_documento);
            $queryUsuario->bindParam(":nom_usuario", $nom_usuario);
            $queryUsuario->bindParam(":nit_prestador", $nit_prestador);
            $queryUsuario->bindParam(":clave", $clavehash);

            $queryUsuario->execute();

            if ($queryUsuario->rowCount() > 0) {

                //guardar los permisos
                //cantidad de permisos seleccionados
                $num_elementos = 0;
                //estado de la insercion del permiso
                $sw = true;

                if (is_array($permisos)) {

                    while ($num_elementos < count($permisos)) {

                        $sqlPermisos = "INSERT INTO USUARIO_PERMISOSRIPS WITH (ROWLOCK) (COD_USUARIO, COD_MENU) VALUES (:co_usuario, :co_permiso)";

                        $queryPermisos = $conn->prepare($sqlPermisos);

                        $queryPermisos->bindParam(":co_usuario", $cod_usuario);
                        $queryPermisos->bindParam(":co_permiso", $permisos[$num_elementos]);

                        $queryPermisos->execute();

                        if ($queryPermisos->rowCount() < 0) {

                            $sw = false;
                        }

                        $num_elementos = $num_elementos + 1;
                    }
                }
            }
        } catch (Exception $e) {

            //echo $e->getMessage() . ' ';
            echo "El ID <strong>" . "$cod_usuario</strong> ya esta asignado. ";

            $sw = false;
        }

        Conexion::cerrar_conexion($conn);

        return $sw;
    }

    /**
     * Metodo que lista los datos de un usuario
     * @param String $cod_usuario
     * @return array
     */
    public function mostrar_usuario($cod_usuario) {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_USUARIO, TIP_DOCUMENTO, NUM_DOCUMENTO, NOM_USUARIO FROM USUARIOS_RIPSWEB WITH (NOLOCK) WHERE COD_USUARIO = :c_usuario";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_usuario", $cod_usuario);

        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que lista los peermisos de un usuario
     * @param type $cod_usuario
     * @return array
     */
    public function listar_permisoMarcado($cod_usuario) {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT COD_MENU FROM USUARIO_PERMISOSRIPS WITH (NOLOCK) WHERE COD_USUARIO = :c_usuario";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_usuario", $cod_usuario);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que actualiza los datos y permisos de un usuario
     * @param type $cod_usuario
     * @param type $tip_documento
     * @param type $num_documento
     * @param type $nom_usuario
     * @param type $clave
     * @param type $permisos
     * @return boolean
     */
    public function editar_usuario($cod_usuario, $tip_documento, $num_documento, $nom_usuario, $clave, $permisos) {

        $conn = Conexion::conexionPDO();

        $cadena = ($clave !== "") ? ', PWD_USUARIO = :clave' : '';

        $sqlUsuario = "UPDATE USUARIOS_RIPSWEB WITH (ROWLOCK) SET TIP_DOCUMENTO = :tip_documento, NUM_DOCUMENTO = :num_documento, NOM_USUARIO = :nom_usuario" . $cadena . " "
                . "WHERE COD_USUARIO = :cod_usuario";

        $queryUsuario = $conn->prepare($sqlUsuario);

        //Hash SHA256 en la contraseña
        $clavehash = hash("SHA256", 'EPSI06' . $clave);

        $queryUsuario->bindParam(":cod_usuario", $cod_usuario);
        $queryUsuario->bindParam(":tip_documento", $tip_documento);
        $queryUsuario->bindParam(":num_documento", $num_documento);
        $queryUsuario->bindParam(":nom_usuario", $nom_usuario);

        if ($clave !== "") {
            $queryUsuario->bindParam(":clave", $clavehash);
        }

        $queryUsuario->execute();


        if ($queryUsuario->rowCount() > 0) {

            //Borro los permisos asignados al usuario
            $sqldelPermisos = "DELETE FROM USUARIO_PERMISOSRIPS WHERE COD_USUARIO = :co_usuario";

            $querydelPermisos = $conn->prepare($sqldelPermisos);

            $querydelPermisos->bindParam(":co_usuario", $cod_usuario);
            $querydelPermisos->execute();

            //inserto los permisos nuevamente
            //cantidad de permisos seleccionados
            $num_elementos = 0;
            //estado de la insercion del permiso
            $sw = true;

            if (is_array($permisos)) {

                while ($num_elementos < count($permisos)) {

                    $sqlPermisos = "INSERT INTO USUARIO_PERMISOSRIPS WITH (ROWLOCK) (COD_USUARIO, COD_MENU) VALUES (:co_usuario, :co_permiso)";

                    $queryPermisos = $conn->prepare($sqlPermisos);

                    $queryPermisos->bindParam(":co_usuario", $cod_usuario);
                    $queryPermisos->bindParam(":co_permiso", $permisos[$num_elementos]);

                    $queryPermisos->execute();

                    if ($queryPermisos->rowCount() < 0) {

                        $sw = false;
                    }

                    $num_elementos = $num_elementos + 1;
                }
            }
        } else {

            $sw = false;
        }

        Conexion::cerrar_conexion($conn);

        return $sw;
    }

    /**
     * Metodo que lista todos los usuarios del sistema
     * @return array
     */
    public function listar_Totalusuarios() {

        $conn = Conexion::conexionPDO();

        $sql = "SELECT COD_USUARIO, TIP_DOCUMENTO, NUM_DOCUMENTO, NOM_USUARIO, PRE.NOM_PRESTADOR, CONVERT(DATETIME2(0),FECHA_REGISTRO) AS FECHA_REGISTRO, COD_ESTADO FROM "
                . "USUARIOS_RIPSWEB URW WITH (NOLOCK) INNER JOIN PRESTADORES PRE WITH (NOLOCK) ON URW.NIT_PRESTADOR = PRE.NIT_PRESTADOR ORDER BY PRE.NIT_PRESTADOR DESC";

        $query = $conn->prepare($sql);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que actualiza la clave de un usuario
     * @param String $cod_usuario
     * @param String $claven
     * @return boolean
     */
    public function actualizarclave($cod_usuario, $claven) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE USUARIOS_RIPSWEB WITH (ROWLOCK) SET PWD_USUARIO = :clave WHERE COD_USUARIO = :co_usuario";
        $query = $conn->prepare($sql);

        //Hash SHA256 en la contraseña
        $clavehash = hash("SHA256", 'EPSI06' . $claven);

        $query->bindParam(":co_usuario", $cod_usuario);
        $query->bindParam(":clave", $clavehash);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que valida los datos de un usuario para ingresar al sistema
     * @param String $cod_usuario
     * @param String $clave
     * @return array
     */
    public function verificar($cod_usuario, $clave) {

        $conn = Conexion::conexionPDO();

        $sql = "SELECT COD_USUARIO, TIP_DOCUMENTO, NUM_DOCUMENTO, NOM_USUARIO, PWD_USUARIO, PRE.NIT_PRESTADOR, PRE.COD_PRESTADOR, PRE.NOM_PRESTADOR, PRE.CLA_PRESTADOR "
                . "FROM USUARIOS_RIPSWEB URW WITH (NOLOCK) INNER JOIN PRESTADORES PRE WITH (NOLOCK) ON URW.NIT_PRESTADOR = PRE.NIT_PRESTADOR WHERE COD_USUARIO = :c_usuario AND PWD_USUARIO = :clave "
                . "AND COD_ESTADO = 'A' AND PRE.COD_ESTADO_WEB = 'A'";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_usuario", $cod_usuario);
        $query->bindParam(":clave", $clave);

        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }
	
	  public function contratos() {
      $conn = Conexion::conexionPDO();
      $sql = "SELECT NUM_CONTRATO, C.* FROM GEMAEPS..CONTRATOSIPS C WITH (NOLOCK)
      WHERE C.NUM_CONTRATO NOT IN ('0241','0272') AND C.TIP_CONTRATACION = 'C'
      AND IIF(C.TIP_PRORROGA = 0, DATEADD(MONTH,12,C.FEC_FINALCONTRAIPS), C.FEC_FINALCONTRAIPS) >= CAST(GETDATE() AS DATE)
      AND C.FEC_INICIOCONTRAIPS <= CAST(GETDATE() AS DATE) AND NIT_PRESTADOR = '000000900580962'
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