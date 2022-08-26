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

    if ($_SESSION["filtrar_remision"] == 1 && $_SESSION['PWD_USER'] == 0) {

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
                        <p class="title is-3">Búsqueda de Rips</p>
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
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <span>Buscar Rips</span>
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
                                <label class="label has-text-left">Fechas de consulta (*):</label>
                                <div class="control has-icons-left">
                                    <input class="input is-hovered" type="text" id="rango_fechas" name="rango_fechas" required>
                                    <span class="icon is-small is-left">
                                        <i class="zmdi zmdi-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="column is-4">
                            <div class="field">
                                <label class="label has-text-left">Modalidad de contratación (*):</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="smodalidad" id="smodalidad">
                                            <option selected value="">Elige una opción</option>
                                            <option value="T">Todas</option>
                                            <option value="C">Cápita</option>
                                            <option value="E">Evento</option>
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
                                            <th class="has-text-centered">Número</th>
                                            <th class="has-text-centered">Fecha Remisión</th>
                                            <th class="has-text-centered">Fecha Cargue</th>
                                            <th class="has-text-centered">Modalidad Contrato</th>
                                            <th class="has-text-centered">Usuario</th>
                                        </tr>
                                    </thead>

                                    <tbody> </tbody>

                                    <tfoot>
                                        <tr>
                                            <th class="has-text-centered">Opciones</th>
                                            <th class="has-text-centered">Número</th>
                                            <th class="has-text-centered">Fecha Remisión</th>
                                            <th class="has-text-centered">Fecha Cargue</th>
                                            <th class="has-text-centered">Modalidad Contrato</th>
                                            <th class="has-text-centered">Usuario</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hide-div" id="informacio_af">
                    <div class="columns" style="margin-top: 10px;">
                        <div class="column is-12"> 
                            <div class="columns is-gap" style="margin-top: 10px;">
                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Código del prestador</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="cod_prestador" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-archive"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">NIT del prestador</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="ni_prestador" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-file"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Número de remisión</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="n_remision" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-attachment-alt"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Modalidad</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="modalidad" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-receipt"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Fecha remisión</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="fec_remision" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-calendar"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="columns is-gap">
                                <div class="column is-9">
                                    <div class="field">
                                        <label class="label has-text-left">Nombre del prestador</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" id="no_prestador" readonly="true">
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
                                            <input class="input has-background-light is-focused" type="text" id="fec_registro" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-calendar-check"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <p class="subtitle is-5 has-text-weight-semibold has-text-centered" style="margin-top: 15px;">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-format-list-bulleted"></i>
                                </span>
                                Archivos relacionados
                            </p>
                        </div>
                    </div>

                    <div class="columns">       
                        <div class="column is-12">
                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                <thead>
                                    <tr class="has-background-primary">
                                        <th colspan="2" class="has-text-centered has-text-success">Tipo Archivo</th>
                                        <th class="has-text-centered has-text-success">Código Archivo</th>
                                        <th class="has-text-centered has-text-success">Fecha Reportada</th>
                                        <th class="has-text-centered has-text-success">Total Registros</th>
                                    </tr>
                                </thead>

                                <tbody id="datos_ct">

                                </tbody>

                            </table>

                            <p class="subtitle is-5 has-text-weight-semibold has-text-centered" style="margin-top: 15px;">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-format-list-numbered"></i>
                                </span>
                                Datos de facturación
                            </p>
                        </div>
                    </div>

                    <div class="columns">       
                        <div class="column is-12">
                            <div class="table-container">
                                <table id="tblinfoaf" class="table is-striped is-hoverable is-fullwidth" >
                                    <thead>
                                        <tr>
                                            <th class="has-text-centered">Número Factura</th>
                                            <th class="has-text-centered">Fecha</th>
                                            <th class="has-text-centered">Valor Copago</th>
                                            <th class="has-text-centered">Valor Comisión</th>
                                            <th class="has-text-centered">Valor Descuento</th>
                                            <th class="has-text-centered">Valor Total</th>
                                        </tr>
                                    </thead>

                                    <tbody> </tbody>

                                    <tfoot>
                                        <tr>
                                            <th class="has-text-centered">Número Factura</th>
                                            <th class="has-text-centered">Fecha</th>
                                            <th class="has-text-centered">Valor Copago</th>
                                            <th class="has-text-centered">Valor Comisión</th>
                                            <th class="has-text-centered">Valor Descuento</th>
                                            <th class="has-text-centered">Valor Total</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="columns has-background-success">   
                        <div class="column is-3 has-text-centered">
                            <strong>Total Copagos:</strong> <label id="copagos"></label>
                        </div>

                        <div class="column is-3 has-text-centered">
                            <strong>Total Comisiones: </strong> <label id="comisiones"></label>
                        </div>

                        <div class="column is-3 has-text-centered">
                            <strong>Total Descuentos: </strong> <label id="descuento"></label>
                        </div>

                        <div class="column is-3 has-text-centered">
                            <strong>Total según RIPS: </strong> <label id="neto_rips"></label>
                        </div>
                    </div>

                    <div class="columns" style="margin-top: 20px;">
                        <div class="column is-12 has-text-centered">
                            <button class="button is-primary" id="reporte" onclick="mostrarform(false)">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-home"></i>
                                </span>
                                <span>
                                    Regresar al filtro
                                </span>
                            </button>
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
    <script type="text/javascript" src="../../public/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="../../public/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="../../public/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.html5.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/jszip.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/vfs_fonts.js"></script>
    <script type="text/javascript" src="../scripts/buscar_rips.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}










