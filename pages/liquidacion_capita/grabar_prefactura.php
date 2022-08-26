<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////      ARCHIVO DE INSERCION DE DATOS       ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////////   GRABA LA INFORMACION CONTENIDA EN LOS ARCHIVOS PLANOS  ///////////
////////////////////////////////////////////////////////////////////////////////

//valida que halla sesion de usuario iniciada
session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    //datos de conexion
    require_once '../../config/Conexion.php';

    //validadores generales
    require_once '../../controladores/funciones_generales.php';

    //zona horaria colombia
    date_default_timezone_set("America/Bogota");

    require '../header.php';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["datos_grabar"]) && isset($_SESSION["datos_prefactura"])) {

            //datos recobidos de la matriz
            $datos       = $_SESSION ["datos_prefactura"]["datos"];
            $n_periodo   = $_SESSION ["datos_prefactura"]["periodo"];
            $fec_actual  = time();
            $cod_usuario = $_SESSION["COD_USUARIO"];


            //elimino las variables de session
            unset($_SESSION["datos_prefactura"]);

            require '../menu.php';
            ?>

            <div class="section" id="wrapper" style="margin-bottom: 30px">
                <div class="container" style="margin-top: 30px;">
                    <div class="column has-background-light" style="margin-top: 0px;">
                        <div class="columns is-12" style="padding: 13px;">
                            <figure class="image is-96x96">
                                <img src="../../public/img/logo_pijaos.png">
                            </figure>

                            <p class="title is-4" style="margin-top: 35px;">
                                &nbsp; Registro de datos de la pre-factura:
                            </p>
                        </div>
                    </div>

                    <div class="columns">       
                        <div class="column is-12">
                            <div class="columns is-gap" style="margin-top: 15px;">
                                <div class="column is-9">
                                    <div class="field">
                                        <label class="label has-text-left">Datos del archivo</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo 'El periodo registrado para esta pre-factura es: ' . $n_periodo; ?>" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-city"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Fecha de registro</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo date("d/m/Y H:i:s", $fec_actual); ?>" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-calendar"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <p class="subtitle is-5 has-text-weight-semibold has-text-centered" style="margin-top: 20px; margin-bottom: 25px;">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-format-list-bulleted"></i>
                                </span>
                                Archivos relacionados
                            </p>
                        </div>
                    </div>

                    <div class="columns">   
                        <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                        <div class="column is-10">

                            <div class="columns is-gap">
                                <div class="column">
                                    <p>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-forward"></i>
                                        </span>
                                        Estado registro archivo de pre-factura:
                                    </p> 
                                </div>

                                <div class="column">
                                    <div class="barra">
                                        <div class="progreso" id="bpf"> <div class="porcentaje" id="ppf"></div></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                    </div>

                    <div class="columns" style="margin-top: 15px;">
                        <div class="column is-12 has-text-centered" id="estado">
                        </div>
                    </div>

                    <div class="columns" style="margin-top: 15px;">
                        <div class="column is-12 has-text-centered">

                            <button class="button  is-primary" disabled="true" id="nuevo_rips" onclick="nuevo()">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-home"></i>
                                </span>
                                <span>Registrar un nuevo periodo</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            require '../footer.php';

            //instancia a la clase conexion
            $conexion = new conexion();

            //invoco el metodo conectar
            $transaccion = $conexion->conexionPDO();

            //añadir la excepcion
            $transaccion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //permitir la insercion de acentos y eñes
            $transaccion->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);

            //inicio la transaccion
            $transaccion->beginTransaction();


            try {

                //Insert del encabezado en: RECEPCIONCAPITA
                $queryrr = "INSERT INTO RECEPCIONCAPITA WITH(ROWLOCK)(PERIODO, FEC_CARGUE, COD_USUARIO) VALUES (:cod_periodo, :fec_cargue, :cod_usuario)";

                $resultadorr = $transaccion->prepare($queryrr);

                $fecha = date("d/m/Y H:i:s", $fec_actual);

                $resultadorr->bindParam(":cod_periodo", $n_periodo);
                $resultadorr->bindParam(":fec_cargue", $fecha);
                $resultadorr->bindParam(":cod_usuario", $cod_usuario);

                $resultadorr->execute();


                //Obtengo la cantidad de lineas del archivo plano
                $total_datos = count($datos);

                //Insert archivo de control en: ARC_CONTROL
                foreach ($datos as $posicion => $linea) {

                    $linea = trim($linea);
                    $datapf = explode(',', $linea);

                    $querypf = "INSERT INTO ARCH_PREFACTURA WITH(ROWLOCK)(PERIODO, NIT_PRESTADOR, NUM_CONTRATO, NUM_AFILIADOS, VR_MES_ANTICIPADO, RECONOCIMIENTOS, RESTITUCIONES, VR_FINAL_CAPITA) "
                            . "VALUES (:cod_periodo, :ni_prestador, :nu_contrato, :nu_afiliados, :m_anticipado, :reconocimientos, :restituciones, :vr_finalc);";


                    $resultadopf = $transaccion->prepare($querypf);

                    $resultadopf->bindParam(":cod_periodo", $datapf[0]);
                    $resultadopf->bindParam(":ni_prestador", $datapf[1]);
                    $resultadopf->bindParam(":nu_contrato", $datapf[2]);
                    $resultadopf->bindParam(":nu_afiliados", $datapf[3]);
                    $resultadopf->bindParam(":m_anticipado", $datapf[4]);
                    $resultadopf->bindParam(":reconocimientos", $datapf[5]);
                    $resultadopf->bindParam(":restituciones", $datapf[6]);
                    $resultadopf->bindParam(":vr_finalc", $datapf[7]);

                    $resultadopf->execute();

                    $posicion++;
                    $porcentaje = round(($posicion / $total_datos) * 100, 0);
                    ?>
                    <script>
                        $("#bpf").width("<?php echo $porcentaje; ?>%");
                        $("#ppf").html("<?php echo $porcentaje; ?>%");
                    </script>
                    <?php
                    flush_buffers();
                    usleep(5000);
                }


                $transaccion->commit();

                //habilito opciones si el insert se completa
                echo "<script>$(function () { exito();  });</script>";
            } catch (PDOException $error) {

                $transaccion->rollBack();
                //echo $error->getMessage();
                //Muestro un error si no se pueden grabar los datos
                echo "<script>$(function () { error();  });</script>";
            }
        } else {

            //no se encuenta el input oculto
            echo "<script>$(function () {  envio_error(1); });</script>";
        }
    } else {
        //no se encuenta el method general
        echo "<script>$(function () {  envio_error(1); });</script>";
    }
    ?>  <script type="text/javascript" src="../scripts/grabar_prefactura.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script> <?php
}
//////////////////// METODOS DEL ARCHIVO GRABAR_PREFACTURA.PHP ////////////////////////

/**
 * Metodos que limpia el buffer para ir actualizando la pagina y cargando las barras
 * de estado de registro
 */
function flush_buffers() {
    //ob_end_flush();
    ob_flush();
    flush();
    //ob_start();
}
