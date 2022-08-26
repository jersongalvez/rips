<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////        MODELO PERMISO       //////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////       CLASE QUE CONTIENE LAS FUNCIONES QUE USAN LOS PERMISOS     /////// 
////////////////////////////////////////////////////////////////////////////////


require_once '../config/Conexion.php';

class Permiso extends Conexion {

    public function __construct() {
        //se deja vacio para implementar instancias hacia esta clase
        //sin enviar parametro
    }

    /**
     * Metodo que lista todos los permisos
     * @return type
     */
    public function listar() {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT * FROM MENU_RIPSWEB WITH (NOLOCK)";

        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que cambia el estado de un permiso a activo 'A'
     * @param type $cod_menu
     * @return boolean
     */
    public function activar($cod_menu) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE MENU_RIPSWEB WITH (ROWLOCK) SET COD_ESTADO = 'A' WHERE COD_MENU = :c_menu";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_menu", $cod_menu);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que cambia el estado de un permiso a inactivo 'I'
     * @param type $cod_menu
     * @return boolean
     */
    public function desactivar($cod_menu) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE MENU_RIPSWEB WITH (ROWLOCK) SET COD_ESTADO = 'I' WHERE COD_MENU = :c_menu";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_menu", $cod_menu);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que inserta un permiso
     * @param String $nombre
     * @param String $descripcion
     * @return boolean
     */
    public function insertar($nombre, $descripcion) {

        $conn = Conexion::conexionPDO();

        $sql = "INSERT INTO MENU_RIPSWEB WITH (ROWLOCK) (NOMBRE, DESCRIPCION, COD_ESTADO) VALUES (:nombre, :desc, 'A')";
        $query = $conn->prepare($sql);

        $query->bindParam(":nombre", $nombre);
        $query->bindParam(":desc", $descripcion);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que lista un permiso en especifico
     * @param int $cod_menu
     * @return boolean
     */
    public function mostrar($cod_menu) {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT * FROM MENU_RIPSWEB WITH (NOLOCK) WHERE COD_MENU = :c_menu";
        $query = $conn->prepare($sql);

        $query->bindParam(":c_menu", $cod_menu);

        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

    /**
     * Metodo que actualiza un permiso
     * @param type $cod_menu
     * @param type $nombre
     * @param type $descripcion
     * @return boolean
     */
    public function editar($cod_menu, $nombre, $descripcion) {

        $conn = Conexion::conexionPDO();

        $sql = "UPDATE MENU_RIPSWEB WITH (ROWLOCK) SET NOMBRE = :nombre, DESCRIPCION = :desc WHERE COD_MENU = :c_menu";
        $query = $conn->prepare($sql);

        $query->bindParam(":nombre", $nombre);
        $query->bindParam(":desc", $descripcion);
        $query->bindParam(":c_menu", $cod_menu);

        $query->execute();

        Conexion::cerrar_conexion($conn);

        if ($query->rowCount() > 0) {

            return true;
        }

        return false;
    }

    /**
     * Metodo que lista los permisos activos
     */
    public function listarActivos() {

        $conn = Conexion::conexionPDO();

        $sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED "
                . "SELECT * FROM MENU_RIPSWEB WITH (NOLOCK) WHERE COD_ESTADO = 'A'";

        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        Conexion::cerrar_conexion($conn);

        return $result;
    }

}
