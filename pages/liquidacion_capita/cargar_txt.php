<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////       ARCHIVO DE PROCESAMIENTO ZIP       ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////  INVOCA LOS METODOS NECESARIOS PARA EL PROCESAMIENTO DE LOS RIPS  //////
////////////////////////////////////////////////////////////////////////////////

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {


    //valido que llegue el archivo y numero de remision
    if (isset($_FILES["archivo"])) {


        //datos del arhivo
        $nombre_archivo = $_FILES['archivo']['name'];
        $tipo_archivo   = $_FILES['archivo']['type'];
        $tamano_archivo = $_FILES['archivo']['size'];
        $obtener_nom    = str_replace('-', '/', substr($nombre_archivo, 0, -4));


        //compruebo si las características del archivo son las definidas
        if (($tipo_archivo !== "text/plain") || ($tamano_archivo > 10000000)) {

            //Envio el frm indicando que las caracteristicas del envio no son correctas
            echo "<script>$(function () { envio_novedades(3); });</script>";
        } else {


            //Invoco el validador del archivo
            require_once '../../controladores/carcapita.php';
            $capita = new Ccapita_validador();

            //Validar el periodo de la remision
            $validar_periodo = $capita->buscar_periodo(utf8_encode($obtener_nom));


            //Se valida si el periodo ya se registro.
            if ($validar_periodo == false) {
                ?>

                <div class=" section container" style="padding-top: 0px !important;">
                    <div class="columns">       
                        <div class="column is-12" id="carga">

                            <div class="has-text-centered">
                                <figure class="image is-96x96 is-inline-block">
                                    <img src="../../public/img/logo_pijaos.png">
                                </figure>

                                <p class="title is-4 has-text-centered" style="margin-top: 3px;">
                                    Resultados de la validación
                                </p>
                            </div>

                            <p class="has-text-justified" style="margin-top: 30px; margin-bottom: 30px;">
                                A continuación se detallan las novedades encontradas en el proceso de validación si es el caso. De ser así, 
                                por favor corrija todos los errores encontrados y cargue nuevamente el archivo plano; de lo contrario 
                                el sistema no lo dejara grabar la información.
                            </p>

                            <div id="cuerpo_validacion">
                                <?php
                                //Obtengo la ubicacion temporal del fichero
                                $ruta = $_FILES["archivo"]["tmp_name"];

                                //Abro el txt y lo cargo a una variable
                                $datos = array_map("utf8_encode", file($ruta));

                                //almacena todos los tipos de errores encontrados en 
                                //los archivos
                                $_SESSION ["logErrores"] = array();

                                //Invoco el metodo que 
                                $Tecapita = $capita->val_cargacapita($datos, $obtener_nom);
                                $total_errores = $Tecapita[0];
                                ?>
                            </div>
                        </div>
                    </div>



                    <?php
                    if ($total_errores > 0) {

                        //oculto el formulario
                        echo "<script>$('#pantalla_prefactura').hide();</script>";

                        //Posiciono el foco en la pantalla de resultados
                        echo "<script> $(document).ready(function () { document.getElementById('carga').scrollIntoView({block: 'start', behavior: 'smooth'}); }); </script>";
                        ?>
                        <div class="columns" style="margin-bottom: 10px;" id="total_error">  
                            <div class="column is-half is-offset-one-quarter">
                                <article class="message is-danger">
                                    <div class="message-body has-text-centered">

                                        <p>
                                            <span class="icon is-small"><i class="fas fa-exclamation-triangle"></i></span> 
                                            Se encontraron <strong><?php echo $total_errores; ?></strong> error(es) en el archivo contrato de capitación.
                                        </p>

                                        <div class="columns is-centered" style="margin-top: 0.5px;">
                                            <div class="column has-text-centered ">
                                                <button class="button is-danger is-small" onclick="log_txt('<?php echo implode(",", $_SESSION ["logErroresCap"]) ?>')">
                                                    <span class="icon is-small">
                                                        <i class="zmdi zmdi-file-text"></i>
                                                    </span>
                                                    <span> Descargar </span>
                                                </button>


                                                <button class="button is-info is-small" onclick="recarga(0)">
                                                    <span class="icon is-small">
                                                        <i class="zmdi zmdi-home"></i>
                                                    </span>
                                                    <span> Continuar </span> 
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                        <?php
                        //Elimino el contenido de la variable $datos
                        unset($datos);
                    } else {


                        echo "<script>$('#pantalla_prefactura').hide();</script>";
                        echo "<script>$('#cuerpo_validacion').hide();</script>";
                        echo "<script> $(document).ready(function () { document.getElementById('carga').scrollIntoView(true); });  </script>";

                        $_SESSION ["datos_prefactura"] = array(
                            "datos"   => $datos,
                            "periodo" => $obtener_nom
                        );
                        ?> 

                        <div class="columns" style="margin-bottom: 10px;" id="grabar_datos">  
                            <div class="column is-12">
                                <article class="message">
                                    <div class="message-header">
                                        <p>Este archivo no presenta errores, verifique la siguiente información:</p>
                                    </div>
                                    <div class="message-body">
                                        <p class="has-text-justified">
                                            El archivo <strong><?php echo $obtener_nom ?></strong> no tiene novedades en su estructura y contenido, por favor verifique 
                                            los siguientes valores y de clic en el botón <strong>“Grabar información”</strong>. Una vez grabados los datos no se podrán
                                            modificar los mismos.
                                        </p>

                                        <div class="columns" style="margin-top: 5px;">   
                                            <div class="column is-3 has-text-centered">
                                                <p><strong>Nº de pre facturas</strong></p>
                                                <?php echo $Tecapita[1]; ?>
                                            </div>

                                            <div class="column is-3 has-text-centered">
                                                <p><strong>Total Mes Anticipado </strong></p>
                                                <?php echo '$' . formatearNumero($Tecapita[2]); ?>
                                            </div>

                                            <div class="column is-3 has-text-centered">
                                                <p><strong>Total Reconocimientos </strong></p>
                                                <?php echo '$' . formatearNumero($Tecapita[3]); ?>
                                            </div>

                                            <div class="column is-3 has-text-centered">
                                                <p><strong>Total Restituciones </strong></p>
                                                <?php echo '$' . formatearNumero($Tecapita[4]); ?>
                                            </div>
                                        </div>

                                        <div class="columns">   
                                            <div class="column is-12 has-text-centered">
                                                <p><strong>Total valor final cápita</strong></p>
                                                <?php echo '$' . formatearNumero($Tecapita[5]); ?>
                                            </div>
                                        </div>

                                        <div class="columns">   
                                            <div class="column is-12 has-text-centered">
                                                <button class="button is-info" onclick="envio_prefactura('<?php echo $obtener_nom; ?>');" id="grabar">
                                                    <span class="icon is-small">
                                                        <i class="zmdi zmdi-floppy"></i>
                                                    </span>
                                                    <span>
                                                        Grabar información - <?php echo $obtener_nom; ?>
                                                    </span>
                                                </button> 

                                                &nbsp; &nbsp;

                                                <button class="button is-primary" onclick="recarga(2)">
                                                    <span class="icon is-small">
                                                        <i class="zmdi zmdi-search-in-file"></i>
                                                    </span>
                                                    <span>
                                                        Cancelar proceso - Nuevo envío
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                    <?php
                }


                //<?php
            } else {

                //Si el periodo ya se registro, se indica la fecha de cargue.
                echo "<script>$(function () { envio_novedades(2, '" . $validar_periodo['FC'] . "'); });</script>";
            }
        }
    } else {

        //Envio el frm indicando que no llegaron los datos para iniciar el proceso
        echo "<script>$(function () { envio_novedades(1); });</script>";
    }
}




























