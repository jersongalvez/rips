<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////         VISTA PROCESAMIENTO ZIP          ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////       VISTA PRINCIPAL PARA PROCESAR EL ARCHIVO COMPRIMIDO       ///////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';

    if ($_SESSION["cargar_capita"] == 1 && $_SESSION['PWD_USER'] == 0) {

        //Invoco las funciones que se trabajaran en todas las validaciones
        require_once '../../controladores/funciones_generales.php';

        //Compruebo si vienen errores
        //isset($_POST['novedad']) ? errorPrefectura($_POST['novedad']) : "";
        ?>

        <div class="modal" id="modal_factura">
            <div class="modal-background"></div>
            <div class="modal-content has-text-centered">
                <img src="../../public/img/load.svg" alt="load"/>
                <p class="has-text-white">
                    Procesando...
                </p>

                <p class="has-text-white" id="modal_prefactura_msg"></p>
            </div>
        </div>

        <div id="modal_novedad"></div>

        <div class="section" id="wrapper" style="margin-bottom: 0px">
            <div class="container" style="margin-top: 40px;">
                <div class="columns">
                    <div class="column is-6">
                        <p class="title is-3">Validar archivo contrato capitación</p>
                        <p class="subtitle is-5">Pijaos Salud EPSI</p>
                    </div>

                    <div class="column is-6">
                        <nav class="breadcrumb is-right" aria-label="breadcrumbs"> 
                            <ul>
                                <li>
                                    <a>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-file-plus" aria-hidden="true"></i>
                                        </span>
                                        <span>Liquidación Cápita</span>
                                    </a>
                                </li>

                                <li class="is-active">
                                    <a>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-cloud-upload"></i>
                                        </span>
                                        <span>Cargar archivo</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-12">
                        <p class="has-text-justified is-size-12">
                            <strong>PIJAOS SALUD EPS INDÍGENA</strong> dentro de sus lineamientos y políticas internas a fines de salvaguardar los derechos que protegen los datos 
                            personales, se acoge a la normatividad y disposiciones que regulan el tema en materia, dando un manejo diligente y responsable al 
                            tratamiento de información en relación a nuestros usuarios y prestadores, entendiendo lo valioso de su intimidad, garantizándole de 
                            esta manera al titular, el pleno y efectivo ejercicio del derecho de habeas data consagrado en la <strong>ley estatutaria 1266 de 2008.</strong>
                        </p>
                    </div>
                </div>

                <div class="columns" id="pantalla_prefactura">
                    <div class="column is-7" style="margin-top: 30px">
                        <p class="title is-4">Instrucciones:</p>

                        <p class="has-text-justified ">
                            Cree un archivo de texto plano (con extensión .txt en minúscula) para contener la información a validar. 
                            Para nombrar el archivo debe hacerlo de la siguiente manera: <strong>mm-aaaa.txt</strong>, por ejemplo <strong>03-2021.txt</strong>
                            el cual hace referencia al periodo a registrar.
                        </p>

                        <p class="has-text-justified" style="margin-top: 15px;">
                            Debe tener en cuenta que el periodo a validar no se puede repetir en envíos posteriores. De lo contrario el sistema no le permitirá iniciar con la revisión del archivo.
                            Si tiene alguna duda o inconsistencia en el proceso de validación, por favor contacte al administrador del sistema.
                        </p>
                    </div>

                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                    <div class="column is-4 has-text-centered">
                        <form action="cargar_archivo_capita.php" method="POST" enctype="multipart/form-data" id="frm_cargue_pref" onsubmit="return validarSubida();">
                            <figure class="image is-96x96 is-inline-block">
                                <img src="../../public/img/logo_pijaos.png">
                            </figure>

                            <div class="field">
                                <label class="label has-text-left">Periodo capitado</label>
                                <div id="input-rips" class="file has-name is-centered">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="archivo" id="archivo" accept=".txt">
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="zmdi zmdi-cloud-upload"></i>
                                            </span>
                                            <span class="file-label">Seleccionar Archivo...</span>
                                        </span>
                                        <span class="file-name">No encontrado...</span>
                                    </label>
                                </div>
                                <p class="help has-text-left">Archivo de texto simple</p>
                            </div> 

                            <button class="button is-fullwidth is-info" type="submit" name="proceso" id="proceso" style="margin-top: 15px;">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-file-text"></i>
                                </span>
                                <span>
                                    Importar
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <!--<div class="loadingpage"></div>-->
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                if (isset($_POST['proceso'])) {

                    require 'cargar_txt.php';
                }
            }
            ?>
        </div>

        <?php
    } else {

        require '../inicio/noacceso.php';
    }

    require '../footer.php';
    ?>
        <script type="text/javascript" src="../scripts/cargue_prefactura.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}









