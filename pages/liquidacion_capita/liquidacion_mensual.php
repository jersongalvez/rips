<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////            VISTA BUSCAR RIPS             ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////          VISTA PRINCIPAL PARA LA BUSQUEDA DE REMISIONES         ///////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';

    if ($_SESSION["consultar_prefactura"] == 1 && $_SESSION['PWD_USER'] == 0) {

        //Invoco las funciones que se trabajaran en todas las validaciones
        require_once '../../controladores/funciones_generales.php';

        //Valido que el codigo de prestador sea el local
        $co_prestador = ($_SESSION['NIT_PRESTADOR'] === '809008362') ? 1 : 0;

        if ($co_prestador == 1) {
            ?>

            <div class="modal is-hidden-mobile" id="modal_bprestador">
                <div class="modal-background"></div>
                <div class="modal-card" style="width: 75%;">
                    <header class="modal-card-head">
                        <p class="modal-card-title"><strong>Búsqueda de prestadores</strong></p>
                        <button class="delete" aria-label="close" onclick="mostrar_bprestador(false)"></button>
                    </header>

                    <section class="modal-card-body">
                        <div class="field">
                            <label class="label has-text-left">Nombre del prestador (*):</label>
                            <div class="field is-grouped">
                                <p class="control is-expanded">
                                    <input class="input is-hovered" type="text" name="nomb_prestador" id="nomb_prestador" autocomplete="off">
                                </p>

                                <p class="control">
                                    <button class="button is-info" onclick="listar_bprestador()" id="filtrar_prestador"> 
                                        <span class="icon is-small"> 
                                            <i class="zmdi zmdi-search"></i>
                                        </span> 
                                    </button>
                                </p>
                            </div>
                        </div>

                        <div class="table-container" style="margin-top: 30px;">
                            <table id="tbllistadoprestador" class="table is-striped is-hoverable is-fullwidth" >
                                <thead>
                                    <tr>
                                        <th class="has-text-centered">Opciones</th>
                                        <th class="has-text-centered">Código</th>
                                        <th class="has-text-centered">Nit</th>
                                        <th class="has-text-centered">Nombre</th>
                                        <th class="has-text-centered">Ciudad</th>
                                        <th class="has-text-centered">Departemento</th>
                                    </tr>
                                </thead>

                                <tbody> </tbody>

                                <tfoot>
                                    <tr>
                                        <th class="has-text-centered">Opciones</th>
                                        <th class="has-text-centered">Código</th>
                                        <th class="has-text-centered">Nit</th>
                                        <th class="has-text-centered">Nombre</th>
                                        <th class="has-text-centered">Ciudad</th>
                                        <th class="has-text-centered">Departemento</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>

                    <footer class="modal-card-foot has-text-centered">
                        <div class="field">
                            <p class="control">
                                <button class="button is-primary" onclick="mostrar_bprestador(false)">
                                    <span class="icon is-small">
                                        <i class="zmdi zmdi-check"></i>
                                    </span>
                                    <span> Aceptar </span>
                                </button>
                            </p>
                        </div>
                    </footer>
                </div>
            </div>  

        <?php } ?>

        <div class="section" id="wrapper">
            <div class="container">
                <div class="columns" style="margin-top: 30px;">
                    <div class="column is-6">
                        <p class="title is-3">Liquidación mensual contratos capitados</p>
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
                                            <i class="zmdi zmdi-search-in-page"></i>
                                        </span>
                                        <span>Liquidación mensual contratos</span>
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

                <div id="formulario_busqueda">
                    <div class="columns" style="margin-top: 10px;">
                        <div class="column is-4">
                            <div class="field">
                                <label class="label has-text-left">NIT del prestador (*):</label>
                                <div class="field is-grouped">
                                    <p class="control is-expanded has-icons-left">
                                        <input class="input is-hovered" type="text" name="nit_prestador" id="nit_prestador" 
                                               value="<?php echo ($co_prestador == 1) ? '' : $_SESSION["NIT_PRESTADOR"] ?>" 
                                               <?php echo ($co_prestador == 1) ? '' : 'readonly' ?>>
                                        <span class="icon is-small is-left">
                                            <i class="zmdi zmdi-attachment-alt"></i>
                                        </span>
                                    </p>
                                    <?php if ($co_prestador == 1) { ?>
                                        <p class="control is-hidden-mobile">
                                            <button class="button is-info" onclick="mostrar_bprestador(true)"> 
                                                <span class="icon is-small"> 
                                                    <i class="zmdi zmdi-search"></i>
                                                </span> 
                                            </button>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="column is-4">
                            <div class="field">
                                <label class="label has-text-left">Vigencia (*):</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="vigencia" id="vigencia" onchange="cargar_meses()"> 
                                            <option value="" selected="">Elige una opción</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="zmdi zmdi-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="column is-4">
                            <div class="field">
                                <label class="label has-text-left">Mes (*):</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="mes" id="mes">
                                            <option value="" selected="">Elige una opción</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="zmdi zmdi-assignment-o"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column is-6">
                            <button class="button is-info is-fullwidth" type="submit" onclick="listar()" id="filtrar">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-search-in-file"></i>
                                </span>
                                <span>
                                    Buscar
                                </span>
                            </button> 
                        </div>

                        <div class="column is-6">
                            <button class="button is-primary is-fullwidth" id="nueva" onclick="limpiar()">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-plus"></i>
                                </span>
                                <span>
                                    Nuevo
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="columns" style="margin-top: 10px;">
                        <div class="column is-12" id="listadoregistros">
                            <div class="table-container">
                                <table id="tbllistado" class="table is-striped is-hoverable is-fullwidth" >
                                    <thead>
                                        <tr>
                                            <th class="has-text-centered">Opciones</th>
                                            <th class="has-text-centered">Periodo</th>
                                            <th class="has-text-centered">Número Contratos</th>
                                            <th class="has-text-centered">Valor Cápita</th>
                                        </tr>
                                    </thead>

                                    <tbody> </tbody>

                                    <tfoot>
                                        <tr>
                                            <th class="has-text-centered">Opciones</th>
                                            <th class="has-text-centered">Periodo</th>
                                            <th class="has-text-centered">Número Contratos</th>
                                            <th class="has-text-centered">Valor Cápita</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {

        require '../inicio/noacceso.php';
    }

    require '../footer.php';
    ?>
    <script type="text/javascript" src="../../public/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.html5.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/jszip.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/vfs_fonts.js"></script>
    <script type="text/javascript" src="../scripts/consulta_liquidacionC.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}










