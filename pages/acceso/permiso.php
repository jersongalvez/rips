<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////      ARCHIVO DE PERMISOS      ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
////////////         VISTA PARA EL PROCESAMIENTO DE LOS PERMISOS     ///////////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';

    if ($_SESSION["acceso"] == 1 && $_SESSION['PWD_USER'] == 0) {
        ?>
        <div class="section" id="wrapper">
            <div class="container">

                <div class="columns" style="margin-top: 25px;">
                    <div class="column is-6">
                        <p class="title is-4"> 
                            <span class="icon is-small">
                                <i class="fa fa-suitcase" aria-hidden="true"></i>
                            </span>
                            <span>&nbsp; Administración de permisos</span>
                        </p>
                    </div>

                    <div class="column is-6">
                        <nav class="breadcrumb is-right" aria-label="breadcrumbs">
                            <ul>
                                <li>
                                    <a>
                                        <span class="icon is-small">
                                            <i class="fas fa-folder-open" aria-hidden="true"></i>
                                        </span>
                                        <span>Acceso</span>
                                    </a>
                                </li>

                                <li class="is-active">
                                    <a>
                                        <span class="icon is-small">
                                            <i class="fa fa-suitcase" aria-hidden="true"></i>
                                        </span>
                                        <span>Permisos</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-12">
                        <article class="panel is-primary">
                            <p class="panel-heading"></p>

                            <div class="panel-block">
                                <p class="subtitle is-5">
                                    Permisos registrados 
                                    <button class="button is-info is-hovered is-small" id="btnagregar" onclick="mostrarform(true)">
                                        <i class="zmdi zmdi-plus"></i> &nbsp;Agregar
                                    </button>
                                </p>
                            </div>

                            <div class="column is-12" id="listadoregistros">
                                <div class="table-container">
                                    <table id="tbllistado" class="table is-striped is-hoverable is-fullwidth" >
                                        <thead>
                                            <tr>
                                                <th class="has-text-centered">Opciones</th>
                                                <th class="has-text-centered">Nombre</th>
                                                <th class="has-text-centered">Descripción</th>
                                                <th class="has-text-centered">Estado</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th class="has-text-centered">Opciones</th>
                                                <th class="has-text-centered">Nombre</th>
                                                <th class="has-text-centered">Descripción</th>
                                                <th class="has-text-centered">Estado</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="column is-12 hide-div" id="formularioregistros">
                                <form name="formulario" id="formulario" method="POST">
                                    <div class="columns">
                                        <div class="column">
                                            <div class="columns">
                                                <div class="column">
                                                    <input type="hidden" name="cod_permiso" id="cod_permiso"> 
                                                    <div class="field">
                                                        <label class="label has-text-left">Nombre (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="text" style="text-transform:uppercase;" onkeyup="javascript:this.value = this.value.toUpperCase();" 
                                                                   name="nombre" id="nombre" minlength="5" maxlength="100" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-file"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese el nombre</p>
                                                    </div>
                                                </div>

                                                <div class="column">
                                                    <div class="field">
                                                        <label class="label has-text-left">Descripción (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="text" style="text-transform:uppercase;" onkeyup="javascript:this.value = this.value.toUpperCase();" 
                                                                   name="descripcion" id="descripcion" minlength="10" maxlength="150" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-attachment-alt"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese la descripción</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <p class="buttons">
                                                <button class="button is-info is-hovered" type="submit" id="btnGuardar">
                                                    <i class="zmdi zmdi-floppy"></i> &nbsp; Guardar
                                                </button>

                                                <button class="button is-danger is-hovered" type="button" onclick="cancelarform()">
                                                    <i class="zmdi zmdi-close"></i> &nbsp; Cancelar
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </article>
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
    <script type="text/javascript" src="../scripts/permiso.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>

    <?php
}
