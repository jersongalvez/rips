<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////   ARCHIVO DE PRESTADORES      ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
////////////      VISTA PARA EL PROCESAMIENTO DE LOS PRESTADORES     ///////////
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
                                <i class="fas fa-clinic-medical" aria-hidden="true"></i>
                            </span>
                            <span>&nbsp; Administración de prestadores</span>
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
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                        </span>
                                        <span>Prestadores</span>
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
                                    Prestadores registrados 
                                </p>
                            </div>

                            <div class="column is-12" id="listadoregistros">
                                <div class="table-container">
                                    <table id="tbllistado" class="table is-striped is-hoverable is-fullwidth" >
                                        <thead>
                                            <tr>
                                                <th class="has-text-centered">Opciones</th>
                                                <th class="has-text-centered">Código</th>
                                                <th class="has-text-centered">Nit</th>
                                                <th class="has-text-centered">Nombre</th>
                                                <th class="has-text-centered">Ciudad</th>
                                                <th class="has-text-centered">Departamento</th>
                                                <th class="has-text-centered">Tipo</th>
                                                <th class="has-text-centered">Clase</th>
                                                <th class="has-text-centered">Estado</th>
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
                                                <th class="has-text-centered">Departamento</th>
                                                <th class="has-text-centered">Tipo</th>
                                                <th class="has-text-centered">Clase</th>
                                                <th class="has-text-centered">Estado</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="column is-12 hide-div" id="formularioregistros">
                                <div class="columns">
                                    <div class="column">
                                        <div class="columns">
                                            <div class="column is-9">
                                                <div class="field">
                                                    <label class="label has-text-left">Prestador</label>
                                                    <div class="control has-icons-right">
                                                        <input class="input is-hovered" type="text" name="nom_prestador" id="nom_prestador" readonly>
                                                        <span class="icon is-small is-right">
                                                            <i class="zmdi zmdi-attachment-alt"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="column">
                                                <div class="field">
                                                    <label class="label has-text-left">Nit</label>
                                                    <div class="control has-icons-right">
                                                        <input class="input is-hovered" type="text" name="nit_prestador" id="nit_prestador" readonly>
                                                        <span class="icon is-small is-right">
                                                            <i class="zmdi zmdi-file"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="" id="listadousuarios">
                                            <p class="buttons" id="btnagregarUsuario">
                                                <button class="button is-info is-hovered" type="button" onclick="mostrarform_usuario(true)">
                                                    <i class="zmdi zmdi-account-add"></i> &nbsp; Agregar usuario
                                                </button>
                                            </p>

                                            <table id="tblusuarios" class="table is-striped is-hoverable is-fullwidth" >
                                                <thead>
                                                    <tr>
                                                        <th class="has-text-centered">Opciones</th>
                                                        <th class="has-text-centered">Código</th>
                                                        <th class="has-text-centered">Tipo</th>
                                                        <th class="has-text-centered">Número</th>
                                                        <th class="has-text-centered">Nombre</th>
                                                        <th class="has-text-centered">Registro</th>
                                                        <th class="has-text-centered">Estado</th>
                                                    </tr>
                                                </thead>

                                                <tbody> </tbody>

                                                <tfoot>
                                                    <tr>
                                                        <th class="has-text-centered">Opciones</th>
                                                        <th class="has-text-centered">Código</th>
                                                        <th class="has-text-centered">Tipo</th>
                                                        <th class="has-text-centered">Número</th>
                                                        <th class="has-text-centered">Nombre</th>
                                                        <th class="has-text-centered">Registro</th>
                                                        <th class="has-text-centered">Estado</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <form name="formulario" id="formulario" method="POST"> 
                                    <div class="columns">
                                        <div class="column">
                                            <div class="columns">
                                                <div class="column is-3">
                                                    <input type="hidden" name="ni_prest" id="ni_prest">
                                                    <div class="field">
                                                        <label class="label has-text-left">Tipo documento (*)</label>
                                                        <div class="control">
                                                            <div class="select is-fullwidth">
                                                                <select name="tipo_documento" id="tipo_documento">
                                                                    <option value="CC" selected>CÉDULA CIUDADANÍA</option>
                                                                    <option value="CE">CÉDULA DE EXTRANJERÍA</option>
                                                                    <option value="CD">CARNÉ DIPLOMÁTICO</option>
                                                                    <option value="PA">PASAPORTE</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese el tipo documento</p>
                                                    </div>
                                                </div>

                                                <div class="column is-6">
                                                    <div class="field">
                                                        <label class="label has-text-left">Número documento (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="number" name="num_documento" id="num_documento" minlength="5" maxlength="18" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-assignment-o"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese el número de documento</p>
                                                    </div>
                                                </div>

                                                <div class="column">
                                                    <div class="field">
                                                        <label class="label has-text-left">Código (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="text" style="text-transform:uppercase;" onkeyup="javascript:this.value = this.value.toUpperCase();" 
                                                                   name="cod_usuario" id="cod_usuario" minlength="5" maxlength="20" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-account-o"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese código de usuario</p>
                                                    </div>
                                                </div>  
                                            </div>

                                            <div class="columns">
                                                <div class="column is-7">
                                                    <div class="field">
                                                        <label class="label has-text-left">Nombre (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="text" style="text-transform:uppercase;" onkeyup="javascript:this.value = this.value.toUpperCase();" 
                                                                   name="nom_usuario" id="nom_usuario" minlength="20" maxlength="50" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-accounts-list"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese el nombre de usuario</p>
                                                    </div>
                                                </div>

                                                <div class="column is-2" id="cambiarContrasena">
                                                    <div class="field">
                                                        <label class="label has-text-centered">¿Cambiar contraseña?</label>
                                                        <div class="control has-text-centered" style="margin-top: 20px;">
                                                            <label class="checkbox">
                                                                <input type="checkbox" id="cambioCa" class="cambioC">
                                                                SI
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="column">
                                                    <div class="field">
                                                        <label class="label has-text-left">Contraseña (*)</label>
                                                        <div class="control has-icons-right">
                                                            <input class="input is-hovered" type="password" name="password" id="password" minlength="5" maxlength="20" autocomplete="off" required>
                                                            <span class="icon is-small is-right">
                                                                <i class="zmdi zmdi-key"></i>
                                                            </span>
                                                        </div>
                                                        <p class="help has-text-left">Ingrese la contraseña</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="columns">
                                                <div class="column">
                                                    <div class="field">
                                                        <label class="label has-text-left">Permisos</label>
                                                        <div id="permisos"> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="buttons" style="margin-top: 30px;">
                                                <button class="button is-info is-hovered" type="submit" id="btnGuardar">
                                                    <i class="zmdi zmdi-floppy"></i> &nbsp; Guardar
                                                </button>

                                                <button class="button is-danger is-hovered" type="button" onclick="cancelarform_usuario()">
                                                    <i class="zmdi zmdi-close"></i> &nbsp; Cancelar
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </form>

                                <p class="buttons" id="btnSalir">
                                    <button class="button is-info is-hovered" type="button" onclick="cancelarform()">
                                        <i class="fas fa-long-arrow-alt-left"></i> &nbsp; Regresar
                                    </button>
                                </p>
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
    <script type="text/javascript" src="../scripts/prestador.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>

    <?php
}
