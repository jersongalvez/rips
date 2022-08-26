<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////      ARCHIVO DE USUARIOS      ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
////////////         VISTA PARA EL PROCESAMIENTO DE LOS USUARIOS     ///////////
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
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </span>
                            <span>&nbsp; Usuarios del sistema</span>
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
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                        </span>
                                        <span>Usuarios</span>
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
                                <p class="subtitle is-5"> Usuarios registrados </p>
                            </div>

                            <div class="column is-12" id="listadoregistros">
                                <div class="table-container">
                                    <table id="tbllistado" class="table is-striped is-hoverable is-fullwidth" >
                                        <thead>
                                            <tr>
                                                <th class="has-text-centered">Código</th>
                                                <th class="has-text-centered">Tipo</th>
                                                <th class="has-text-centered">Documento</th>
                                                <th class="has-text-centered">Nombre</th>
                                                <th class="has-text-centered">Prestador</th>
                                                <th class="has-text-centered">Registro</th>
                                                <th class="has-text-centered">Estado</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th class="has-text-centered">Código</th>
                                                <th class="has-text-centered">Tipo</th>
                                                <th class="has-text-centered">Documento</th>
                                                <th class="has-text-centered">Nombre</th>
                                                <th class="has-text-centered">Prestador</th>
                                                <th class="has-text-centered">Registro</th>
                                                <th class="has-text-centered">Estado</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
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
    <script type="text/javascript" src="../scripts/usuario.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}




