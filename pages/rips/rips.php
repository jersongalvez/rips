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

    if ($_SESSION["val_rips"] == 1 && $_SESSION['PWD_USER'] == 0) {

        //Invoco las funciones que se trabajaran en todas las validaciones
        require_once '../../controladores/funciones_generales.php';
        
        //Compruebo si vienen errores
        isset($_POST['novedad']) ? errorRips($_POST['novedad']) : "";

        ?>

        <div class="modal" id="modal_rips">
            <div class="modal-background"></div>
            <div class="modal-content has-text-centered">
                <img src="../../public/img/load.svg" alt="load"/>
                <p class="has-text-white">
                    Procesando...
                </p>

                <p class="has-text-white" id="modal_rips_msg"></p>
            </div>
        </div>

        <div id="modal_novedad"></div>

        <div class="section" id="wrapper" style="margin-bottom: 0px">
            <div class="container" style="margin-top: 40px;">
                <div class="columns">
                    <div class="column is-6">
                        <p class="title is-3">Validación de Rips</p>
                        <p class="subtitle is-5">Pijaos Salud EPSI</p>
                    </div>

                    <div class="column is-6">
                        <nav class="breadcrumb is-right" aria-label="breadcrumbs"> 
                            <ul>
                                <li>
                                    <a>
                                        <span class="icon is-small">
                                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                                        </span>
                                        <span>Validación Rips</span>
                                    </a>
                                </li>

                                <li class="is-active">
                                    <a>
                                        <span class="icon is-small">
                                            <i class="far fa-file-archive"></i>
                                        </span>
                                        <span>Cargar remisión</span>
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

                <div class="columns" id="pantalla_rips">
                    <div class="column is-7" style="margin-top: 15px">
                        <p class="title is-4">Instrucciones:</p>

                        <p class="has-text-justified ">
                            Comprima todo en un archivo tipo .zip, verifique que todos los archivos con extensión (.txt en minúscula) estén declarados
                            en el archivo de control CTXXXXXX.txt, luego cárguelos al sistema dando clic en el botón "Seleccionar Archivo". No son obligatorios
                            los 11 tipos de archivos, deben estar por lo mínimo el de CONTROL (CTXXXXXX.txt), TRANSACCIONES (AFXXXXXX.txt), USUARIOS (USXXXXXX.txt)
                            y uno de DATOS.
                        </p>

                        <p class="has-text-justified" style="margin-top: 15px;">
                            En los archivos .txt a validar los primeros dos caracteres para identificar el tipo de archivo deben estar en mayúsculas
                            (CT, US, AT, etc.…) y seis caracteres máximos para identificar el envío (CT123456.txt), los cuáles no se deben repetir 
                            en envíos posteriores. Para más información consultar la <strong>Resolución Numero 3374 de 2000.</strong> 
                        </p>

                        <p class="has-text-justified" style="margin-top: 15px;">
                            Si tiene algún inconveniente relacionado con la radicación y validación de Rips, repórtelo a la menor brevedad al correo 
                            <strong>oficinavirtual@pijaossalud.com.co</strong>.
                        </p>

                        <p class="has-text-justified" style="margin-top: 30px;">
                            <a class="has-text-grey-dark" href="../../documentos/Lineamientos_Tecnicos_IPS.pdf" target="_blank">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-forward"></i>
                                </span>
                                Lineamientos técnicos para IPS
                            </a>
                        </p>

                        <p class="has-text-justified" style="margin-top: 35px;">
                            <a class="has-text-grey-dark" href="../../documentos/Resolucion_3374.pdf" target="_blank">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-forward"></i>
                                </span>
                                Reglamentación RIPS (Resolución MPS 3374 de 2000)
                            </a>
                        </p>
                    </div>

                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                    <div class="column is-4 has-text-centered">
                        <form action="rips.php" method="POST" enctype="multipart/form-data" id="frm_cargue_rips" onsubmit="return validarSubida();">
                            <figure class="image is-128x128 is-inline-block">
                                <img src="../../public/img/logo_pijaos.png">
                            </figure>

                            <div class="field">
                                <label class="label has-text-left">Número de remisión</label>
                                <div class="control has-icons-right">
                                    <input class="input is-hovered" type="text" name="txtNremision" id="txtNremision" autofocus autocomplete="off">
                                    <span class="icon is-small is-right">
                                        <i class="zmdi zmdi-attachment-alt"></i>
                                    </span>
                                </div>
                                <p class="help has-text-left">Ingrese la remisión</p>
                            </div>

                            <div class="field">
                                <label class="label has-text-left">Modalidad de contratación</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="smodalidad" id="smodalidad">
                                            <option value="" selected>Escoge una opción</option>
                                            <option value="C">Cápita</option>
                                            <option value="E">Evento</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="zmdi zmdi-assignment-o"></i>
                                    </div>
                                </div>
                                <p class="help has-text-left">Ingrese la modalidad</p>
                            </div>

                            <!--TICKET DE CONTRATOS -->
							<div class="field" id="hidden_contrato" hidden>
                                <label class="label has-text-left">Contrato RIPS capita</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="cont_vista" id="cont_vista">
                                            <option value="" selected>Escoge una opción</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="zmdi zmdi-assignment-o"></i>
                                    </div>
                                </div>
                                <p class="help has-text-left">Ingrese el numero de contrato</p>
                            </div>

                            <div class="field">
                                <div id="input-rips" class="file has-name is-centered">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="archivo" id="archivo" accept=".zip">
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="zmdi zmdi-cloud-upload"></i>
                                            </span>
                                            <span class="file-label">Seleccionar Archivo...</span>
                                        </span>
                                        <span class="file-name">No encontrado...</span>
                                    </label>
                                </div>
                                <p class="help has-text-left">Archivo comprimido</p>
                            </div> 

                            <button class="button is-fullwidth is-info" type="submit" name="proceso" id="proceso" style="margin-top: 25px;">
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

                    require 'cargar_zip.php';
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
    <script type="text/javascript" src="../../public/js/tabs.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <script type="text/javascript" src="../scripts/cargue_rips.js"></script>
    <?php
}









